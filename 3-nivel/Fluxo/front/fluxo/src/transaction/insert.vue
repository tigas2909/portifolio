<script setup>
import { computed, ref } from 'vue'
import { useRouter } from 'vue-router'
import { inserirPagamento } from './transactionStore'

const router = useRouter()

const descricao = ref('')
const valor = ref('')
const tipo = ref('ONE_TIME')
const data = ref(new Date().toLocaleDateString('sv-SE')) // hoje, AAAA-MM-DD
const totalParcelas = ref(2)

const erro = ref('')
const salvando = ref(false)

const parcelado = computed(() => tipo.value === 'INSTALLMENT')

// Em parcelado, o valor digitado é o total; o banco divide pelas parcelas.
const valorParcela = computed(() => {
  const total = Number(valor.value)
  const n = Number(totalParcelas.value)

  if (!parcelado.value || !(total > 0) || !(n >= 2)) return ''

  return (total / n).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' })
})

function tratarErro(e) {
  if (e.status === 401) {
    router.push('/login')
    return
  }

  erro.value = e.message
}

async function salvar() {
  erro.value = ''
  salvando.value = true

  const dados = {
    description: descricao.value,
    amount: Number(valor.value),
    type: tipo.value,
    due_date: data.value,
    total_installments: parcelado.value ? Number(totalParcelas.value) : 1,
  }

  try {
    await inserirPagamento(dados)

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
      <h1>Inserir pagamento</h1>

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
          />
        </label>

        <label for="tipo">
          Tipo do pagamento:
          <select id="tipo" v-model="tipo" name="tipo" required>
            <option value="ONE_TIME">Pagamento único</option>
            <option value="RECURRING">Recorrente (todo mês)</option>
            <option value="INSTALLMENT">Parcelado</option>
          </select>
        </label>

        <label for="valor">
          {{ parcelado ? 'Valor total (R$):' : 'Valor (R$):' }}
          <input
            id="valor"
            v-model="valor"
            name="valor"
            type="number"
            step="0.01"
            min="0.01"
            inputmode="decimal"
            required
          />
        </label>

        <label v-if="parcelado" for="totalParcelas">
          Número de parcelas:
          <input
            id="totalParcelas"
            v-model.number="totalParcelas"
            name="totalParcelas"
            type="number"
            min="2"
            step="1"
            required
          />
        </label>

        <p v-if="valorParcela" class="dica">Cada parcela: {{ valorParcela }}</p>

        <label for="data">
          {{ parcelado ? 'Vencimento da primeira parcela:' : 'Data de vencimento:' }}
          <input id="data" v-model="data" name="data" type="date" required />
        </label>

        <button type="submit" :disabled="salvando">
          {{ salvando ? 'Salvando...' : 'Salvar' }}
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

    select {
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

select:focus-visible {
  outline: 2px solid var(--accent);
  outline-offset: 2px;
}

.dica {
  margin: -6px 0 14px;
  font-size: 0.85rem;
  color: var(--muted);
  text-align: left;
}
</style>