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