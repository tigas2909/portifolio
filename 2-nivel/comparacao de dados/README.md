# Comparação de Dados de Participantes

> Aplicação desenvolvida em Java para comparar listas de participantes de um evento utilizando CPF como identificador, facilitando a identificação de registros presentes em uma lista e ausentes em outra.

## Classificação

- **Nível:** 2 — Projetos Simples
- **Categoria:** Processamento de Dados / Automação
- **Status:** Finalizado
- **Contexto:** Acadêmico / Necessidade real de um evento

## 1. Visão Geral

Este projeto surgiu da necessidade de comparar os dados de participantes de um evento acadêmico.

Durante a organização do evento, foi necessário identificar participantes que ainda não possuíam inscrição registrada e realizar a inclusão de seus dados.

As informações dos participantes foram disponibilizadas em uma planilha Excel. Para facilitar o processamento, os dados foram convertidos para o formato CSV e utilizados como entrada para uma aplicação desenvolvida em Java.

A aplicação realiza a comparação entre os registros utilizando o CPF como identificador, permitindo localizar correspondências sem depender exclusivamente dos nomes dos participantes.

## 2. Objetivo

Automatizar a comparação entre conjuntos de dados de participantes, identificando registros correspondentes por meio do CPF.

O objetivo foi reduzir a necessidade de conferência manual, facilitar a identificação dos participantes e tornar o processo de análise dos dados mais eficiente.

## 3. Motivação

O projeto foi desenvolvido para resolver uma necessidade prática durante a organização de um evento acadêmico.

Os dados dos participantes foram fornecidos em uma planilha Excel, mas era necessário compará-los com outra base de dados para identificar quais pessoas ainda não possuíam inscrição registrada.

A comparação exclusivamente pelos nomes não era confiável, pois poderiam existir diferenças na escrita ou outras inconsistências nos registros.

Por esse motivo, o CPF foi escolhido como critério de comparação, considerando que cada participante deveria possuir um CPF próprio e que esse dado era o identificador mais confiável disponível nas listas utilizadas.

Em vez de conferir os registros individualmente, a proposta foi utilizar Java para automatizar a leitura e a comparação dos dados.

## 4. Tecnologias Utilizadas

| Tecnologia | Finalidade |
|---|---|
| Java | Leitura, processamento e comparação dos dados. |
| CSV | Formato utilizado para disponibilizar os dados à aplicação. |
| Excel | Formato original da planilha recebida. |

## 5. Funcionalidades

- **Leitura de arquivos CSV:** permite processar os dados dos participantes disponibilizados em arquivos.
- **Comparação por CPF:** utiliza o CPF como identificador para comparar os registros entre as listas.
- **Identificação de correspondências:** permite localizar registros que possuem o mesmo CPF nas bases comparadas.
- **Automatização da conferência:** reduz a necessidade de verificar manualmente cada participante.

## 6. Implementação

A solução foi desenvolvida em Java, utilizando arquivos CSV como fonte de dados.

O processamento consiste em ler os registros das listas e comparar os CPFs para identificar correspondências entre os participantes.

O uso do CPF permite realizar a comparação sem depender da correspondência exata entre os nomes, que podem apresentar diferenças de escrita.

### Fluxo de processamento

```text
Planilha Excel recebida
          |
          v
Conversão dos dados para CSV
          |
          v
Leitura dos arquivos em Java
          |
          v
Comparação dos CPFs
          |
          v
Identificação dos registros
```

O CPF foi utilizado como critério de comparação por ser o identificador mais confiável disponível nos dados recebidos.

## 7. Regras de Funcionamento

A comparação dos dados segue o seguinte princípio:

- O CPF é utilizado como identificador para relacionar os registros.
- Os nomes não são utilizados como critério principal de correspondência.
- Os registros são comparados entre as listas fornecidas à aplicação.

A identificação de participantes sem inscrição depende da comparação entre a lista recebida e a base que representa as inscrições já registradas.

## 8. Como Executar

### Pré-requisitos

- Java instalado.
- Arquivos CSV contendo os dados que serão comparados.

### Preparação dos dados

1. Abra a planilha Excel que contém os dados dos participantes.
2. Converta os dados necessários para o formato CSV.
3. Prepare os arquivos de entrada conforme o formato esperado pela aplicação.
4. Verifique se os arquivos contêm a coluna de CPF necessária para a comparação.

### Execução

Execute a aplicação Java conforme a configuração do projeto.

Os arquivos CSV devem estar disponíveis nos caminhos esperados pela implementação.

**Observação:** os comandos de execução e os nomes dos arquivos devem ser documentados de acordo com a estrutura real do projeto.

## 9. Resultados e Aprendizados

O projeto permitiu aplicar conhecimentos de programação na resolução de um problema real relacionado à organização de um evento acadêmico.

A utilização de Java para processar arquivos CSV possibilitou automatizar a comparação dos registros e utilizar um identificador mais confiável do que os nomes dos participantes.

O desenvolvimento também envolveu a transformação de dados provenientes de uma planilha para um formato adequado ao processamento programático.

Entre os conceitos aplicados estão:

- Leitura e processamento de arquivos.
- Manipulação de dados em Java.
- Comparação de registros entre conjuntos de dados.
- Utilização de identificadores para relacionar informações.
- Automação de tarefas administrativas.

## 10. Limitações Conhecidas

A confiabilidade da comparação depende da qualidade e da consistência dos dados fornecidos.

Como o CPF é utilizado como identificador, registros com CPFs ausentes, inválidos ou inconsistentes podem comprometer a identificação correta das correspondências.

Além disso, os arquivos de entrada precisam seguir o formato esperado pela aplicação.

## 11. Melhorias Futuras

Possíveis melhorias incluem:

- Implementar validação dos CPFs antes da comparação.
- Identificar e registrar CPFs duplicados nos arquivos.
- Gerar um relatório com os participantes encontrados e não encontrados.
- Tratar automaticamente diferenças de formatação nos CPFs.
- Implementar mensagens de erro para arquivos inválidos ou incompletos.
- Permitir a seleção dos arquivos de entrada sem alterar o código-fonte.

Essas melhorias são sugestões para versões futuras e não representam necessariamente funcionalidades já implementadas.

## 12. Considerações Finais

O projeto demonstra como uma aplicação simples pode automatizar uma tarefa administrativa que exigiria conferência manual.

A utilização de Java e arquivos CSV permitiu comparar os registros dos participantes por meio do CPF, atendendo à necessidade de identificar pessoas cujas inscrições ainda precisavam ser verificadas.

A solução surgiu de uma demanda real e exemplifica a aplicação prática da programação no processamento e na organização de dados.

---

*Projeto desenvolvido para automatizar a comparação de dados de participantes de um evento acadêmico.*
