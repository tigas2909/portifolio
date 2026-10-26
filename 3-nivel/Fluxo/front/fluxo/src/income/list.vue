<script setup>
import { onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import { ganhos, carregando, carregarGanhos } from '../income/incomeStore'

const router = useRouter()
const erro = ref('')

const moeda = (valor) =>
  Number(valor).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' })

// AAAA-MM-DD -> DD/MM/AAAA
const dataBR = (data) => data.split('-').reverse().join('/')

async function carregar() {
  erro.value = ''

  try {
    // Se já há dados em memória, mostra na hora e atualiza em segundo plano.
    await carregarGanhos({ silencioso: ganhos.value.length > 0 })
  } catch (e) {
    if (e.status === 401) {
      router.push('/login')
      return
    }

    erro.value = e.message
  }
}

onMounted(carregar)
</script>

<template>
  <div class="pagina">
    <main>
      <header class="topo">
        <RouterLink to="/income" class="voltar">← Voltar</RouterLink>
        <h1>Listar ganhos</h1>
        <RouterLink to="/income/create" class="novo">Novo ganho</RouterLink>
      </header>

      <p v-if="erro" class="erro" role="alert">{{ erro }}</p>
      <p v-else-if="carregando" class="aviso" role="status">Carregando ganhos...</p>
      <p v-else-if="!ganhos.length" class="aviso">Nenhum ganho neste mês.</p>

      <ul v-else class="cards">
        <li v-for="ganho in ganhos" :key="ganho.id">
          <RouterLink :to="`/income/update/${ganho.id}`" class="card">
            <span class="descricao">{{ ganho.description }}</span>
            <span class="valor">{{ moeda(ganho.amount) }}</span>
            <span class="data">{{ dataBR(ganho.received_at) }}</span>
            <span class="alterar">Alterar →</span>
          </RouterLink>
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
</style>