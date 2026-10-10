# API Genérica de CRUD

API desenvolvida em PHP para realizar operações CRUD genéricas em bancos
de dados MySQL que seguem uma estrutura padronizada.

O projeto permite executar operações de criação, consulta, atualização
e inativação de registros por meio de requisições HTTP, sem a necessidade
de implementar um Controller e um Repository específicos para cada tabela.

## Classificação

- **Nível:** 1 — Testes
- **Categoria:** API / Banco de Dados
- **Linguagem:** PHP
- **Banco de dados:** MySQL
- **Comunicação com o banco:** PDO
- **Finalidade:** Testes rápidos, prototipação e experimentação.

## Objetivo

Desenvolver uma ferramenta capaz de simplificar testes e operações
em bancos de dados que seguem convenções predefinidas.

A proposta é permitir que diferentes tabelas compatíveis utilizem
as mesmas operações genéricas, reduzindo a necessidade de implementar
uma estrutura específica para cada entidade.

## Funcionalidades

- Consulta de registros.
- Consulta de registros por ID.
- Filtragem de registros ativos e inativos.
- Inserção de registros.
- Atualização de registros.
- Exclusão lógica por meio da inativação.
- Conexão com MySQL utilizando PDO.
- Operações genéricas baseadas em convenções de nomenclatura.

## Arquitetura

A aplicação utiliza uma arquitetura em camadas:

Request → Router → Controller → Repository → PDO → MySQL

### Router
Identifica o método HTTP e os parâmetros da requisição.

### Controller
Recebe as requisições e direciona a execução das operações.

### Repository
Centraliza as operações de acesso e manipulação dos dados.

### PDO
Realiza a comunicação entre a aplicação PHP e o banco de dados.

## Convenções do banco de dados

Para utilizar a API, as tabelas devem seguir as convenções estabelecidas
pela implementação.

- **Prefixo:** `tb_`
- **Chave primária:** `id_nomeDaTabela`
- **Status:** coluna `status` com os valores `ativo` e `inativo`,
  necessária para as operações relacionadas à inativação lógica.

Exemplo:

```sql
CREATE TABLE tb_produto (
    id_produto INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    valor DECIMAL(10,2),
    status ENUM('ativo', 'inativo') DEFAULT 'ativo'
);
