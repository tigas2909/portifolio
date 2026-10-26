<script setup>
import { onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import {
  transaction,
  carregando,
  mes,
  carregarPagamento,
  pagarPagamento,
} from './transactionStore'

const router = useRouter()

const erro = ref('')
const pagandoChave = ref(null)

const mesAtual = new Date().toLocaleDateString('sv-SE').slice(0, 7) // AAAA-MM
const mesSelecionado = ref(mes.value || mesAtual)

const moeda = (valor) =>
  Number(valor).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' })

// AAAA-MM-DD -> DD/MM/AAAA
const dataBR = (data) => data.split('-').reverse().join('/')

const chave = (p) => `${p.id}-${p.installment_id ?? 'unica'}`
const pago = (p) => p.status === 'PAID'

const rotulo = (p) =>
  p.type === 'INSTALLMENT'
    ? `Pagar parcela ${p.number}/${p.total_installments}`
    : 'Marcar como pago'

function tratarErro(e) {
  if (e.status === 401) {
    router.push('/login')
    return
  }

  erro.value = e.message
}

async function carregar(silencioso = false) {
  erro.value = ''

  try {
    await carregarPagamento({ month: mesSelecionado.value, silencioso })
  } catch (e) {
    tratarErro(e)
  }
}

async function pagar(p) {
  if (pago(p) || pagandoChave.value !== null) return

  pagandoChave.value = chave(p)
  erro.value = ''

  try {
    await pagarPagamento(p)
  } catch (e) {
    tratarErro(e)
  } finally {
    pagandoChave.value = null
  }
}

// Se já há dados em memória, mostra na hora e atualiza em segundo plano.
onMounted(() => carregar(transaction.value.length > 0))
</script>

<template>
  <div class="pagina">
    <main>
      <header class="topo">
        <RouterLink to="/transaction" class="voltar">← Voltar</RouterLink>
        <h1>Listar pagamentos</h1>
        <RouterLink to="/transaction/create" class="novo">Novo pagamento</RouterLink>
      </header>

      <div class="filtro">
        <label for="mes">
          Mês:
          <input id="mes" v-model="mesSelecionado" type="month" @change="carregar(false)" />
        </label>
      </div>

      <p v-if="erro" class="erro" role="alert">{{ erro }}</p>
      <p v-else-if="carregando" class="aviso" role="status">Carregando pagamentos...</p>
      <p v-else-if="!transaction.length" class="aviso">Nenhum pagamento neste mês.</p>

      <ul v-else class="cards">
        <li v-for="p in transaction" :key="chave(p)">
          <article class="card">
            <span class="descricao">{{ p.description }}</span>
            <span class="valor">{{ moeda(p.amount) }}</span>

            <span class="data">
              Vence em {{ dataBR(p.due_date) }}
              <template v-if="p.type === 'INSTALLMENT'">
                · parcela {{ p.number }}/{{ p.total_installments }}
              </template>
            </span>
            <span class="selo" :class="pago(p) ? 'pago' : 'pendente'">
              {{ pago(p) ? 'Pago' : 'Pendente' }}
            </span>

            <div class="acoes">
              <RouterLink :to="`/transaction/update/${p.id}`" class="alterar">
                Alterar →
              </RouterLink>

              <button
                v-if="!pago(p)"
                type="button"
                class="pagar"
                :disabled="pagandoChave !== null"
                @click="pagar(p)"
              >
                {{ pagandoChave === chave(p) ? 'Salvando...' : rotulo(p) }}
              </button>
            </div>
          </article>
        </li>
      </ul>
    </main>
  </div>
</template>

    <style scoped>
    :global(body) {
    margin: 0;
    }

    * {
    box-sizing: border-box;
    }

    .pagina {
    --erro: #b42318;

    display: flex;
    align-items: flex-start;
    justify-content: center;
    min-height: 100vh;
    min-height: 100dvh;
    padding: 1.5rem 1rem;
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
    max-width: 64rem;
    padding: 36px;
    background: var(--card);
    color: var(--text);
    border: 1px solid var(--line);
    border-radius: 12px;
    }

    /* Topo: voltar, título e novo ganho */
    .topo {
    display: grid;
    grid-template-columns: 1fr auto 1fr;
    align-items: center;
    gap: 1rem;
    margin-bottom: 28px;
    }

    h1 {
    margin: 0;
    font-size: 1.9rem;
    text-align: center;
    }

    .voltar {
    justify-self: start;
    font-size: 1.05rem;
    color: var(--accent);
    text-decoration: none;
    }

    .novo {
    justify-self: end;
    padding: 0.7rem 1.4rem;
    border-radius: 8px;
    background: var(--accent);
    color: var(--accent-text);
    font-size: 1.05rem;
    font-weight: 600;
    text-decoration: none;
    }

    /* Mensagens */
    .aviso,
    .erro {
    margin: 1.5rem 0;
    font-size: 1.1rem;
    text-align: center;
    }

    .aviso {
    color: var(--muted);
    }

    .erro {
    color: var(--erro);
    }

    /* Cards */
    .cards {
    list-style: none;
    margin: 0;
    padding: 0;
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(19rem, 1fr));
    gap: 1rem;
    }

    .card {
    display: grid;
    grid-template-columns: 1fr auto;
    gap: 0.5rem 1rem;
    height: 100%;
    padding: 1.3rem 1.4rem;
    background: var(--bg);
    color: var(--text);
    border: 1px solid var(--line);
    border-radius: 12px;
    text-decoration: none;
    }

    .card:hover {
    border-color: var(--accent);
    }

    .descricao {
    font-size: 1.2rem;
    font-weight: 600;
    overflow-wrap: anywhere;
    }

    .valor {
    font-size: 1.4rem;
    font-weight: 700;
    color: var(--accent);
    text-align: right;
    }

    .data {
    font-size: 1rem;
    color: var(--muted);
    }

    .alterar {
    font-size: 1rem;
    color: var(--accent);
    text-align: right;
    }

    .voltar:focus-visible,
    .novo:focus-visible,
    .card:focus-visible {
    outline: 2px solid var(--accent);
    outline-offset: 2px;
    }

    @media (max-width: 520px) {
    main {
        padding: 20px;
    }

    .topo {
        grid-template-columns: 1fr 1fr;
    }

    h1 {
        grid-column: 1 / -1;
        order: -1;
        font-size: 1.6rem;
    }

    .cards {
        grid-template-columns: 1fr;
    }
    }

    .pagina {
  --c-apagar: #d9822b;
}

@media (prefers-color-scheme: dark) {
  .pagina {
    --c-apagar: #e8a15a;
  }
}

.filtro {
  margin-bottom: 1.25rem;
  color: var(--muted);
}

.filtro input {
  margin-left: 0.5rem;
  padding: 0.5rem 0.7rem;
  border: 1px solid var(--line);
  border-radius: 8px;
  background: var(--bg);
  color: var(--text);
  font: inherit;
}

.selo {
  justify-self: end;
  padding: 0.1rem 0.6rem;
  border: 1px solid;
  border-radius: 999px;
  font-size: 0.8rem;
}

.selo.pago {
  color: var(--accent);
  border-color: var(--accent);
}

.selo.pendente {
  color: var(--c-apagar);
  border-color: var(--c-apagar);
}

.acoes {
  grid-column: 1 / -1;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  margin-top: 0.25rem;
}

.alterar {
  color: var(--accent);
  text-align: left;
  text-decoration: none;
}

.pagar {
  padding: 0.4rem 0.9rem;
  border: 1px solid var(--accent);
  border-radius: 8px;
  background: transparent;
  color: var(--accent);
  font: inherit;
  cursor: pointer;
}

.pagar:disabled {
  opacity: 0.6;
  cursor: default;
}
</style>