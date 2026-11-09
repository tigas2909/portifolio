# Fluxo

Sistema de controle financeiro pessoal desenvolvido com PHP 8, MySQL e Vue, voltado ao acompanhamento de ganhos, movimentações e compromissos financeiros.

O projeto está em desenvolvimento. O login e o CRUD principal já estão funcionando; as demais funcionalidades seguem o planejamento descrito abaixo.

## Tecnologias

| Camada | Tecnologias |
| --- | --- |
| Backend | PHP 8 |
| Banco de dados | MySQL |
| Autenticação | JWT |
| Frontend | Vue |
| Desenvolvimento do frontend | Vite |
| Navegação | Vue Router |

## Status do projeto

- [x] Login funcionando.
- [x] CRUD principal de ganhos e transferências funcionando.
- [ ] Perfil do usuário.
- [ ] Desfazer pagamento.
- [ ] Gestão de parcelas.
- [ ] Gestão de crédito e cartões.
- [ ] Faturas mensais.
- [ ] Cofrinho.
- [ ] Histórico por mês.
- [ ] Exportação CSV.

> Funcionalidade funcionando não significa que todos os seus cenários de teste foram validados. As prioridades e dependências abaixo representam uma proposta de organização do desenvolvimento.

## Visão por módulos

| Módulo | Situação atual | Prioridade sugerida | Dependências | Pendências |
| --- | --- | --- | --- | --- |
| Autenticação JWT | Login funcionando | Alta | Usuários | Validar expiração, renovação, logout e acesso às rotas protegidas |
| Usuários | Base utilizada pelo login; perfil planejado | Alta | Autenticação JWT | Consultar e editar perfil; validar alteração de senha |
| Ganhos | CRUD principal funcionando | Alta | Autenticação JWT, usuários | Separar valores recebidos e futuros; permitir registrar recebimento |
| Transferências | CRUD principal funcionando | Alta | Autenticação JWT, usuários | Revisar estados das movimentações e permitir desfazer pagamento |
| Parcelas | Planejado | Alta | Transferências, definição das regras de parcelamento | Modelar compra e parcelas; controlar vencimentos e pagamentos individuais |
| Crédito | Planejado | Alta | Usuários, transferências, parcelas | Cadastrar cartões e definir regras de limite, fechamento e vencimento |
| Faturas | Planejado | Alta | Crédito, parcelas | Agrupar cobranças por competência e registrar pagamento da fatura |
| Cofrinho | Planejado | Média | Usuários, regras de saldo | Registrar depósitos e retiradas; separar reserva e saldo disponível |
| Histórico mensal | Planejado | Alta | Ganhos, transferências, regras de competência | Consultar meses anteriores e filtrar movimentações por mês |
| Exportação CSV | Planejado | Média | Histórico mensal e filtros | Definir colunas, formatação e exportação dos dados selecionados |

## Implantação

### Funcionamento informado

- Login.
- CRUD principal de ganhos e transferências.

### Controle de versões implantadas

Registrar cada implantação para identificar quais funcionalidades estão disponíveis no ambiente.

| Versão ou commit | Data | Ambiente | Alterações | Validação |
| --- | --- | --- | --- | --- |
| A registrar | — | — | Login e CRUD principal informados como funcionando | Checklist de testes pendente |

As melhorias planejadas não devem ser consideradas implantadas sem registro de entrega e validação.

## Roadmap

### 1. Consolidar a base

- [ ] Validar o ciclo completo da autenticação JWT.
- [ ] Revisar validações do backend.
- [ ] Verificar isolamento dos dados por usuário.
- [ ] Implementar a página de perfil.
- [ ] Implementar a opção de desfazer pagamento.
- [ ] Diferenciar ganhos futuros de valores recebidos.

### 2. Parcelamento e crédito

- [ ] Definir a modelagem de compras parceladas.
- [ ] Registrar vencimento, valor e situação de cada parcela.
- [ ] Permitir consultar parcelas pagas e pendentes.
- [ ] Implementar cadastro de cartões.
- [ ] Definir regras de fechamento e vencimento.
- [ ] Implementar faturas mensais.
- [ ] Evitar contabilizar a compra e o pagamento da fatura como duas despesas.

### 3. Histórico e exportação

- [ ] Implementar seleção de mês.
- [ ] Consultar movimentações de meses anteriores.
- [ ] Definir o período exibido inicialmente.
- [ ] Implementar filtros.
- [ ] Exportar os dados filtrados em CSV.

### 4. Reservas

- [ ] Definir as regras do cofrinho.
- [ ] Implementar depósitos e retiradas.
- [ ] Exibir saldo reservado e saldo disponível.

## Dependências funcionais

| Funcionalidade | Definição necessária |
| --- | --- |
| Desfazer pagamento | Estados da movimentação e efeito de cada estado no saldo |
| Ganhos futuros | Critério para considerar um ganho como recebido |
| Parcelamento | Relação entre compra, parcelas, vencimentos e pagamentos |
| Crédito e faturas | Limite, fechamento, vencimento e competência das cobranças |
| Cofrinho | Efeito dos depósitos e retiradas no saldo disponível |
| Histórico mensal | Critério de agrupamento: competência, vencimento ou pagamento |
| Exportação CSV | Campos exportados, filtros e regras do histórico |

## Testes

Os itens abaixo são cenários planejados. A marcação deve ocorrer somente após execução e confirmação do resultado.

### Autenticação e usuários

- [ ] Login com credenciais válidas.
- [ ] Rejeição de credenciais inválidas.
- [ ] Rejeição de token expirado ou adulterado.
- [ ] Renovação de sessão conforme a regra definida.
- [ ] Logout e comportamento posterior da sessão.
- [ ] Proteção das rotas e endpoints.
- [ ] Impedir acesso aos dados de outro usuário.
- [ ] Validar edição de perfil e alteração de senha após implementação.

### Ganhos e transferências

- [ ] Criar, consultar, editar e excluir registros.
- [ ] Validar campos obrigatórios, datas e valores.
- [ ] Conferir atualização dos totais após alterações.
- [ ] Evitar duplicidade por envio repetido.
- [ ] Validar pagamento e reversão após implementação.
- [ ] Conferir separação entre ganhos futuros e recebidos.

### Parcelas, crédito e faturas

- [ ] Conferir soma das parcelas e tratamento de arredondamentos.
- [ ] Validar vencimentos na mudança de mês e ano.
- [ ] Registrar pagamento individual sem alterar outras parcelas.
- [ ] Conferir distribuição das compras entre faturas.
- [ ] Validar compras próximas ao fechamento.
- [ ] Evitar dupla contabilização no pagamento da fatura.

### Cofrinho, histórico e CSV

- [ ] Conferir depósitos, retiradas e saldo reservado.
- [ ] Validar filtros por mês e mudança de ano.
- [ ] Conferir totais do histórico.
- [ ] Exportar somente os registros selecionados.
- [ ] Validar acentos, separadores, aspas, datas e valores no CSV.
- [ ] Conferir exportação sem registros.

## Bugs

Nenhum bug específico foi confirmado nesta documentação. Os cenários de teste não representam falhas já identificadas.

Ao encontrar um problema, registrar:

| Campo | Descrição |
| --- | --- |
| ID | Identificador do bug |
| Título | Resumo do comportamento incorreto |
| Módulo | Área afetada |
| Severidade | Crítica, alta, média ou baixa |
| Status | Aberto, em análise, em correção, em validação ou resolvido |
| Ambiente | Local, testes ou produção |
| Reprodução | Passos necessários para reproduzir |
| Esperado | Comportamento correto |
| Obtido | Comportamento observado |
| Evidências | Logs, imagens ou mensagens de erro |
| Correção | Commit ou versão da solução |

## Decisões técnicas

### Base definida

| Tema | Decisão |
| --- | --- |
| Backend | PHP 8 |
| Persistência | MySQL |
| Autenticação | JWT |
| Frontend | Vue |
| Ferramenta de desenvolvimento | Vite |
| Navegação | Vue Router |

### Decisões pendentes

- Modelo de dados para compras e parcelas.
- Regras de crédito, fechamento e vencimento.
- Tratamento do pagamento das faturas no saldo.
- Critério de agrupamento do histórico mensal.
- Efeito do cofrinho no saldo disponível.
- Formato e implementação da exportação CSV.

### Modelo de registro

Cada nova decisão técnica pode seguir esta estrutura:

- **ID e título:**
- **Data:**
- **Status:** proposta, aceita ou substituída.
- **Contexto:** problema ou necessidade.
- **Alternativas:** opções consideradas.
- **Decisão:** solução escolhida.
- **Consequências:** benefícios, limitações e impactos.
- **Referências:** tarefas, commits ou documentação relacionada.

## Critério de conclusão

Uma tarefa pode ser marcada como concluída quando:

- [ ] O comportamento previsto foi implementado.
- [ ] As validações necessárias foram aplicadas.
- [ ] Os cenários de teste pertinentes foram executados.
- [ ] As dependências foram atendidas.
- [ ] A documentação foi atualizada.

A implantação deve ser registrada separadamente, com versão ou commit e ambiente de destino.