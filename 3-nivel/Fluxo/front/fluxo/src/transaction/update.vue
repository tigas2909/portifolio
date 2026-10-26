<script setup>
import { onMounted, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import {
  alterarPagamento,
  buscarPagamento,
  excluirPagamento,
  listarParcelas,
  pagarParcela,
} from './transactionStore'

const route = useRoute()
const router = useRouter()

const id = Number(route.params.id)

const pagamento = ref(null) // dados da transação (somente leitura)
const parcelas = ref([])
const descricao = ref('')

const erro = ref('')
const carregando = ref(true)
const salvando = ref(false)
const pagandoData = ref(null)
const bloqueado = ref(false)

const tipos = {
  ONE_TIME: 'Pagamento único',
  RECURRING: 'Recorrente',
  INSTALLMENT: 'Parcelado',
}

const situacoes = {
  PENDING: 'Pendente',
  PAID: 'Pago',
  COMPLETED: 'Concluído',
}

const moeda = (valor) =>
  Number(valor).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' })

// AAAA-MM-DD -> DD/MM/AAAA
const dataBR = (data) => data.split('-').reverse().join('/')

function tratarErro(e) {
  if (e.status === 401) {
    router.push('/login')
    return
  }

  erro.value = e.message
}

async function carregar() {
  const dados = await buscarPagamento(id)

  pagamento.value = dados
  descricao.value = dados.description

  // Pagamento único não tem parcelas.
  parcelas.value = dados.type === 'ONE_TIME' ? [] : await listarParcelas(id)
}

onMounted(async () => {
  if (!Number.isInteger(id) || id <= 0) {
    erro.value = 'Pagamento inválido.'
    bloqueado.value = true
    carregando.value = false
    return
  }

  try {
    await carregar()
  } catch (e) {
    bloqueado.value = true
    tratarErro(e)
  } finally {
    carregando.value = false
  }
})

async function salvar() {
  erro.value = ''
  salvando.value = true

  try {
    await alterarPagamento(id, descricao.value)

    await router.push('/transaction/list')
  } catch (e) {
    tratarErro(e)
  } finally {
    salvando.value = false
  }
}

// Paga ou antecipa uma parcela.
async function pagar(parcela) {
  if (pagandoData.value !== null) return

  pagandoData.value = parcela.due_date
  erro.value = ''

  try {
    await pagarParcela(id, parcela.due_date)

    await carregar()
  } catch (e) {
    tratarErro(e)
  } finally {
    pagandoData.value = null
  }
}

async function excluir() {
  if (!window.confirm('Excluir este pagamento e todas as parcelas dele?')) return

  erro.value = ''
  salvando.value = true

  try {
    await excluirPagamento(id)

    await router.push('/transaction/list')
  } catch (e) {
    tratarErro(e)
  } finally {
    salvando.value = false
  }
}
</script>

<template>
  <div class="pagina">
    <main>
      <h1>Alterar pagamento</h1>

      <p v-if="carregando" role="status">Carregando pagamento...</p>

      <dl v-if="pagamento" class="resumo">
        <div>
          <dt>{{ pagamento.type === 'INSTALLMENT' ? 'Valor total' : 'Valor' }}</dt>
          <dd>{{ moeda(pagamento.amount) }}</dd>
        </div>
        <div>
          <dt>Tipo</dt>
          <dd>{{ tipos[pagamento.type] }}</dd>
        </div>
        <div>
          <dt>Próximo vencimento</dt>
          <dd>{{ dataBR(pagamento.due_date) }}</dd>
        </div>
        <div v-if="pagamento.type === 'INSTALLMENT'">
          <dt>Parcelas pagas</dt>
          <dd>{{ pagamento.paid_installments }}/{{ pagamento.total_installments }}</dd>
        </div>
        <div>
          <dt>Situação</dt>
          <dd>{{ situacoes[pagamento.status] }}</dd>
        </div>
      </dl>

      <section v-if="parcelas.length" class="parcelas">
        <h2>Parcelas</h2>

        <ul>
          <li v-for="(parcela, indice) in parcelas" :key="parcela.id">
            <span>{{ indice + 1 }}. {{ dataBR(parcela.due_date) }}</span>
            <span>{{ moeda(parcela.amount) }}</span>

            <span v-if="parcela.status === 'PAID'" class="ok">Paga</span>

            <button
              v-else-if="pagamento && pagamento.type === 'INSTALLMENT'"
              type="button"
              class="pagar"
              :disabled="pagandoData !== null"
              @click="pagar(parcela)"
            >
              {{ pagandoData === parcela.due_date ? 'Salvando...' : 'Pagar' }}
            </button>

            <span v-else class="pendente">Pendente</span>
          </li>
        </ul>
      </section>

      <form @submit.prevent="salvar">
        <label for="descricao">
          Descrição:
          <input
            id="descricao"
            v-model.trim="descricao"
            name="descricao"
            type="text"
            maxlength="255"
            autocomplete="off"
            required
            :disabled="bloqueado"
          />
        </label>

        <button type="submit" :disabled="carregando || salvando || bloqueado">
          {{ salvando ? 'Salvando...' : 'Salvar alterações' }}
        </button>

        <button
          type="button"
          class="perigo"
          :disabled="carregando || salvando || bloqueado"
          @click="excluir"
        >
          Excluir pagamento
        </button>

        <RouterLink to="/transaction/list">Cancelar</RouterLink>

        <p v-if="erro" class="erro" role="alert">{{ erro }}</p>
      </form>
    </main>
  </div>
</template>
<style scoped>
    :global(body) {
    margin: 0;
    }

        

    .pagina {
    --erro: #b42318;

    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 100vh;
    min-height: 100dvh;
    padding: 1rem;
    background: var(--bg);
    color: var(--text);
    font-family: system-ui, -apple-system, 'Segoe UI', sans-serif;
    }

    @media (prefers-color-scheme: dark) {
    .pagina {
    --erro: #f97066;
    }
    }

    main {
    width: 100%;
    max-width: 380px;
    padding: 28px;
    background: var(--card);
    color: var(--text);
    border: 1px solid var(--line);
    border-radius: 12px;
    }

    h1 {
    margin: 0 0 20px;
    font-size: 1.4rem;
    text-align: center;
    }

    label {
    display: block;
    margin-bottom: 14px;
    font-size: 0.9rem;
    color: var(--muted);
    }

    input {
    display: block;
    width: 100%;
    margin-top: 6px;
    padding: 10px 12px;
    border: 1px solid var(--line);
    border-radius: 8px;
    background: var(--bg);
    color: var(--text);
    font: inherit;
    }

    input:disabled {
    opacity: 0.6;
    }

    input:focus-visible,
    button:focus-visible,
    a:focus-visible {
    outline: 2px solid var(--accent);
    outline-offset: 2px;
    }

    button {
    display: block;
    margin: 20px auto 0;
    padding: 10px 28px;
    border: 0;
    border-radius: 8px;
    background: var(--accent);
    color: var(--accent-text);
    font: inherit;
    font-weight: 600;
    cursor: pointer;
    }

    button:disabled {
    opacity: 0.6;
    cursor: wait;
    }

    a {
    display: block;
    margin-top: 16px;
    color: var(--accent);
    text-align: center;
    }

    p {
    text-align: center;
    color: var(--muted);
    }

    p.erro {
    color: var(--erro);
    }
    .resumo {
  display: grid;
  gap: 0.5rem;
  margin: 0 0 20px;
}

.resumo div {
  display: flex;
  justify-content: space-between;
  gap: 1rem;
  padding-bottom: 0.4rem;
  border-bottom: 1px solid var(--line);
}

.resumo dt {
  color: var(--muted);
}

.resumo dd {
  margin: 0;
  font-weight: 600;
}

.parcelas {
  margin: 0 0 20px;
}

.parcelas h2 {
  margin: 0 0 0.5rem;
  font-size: 1rem;
}

.parcelas ul {
  list-style: none;
  margin: 0;
  padding: 0;
  display: grid;
  gap: 0.5rem;
}

.parcelas li {
  display: grid;
  grid-template-columns: 1fr auto auto;
  align-items: center;
  gap: 0.75rem;
  padding-bottom: 0.5rem;
  border-bottom: 1px solid var(--line);
  font-size: 0.9rem;
}

.ok {
  color: var(--accent);
}

.pendente {
  color: var(--muted);
}

.pagar {
  display: inline-block;
  margin: 0;
  padding: 0.25rem 0.7rem;
  border: 1px solid var(--accent);
  border-radius: 6px;
  background: transparent;
  color: var(--accent);
  font: inherit;
  font-size: 0.85rem;
  cursor: pointer;
}

.perigo {
  background: transparent;
  color: var(--erro);
  border: 1px solid var(--erro);
}
</style>