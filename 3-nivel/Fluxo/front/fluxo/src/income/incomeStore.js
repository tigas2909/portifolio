import { ref } from 'vue'
import { chamar } from '../service/api'

const API_URL = 'http://localhost:8080/financia/src/index.php'

// ---------- Estado compartilhado ----------
export const ganhos = ref([])
export const carregando = ref(false)

// Mês que a lista está mostrando: null = mês atual, "all" = todos, ou "AAAA-MM"
export const mes = ref(null)



// ---------- Auxiliares ----------
const hoje = () => new Date().toLocaleDateString('sv-SE') // AAAA-MM-DD

const ordenar = (lista) =>
  [...lista].sort(
    (a, b) => b.received_at.localeCompare(a.received_at) || b.id - a.id
  )

// Aceita 2026-9, 2026/9, 2026-09 e 2026/09 e devolve AAAA-MM
function normalizarMes(valor) {
  const m = /^(\d{4})[/-](\d{1,2})$/.exec(String(valor).trim())
  return m ? `${m[1]}-${m[2].padStart(2, '0')}` : null
}

// Diz se um ganho com esta data deve aparecer na lista que está na tela.
function pertenceALista(data) {
  if (mes.value === 'all') return true

  const alvo = mes.value ? normalizarMes(mes.value) : hoje().slice(0, 7)

  return data.slice(0, 7) === alvo
}

// ---------- Funções que as telas chamam ----------

// Lista. Sem argumentos: mês atual. Com { month: '2026-09' }: aquele mês.
export async function carregarGanhos({ month = null, silencioso = false } = {}) {
  if (!silencioso) carregando.value = true

  try {
    const resposta = await chamar('income', 'selectAll', month ? { month } : {})

    mes.value = month
    ganhos.value = resposta.result
  } finally {
    carregando.value = false
  }
}

// Busca um ganho pelo id (usado pelo formulário de alterar). Devolve o ganho.
export async function buscarGanho(id) {
  const resposta = await chamar('income', 'selectById', { id })

  return resposta.result
}

// Insere. O ganho entra na lista na hora, se for do mês que ela mostra.
export async function inserirGanho(dados) {
  const resposta = await chamar('income', 'create', dados)

  if (pertenceALista(dados.received_at)) {
    ganhos.value = ordenar([{ id: resposta.id, ...dados }, ...ganhos.value])
  }

  return resposta
}

// Altera. Se a data mudou para fora do mês da lista, o ganho sai dela.
export async function alterarGanho(id, dados) {
  await chamar('income', 'update', { id, ...dados })

  const restantes = ganhos.value.filter((g) => g.id !== id)

  ganhos.value = pertenceALista(dados.received_at)
    ? ordenar([...restantes, { id, ...dados }])
    : restantes
}

// Exclui. Some da lista na hora.
export async function excluirGanho(id) {
  await chamar('income', 'delete', { id })

  ganhos.value = ganhos.value.filter((g) => g.id !== id)
}

// Chame no logout, para o próximo usuário não ver os dados do anterior.
export function limparGanhos() {
  ganhos.value = []
  mes.value = null
}