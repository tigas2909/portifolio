import { ref } from 'vue'
import { chamar, chamarLogin } from '../service/api'
import { limparGanhos } from '../income/incomeStore'
import { limparPagamento } from '../transaction/transactionStore'

// ---------- Estado do painel ----------
export const ganhos = ref([])
export const pagamentos = ref([])
export const carregando = ref(false)
export const pagandoChave = ref(null)

// ---------- Conversões (API -> formato do painel) ----------
const paraGanho = (g) => ({
  chave: `g-${g.id}`,
  id: g.id,
  descricao: g.description,
  valor: g.amount,
  data: g.received_at,
})

const paraPagamento = (p) => ({
  chave: `p-${p.id}-${p.installment_id ?? 'unica'}`,
  id: p.id, // id da transação
  descricao: p.description,
  valor: p.amount, // já é o valor da parcela do mês
  data: p.due_date,
  pago: p.status === 'PAID',
  parcela: p.number,
  parcelas: p.total_installments,
  tipoTransacao: p.type,
})

// ---------- Funções que a tela chama ----------

// Sem "month", a API devolve só o mês atual.
export async function carregarPainel() {
  carregando.value = true

  try {
    const [respGanhos, respPagamentos] = await Promise.all([
      chamar('income', 'selectAll'),
      chamar('transaction', 'selectAll'),
    ])

    ganhos.value = respGanhos.result.map(paraGanho)
    pagamentos.value = respPagamentos.result.map(paraPagamento)
  } finally {
    carregando.value = false
  }
}

// Paga uma linha do painel e recarrega os totais.
export async function pagarLinha(linha) {
  if (linha.pago || pagandoChave.value !== null) return

  pagandoChave.value = linha.chave

  try {
    if (linha.tipoTransacao === 'INSTALLMENT') {
      await chamar('transaction', 'payInstallment', { id: linha.id, due_date: linha.data })
    } else {
      await chamar('transaction', 'pay', { id: linha.id })
    }

    await carregarPainel()
  } finally {
    pagandoChave.value = null
  }
}

// Logout: mesmo se a API falhar, os dados em memória são limpos.
export async function sair() {
  try {
    await chamarLogin('users', 'logout')
  } catch {
    // segue para o login
  }

  ganhos.value = []
  pagamentos.value = []
  limparGanhos()
  limparPagamento()
}