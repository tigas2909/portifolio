import { ref } from 'vue'
import { chamar } from '../service/api'

// ---------- Estado compartilhado ----------
export const transaction = ref([])
export const carregando = ref(false)

// Mês que a lista está mostrando: null = mês atual, ou "AAAA-MM"
export const mes = ref(null)

// ---------- Leitura ----------

// Lista do mês: cada linha é um pagamento único ou uma parcela que vence no mês.
export async function carregarPagamento({ month = null, silencioso = false } = {}) {
  if (!silencioso) carregando.value = true

  try {
    const resposta = await chamar('transaction', 'selectAll', month ? { month } : {})

    mes.value = month
    transaction.value = resposta.result
  } finally {
    carregando.value = false
  }
}

// Recarrega o mês que a lista está mostrando.
const recarregar = () => carregarPagamento({ month: mes.value, silencioso: true })

// Busca uma transação pelo id (tela de alterar).
export async function buscarPagamento(id) {
  const resposta = await chamar('transaction', 'selectById', { id })

  return resposta.result
}

// Parcelas de uma transação.
export async function listarParcelas(id) {
  const resposta = await chamar('transaction', 'selectInstallments', { id })

  return resposta.result
}

// ---------- Escrita ----------

// As parcelas são geradas no banco, então a lista é buscada de novo
// e passa a mostrar o mês do novo pagamento.
export async function inserirPagamento(dados) {
  const resposta = await chamar('transaction', 'create', dados)

  await carregarPagamento({ month: dados.due_date.slice(0, 7), silencioso: true })

  return resposta
}

// Só a descrição pode ser alterada.
export async function alterarPagamento(id, description) {
  await chamar('transaction', 'update', { id, description })

  await recarregar()
}

// Paga uma linha da lista: pay (único e recorrente) ou payInstallment (parcelado).
export async function pagarPagamento(linha) {
  if (linha.type === 'INSTALLMENT') {
    await chamar('transaction', 'payInstallment', { id: linha.id, due_date: linha.due_date })
  } else {
    await chamar('transaction', 'pay', { id: linha.id })
  }

  await recarregar()
}

// Paga ou antecipa uma parcela escolhida (tela de alterar).
export async function pagarParcela(id, dueDate) {
  await chamar('transaction', 'payInstallment', { id, due_date: dueDate })

  await recarregar()
}

// Exclui a transação e todas as parcelas dela.
export async function excluirPagamento(id) {
  await chamar('transaction', 'delete', { id })

  transaction.value = transaction.value.filter((p) => p.id !== id)
}

// Chame no logout, para o próximo usuário não ver os dados do anterior.
export function limparPagamento() {
  transaction.value = []
  mes.value = null
}