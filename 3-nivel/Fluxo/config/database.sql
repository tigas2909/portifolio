CREATE DATABASE financia
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE financia;


-- =========================================
-- TABLE: USERS
-- =========================================

CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    name VARCHAR(255) NOT NULL,

    email VARCHAR(255) NOT NULL UNIQUE,

    password VARCHAR(255) NOT NULL,

    password_type ENUM('TEMPORARY', 'PERMANENT')
        NOT NULL DEFAULT 'TEMPORARY',

    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);


-- =========================================
-- TABLE: INCOME
-- =========================================

CREATE TABLE income (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    user_id BIGINT UNSIGNED NOT NULL,

    description VARCHAR(255) NOT NULL,

    amount DECIMAL(12,2) NOT NULL,

    received_at DATE NOT NULL,

    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_income_user
        FOREIGN KEY (user_id)
        REFERENCES users(id)
        ON DELETE CASCADE
);


-- =========================================
-- TABLE: TRANSACTIONS
-- =========================================

CREATE TABLE transactions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    user_id BIGINT UNSIGNED NOT NULL,

    description VARCHAR(255) NOT NULL,

    amount DECIMAL(12,2) NOT NULL,

    type ENUM(
        'RECURRING',
        'INSTALLMENT',
        'ONE_TIME'
    ) NOT NULL,

    status ENUM(
        'PENDING',
        'PAID',
        'COMPLETED'
    ) NOT NULL DEFAULT 'PENDING',

    due_date DATE NOT NULL,

    paid_at DATE NULL,

    total_installments INT UNSIGNED NOT NULL DEFAULT 1,

    paid_installments INT UNSIGNED NOT NULL DEFAULT 0,

    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_transactions_user
        FOREIGN KEY (user_id)
        REFERENCES users(id)
        ON DELETE CASCADE
);

CREATE TABLE installments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    transaction_id BIGINT UNSIGNED NOT NULL,

    amount DECIMAL(12,2) NOT NULL,

    due_date DATE NOT NULL,

    status ENUM('PENDING', 'PAID') NOT NULL DEFAULT 'PENDING',

    paid_at DATE NULL,

    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_installments_transaction
        FOREIGN KEY (transaction_id)
        REFERENCES transactions(id)
        ON DELETE CASCADE,

    CONSTRAINT chk_installments_amount
        CHECK (amount > 0),

    CONSTRAINT chk_installments_paid_at
        CHECK (
            (status = 'PAID' AND paid_at IS NOT NULL)
            OR
            (status = 'PENDING' AND paid_at IS NULL)
        )
);

DELIMITER $$

CREATE PROCEDURE INSERT_TRANSACTION(
    IN P_USER_ID BIGINT UNSIGNED,
    IN P_DESCRIPTION VARCHAR(255),
    IN P_AMOUNT DECIMAL(12,2),
    IN P_TYPE VARCHAR(20),
    IN P_DUE_DATE DATE,
    IN P_TOTAL_INSTALLMENTS INT UNSIGNED
)
BEGIN
    DECLARE V_TRANSACTION_ID BIGINT UNSIGNED;
    DECLARE V_COUNTER INT UNSIGNED DEFAULT 1;
    DECLARE V_INSTALLMENT_AMOUNT DECIMAL(12,2);

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    START TRANSACTION;

    -- Valida os dados obrigatórios
    IF P_USER_ID IS NULL
       OR P_DESCRIPTION IS NULL
       OR TRIM(P_DESCRIPTION) = ''
       OR P_AMOUNT IS NULL
       OR P_DUE_DATE IS NULL
       OR P_TYPE IS NULL THEN

        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Os campos obrigatórios devem ser preenchidos';
    END IF;

    -- Valida o valor
    IF P_AMOUNT <= 0 THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'O valor deve ser maior que zero';
    END IF;

    -- Valida o tipo
    IF P_TYPE NOT IN ('ONE_TIME', 'INSTALLMENT', 'RECURRING') THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Tipo de transação inválido';
    END IF;

    -- Valida a quantidade de parcelas
    IF P_TYPE = 'INSTALLMENT'
       AND (
           P_TOTAL_INSTALLMENTS IS NULL
           OR P_TOTAL_INSTALLMENTS < 2
       ) THEN

        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'O parcelamento deve ter pelo menos duas parcelas';
    END IF;

    -- Cria a transação principal
    INSERT INTO transactions (
        user_id,
        description,
        amount,
        type,
        status,
        due_date,
        total_installments,
        paid_installments
    )
    VALUES (
        P_USER_ID,
        P_DESCRIPTION,
        P_AMOUNT,
        P_TYPE,
        'PENDING',
        P_DUE_DATE,
        CASE
            WHEN P_TYPE = 'INSTALLMENT'
                THEN P_TOTAL_INSTALLMENTS
            ELSE 1
        END,
        0
    );

    SET V_TRANSACTION_ID = LAST_INSERT_ID();

    -- Cria todas as parcelas ou a primeira ocorrência
    IF P_TYPE = 'INSTALLMENT' THEN

        SET V_INSTALLMENT_AMOUNT =
            ROUND(P_AMOUNT / P_TOTAL_INSTALLMENTS, 2);

        WHILE V_COUNTER <= P_TOTAL_INSTALLMENTS DO

            INSERT INTO installments (
                transaction_id,
                amount,
                due_date,
                status
            )
            VALUES (
                V_TRANSACTION_ID,

                -- Ajusta a última parcela para fechar o valor total
                CASE
                    WHEN V_COUNTER = P_TOTAL_INSTALLMENTS
                    THEN P_AMOUNT -
                        (
                            V_INSTALLMENT_AMOUNT
                            * (P_TOTAL_INSTALLMENTS - 1)
                        )
                    ELSE V_INSTALLMENT_AMOUNT
                END,

                -- Cada parcela vence um mês após a anterior,
                -- calculada a partir da data inicial
                DATE_ADD(
                    P_DUE_DATE,
                    INTERVAL (V_COUNTER - 1) MONTH
                ),

                'PENDING'
            );

            SET V_COUNTER = V_COUNTER + 1;

        END WHILE;

    ELSEIF P_TYPE = 'RECURRING' THEN

        -- Cria somente a primeira ocorrência.
        -- A próxima será criada após o pagamento da atual.
        INSERT INTO installments (
            transaction_id,
            amount,
            due_date,
            status
        )
        VALUES (
            V_TRANSACTION_ID,
            P_AMOUNT,
            P_DUE_DATE,
            'PENDING'
        );

    END IF;

    COMMIT;

    -- Retorna o ID criado
    SELECT V_TRANSACTION_ID AS transaction_id;

END$$

DELIMITER ;

DELIMITER $$

CREATE PROCEDURE UPDATE_PAYMENT(
    IN P_USER_ID BIGINT UNSIGNED,
    IN P_TRANSACTION_ID BIGINT UNSIGNED
)
BEGIN
    DECLARE V_TYPE VARCHAR(20);
    DECLARE V_STATUS VARCHAR(20);
    DECLARE V_AMOUNT DECIMAL(12,2);
    DECLARE V_DUE_DATE DATE;
    DECLARE V_INSTALLMENT_ID BIGINT UNSIGNED;
    DECLARE V_NEXT_DUE_DATE DATE;
    DECLARE V_PENDING_COUNT INT UNSIGNED DEFAULT 0;
    DECLARE V_PAID_INSTALLMENTS INT UNSIGNED DEFAULT 0;
    DECLARE V_TRANSACTION_EXISTS INT DEFAULT 0;

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    START TRANSACTION;

    -- Verifica se a transação pertence ao usuário
    SELECT COUNT(*)
    INTO V_TRANSACTION_EXISTS
    FROM transactions
    WHERE id = P_TRANSACTION_ID
      AND user_id = P_USER_ID;

    IF V_TRANSACTION_EXISTS = 0 THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Transação não encontrada';
    END IF;

    -- Bloqueia a transação durante a atualização
    SELECT type, status, amount, due_date, paid_installments
    INTO V_TYPE, V_STATUS, V_AMOUNT, V_DUE_DATE, V_PAID_INSTALLMENTS
    FROM transactions
    WHERE id = P_TRANSACTION_ID
      AND user_id = P_USER_ID
    FOR UPDATE;

    IF V_STATUS IN ('PAID', 'COMPLETED') THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Esta transação já foi concluída';
    END IF;

    -- ONE_TIME: marca a transação como paga
    IF V_TYPE = 'ONE_TIME' THEN

        UPDATE transactions
        SET status = 'PAID'
        WHERE id = P_TRANSACTION_ID
          AND user_id = P_USER_ID;

    -- INSTALLMENT: paga a parcela atual
    ELSEIF V_TYPE = 'INSTALLMENT' THEN

        SELECT id
        INTO V_INSTALLMENT_ID
        FROM installments
        WHERE transaction_id = P_TRANSACTION_ID
          AND due_date = V_DUE_DATE
          AND status = 'PENDING'
        ORDER BY id
        LIMIT 1
        FOR UPDATE;

        UPDATE installments
        SET status = 'PAID',
            paid_at = CURRENT_DATE()
        WHERE id = V_INSTALLMENT_ID
          AND transaction_id = P_TRANSACTION_ID
          AND status = 'PENDING';

        SET V_PAID_INSTALLMENTS = V_PAID_INSTALLMENTS + 1;

        UPDATE transactions
        SET paid_installments = V_PAID_INSTALLMENTS
        WHERE id = P_TRANSACTION_ID
          AND user_id = P_USER_ID;

        -- Procura a próxima parcela pendente
        SELECT COUNT(*)
        INTO V_PENDING_COUNT
        FROM installments
        WHERE transaction_id = P_TRANSACTION_ID
          AND status = 'PENDING';

        IF V_PENDING_COUNT = 0 THEN

            UPDATE transactions
            SET status = 'COMPLETED'
            WHERE id = P_TRANSACTION_ID
              AND user_id = P_USER_ID;

        ELSE

            SELECT MIN(due_date)
            INTO V_NEXT_DUE_DATE
            FROM installments
            WHERE transaction_id = P_TRANSACTION_ID
              AND status = 'PENDING';

            UPDATE transactions
            SET due_date = V_NEXT_DUE_DATE
            WHERE id = P_TRANSACTION_ID
              AND user_id = P_USER_ID;

        END IF;

    -- RECURRING: paga a ocorrência e cria a próxima
    ELSEIF V_TYPE = 'RECURRING' THEN

        SELECT id
        INTO V_INSTALLMENT_ID
        FROM installments
        WHERE transaction_id = P_TRANSACTION_ID
          AND due_date = V_DUE_DATE
          AND status = 'PENDING'
        ORDER BY id
        LIMIT 1
        FOR UPDATE;

        -- Marca a ocorrência atual como paga
        UPDATE installments
        SET status = 'PAID',
            paid_at = CURRENT_DATE()
        WHERE id = V_INSTALLMENT_ID
          AND transaction_id = P_TRANSACTION_ID
          AND status = 'PENDING';

        -- Calcula o próximo vencimento
        SET V_NEXT_DUE_DATE = DATE_ADD(
            V_DUE_DATE,
            INTERVAL 1 MONTH
        );

        -- Cria a próxima ocorrência
        INSERT INTO installments (
            transaction_id,
            amount,
            due_date,
            status
        )
        VALUES (
            P_TRANSACTION_ID,
            V_AMOUNT,
            V_NEXT_DUE_DATE,
            'PENDING'
        );

        -- Atualiza a data exibida pela transação
        UPDATE transactions
        SET due_date = V_NEXT_DUE_DATE
        WHERE id = P_TRANSACTION_ID
          AND user_id = P_USER_ID;

    END IF;

    COMMIT;

    SELECT
        P_TRANSACTION_ID AS transaction_id,
        V_TYPE AS type,
        V_NEXT_DUE_DATE AS next_due_date;

END$$

DELIMITER ;

DELIMITER $$

CREATE PROCEDURE PAY_INSTALLMENT(
    IN P_USER_ID BIGINT UNSIGNED,
    IN P_TRANSACTION_ID BIGINT UNSIGNED,
    IN P_DUE_DATE DATE
)
BEGIN
    DECLARE V_TYPE VARCHAR(20);
    DECLARE V_STATUS VARCHAR(20);
    DECLARE V_INSTALLMENT_ID BIGINT UNSIGNED;
    DECLARE V_INSTALLMENT_EXISTS INT DEFAULT 0;
    DECLARE V_PENDING_COUNT INT UNSIGNED DEFAULT 0;
    DECLARE V_PAID_COUNT INT UNSIGNED DEFAULT 0;
    DECLARE V_NEXT_DUE_DATE DATE;

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    START TRANSACTION;

    -- Verifica se a transação pertence ao usuário
    SELECT COUNT(*)
    INTO V_INSTALLMENT_EXISTS
    FROM transactions
    WHERE id = P_TRANSACTION_ID
      AND user_id = P_USER_ID;

    IF V_INSTALLMENT_EXISTS = 0 THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Transação não encontrada';
    END IF;

    -- Bloqueia a transação durante a operação
    SELECT type, status
    INTO V_TYPE, V_STATUS
    FROM transactions
    WHERE id = P_TRANSACTION_ID
      AND user_id = P_USER_ID
    FOR UPDATE;

    -- Só permite antecipação de parcelas
    IF V_TYPE <> 'INSTALLMENT' THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'A transação não é parcelada';
    END IF;

    IF V_STATUS = 'COMPLETED' THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'A transação já foi concluída';
    END IF;

    IF P_DUE_DATE IS NULL THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'A data da parcela é obrigatória';
    END IF;

    -- Verifica se existe uma parcela pendente
    -- para essa transação e data
    SELECT COUNT(*)
    INTO V_INSTALLMENT_EXISTS
    FROM installments
    WHERE transaction_id = P_TRANSACTION_ID
      AND due_date = P_DUE_DATE
      AND status = 'PENDING';

    IF V_INSTALLMENT_EXISTS = 0 THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Parcela não encontrada ou já paga';
    END IF;

    -- Recupera o ID da parcela que será antecipada
    SELECT id
    INTO V_INSTALLMENT_ID
    FROM installments
    WHERE transaction_id = P_TRANSACTION_ID
      AND due_date = P_DUE_DATE
      AND status = 'PENDING'
    ORDER BY id
    LIMIT 1
    FOR UPDATE;

    -- Marca somente a parcela escolhida como paga
    UPDATE installments
    SET status = 'PAID',
        paid_at = CURRENT_DATE()
    WHERE id = V_INSTALLMENT_ID
      AND transaction_id = P_TRANSACTION_ID
      AND status = 'PENDING';

    -- Recalcula quantas parcelas já foram pagas
    SELECT COUNT(*)
    INTO V_PAID_COUNT
    FROM installments
    WHERE transaction_id = P_TRANSACTION_ID
      AND status = 'PAID';

    UPDATE transactions
    SET paid_installments = V_PAID_COUNT
    WHERE id = P_TRANSACTION_ID
      AND user_id = P_USER_ID;

    -- Verifica se ainda existem parcelas pendentes
    SELECT COUNT(*)
    INTO V_PENDING_COUNT
    FROM installments
    WHERE transaction_id = P_TRANSACTION_ID
      AND status = 'PENDING';

    IF V_PENDING_COUNT = 0 THEN

        -- Todas as parcelas foram pagas
        UPDATE transactions
        SET status = 'COMPLETED'
        WHERE id = P_TRANSACTION_ID
          AND user_id = P_USER_ID;

    ELSE

        -- Busca a primeira parcela que ainda está pendente
        SELECT MIN(due_date)
        INTO V_NEXT_DUE_DATE
        FROM installments
        WHERE transaction_id = P_TRANSACTION_ID
          AND status = 'PENDING';

        -- Mantém o próximo vencimento correto
        UPDATE transactions
        SET due_date = V_NEXT_DUE_DATE,
            status = 'PENDING'
        WHERE id = P_TRANSACTION_ID
          AND user_id = P_USER_ID;

    END IF;

    COMMIT;

    SELECT
        P_TRANSACTION_ID AS transaction_id,
        V_INSTALLMENT_ID AS installment_id,
        V_PAID_COUNT AS paid_installments,
        V_PENDING_COUNT AS pending_installments,
        V_NEXT_DUE_DATE AS next_due_date;

END$$

DELIMITER ;