# Fluxo

API REST em PHP para controle financeiro pessoal. O sistema registra ganhos e transações (contas recorrentes, parceladas e ocasionais), com status de pagamento e controle de parcelas, como uma planilha simplificada acessada por requisições JSON. O projeto inclui uma implementação própria de autenticação por JWT (HS256), ainda em fase de integração.

## Classificação

- **Categoria:** Projetos Interessantes e Desafiadores
- **Status:** Em desenvolvimento
- **Contexto:** Pessoal

> **Aviso de status:** o projeto está em construção. A persistência e a validação de ganhos e o módulo JWT já estão escritos, mas a API ainda não foi testada de ponta a ponta. Login, criação de transações, rotas protegidas e o front-end em Vue.js ainda não foram implementados. As seções abaixo separam claramente o que existe no código do que é planejado.

---

## 1. Visão geral

O Fluxo é uma API para que uma pessoa registre o dinheiro que entra (ganhos) e o que precisa pagar (transações), acompanhando o que está pendente, o que já foi pago e, no caso de compras parceladas, quantas parcelas já foram quitadas.

- **O que é:** uma API REST em PHP, com banco MySQL, que recebe e responde JSON.
- **Problema que procura resolver:** substituir planilhas de controle financeiro por um sistema com usuários, validação de dados e persistência.
- **Público-alvo:** pessoas que querem organizar suas finanças pessoais.
- **Principais funcionalidades planejadas:** cadastro e login de usuários com token JWT, registro de ganhos e transações, controle de parcelas e status de pagamento, e um front-end em Vue.js.

## 2. Objetivos

### Objetivo principal

Construir um sistema de controle financeiro pessoal com autenticação, em que cada usuário acessa apenas os seus próprios dados.

### Objetivos específicos

- Modelar ganhos e transações (recorrentes, parceladas e ocasionais) com status e controle de parcelas.
- Validar os dados de entrada antes de gravá-los no banco.
- Implementar autenticação com JWT e identificar o usuário a partir do token.
- Criar um front-end em Vue.js para usar e testar a API.

## 3. Motivação

Projeto pessoal desenvolvido aos poucos como forma de aprender e de se desafiar tecnicamente. Reúne tópicos que não apareciam nos exercícios anteriores do portfólio: modelagem de dados com regras de negócio (parcelas e status), validação de entrada em camadas e autenticação baseada em token implementada manualmente, sem biblioteca.

## 4. Tecnologias utilizadas

| Tecnologia | Finalidade |
|---|---|
| PHP 8 | Linguagem da API (usa tipos de união, que exigem PHP 8.0 ou superior) |
| PDO (MySQL) | Acesso ao banco com consultas preparadas |
| MySQL / MariaDB | Persistência de usuários, ganhos e transações |
| JSON | Formato de entrada e saída da API |
| JWT (HS256, implementação própria) | Autenticação por token, usando `hash_hmac` e base64url |
| Vue.js *(planejado)* | Front-end para consumir a API |

## 5. Requisitos e funcionalidades

### Implementadas no código

- Roteamento em `index.php` por `type` (`income` ou `transaction`), método HTTP e `action`, com resposta 400 para JSON, tipo, método ou ação inválidos.
- Criação de ganho (`income`): validação, gravação e retorno do id criado.
- Listagem de ganhos e busca de ganho por id.
- Listagem de transações e busca de transação por id.
- Validação dos dados de ganhos e transações no `CreateDTO`.
- Esquema do banco com usuários, ganhos e transações.
- Módulo JWT (`ConfigJWT`): geração de token de acesso, validação de token (header, claims, expiração e assinatura), geração de token de refresh e extração do id do usuário.

### Em andamento

- Integração do JWT com as rotas (o módulo existe, mas ainda não é chamado pela API).
- Criação de transações (a validação existe, o `create` do DAO ainda não).

### Planejadas

- Cadastro e login de usuários (com `password_hash` / `password_verify`).
- Atualização e exclusão de ganhos e transações.
- Filtro dos dados pelo usuário do token.
- Atualização de pagamento e de status das transações.
- Front-end em Vue.js.
- Lançamento de transações por e-mail (versão futura).
- Testes automatizados.

### Regras de negócio

Implementadas na validação:

- O valor (`amount`) deve ser numérico e maior que zero.
- O tipo da transação deve ser `RECURRING`, `INSTALLMENT` ou `ONE_TIME`.
- O total de parcelas deve ser positivo e as parcelas pagas não podem ser negativas.
- As parcelas pagas não podem ser maiores que o total de parcelas.
- Descrição, valor e data são obrigatórios.

Definidas no esquema do banco:

- Status da transação: `PENDING` (padrão), `PAID` ou `COMPLETED`.
- Total de parcelas com padrão 1 e parcelas pagas com padrão 0.
- O e-mail do usuário é único.

### Requisitos e restrições

- PHP 8.0 ou superior com a extensão `pdo_mysql`.
- MySQL ou MariaDB com suporte a `utf8mb4`.
- Um arquivo de configuração local (`config/config.php`) com as credenciais do banco e o segredo do JWT. Esse arquivo não deve ser versionado.

## 6. Arquitetura e organização

### Arquitetura utilizada

Arquitetura em camadas, com a responsabilidade separada entre entrada e roteamento, orquestração, validação e acesso a dados. A separação facilita trocar ou testar uma camada isolada e prepara o projeto para receber a autenticação em um único ponto de entrada.

### Fluxo de funcionamento

```text
Cliente (Postman ou front-end Vue, planejado)
        |  JSON: { type, action, data }
        v
src/index.php
(valida o JSON e roteia por type + método HTTP + action)
        |
        v
Controller
(orquestra o caso de uso)
        |
        v
CreateDTO
(valida e normaliza os dados de entrada)
        |
        v
DAO (PDO, consultas preparadas)
        |
        v
MySQL
```

Planejado: antes do roteamento, o token enviado em `Authorization: Bearer <token>` será validado por `ConfigJWT` e o id do usuário será repassado ao Controller e aos DAOs.

### Estrutura do projeto

```text
fluxo/
├── config/
│   ├── database.sql         # esquema do banco
│   ├── DatabaseConfig.php   # conexão PDO
│   └── config.php           # configuração local (não versionar)
├── src/
│   ├── index.php            # entrada da API e roteamento
│   ├── controller/
│   │   └── Controller.php   # orquestração dos casos de uso
│   ├── DTOs/
│   │   ├── CreateDTO.php    # validação de entrada
│   │   └── ResponseDTO.php  # padronização de respostas (em desenvolvimento)
│   ├── DAO/
│   │   ├── IncomeDAO.php
│   │   ├── TransactionDAO.php
│   │   └── UserDao.php
│   └── jwt/
│       └── ConfigJWT.php    # geração e validação de tokens
├── test/                    # reservado para testes (vazio)
├── view/                    # reservado para o front-end (vazio)
└── README.md
```

## 7. Implementação técnica

- **Entrada e roteamento:** `index.php` lê o corpo JSON, extrai `type`, `action` e `data`, e delega ao `Controller` conforme o método HTTP.
- **Validação:** o `CreateDTO` valida campos obrigatórios, tipos, valores positivos, lista de tipos permitidos e a consistência entre parcelas pagas e totais, devolvendo uma lista de erros em vez de lançar exceção.
- **Acesso a dados:** os DAOs usam `PDO` com parâmetros nomeados e capturam `PDOException`, devolvendo uma resposta padronizada com `success` e `data` ou `errors`.
- **JWT:** `ConfigJWT` monta o token com header, payload e assinatura codificados em base64url. A assinatura usa HMAC-SHA256 e é conferida com `hash_equals`. O payload contém `id`, `type` (`access` ou `refresh`), `iat` e `exp`.

### Decisões técnicas

| Decisão | Justificativa |
|---|---|
| `DECIMAL(12,2)` para valores monetários | Evita erros de arredondamento de ponto flutuante |
| `ENUM` para tipo e status das transações | Restringe os valores válidos no próprio banco |
| Chaves estrangeiras com `ON DELETE CASCADE` | Remove ganhos e transações junto com o usuário |
| `ATTR_EMULATE_PREPARES => false` e consultas preparadas | Mantém os valores separados do SQL, protegendo contra injeção de SQL |
| Resposta padronizada (`success` e `data`/`errors`) | Simplifica o consumo da API pelo front-end |
| JWT implementado sem biblioteca | Entender na prática a estrutura do padrão (header, payload e assinatura) |
| `user_id` obtido do token (planejado) | Evita que o cliente informe o dono do registro no corpo da requisição |

## 8. Banco de dados e persistência

### Tecnologia utilizada

MySQL (ou MariaDB), com acesso via PDO. O banco se chama `financia`, nome original do projeto.

### Estrutura dos dados

- **`users`**: `id`, `name`, `email` (único), `password`, `created_at`.
- **`income`** (ganhos): `id`, `user_id`, `description`, `amount`, `received_at`, `created_at`.
- **`transactions`**: `id`, `user_id`, `description`, `amount`, `type` (`RECURRING`, `INSTALLMENT`, `ONE_TIME`), `status` (`PENDING`, `PAID`, `COMPLETED`), `due_date`, `paid_at`, `total_installments`, `paid_installments`, `created_at`.

Cada ganho e cada transação pertence a um usuário (`user_id`, chave estrangeira para `users`).

### Estratégia de persistência

Inserção e consulta (listar tudo e por id) estão escritas para ganhos. Para transações, só as consultas estão escritas. Atualização e exclusão ainda não estão implementadas.

## 9. Segurança e validações

- **Autenticação:** o módulo de geração e validação de tokens JWT está implementado, mas ainda não está integrado às rotas e não há cadastro nem login.
- **Autorização:** não implementada. Atualmente os dados não são filtrados por usuário, e o `user_id` ainda é enviado no corpo da requisição.
- **Validação:** implementada para criação de ganhos e de transações (ver seção 5). As datas ainda não têm validação de formato.
- **Tratamento de erros:** erros de banco são capturados nos DAOs e devolvidos como resposta padronizada. Entradas inválidas na API recebem HTTP 400.
- **Proteção de dados:** as consultas usam parâmetros preparados. A coluna `password` comporta o hash de senha, mas o armazenamento com `password_hash` ainda não foi implementado. As credenciais ficam em um arquivo de configuração local que não deve ir para o repositório.

## 10. Como executar o projeto

> A execução de ponta a ponta ainda não foi validada. Estes passos descrevem a configuração prevista.

### Pré-requisitos

- PHP 8.0 ou superior com `pdo_mysql`.
- MySQL ou MariaDB.
- Um cliente HTTP para testar (Postman, Thunder Client ou `curl`).

### 1. Clonar o repositório

```bash
git clone <URL_DO_REPOSITORIO>
cd fluxo
```

### 2. Configurar o ambiente

Crie o banco executando o script do esquema:

```bash
mysql -u <usuario> -p < config/database.sql
```

Crie o arquivo `config/config.php` com as suas credenciais (não versione este arquivo):

```php
<?php
define('HOST', 'localhost');
define('DATABASE', 'financia');
define('USERNAME', 'seu_usuario');
define('PASSWORD_DATABASE', 'sua_senha');
define('PASSWORD_JWT', 'segredo_longo_e_aleatorio');
```

Para gerar um segredo aleatório para o JWT:

```bash
php -r "echo bin2hex(random_bytes(32));"
```

### 3. Instalar dependências

O projeto não usa dependências externas (sem Composer).

### 4. Executar a aplicação

```bash
php -S localhost:8000 -t src
```

### 5. Acessar ou utilizar o sistema

Envie requisições JSON para `http://localhost:8000/index.php`, como nos exemplos abaixo.

## 11. Exemplos de utilização

O formato atual das requisições usa um envelope com `type`, `action` e `data` no corpo JSON. Esse formato pode mudar durante o desenvolvimento.

### Criar um ganho

**Entrada** (`POST`):

```json
{
  "type": "income",
  "action": "create",
  "data": {
    "user_id": 1,
    "description": "salario mensal",
    "amount": 3000.32,
    "received_at": "2026-09-30"
  }
}
```

**Resultado esperado:**

```json
{
  "success": true,
  "id": "1"
}
```

### Listar ganhos

```bash
curl -X GET http://localhost:8000/index.php \
  -H "Content-Type: application/json" \
  -d '{"type": "income", "action": "selectAll"}'
```

**Resultado esperado:**

```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "user_id": 1,
      "description": "salario mensal",
      "amount": "3000.32",
      "received_at": "2026-09-30",
      "created_at": "2026-10-11 10:00:00"
    }
  ]
}
```

Os resultados acima são o comportamento previsto pelo código, ainda não confirmado por execução.

## 12. Testes e resultados

### Testes realizados

Ainda não foram realizados testes automatizados nem validação ponta a ponta da API.

### Testes planejados

- Criação e listagem de ganhos pela API.
- Validação de entradas inválidas (valor negativo, tipo inexistente, parcelas pagas maiores que o total).
- Geração e validação de tokens JWT (token válido, expirado, adulterado e de tipo errado).
- Isolamento de dados entre usuários, depois da integração do JWT.

A pasta `test/` está reservada para esses testes.

## 13. Desafios técnicos

### Implementar JWT sem biblioteca

- **Problema:** gerar e validar tokens HS256 manualmente.
- **Por que exige atenção:** a validação precisa verificar o algoritmo declarado, a estrutura do token, os tipos dos claims, a expiração e a assinatura, e comparar a assinatura sem vazar informação por tempo de resposta.
- **Solução adotada:** codificação base64url com decodificação estrita, validação do header (`typ` e `alg` exatos), checagem dos claims, assinatura com `hash_hmac` e comparação com `hash_equals`.
- **Limitações que permanecem:** o módulo ainda não está integrado às rotas e tem pontos a revisar (ver seção 14).

### Modelar transações recorrentes, parceladas e ocasionais

- **Problema:** representar tipos diferentes de despesa em uma única tabela.
- **Solução adotada:** campos de tipo, status e controle de parcelas (`total_installments` e `paid_installments`), com validação da consistência entre eles.
- **Limitações que permanecem:** a lógica de pagamento e de mudança de status ainda não está implementada.

## 14. Limitações conhecidas

- A API ainda não foi executada de ponta a ponta. Há pendências de integração entre os componentes (nomes de classes e métodos e configuração da conexão) a corrigir antes da primeira versão funcional.
- Não há cadastro, login nem rotas protegidas. O JWT está escrito, mas não é usado.
- Os dados não são filtrados por usuário.
- A criação de transações, a atualização e a exclusão não estão implementadas.
- As requisições de consulta usam corpo JSON em requisições `GET`, formato que muitos clientes (incluindo o `fetch` dos navegadores) não aceitam. Deve mudar quando o front-end entrar.
- Não há tratamento de CORS para um front-end em outra origem.
- As mensagens de erro do banco são devolvidas ao cliente e devem ser substituídas por mensagens genéricas.
- Pontos a revisar no módulo JWT: a verificação de existência do usuário compara o id com a contagem de usuários em vez de consultar o usuário, e os tokens de acesso e de refresh ainda não são claramente separados.
- Sem testes automatizados.

## 15. Possíveis melhorias

- Implementar cadastro e login, com `password_hash` e `password_verify`.
- Proteger as rotas com o JWT e filtrar todos os dados pelo usuário do token.
- Completar o CRUD de ganhos e transações.
- Implementar o fluxo de pagamento, com atualização de parcelas e status.
- Adotar rotas REST convencionais, com `GET` sem corpo e parâmetros na URL.
- Adicionar CORS, paginação e um resumo de saldo por período.
- Criar o front-end em Vue.js.
- Permitir o lançamento de transações por e-mail.
- Escrever testes automatizados para validação, DAOs e JWT.

## 16. Aprendizados

Durante o desenvolvimento estão sendo praticados: a modelagem de um domínio financeiro com ENUMs, chaves estrangeiras e `DECIMAL`; o uso de PDO com consultas preparadas; a validação de entrada em camadas; a estrutura e a validação de tokens JWT; e a organização de uma API em camadas (entrada, controller, DTO e DAO).

## 17. Licença e uso

Licença ainda não definida. Até que uma licença seja escolhida,
