# API Genérica

> API CRUD genérica desenvolvida em PHP para facilitar testes e prototipação com bancos de dados MySQL, permitindo realizar operações em diferentes tabelas sem a necessidade de implementar um controlador e um repositório específicos para cada uma delas.

## Classificação

- **Nível:** 1 — Testes
- **Categoria:** API / Banco de Dados
- **Status:** [Informe o status atual]
- **Contexto:** Pessoal / Experimental

## 1. Objetivo

Desenvolver uma API genérica capaz de executar operações CRUD em tabelas de um banco de dados MySQL, reduzindo a necessidade de implementar estruturas específicas para cada tabela.

O projeto tem como finalidade facilitar testes, experimentações e prototipação, permitindo reutilizar a mesma estrutura para diferentes tabelas que sigam determinadas convenções.

## 2. Motivação

O projeto foi desenvolvido com o objetivo de simplificar a realização de testes envolvendo bancos de dados.

Em vez de criar controladores e repositórios específicos para cada tabela, a proposta consiste em utilizar uma estrutura genérica que identifique a tabela e a operação solicitada, executando a ação correspondente.

Dessa forma, é possível reutilizar a implementação em diferentes cenários, desde que as tabelas respeitem as convenções estabelecidas pela API.

## 3. Tecnologias Utilizadas

- **Linguagem:** PHP
- **Banco de dados:** MySQL
- **Acesso ao banco:** PDO
- **Formato de comunicação:** JSON
- **Protocolo:** HTTP

## 4. Funcionamento

A API recebe requisições HTTP contendo informações sobre a conexão com o banco de dados e a operação desejada.

As informações são processadas pelo roteador, que direciona a requisição para o controlador responsável. O controlador utiliza o repositório para executar a operação no banco de dados por meio do PDO.

### Fluxo de execução

```text
Requisição HTTP
       |
       v
     Router
       |
       v
   Controller
       |
       v
   Repository
       |
       v
      PDO
       |
       v
   MySQL
```

### Convenções das tabelas

Para que a API consiga operar sobre uma tabela, ela deve respeitar as seguintes convenções:

- **Prefixo:** as tabelas devem começar com `tb_`.
- **Chave primária:** deve seguir o padrão `id_nomeDaTabela`.
- **Status:** deve existir uma coluna `status`, utilizada para identificar registros ativos e inativos.

Os valores utilizados para o status são:

- `ativo`: identifica registros ativos.
- `inativo`: identifica registros inativos.

Essas convenções permitem que a API identifique e manipule as tabelas sem exigir uma implementação específica para cada uma delas.

## 5. Funcionalidades

- **Consulta de registros:** permite consultar os registros de uma tabela.
- **Consulta por identificador:** permite buscar um registro específico pelo ID.
- **Consulta de registros ativos:** permite recuperar registros com status `ativo`.
- **Consulta de registros inativos:** permite recuperar registros com status `inativo`.
- **Inserção:** permite inserir novos registros.
- **Atualização:** permite modificar registros existentes.
- **Exclusão lógica:** permite marcar registros como inativos sem removê-los fisicamente do banco de dados.
- **Seleção dinâmica de tabelas:** permite utilizar a mesma estrutura para diferentes tabelas compatíveis com as convenções estabelecidas.

## 6. Implementação

A implementação utiliza uma arquitetura dividida em componentes com responsabilidades distintas.

- **Router:** identifica a rota e direciona a requisição.
- **Controller:** interpreta as informações recebidas e coordena a operação solicitada.
- **Repository:** concentra a comunicação com o banco de dados.
- **PDO:** realiza a interação entre a aplicação e o MySQL.

A requisição utiliza JSON para transmitir os dados necessários à operação.

### Estrutura da requisição

O JSON utiliza dois campos principais:

- `connection`: contém as informações necessárias para estabelecer a conexão com o banco de dados.
- `info`: contém as informações relacionadas à tabela e à operação solicitada.

A estrutura de `connection` contempla os seguintes parâmetros:

| Parâmetro | Descrição |
|---|---|
| `dsn` | String de conexão com o banco de dados. |
| `username` | Usuário utilizado na conexão. |
| `password` | Senha utilizada na conexão. |

O campo `info` contém os dados necessários para identificar a operação e seus parâmetros.

A estrutura exata desse campo depende da operação solicitada.

### Operações disponíveis

| Método HTTP | Parâmetro ou rota | Operação |
|---|---|---|
| GET | `/index.php` | Consulta registros. |
| GET | `?id=10` | Consulta um registro pelo identificador. |
| GET | `?acao=selectAtivo` | Consulta registros ativos. |
| GET | `?acao=selectInativo` | Consulta registros inativos. |
| POST | `/index.php` | Insere registros. |
| PUT | `?acao=atualizar` | Atualiza registros existentes. |
| PUT | `?acao=delete` | Realiza a exclusão lógica de registros. |

Os parâmetros apresentados são exemplos de utilização. A requisição deve respeitar a estrutura de dados esperada pela implementação.

## 7. Resultados e Aprendizados

O projeto explora a possibilidade de reutilizar uma única estrutura de API para executar operações em diferentes tabelas de um banco de dados.

A implementação permite experimentar operações CRUD sem a necessidade de desenvolver um controlador e um repositório específicos para cada tabela.

Entre os conceitos explorados estão:

- Organização de uma API em componentes com responsabilidades distintas.
- Manipulação de requisições HTTP.
- Comunicação entre aplicação e banco de dados utilizando PDO.
- Utilização de JSON para transmitir parâmetros.
- Reutilização de código por meio de uma estrutura genérica.
- Aplicação de convenções para padronizar a estrutura das tabelas.
- Exclusão lógica de registros.

## 8. Como Executar

### Pré-requisitos

- PHP com suporte ao PDO e ao driver PDO MySQL.
- Servidor MySQL.
- Servidor web compatível com PHP ou ambiente local equivalente.
- Banco de dados com tabelas compatíveis com as convenções da API.
- Cliente HTTP para enviar requisições, como Postman ou Insomnia.

### Configuração

1. Obtenha o código-fonte do projeto.
2. Configure o ambiente PHP e o servidor MySQL.
3. Crie ou selecione um banco de dados.
4. Prepare as tabelas respeitando as convenções estabelecidas pela API.
5. Configure os parâmetros de conexão conforme o formato esperado pela aplicação.

**Observação:** a API recebe informações de conexão por meio do campo `connection`. Utilize apenas bancos de dados e credenciais autorizados. Não exponha credenciais reais em repositórios públicos.

### Execução

Inicie o servidor web ou o ambiente PHP configurado para executar a aplicação.

Em seguida, utilize um cliente HTTP para enviar as requisições à rota de entrada da API, informando os parâmetros e os dados esperados para cada operação.

A estrutura exata do corpo das requisições deve seguir a implementação do projeto.

## 9. Limitações e Observações

A API foi desenvolvida com foco em testes, experimentação e prototipação.

Seu funcionamento depende de convenções específicas na estrutura das tabelas, especialmente no que diz respeito ao prefixo, à chave primária e à coluna de status.

A generalização das operações também implica limitações: tabelas com estruturas incompatíveis ou regras de negócio específicas podem exigir adaptações.

O projeto não deve ser considerado uma solução pronta para ambientes de produção sem uma avaliação adicional de seus mecanismos de segurança, validação e controle de acesso.

Entre os aspectos que devem ser avaliados antes de uma utilização em produção estão:

- Autenticação e autorização.
- Validação dos dados recebidos.
- Proteção contra operações não autorizadas.
- Restrições de acesso às tabelas.
- Tratamento de erros e exceções.
- Registro de operações e auditoria.
- Controle de requisições e proteção contra abusos.

## 10. Uso Previsto

A API foi desenvolvida principalmente para:

- Realizar testes envolvendo operações CRUD.
- Experimentar a comunicação entre PHP e MySQL.
- Facilitar a prototipação de aplicações.
- Reutilizar operações de banco de dados em diferentes tabelas.
- Explorar a construção de APIs genéricas.

Seu propósito é simplificar experimentações e reduzir código repetitivo em cenários controlados, não substituir APIs especializadas que implementam regras de negócio complexas.

## 11. Possíveis Melhorias

Algumas possibilidades de evolução incluem:

- Implementar mecanismos mais completos de autenticação e autorização.
- Aprimorar a validação das tabelas e dos parâmetros recebidos.
- Padronizar as respostas e o tratamento de erros.
- Adicionar testes automatizados.
- Melhorar a documentação dos formatos de requisição e resposta.
- Implementar mecanismos de controle de acesso por tabela e operação.

Essas possibilidades representam sugestões de evolução, não funcionalidades necessariamente presentes na implementação atual.

---

*Projeto desenvolvido com foco em testes, experimentação e prototipação de operações CRUD utilizando PHP e MySQL.*
