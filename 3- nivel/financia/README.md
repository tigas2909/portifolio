# Fluxo

Sistema de controle financeiro pessoal, composto por uma API em PHP e uma interface web. O Fluxo permite registrar ganhos e gastos, acompanhar parcelas pagas e pendentes e visualizar a situação financeira do mês, de forma simples, para quem tem pouca familiaridade com planilhas eletrônicas. O projeto inclui uma implementação própria de autenticação por JWT (HS256), ainda em fase de integração.

## Classificação

- **Categoria:** Projetos Interessantes e Desafiadores
- **Status:** Em desenvolvimento
- **Contexto:** Pessoal

> **Aviso de status:** o projeto está em construção. A persistência e a validação de ganhos e o módulo JWT já estão escritos, mas a API ainda não foi testada de ponta a ponta. Cadastro, login, rotas protegidas, o modelo completo de parcelas, o dashboard e a interface web ainda não foram implementados. As seções abaixo separam o que existe no código do que faz parte do escopo planejado.

---

## 1. Visão geral

O Fluxo centraliza o registro e o acompanhamento de ganhos, gastos, parcelas e pagamentos em uma interface simples, para que o usuário veja a sua situação financeira sem depender de conhecimentos avançados em planilhas.

- **O que é:** uma aplicação web com backend/API em PHP e MySQL e uma interface web (planejada em Vue.js).
- **Problema que procura resolver:** organizar a vida financeira pessoal, em especial o acompanhamento de gastos parcelados, das parcelas já pagas e dos valores ainda pendentes.
- **Público-alvo:** pessoas que querem organizar suas finanças, mas têm pouca familiaridade com planilhas.
- **Principais funcionalidades planejadas:** conta de usuário com login por JWT, registro de ganhos e gastos, geração automática de parcelas, registro de pagamentos, histórico mensal, dashboard e cálculo de saldo.

## 2. Objetivos

### Objetivo principal

Construir uma aplicação web de controle financeiro pessoal, com autenticação e controle de acesso, em que cada usuário vê e gerencia apenas os seus próprios dados.

### Objetivos específicos

- Permitir criar e gerenciar a conta do usuário.
- Registrar ganhos com suas datas de entrada e gastos em uma estrutura de parcelas.
- Gerar automaticamente as parcelas futuras de um gasto e acompanhar as parcelas pagas e pendentes.
- Registrar o valor e a data efetivamente pagos.
- Consultar ganhos e gastos por período e visualizar o histórico mensal.
- Exibir um dashboard com a situação financeira atual e o saldo, calculado com os valores efetivamente recebidos e pagos.
- Publicar o sistema em um domínio próprio, em uma versão beta.

## 3. Motivação

Muitas pessoas querem organizar suas finanças, mas desistem de planilhas por falta de familiaridade com a ferramenta. O Fluxo busca oferecer uma alternativa mais simples, com atenção especial aos gastos parcelados, que são difíceis de acompanhar em controles manuais.

Para o autor, o projeto também é um desafio técnico pessoal, desenvolvido aos poucos: modelagem de dados com regras de negócio, validação de entrada em camadas e autenticação por token implementada manualmente, sem biblioteca.

## 4. Tecnologias utilizadas

| Tecnologia | Finalidade |
|---|---|
| PHP 8 | Linguagem da API (usa tipos de união, que exigem PHP 8.0 ou superior) |
| PDO (MySQL) | Acesso ao banco com consultas preparadas |
| MySQL / MariaDB | Persistência de usuários, ganhos e transações |
| JSON | Formato de entrada e saída da API |
| JWT (HS256, implementação própria) | Autenticação por token, usando `hash_hmac` e base64url |
| Vue.js *(planejado)* | Interface web |
| Apache *(planejado)* | Servidor para a publicação em domínio próprio |
| Composer *(planejado)* | Gerenciamento de dependências, quando forem necessárias |

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

### Planejadas para a primeira versão

- Cadastro com nome, e-mail e senha, com a senha armazenada com hash.
- Login com JWT (access token e refresh token em cookies) e proteção das rotas.
- Controle de acesso: cada usuário acessa apenas os seus dados.
- Cadastro, edição e exclusão de ganhos e de gastos.
- Gastos tratados sempre por meio de parcelas: um gasto à vista é uma parcela, e um gasto parcelado tem duas ou mais.
- Geração automática das parcelas futuras de um gasto.
- Marcação de parcelas como pagas, com registro do valor e da data efetivamente pagos.
- Controle de gastos pendentes e pagos.
- Consulta de ganhos e gastos por período, histórico mensal e detalhamento das parcelas.
- Dashboard financeiro e cálculo do saldo com base nos valores efetivamente recebidos e pagos.
- Exclusão da conta junto com os dados financeiros relacionados.
- Interface web.
- Testes manuais e, depois, testes automatizados.
- Publicação em domínio próprio e teste da versão beta com quatro usuários.

### Fora do escopo da primeira versão

- Aplicativo mobile.
- Integração automática com bancos, importação de extratos e pagamentos automáticos.
- Confirmação de e-mail, bloqueio de conta por tentativas de login e recuperação de senha.
- Recorrência automática de ganhos.
- Sistema de notificações.
- Retenção do histórico financeiro após a exclusão da conta.
- Lançamento de transações por e-mail (versão futura).

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

Planejadas:

- Todo gasto é representado por parcelas, e as parcelas futuras são geradas automaticamente.
- O saldo considera apenas os valores efetivamente recebidos e pagos.
- A exclusão da conta remove os dados financeiros relacionados.

O esquema atual (contadores de parcelas na tabela `transactions`) deve ser revisto para comportar parcelas individuais, com vencimento, valor e data de pagamento por parcela.

### Requisitos e restrições

- PHP 8.0 ou superior com a extensão `pdo_mysql`.
- MySQL ou MariaDB com suporte a `utf8mb4`.
- Um arquivo de configuração local (`config/config.php`) com as credenciais do banco e o segredo do JWT. Esse arquivo não deve ser versionado.
- O projeto é desenvolvido individualmente, com tempo e recursos limitados, e a primeira versão tem escopo reduzido, priorizando as funcionalidades essenciais.
- O sistema depende de o próprio usuário preencher as informações financeiras.
- A versão beta terá acesso controlado.

## 6. Arquitetura e organização

### Arquitetura utilizada

Arquitetura em camadas, com a responsabilidade separada entre entrada e roteamento, orquestração, validação e acesso a dados. A separação facilita trocar ou testar uma camada isolada e prepara o projeto para receber a autenticação em um único ponto de entrada.

### Fluxo de funcionamento

```text
Cliente (Postman ou interface web em Vue.js, planejada)
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

Planejado: antes do roteamento, o token enviado em cookie será validado por `ConfigJWT` e o id do usuário será repassado ao Controller e aos DAOs.

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
├── view/                    # reservado para a interface web (vazio)
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
| Chaves estrangeiras com `ON DELETE CASCADE` | Remove ganhos e transações junto com o usuário, o que também atende à exclusão de conta planejada |
| `ATTR_EMULATE_PREPARES => false` e consultas preparadas | Mantém os valores separados do SQL, protegendo contra injeção de SQL |
| Resposta padronizada (`success` e `data`/`errors`) | Simplifica o consumo da API pela interface web |
| JWT implementado sem biblioteca | Entender na prática a estrutura do padrão (header, payload e assinatura) |
| `user_id` obtido do token (planejado) | Evita que o cliente informe o dono do registro no corpo da requisição |
| Tokens em cookies (planejado) | Evita expor o token ao JavaScript da interface, usando cookies `HttpOnly` |

## 8. Banco de dados e persistência

### Tecnologia utilizada

MySQL (ou MariaDB), com acesso via PDO. O banco se chama `financia`, nome original do projeto.

### Estrutura dos dados

- **`users`**: `id`, `name`, `email` (único), `password`, `created_at`.
- **`income`** (ganhos): `id`, `user_id`, `description`, `amount`, `received_at`, `created_at`.
- **`transactions`**: `id`, `user_id`, `description`, `amount`, `type` (`RECURRING`, `INSTALLMENT`, `ONE_TIME`), `status` (`PENDING`, `PAID`, `COMPLETED`), `due_date`, `paid_at`, `total_installments`, `paid_installments`, `created_at`.

Cada ganho e cada transação pertence a um usuário (`user_id`, chave estrangeira para `users`).

Planejado: uma estrutura de parcelas individuais ligada a cada gasto, com número da parcela, vencimento, valor, data e valor efetivamente pagos e status.

### Estratégia de persistência

Inserção e consulta (listar tudo e por id) estão escritas para ganhos. Para transações, só as consultas estão escritas. Atualização e exclusão ainda não estão implementadas.

## 9. Segurança e validações

- **Autenticação:** o módulo de geração e validação de tokens JWT está implementado, mas ainda não está integrado às rotas e não há cadastro nem login. Planejado: access token e refresh token em cookies, com senhas armazenadas com hash.
- **Autorização:** não implementada. Atualmente os dados não são filtrados por usuário, e o `user_id` ainda é enviado no corpo da requisição. Planejado: o id do usuário virá do token e todas as consultas serão filtradas por ele.
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

Por enquanto o projeto não usa dependências externas (sem Composer).

### 4. Executar a aplicação

```bash
php -S localhost:8000 -t src
```

### 5. Acessar ou utilizar o sistema

Envie requisições JSON para `http://localhost:8000/index.php`, como nos exemplos abaixo. A publicação em Apache, em domínio próprio, está planejada para a versão beta.

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

- Testes manuais da API (criação e listagem de ganhos, entradas inválidas, geração e pagamento de parcelas).
- Validação de entradas inválidas (valor negativo, tipo inexistente, parcelas pagas maiores que o total).
- Geração e validação de tokens JWT (token válido, expirado, adulterado e de tipo errado).
- Isolamento de dados entre usuários, depois da integração do JWT.
- Cálculo de saldo e de parcelas.
- Testes automatizados, em uma etapa posterior (a pasta `test/` está reservada para eles).
- Teste da versão beta com quatro usuários testers.

## 13. Desafios técnicos

### Implementar JWT sem biblioteca

- **Problema:** gerar e validar tokens HS256 manualmente.
- **Por que exige atenção:** a validação precisa verificar o algoritmo declarado, a estrutura do token, os tipos dos claims, a expiração e a assinatura, e comparar a assinatura sem vazar informação por tempo de resposta.
- **Solução adotada:** codificação base64url com decodificação estrita, validação do header (`typ` e `alg` exatos), checagem dos claims, assinatura com `hash_hmac` e comparação com `hash_equals`.
- **Limitações que permanecem:** o módulo ainda não está integrado às rotas e tem pontos a revisar (ver seção 14).

### Modelar gastos e parcelas

- **Problema:** representar gastos à vista e parcelados com acompanhamento do que foi pago e do que está pendente.
- **Solução parcial:** o esquema atual tem campos de tipo, status e contadores de parcelas, com validação da consistência entre eles.
- **Limitações que permanecem:** o modelo ainda não tem parcelas individuais, a geração automática das parcelas e a lógica de pagamento e de mudança de status não foram implementadas, e o esquema deve ser revisto para isso.

## 14. Limitações conhecidas

- A API ainda não foi executada de ponta a ponta. Há pendências de integração entre os componentes (nomes de classes e métodos e configuração da conexão) a corrigir antes da primeira versão funcional.
- Não há cadastro, login nem rotas protegidas. O JWT está escrito, mas não é usado.
- Os dados não são filtrados por usuário.
- A criação de transações, a atualização e a exclusão não estão implementadas.
- O modelo de parcelas, o dashboard, o histórico mensal e o cálculo de saldo ainda não existem.
- As requisições de consulta usam corpo JSON em requisições `GET`, formato que muitos clientes (incluindo o `fetch` dos navegadores) não aceitam. Deve mudar quando a interface web entrar.
- Não há tratamento de CORS para uma interface em outra origem. Com tokens em cookies, será preciso informar a origem exata e permitir credenciais.
- As mensagens de erro do banco são devolvidas ao cliente e devem ser substituídas por mensagens genéricas.
- Pontos a revisar no módulo JWT: a verificação de existência do usuário compara o id com a contagem de usuários em vez de consultar o usuário, os tokens de acesso e de refresh ainda não são claramente separados e o refresh tem a mesma duração do access.
- Sem testes automatizados.

## 15. Possíveis melhorias

Além do escopo planejado na seção 5:

- Adotar rotas REST convencionais, com `GET` sem corpo e parâmetros na URL.
- Paginação nas listagens.
- Recuperação de senha, confirmação de e-mail e bloqueio por tentativas de login.
- Recorrência automática de ganhos e notificações.
- Lançamento de transações por e-mail.
- Testes automatizados para validação, DAOs e JWT.

## 16. Aprendizados

Durante o desenvolvimento estão sendo praticados: a modelagem de um domínio financeiro com ENUMs, chaves estrangeiras e `DECIMAL`; o uso de PDO com consultas preparadas; a validação de entrada em camadas; a estrutura e a validação de tokens JWT; e a organização de uma API em camadas (entrada, controller, DTO e DAO).

## 17. Licença e uso

Licença ainda não definida. Até que uma licença seja escolhida, todos os direitos permanecem com o autor.
