# Tigas Eletrick — Sistema de Cadastro de Eletrônicos

## 📌 Sobre o Projeto

O Tigas Eletrick é uma aplicação web desenvolvida em PHP para cadastrar e gerenciar informações de produtos eletrônicos.

O sistema utiliza arquivos JSON para persistir os dados, permitindo cadastrar produtos, consultar registros, filtrar por categoria e excluir itens cadastrados.

O projeto foi desenvolvido com o objetivo de praticar programação web, manipulação de dados e persistência em arquivos.

## 🎯 Objetivos

- Implementar um sistema de cadastro de produtos.
- Trabalhar com persistência de dados utilizando JSON.
- Desenvolver operações para consultar, filtrar e excluir registros.
- Praticar o processamento de formulários com PHP.
- Construir uma interface web utilizando HTML e CSS.
- Utilizar JavaScript para adicionar interatividade à interface.

## 💡 Motivação

O projeto surgiu como uma oportunidade de praticar o desenvolvimento de aplicações web e explorar formas de armazenar e manipular dados sem utilizar um sistema gerenciador de banco de dados.

A utilização de arquivos JSON permitiu implementar a persistência dos registros e compreender o fluxo de leitura, alteração e gravação de dados.

## 🛠️ Tecnologias Utilizadas

- **PHP:** processamento das requisições, validação dos dados e manipulação dos registros.
- **JSON:** armazenamento persistente dos produtos.
- **HTML5:** estrutura das páginas.
- **CSS3:** estilização da interface.
- **JavaScript:** interação com o menu de navegação.

## ⚙️ Funcionalidades

- Cadastro de produtos eletrônicos.
- Registro de informações como tipo, marca, modelo, armazenamento, processador e memória RAM.
- Geração de identificadores individuais para os registros.
- Listagem dos produtos cadastrados.
- Filtragem de produtos por categoria.
- Exclusão de registros pelo identificador.
- Validação básica dos campos do formulário.
- Confirmação da exclusão antes do envio da solicitação.
- Persistência dos dados em arquivos JSON.

### Categorias disponíveis

- Celular
- PC
- Notebook
- Videogame

## 🧠 Funcionamento

A aplicação utiliza PHP para processar os formulários e manipular os dados armazenados em JSON.

As operações de persistência são centralizadas em funções reutilizáveis, definidas no arquivo `comandos.php`.

O fluxo principal funciona da seguinte maneira:

1. O usuário acessa a página inicial.
2. A partir do menu, acessa o cadastro ou a listagem de produtos.
3. No cadastro, preenche as informações do eletrônico.
4. O PHP verifica os campos e prepara os dados do produto.
5. O registro recebe um identificador individual e é adicionado à coleção.
6. Os dados são serializados em JSON e gravados no arquivo.
7. Na listagem, os registros podem ser consultados, filtrados por categoria ou excluídos.

## 🗂️ Estrutura do Projeto

A estrutura principal é organizada da seguinte forma:

- `index.php`: página inicial e menu de navegação.
- `cadastro.php`: formulário e processamento do cadastro.
- `lista.php`: listagem, filtragem e exclusão de produtos.
- `comandos.php`: funções de leitura, gravação, filtragem e exclusão.
- `banco/`: diretório destinado aos arquivos de persistência.
- `banco/banco.json`: arquivo utilizado para armazenar os produtos cadastrados.

## 💾 Persistência de Dados

Os produtos são armazenados em um arquivo JSON, no qual cada registro contém os seguintes campos:

| Campo | Descrição |
|---|---|
| `id` | Identificador individual do registro. |
| `tipo` | Categoria do produto. |
| `marca` | Fabricante informado. |
| `modelo` | Modelo do eletrônico. |
| `armazenamento` | Capacidade de armazenamento e unidade. |
| `processador` | Processador informado. |
| `memoria_ram` | Capacidade de memória RAM e unidade. |
| `selecao` | Código da categoria utilizado na filtragem. |

A persistência é implementada por meio das funções `buscar()` e `salvar()`, responsáveis pela leitura e gravação dos dados.

As funções `filtrar()` e `excluir()` permitem consultar subconjuntos de registros e remover produtos pelo identificador.

## ▶️ Como Executar

### Pré-requisitos

- PHP instalado.
- Servidor web com suporte a PHP, como Apache.
- Navegador web.

### Procedimento

1. Configure um ambiente local com PHP e um servidor web.
2. Coloque os arquivos do projeto no diretório público do servidor.
3. Verifique se o diretório `banco/` existe e permite a gravação dos arquivos pelo processo do servidor.
4. Acesse `index.php` pelo navegador, utilizando o endereço local correspondente à configuração do servidor.

**Observação:** o projeto foi desenvolvido para execução local. Os caminhos utilizados na navegação devem ser ajustados caso a aplicação seja instalada em outro diretório.

## 📚 Aprendizados

O desenvolvimento do projeto proporcionou experiência prática com:

- Manipulação de dados em PHP.
- Leitura e gravação de arquivos JSON.
- Processamento de formulários HTML.
- Validação básica de entradas.
- Manipulação de arrays.
- Operações de cadastro, consulta e exclusão.
- Filtragem de registros.
- Identificação individual de dados.
- Integração entre PHP, HTML, CSS e JavaScript.
- Organização de funções reutilizáveis.

## ⚠️ Limitações Conhecidas

- A persistência depende de arquivos locais, sem os recursos transacionais de um banco de dados.
- O sistema não implementa autenticação de usuários.
- O código não possui tratamento completo de erros de leitura, conversão e gravação de JSON.
- A validação dos dados é básica.
- Não há proteção completa contra vulnerabilidades comuns de aplicações web.

O projeto tem finalidade educacional e não deve ser considerado pronto para uso em produção sem melhorias de segurança e confiabilidade.

## 🚀 Possíveis Melhorias

- Implementar edição dos produtos cadastrados.
- Melhorar o tratamento de erros na persistência.
- Reforçar a validação dos dados recebidos.
- Adicionar proteção contra XSS e CSRF.
- Melhorar a acessibilidade e a responsividade da interface.
- Migrar a persistência para um banco de dados relacional.
- Implementar autenticação e autorização, caso necessário ao escopo futuro.

## 📄 Licença

Definir uma licença caso o projeto seja disponibilizado publicamente.
