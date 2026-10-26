<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { inserirGanho } from './incomeStore'

const router = useRouter()

const descricao = ref('')
const valor = ref('')
const data = ref(new Date().toLocaleDateString('sv-SE')) // hoje, AAAA-MM-DD

const erro = ref('')
const salvando = ref(false)

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
    received_at: data.value,
  }

  try {
    await inserirGanho(dados)

    await router.push('/income/list')
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
      <h1>Inserir ganho</h1>

      <form @submit.prevent="salvar">
        <label for="descricao">
          Descrição:
          <input
            id="descricao"
            v-model.trim="descricao"
            name="descricao"
            type="text"
            autocomplete="off"
            required
          />
        </label>

        <label for="valor">
          Valor (R$):
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

        <label for="data">
          Data de recebimento:
          <input
            id="data"
            v-model="data"
            name="data"
            type="date"
            required
          />
        </label>

        <button type="submit" :disabled="salvando">
          {{ salvando ? 'Salvando...' : 'Salvar' }}
        </button>

        <RouterLink to="/income/list">Cancelar</RouterLink>

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
</style>