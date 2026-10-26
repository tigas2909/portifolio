Fluxo 💰

Fluxo é um sistema de controle financeiro pessoal desenvolvido para facilitar a organização de ganhos, gastos e transações financeiras. O objetivo é permitir que o usuário acompanhe sua situação financeira sem depender de planilhas complexas.

O projeto possui uma API desenvolvida em PHP, banco de dados MySQL e uma interface web desenvolvida com Vue.js. Também conta com um módulo próprio de autenticação JWT, que faz parte do desenvolvimento da aplicação.

Status: Em desenvolvimento — versão beta.

O projeto está em construção. Os componentes do backend e do frontend já possuem uma estrutura inicial, mas a integração completa, a validação de todos os fluxos e os testes de ponta a ponta ainda precisam ser confirmados.

📌 Classificação

Categoria: Projetos Interessantes e Desafiadores

Contexto: Projeto pessoal

Status: Em desenvolvimento

Versão: Beta

Desenvolvedor: Tiago Siqueira

🎯 Objetivo do projeto

O objetivo do Fluxo é oferecer uma maneira simples de organizar as finanças pessoais, com atenção especial ao acompanhamento de gastos e pagamentos.

A aplicação busca centralizar informações financeiras e facilitar a consulta dos dados cadastrados.

Problema que procura resolver

Planilhas eletrônicas podem ser pouco intuitivas para pessoas que não estão acostumadas a utilizá-las. Além disso, acompanhar gastos parcelados, vencimentos e pagamentos pode se tornar complicado quando as informações estão espalhadas.

O Fluxo pretende oferecer uma alternativa mais acessível, reunindo essas informações em uma única aplicação.

Público-alvo

Pessoas que desejam organizar suas finanças pessoais, acompanhar seus ganhos e gastos e compreender melhor sua situação financeira.

✨ Funcionalidades

As funcionalidades abaixo representam o escopo do projeto. A presença de arquivos relacionados a uma funcionalidade não significa, por si só, que todo o fluxo esteja finalizado ou validado.

Backend — API

Estrutura de API em PHP.

Processamento de requisições e respostas JSON.

Separação de responsabilidades entre controllers, DTOs e DAOs.

Validação de dados recebidos.

Persistência de dados utilizando PDO e MySQL.

Estrutura de cadastro e gerenciamento de usuários.

Módulo próprio de geração e validação de tokens JWT.

Estrutura para gerenciamento de ganhos e transações.

Padronização de respostas da aplicação.

Frontend — Vue.js

A interface possui componentes organizados por funcionalidade:

Login: interface de autenticação.

Cadastro de usuário: formulário de criação de conta.

Atualização de senha: interface relacionada à alteração de senha.

Ganhos: componentes para cadastro, listagem, consulta e edição.

Transações: componentes para cadastro, listagem, consulta e edição.

Página principal: estrutura da área principal da aplicação.

Roteamento: navegação entre as páginas.

Stores: organização do estado relacionado a ganhos, transações e página principal.

Serviço de API: módulo para centralizar a comunicação com o backend.

Estilização: arquivo CSS para o tema da interface.

Observação: os componentes estão presentes no código, mas os fluxos completos de autenticação, persistência, atualização e exclusão precisam ser verificados individualmente.

Funcionalidades planejadas

Autenticação integrada ao backend.

Proteção de rotas e controle de acesso por usuário.

Cadastro, consulta, edição e exclusão de registros financeiros.

Acompanhamento de parcelas individuais.

Registro de vencimentos e pagamentos.

Histórico financeiro por período.

Dashboard com resumo financeiro.

Cálculo de saldo com base nos valores efetivamente recebidos e pagos.

Testes automatizados e validação de segurança.

Publicação em domínio próprio para disponibilizar uma versão beta.

🛠️ Tecnologias utilizadas

Tecnologia

Utilização

PHP 8 ou superior

Desenvolvimento do backend

PDO

Comunicação com o banco de dados

MySQL / MariaDB

Persistência de dados

SQL

Definição e consulta de dados

JSON

Comunicação entre cliente e API

JWT — HS256

Estrutura de autenticação por tokens

JavaScript

Lógica da interface web

Vue.js

Desenvolvimento do frontend

Vite

Ambiente de desenvolvimento do frontend

CSS

Estilização da interface

Git e GitHub

Controle de versão e publicação do código

Tecnologias e conceitos complementares

Arquitetura em camadas.

Validação de dados.

Consultas preparadas.

Separação entre regras de aplicação e acesso a dados.

Controle de versão.

Organização modular do frontend.

🏗️ Arquitetura do projeto

O Fluxo separa o backend da interface web.

O backend é responsável por processar as requisições, validar os dados e acessar o banco de dados. O frontend oferece a interface utilizada pelo usuário e se comunica com a API.

Fluxo previsto de comunicação

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
               │ Requisições HTTP
               │ JSON
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

Esse diagrama representa a arquitetura pretendida. A integração efetiva entre todos os componentes deve ser validada durante os testes.

Organização do backend

Entrada e roteamento: recebe as requisições e direciona o processamento.

Controllers: coordenam os casos de uso.

DTOs: concentram a validação e a organização dos dados recebidos.

DAOs: realizam as operações de acesso ao banco de dados.

Configuração: centraliza os parâmetros de conexão e configuração.

JWT: contém a implementação relacionada à geração e validação de tokens.

Organização do frontend

Páginas: representam as telas da aplicação.

Stores: organizam o estado utilizado pelos componentes.

Services: centralizam funcionalidades de comunicação com a API.

Router: configura a navegação entre as páginas.

Assets: armazenam recursos de estilo e outros arquivos da interface.

📂 Estrutura de diretórios

A estrutura abaixo corresponde à organização dos arquivos incluídos na versão publicada do projeto.

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

O arquivo config/config.php é uma configuração local e não deve conter credenciais reais no repositório público.

🗄️ Banco de dados

O projeto utiliza MySQL ou MariaDB, com acesso por PDO.

O esquema inicial contempla as entidades principais do sistema financeiro.

Usuários — users

Responsável por armazenar os dados de cadastro dos usuários.

Campos previstos no esquema inicial:

id

name

email

password

created_at

O e-mail é definido como único no esquema inicial.

Ganhos — income

Armazena os registros de valores recebidos ou ganhos.

Campos previstos no esquema inicial:

id

user_id

description

amount

received_at

created_at

Transações — transactions

Armazena os registros de gastos e informações relacionadas às transações.

Campos previstos no esquema inicial:

id

user_id

description

amount

type

status

due_date

paid_at

total_installments

paid_installments

created_at

Os campos acima descrevem o esquema inicial documentado. Alterações posteriores no banco devem ser refletidas nesta seção.

Modelagem financeira

O projeto pretende permitir que os gastos sejam acompanhados de acordo com seus vencimentos e pagamentos.

Uma das etapas de evolução consiste em representar cada parcela individualmente, incluindo:

Número da parcela.

Valor previsto.

Data de vencimento.

Valor efetivamente pago.

Data do pagamento.

Situação da parcela.

Essa estrutura é importante para calcular corretamente os valores pendentes e o saldo financeiro.

🔐 Segurança

A segurança é uma parte importante do desenvolvimento do Fluxo, especialmente porque a aplicação lida com informações financeiras pessoais.

JWT

O projeto contém uma implementação própria de JWT utilizando o algoritmo HS256.

O módulo ConfigJWT.php concentra funcionalidades relacionadas à criação e à validação dos tokens.

A implementação utiliza recursos do PHP, como hash_hmac, codificação base64url e comparação de assinaturas com hash_equals.

A presença desse módulo não significa que a autenticação esteja completamente integrada ao sistema.

Boas práticas previstas

Utilizar consultas preparadas por meio do PDO.

Validar os dados recebidos pela API.

Manter as credenciais fora do repositório público.

Armazenar senhas utilizando mecanismos apropriados de hash.

Validar os tokens antes de permitir o acesso às rotas protegidas.

Obter a identificação do usuário autenticado a partir de uma fonte confiável.

Garantir que cada usuário acesse somente seus próprios registros.

Evitar expor detalhes internos do banco de dados nas respostas públicas.

Configurar adequadamente CORS e cookies caso o frontend e o backend sejam publicados em origens diferentes.

Importante: essas medidas devem ser verificadas no código e nos testes antes de considerar a aplicação pronta para uso com dados financeiros reais.

⚙️ Como executar o projeto

Os procedimentos abaixo descrevem a configuração de desenvolvimento local. Os caminhos e as configurações podem precisar de ajustes conforme o ambiente utilizado.

Pré-requisitos

PHP 8.0 ou superior.

Extensão pdo_mysql habilitada.

MySQL ou MariaDB.

Node.js e npm.

Git.

Um navegador web.

Um cliente HTTP, como Postman ou Thunder Client, para testar a API.

1. Obter o projeto

O Fluxo está publicado dentro do repositório de portfólio:

Repositório:
https://github.com/tigas2909/portifolio

Clone o repositório:

git clone https://github.com/tigas2909/portifolio.git

Entre no diretório do Fluxo dentro da pasta de nível 3. O caminho exato depende do nome da pasta utilizado no portfólio.

2. Configurar o banco de dados

Crie o banco conforme o esquema fornecido:

mysql -u <usuario> -p < config/database.sql

Substitua <usuario> pelo usuário do banco de dados.

Confira o conteúdo de config/database.sql para verificar o nome do banco e as instruções executadas antes de utilizar o comando.

3. Configurar o backend

Crie o arquivo local config/config.php, caso ele ainda não exista.

Exemplo de configuração:

<?php

define('HOST', 'localhost');
define('DATABASE', 'financia');
define('USERNAME', 'seu_usuario');
define('PASSWORD_DATABASE', 'sua_senha');
define('PASSWORD_JWT', 'seu_segredo_aleatorio');

Os nomes das constantes devem corresponder aos utilizados pelo código da aplicação.

Para gerar um segredo aleatório para o JWT, pode-se utilizar:

php -r "echo bin2hex(random_bytes(32));"

Não publique senhas, tokens ou segredos reais no GitHub.

4. Iniciar o backend

Na raiz do projeto, execute:

php -S localhost:8000 -t src

Esse comando inicia o servidor de desenvolvimento embutido do PHP.

O comando pressupõe que a estrutura e os caminhos utilizados pela API sejam compatíveis com essa configuração. Caso ocorram erros de roteamento ou inclusão de arquivos, será necessário ajustar a inicialização conforme a estrutura efetiva do backend.

5. Iniciar o frontend

Entre no diretório do frontend Vue:

cd front/fluxo

Instale as dependências:

npm install

Inicie o servidor de desenvolvimento:

npm run dev

O Vite exibirá no terminal o endereço local em que a interface estará disponível.

Observação: a configuração do serviço de API no frontend deve apontar para o endereço correto do backend. A comunicação entre os dois componentes precisa ser validada, incluindo CORS, métodos HTTP e formato das requisições.

🧪 Testes

O projeto possui uma pasta test/ com arquivos destinados a testes de controllers, DTOs e respostas.

A existência desses arquivos não confirma que todos os testes estejam automatizados, atualizados ou passando.

Testes importantes para a evolução do projeto

Validação de campos obrigatórios.

Rejeição de valores monetários inválidos.

Validação de datas.

Persistência e consulta de ganhos.

Persistência e consulta de transações.

Cadastro e autenticação de usuários.

Geração e validação de JWT.

Rejeição de tokens inválidos ou expirados.

Isolamento dos dados entre usuários.

Atualização e exclusão de registros.

Comunicação entre o frontend e o backend.

Cálculo de parcelas, pagamentos e saldo financeiro.

Os resultados dos testes devem ser registrados conforme forem executados, distinguindo funcionalidades implementadas de funcionalidades efetivamente verificadas.

🚧 Limitações e pontos de atenção

Como o Fluxo está em desenvolvimento, alguns aspectos precisam ser avaliados antes da publicação de uma versão estável:

Integração completa entre frontend e backend.

Validação de todos os fluxos de cadastro, login e atualização de senha.

Integração efetiva do JWT com as rotas protegidas.

Garantia de isolamento dos dados financeiros por usuário.

Implementação e validação das operações de escrita e atualização de transações.

Modelagem completa de parcelas individuais.

Cálculo do saldo com base nos valores efetivamente recebidos e pagos.

Tratamento de erros e padronização das respostas.

Configuração de CORS para o ambiente de publicação.

Execução dos testes e verificação do comportamento de ponta a ponta.

Revisão de segurança antes de utilizar dados financeiros reais.

Esses pontos devem ser considerados durante a evolução do sistema, sem pressupor que todos estejam pendentes ou resolvidos apenas pela existência de arquivos relacionados.

🔮 Próximos passos

A evolução do Fluxo está organizada em torno dos seguintes objetivos:

Validar o funcionamento dos componentes existentes.

Concluir a integração entre frontend e backend.

Finalizar e verificar os fluxos de autenticação.

Garantir o controle de acesso aos dados de cada usuário.

Evoluir o modelo de gastos e parcelas.

Implementar e validar as operações financeiras necessárias.

Desenvolver o resumo financeiro e o histórico por período.

Ampliar a cobertura de testes.

Revisar a segurança e a configuração do ambiente.

Preparar a publicação e os testes da versão beta.

O escopo pode ser ajustado conforme os resultados dos testes e as necessidades identificadas durante o desenvolvimento.

📚 Aprendizados

O desenvolvimento do Fluxo envolve a aplicação prática de conceitos de desenvolvimento web e organização de sistemas, incluindo:

Desenvolvimento de APIs com PHP.

Persistência de dados utilizando PDO e MySQL.

Validação de entradas e separação de responsabilidades.

Organização de controllers, DTOs e DAOs.

Estrutura e validação de tokens JWT.

Desenvolvimento de interfaces com Vue.js.

Organização de componentes, stores e serviços.

Comunicação entre frontend e backend.

Modelagem de dados financeiros.

Controle de versão utilizando Git e GitHub.

Testes e validação incremental de funcionalidades.

📄 Licença e uso

A licença do projeto ainda não foi definida.

Até que uma licença seja escolhida, os direitos sobre o código permanecem com seu autor. A publicação do código no GitHub não implica, por si só, autorização para redistribuí-lo ou utilizá-lo sob uma licença específica.
