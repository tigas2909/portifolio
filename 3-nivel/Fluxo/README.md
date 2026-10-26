# Fluxo 💰

O **Fluxo** é um sistema de controle financeiro pessoal desenvolvido para facilitar a organização de ganhos, gastos e transações financeiras. O objetivo é permitir que o usuário acompanhe sua situação financeira sem depender de planilhas complexas.

O projeto possui uma API desenvolvida em PHP, banco de dados MySQL e uma interface web desenvolvida com Vue.js. Também conta com um módulo próprio de autenticação JWT, que faz parte do desenvolvimento da aplicação.

> **Status:** Em desenvolvimento — versão beta.
>
> O projeto está em construção. Os componentes do backend e do frontend já possuem uma estrutura inicial, mas a integração completa, a validação de todos os fluxos e os testes de ponta a ponta ainda precisam ser confirmados.

---

## 📌 Classificação

- **Categoria:** Projetos Interessantes e Desafiadores
- **Contexto:** Projeto pessoal
- **Status:** Em desenvolvimento
- **Versão:** Beta
- **Desenvolvedor:** Tiago Siqueira

---

## 🎯 Objetivo do projeto

O objetivo do Fluxo é oferecer uma maneira simples de organizar as finanças pessoais, com atenção especial ao acompanhamento de gastos e pagamentos.

A aplicação busca centralizar informações financeiras e facilitar a consulta dos dados cadastrados.

### Problema que procura resolver

Planilhas eletrônicas podem ser pouco intuitivas para pessoas que não estão acostumadas a utilizá-las. Além disso, acompanhar gastos parcelados, vencimentos e pagamentos pode se tornar complicado quando as informações estão espalhadas.

O Fluxo pretende oferecer uma alternativa mais acessível, reunindo essas informações em uma única aplicação.

### Público-alvo

Pessoas que desejam organizar suas finanças pessoais, acompanhar seus ganhos e gastos e compreender melhor sua situação financeira.

---

## ✨ Funcionalidades

As funcionalidades abaixo representam o escopo do projeto. A presença de arquivos relacionados a uma funcionalidade não significa, por si só, que todo o fluxo esteja finalizado ou validado.

### Backend — API

- Estrutura de API em PHP.
- Processamento de requisições e respostas JSON.
- Separação de responsabilidades entre controllers, DTOs e DAOs.
- Validação de dados recebidos.
- Persistência de dados utilizando PDO e MySQL.
- Estrutura de cadastro e gerenciamento de usuários.
- Módulo próprio de geração e validação de tokens JWT.
- Estrutura para gerenciamento de ganhos e transações.
- Padronização de respostas da aplicação.

### Frontend — Vue.js

A interface possui componentes organizados por funcionalidade:

- **Login:** interface de autenticação.
- **Cadastro de usuário:** formulário de criação de conta.
- **Atualização de senha:** interface relacionada à alteração de senha.
- **Ganhos:** componentes para cadastro, listagem, consulta e edição.
- **Transações:** componentes para cadastro, listagem, consulta e edição.
- **Página principal:** estrutura da área principal da aplicação.
- **Roteamento:** navegação entre as páginas.
- **Stores:** organização do estado relacionado a ganhos, transações e página principal.
- **Serviço de API:** módulo para centralizar a comunicação com o backend.
- **Estilização:** arquivo CSS para o tema da interface.

> **Observação:** os componentes estão presentes no código, mas os fluxos completos de autenticação, persistência, atualização e exclusão precisam ser verificados individualmente.

### Funcionalidades planejadas

- [ ] Autenticação integrada ao backend.
- [ ] Proteção de rotas e controle de acesso por usuário.
- [ ] Cadastro, consulta, edição e exclusão de registros financeiros.
- [ ] Acompanhamento de parcelas individuais.
- [ ] Registro de vencimentos e pagamentos.
- [ ] Histórico financeiro por período.
- [ ] Dashboard com resumo financeiro.
- [ ] Cálculo de saldo com base nos valores efetivamente recebidos e pagos.
- [ ] Testes automatizados e validação de segurança.
- [ ] Publicação em domínio próprio para disponibilizar uma versão beta.

---

## 🛠️ Tecnologias utilizadas

| Tecnologia | Utilização |
|---|---|
| PHP 8 ou superior | Desenvolvimento do backend |
| PDO | Comunicação com o banco de dados |
| MySQL / MariaDB | Persistência de dados |
| SQL | Definição e consulta de dados |
| JSON | Comunicação entre cliente e API |
| JWT — HS256 | Estrutura de autenticação por tokens |
| JavaScript | Lógica da interface web |
| Vue.js | Desenvolvimento do frontend |
| Vite | Ambiente de desenvolvimento do frontend |
| CSS | Estilização da interface |
| Git e GitHub | Controle de versão e publicação do código |

### Tecnologias e conceitos complementares

- Arquitetura em camadas.
- Validação de dados.
- Consultas preparadas.
- Separação entre regras de aplicação e acesso a dados.
- Controle de versão.
- Organização modular do frontend.

---

## 🏗️ Arquitetura do projeto

O Fluxo separa o backend da interface web.

O backend é responsável por processar as requisições, validar os dados e acessar o banco de dados. O frontend oferece a interface utilizada pelo usuário e se comunica com a API.

### Fluxo previsto de comunicação

```text
┌──────────────────────────────┐
│            Usuário           │
└──────────────┬───────────────┘
               │
               ▼
┌──────────────────────────────┐
│       Frontend — Vue.js      │
│                              │
│  Páginas, stores e serviços  │
└──────────────┬───────────────┘
               │
               │ Requisições HTTP / JSON
               ▼
┌──────────────────────────────┐
│         Backend — PHP        │
│                              │
│  Entrada e roteamento        │
│  Controllers                 │
│  DTOs                        │
│  DAOs                        │
│  Módulo JWT                  │
└──────────────┬───────────────┘
               │
               │ PDO
               ▼
┌──────────────────────────────┐
│        MySQL / MariaDB       │
│                              │
│  Usuários                    │
│  Ganhos                      │
│  Transações                  │
└──────────────────────────────┘
```

Esse diagrama representa a arquitetura pretendida. A integração efetiva entre todos os componentes deve ser validada durante os testes.

### Organização do backend

- **Entrada e roteamento:** recebe as requisições e direciona o processamento.
- **Controllers:** coordenam os casos de uso.
- **DTOs:** concentram a validação e a organização dos dados recebidos.
- **DAOs:** realizam as operações de acesso ao banco de dados.
- **Configuração:** centraliza os parâmetros de conexão e configuração.
- **JWT:** contém a implementação relacionada à geração e validação de tokens.

### Organização do frontend

- **Páginas:** representam as telas da aplicação.
- **Stores:** organizam o estado utilizado pelos componentes.
- **Services:** centralizam funcionalidades de comunicação com a API.
- **Router:** configura a navegação entre as páginas.
- **Assets:** armazenam recursos de estilo e outros arquivos da interface.

---

## 📂 Estrutura de diretórios

A estrutura abaixo corresponde à organização dos arquivos incluídos na versão publicada do projeto.

```text
Fluxo/
├── .gitignore
│
├── config/
│   ├── DatabaseConfig.php
│   ├── config.php              # Configuração local, não versionada
│   └── database.sql
│
├── front/
│   ├── package.json
│   ├── package-lock.json
│   │
│   └── fluxo/
│       ├── .editorconfig
│       ├── .gitattributes
│       ├── .gitignore
│       ├── .oxlintrc.json
│       ├── .prettierrc.json
│       ├── README.md
│       ├── eslint.config.js
│       ├── index.html
│       ├── jsconfig.json
│       ├── package.json
│       ├── package-lock.json
│       ├── vite.config.js
│       │
│       ├── public/
│       │   └── favicon.ico
│       │
│       └── src/
│           ├── App.vue
│           ├── main.js
│           │
│           ├── asset/
│           │   └── theme.css
│           │
│           ├── income/
│           │   ├── incomeStore.js
│           │   ├── index.vue
│           │   ├── insert.vue
│           │   ├── list.vue
│           │   └── update.vue
│           │
│           ├── login/
│           │   ├── CreateUser.vue
│           │   ├── Login.vue
│           │   └── UpdatePassword.vue
│           │
│           ├── principal/
│           │   ├── pagePrincipal.vue
│           │   ├── principalStore.js
│           │   ├── public.vue
│           │   └── temporario.vue
│           │
│           ├── router/
│           │   └── Router.js
│           │
│           ├── service/
│           │   └── api.js
│           │
│           └── transaction/
│               ├── index.vue
│               ├── insert.vue
│               ├── list.vue
│               ├── transactionStore.js
│               └── update.vue
│
├── src/
│   ├── index.php
│   ├── loginIndex.php
│   │
│   ├── controller/
│   │   ├── Controller.php
│   │   └── loginController.php
│   │
│   ├── DTOs/
│   │   ├── CreateDTO.php
│   │   └── ResponseDTO.php
│   │
│   ├── DAO/
│   │   ├── IncomeDAO.php
│   │   ├── MonthRange.php
│   │   ├── TransactionDAO.php
│   │   └── UserDao.php
│   │
│   ├── jwt/
│   │   └── ConfigJWT.php
│   │
│   └── response/
│       └── ViewResponse.php
│
├── test/
│   ├── ControllersTest.php
│   ├── DtosTest.php
│   ├── GenericTest.php
│   └── ResponseTest.php
│
└── jsonTest.json
```

O arquivo `config/config.php` é uma configuração local e não deve conter credenciais reais no repositório público.

---

## 🗄️ Banco de dados

O projeto utiliza MySQL ou MariaDB, com acesso por PDO.

O esquema inicial contempla as entidades principais do sistema financeiro.

### Usuários — `users`

Responsável por armazenar os dados de cadastro dos usuários.

**Campos previstos no esquema inicial:**

| Campo | Descrição |
|---|---|
| `id` | Identificador do usuário |
| `name` | Nome do usuário |
| `email` | E-mail do usuário |
| `password` | Senha armazenada |
| `created_at` | Data de criação do registro |

O e-mail é definido como único no esquema inicial.

### Ganhos — `income`

Armazena os registros de valores recebidos ou ganhos.

**Campos previstos no esquema inicial:**

| Campo | Descrição |
|---|---|
| `id` | Identificador do ganho |
| `user_id` | Identificador do usuário relacionado |
| `description` | Descrição do ganho |
| `amount` | Valor do ganho |
| `received_at` | Data de recebimento |
| `created_at` | Data de criação do registro |

### Transações — `transactions`

Armazena os registros de gastos e informações relacionadas às transações.

**Campos previstos no esquema inicial:**

| Campo | Descrição |
|---|---|
| `id` | Identificador da transação |
| `user_id` | Identificador do usuário relacionado |
| `description` | Descrição da transação |
| `amount` | Valor da transação |
| `type` | Tipo da transação |
| `status` | Situação da transação |
| `due_date` | Data de vencimento |
| `paid_at` | Data do pagamento |
| `total_installments` | Quantidade total de parcelas |
| `paid_installments` | Quantidade de parcelas pagas |
| `created_at` | Data de criação do registro |

Os campos acima descrevem o esquema inicial documentado. Alterações posteriores no banco devem ser refletidas nesta seção.

### Modelagem financeira

O projeto pretende permitir que os gastos sejam acompanhados de acordo com seus vencimentos e pagamentos.

Uma das etapas de evolução consiste em representar cada parcela individualmente, incluindo:

- Número da parcela.
- Valor previsto.
- Data de vencimento.
- Valor efetivamente pago.
- Data do pagamento.
- Situação da parcela.

Essa estrutura é importante para calcular corretamente os valores pendentes e o saldo financeiro.

---

## 🔐 Segurança

A segurança é uma parte importante do desenvolvimento do Fluxo, especialmente porque a aplicação lida com informações financeiras pessoais.

### JWT

O projeto contém uma implementação própria de JWT utilizando o algoritmo HS256.

O módulo `ConfigJWT.php` concentra funcionalidades relacionadas à criação e à validação dos tokens.

A implementação utiliza recursos do PHP, como `hash_hmac`, codificação base64url e comparação de assinaturas com `hash_equals`.

A presença desse módulo não significa que a autenticação esteja completamente integrada ao sistema.

### Boas práticas previstas

- Utilizar consultas preparadas por meio do PDO.
- Validar os dados recebidos pela API.
- Manter as credenciais fora do repositório público.
- Armazenar senhas utilizando mecanismos apropriados de hash.
- Validar os tokens antes de permitir o acesso às rotas protegidas.
- Obter a identificação do usuário autenticado a partir de uma fonte confiável.
- Garantir que cada usuário acesse somente seus próprios registros.
- Evitar expor detalhes internos do banco de dados nas respostas públicas.
- Configurar adequadamente CORS e cookies caso o frontend e o backend sejam publicados em origens diferentes.

> **Importante:** essas medidas devem ser verificadas no código e nos testes antes de considerar a aplicação pronta para uso com dados financeiros reais.

---

## ⚙️ Como executar o projeto

Os procedimentos abaixo descrevem a configuração de desenvolvimento local. Os caminhos e as configurações podem precisar de ajustes conforme o ambiente utilizado.

### Pré-requisitos

- PHP 8.0 ou superior.
- Extensão `pdo_mysql` habilitada.
- MySQL ou MariaDB.
- Node.js e npm.
- Git.
- Um navegador web.
- Um cliente HTTP, como Postman ou Thunder Client, para testar a API.

### 1. Obter o projeto

O Fluxo está publicado dentro do repositório de portfólio:

**Repositório:** https://github.com/tigas2909/portifolio

Clone o repositório:

```bash
git clone https://github.com/tigas2909/portifolio.git
```

Entre no diretório do Fluxo dentro da pasta de nível 3. O caminho exato depende do nome da pasta utilizado no portfólio.

### 2. Configurar o banco de dados

Crie o banco conforme o esquema fornecido:

```bash
mysql -u SEU_USUARIO -p < config/database.sql
```

Substitua `SEU_USUARIO` pelo usuário do banco de dados.

Confira o conteúdo de `config/database.sql` para verificar o nome do banco e as instruções executadas antes de utilizar o comando.

### 3. Configurar o backend

Crie o arquivo local `config/config.php`, caso ele ainda não exista.

**Exemplo de configuração:**

