<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import {
  ganhos,
  pagamentos,
  carregando,
  pagandoChave,
  carregarPainel,
  pagarLinha,
  sair,
} from './principalStore'

const router = useRouter()
const route = useRoute()

const menuAberto = ref(false)
const itensMenu = [
  { nome: 'Ganhos', caminho: '/income' },
  { nome: 'Pagamentos', caminho: '/transaction' },
  { nome: 'Geral', caminho: '/pagina-principal' },
  { nome: 'Perfil', caminho: '/perfil' },
  { nome: 'Sair', acao: 'sair' },
]

const erro = ref('')

function tratarErro(e) {
  if (e.status === 401) {
    router.push('/login')
    return
  }

  erro.value = e.message
}

async function carregar() {
  erro.value = ''

  try {
    await carregarPainel()
  } catch (e) {
    tratarErro(e)
  }
}

onMounted(carregar)

async function pagar(linha) {
  erro.value = ''

  try {
    await pagarLinha(linha)
  } catch (e) {
    tratarErro(e)
  }
}

function rotuloPagar(l) {
  if (l.pago) return 'Pago'
  if (pagandoChave.value === l.chave) return 'Salvando...'

  return l.tipoTransacao === 'INSTALLMENT'
    ? `Pagar parcela ${l.parcela}/${l.parcelas}`
    : 'Marcar como pago'
}

async function escolher(item) {
  menuAberto.value = false

  if (item.acao === 'sair') {
    await sair()
    router.push('/login')
    return
  }

  router.push(item.caminho)
}

// ---------- Totais ----------

const moeda = (v) =>
  v.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' })

const totalGanhos = computed(() => ganhos.value.reduce((s, g) => s + g.valor, 0))
const totalPago = computed(() =>
  pagamentos.value.filter((p) => p.pago).reduce((s, p) => s + p.valor, 0)
)
const totalAPagar = computed(() =>
  pagamentos.value.filter((p) => !p.pago).reduce((s, p) => s + p.valor, 0)
)
// O saldo só diminui quando o pagamento é marcado como pago
const saldo = computed(() => totalGanhos.value - totalPago.value)

const linhas = computed(() =>
  [
    ...ganhos.value.map((g) => ({ ...g, tipo: 'ganho' })),
    ...pagamentos.value.map((p) => ({ ...p, tipo: 'pagamento' })),
  ].sort((a, b) => b.data.localeCompare(a.data))
)

// Gráfico de rosca em SVG (sem biblioteca): pago / a pagar / sobra
const R = 15.915 // circunferência = 100
const fatias = computed(() => {
  // Base: o maior entre ganhos e gastos, para o gráfico não passar de 100%
  const total = Math.max(totalGanhos.value, totalPago.value + totalAPagar.value) || 1
  const partes = [
    { nome: 'Pago', valor: totalPago.value, cor: 'var(--c-pago)' },
    { nome: 'A pagar', valor: totalAPagar.value, cor: 'var(--c-apagar)' },
    {
      nome: 'Sobra',
      valor: Math.max(totalGanhos.value - totalPago.value - totalAPagar.value, 0),
      cor: 'var(--c-sobra)',
    },
  ]
  let acumulado = 0
  return partes.map((p) => {
    const pct = (p.valor / total) * 100
    const fatia = { ...p, pct, offset: -acumulado }
    acumulado += pct
    return fatia
  })
})
</script>

<template>
  <div class="app">
    <header class="topo">
      <h1>Fluxo</h1>
      <button
        class="hamburguer"
        :aria-expanded="menuAberto"
        aria-label="Abrir menu"
        @click="menuAberto = !menuAberto"
      >
        <span /><span /><span />
      </button>

      <nav v-if="menuAberto" class="menu">
        <button
          v-for="item in itensMenu"
          :key="item.nome"
          :class="{ ativo: item.caminho === route.path }"
          @click="escolher(item)"
        >
          {{ item.nome }}
        </button>
      </nav>
    </header>

    <main class="grade">
      <p v-if="erro" class="erro" role="alert">{{ erro }}</p>
      <p v-else-if="carregando" class="aviso" role="status">Carregando...</p>

      <section class="caixa grafico">
        <h2>Gráfico</h2>
        <svg viewBox="0 0 36 36" role="img" aria-label="Distribuição dos ganhos">
          <circle cx="18" cy="18" :r="R" fill="none" stroke="var(--c-borda)" stroke-width="5" />
          <circle
            v-for="f in fatias"
            :key="f.nome"
            cx="18" cy="18" :r="R" fill="none"
            :stroke="f.cor" stroke-width="5"
            :stroke-dasharray="`${f.pct} ${100 - f.pct}`"
            :stroke-dashoffset="f.offset + 25"
          />
        </svg>
        <ul class="legenda">
          <li v-for="f in fatias" :key="f.nome">
            <i :style="{ background: f.cor }" />{{ f.nome }}
          </li>
        </ul>
      </section>

      <section class="caixa">
        <h2>Total a pagar no mês</h2>
        <p class="valor">{{ moeda(totalAPagar) }}</p>
      </section>

      <section class="caixa">
        <h2>Saldo</h2>
        <p class="valor" :class="{ negativo: saldo < 0 }">{{ moeda(saldo) }}</p>
      </section>

      <section class="caixa linhas">
        <h2>Linhas</h2>

        <p v-if="!linhas.length && !carregando" class="vazio">
          Nenhum lançamento neste mês.
        </p>

        <ul v-else>
          <li v-for="l in linhas" :key="l.chave" :class="l.tipo">
            <span>{{ l.descricao }}</span>
            <span>{{ l.tipo === 'ganho' ? '+' : '−' }} {{ moeda(l.valor) }}</span>
            <button
              v-if="l.tipo === 'pagamento'"
              class="marcar"
              :disabled="l.pago || pagandoChave !== null"
              @click="pagar(l)"
            >
              {{ rotuloPagar(l) }}
            </button>
          </li>
        </ul>
      </section>
    </main>

    <footer class="rodape">
      <span>Criador: Tigas2909</span>
      <span>beta: 0.1</span>
    </footer>
  </div>
</template>

<style scoped>
:global(body) {
  margin: 0;
}

.app {
  /* Ponte entre os nomes que o painel usa e o tema global (theme.css) */
  --c-fundo: var(--bg);
  --c-caixa: var(--card);
  --c-texto: var(--text);
  --c-suave: var(--muted);
  --c-borda: var(--line);
  --c-destaque: var(--accent);
  --c-pago: var(--accent);

  /* Cores que só existem no painel */
  --c-apagar: #d9822b;
  --c-sobra: #a9c7c1;
  --c-negativo: #b42318;

  min-height: 100vh;
  display: flex;
  flex-direction: column;
  background: var(--c-fundo);
  color: var(--c-texto);
  font-family: system-ui, -apple-system, 'Segoe UI', sans-serif;
}

@media (prefers-color-scheme: dark) {
  .app {
    --c-apagar: #e8a15a;
    --c-sobra: #4c6b65;
    --c-negativo: #f97066;
  }
}

.app * {
  box-sizing: border-box;
}

/* Topo: título centralizado, hambúrguer à direita */
.topo {
  position: relative;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 0.75rem 3.5rem;
  background: var(--c-caixa);
  border-bottom: 1px solid var(--c-borda);
  text-align: center;
}
.topo h1 {
  margin: 0;
  font-size: 1.4rem;
  letter-spacing: 0.02em;
}
.hamburguer {
  position: absolute;
  right: 0.5rem;
  top: 50%;
  transform: translateY(-50%);
  display: grid;
  gap: 5px;
  padding: 0.6rem;
  background: none;
  border: 0;
  cursor: pointer;
}
.hamburguer span {
  width: 22px;
  height: 2px;
  background: var(--c-texto);
}

/* Menu suspenso (texto à esquerda para facilitar a leitura) */
.menu {
  position: absolute;
  top: 100%;
  right: 0.5rem;
  z-index: 10;
  min-width: 11rem;
  display: grid;
  background: var(--c-caixa);
  border: 1px solid var(--c-borda);
  border-radius: 8px;
  overflow: hidden;
  text-align: left;
}
.menu button {
  padding: 0.8rem 1rem;
  text-align: left;
  font: inherit;
  color: inherit;
  background: none;
  border: 0;
  border-bottom: 1px solid var(--c-borda);
  cursor: pointer;
}
.menu button:last-child {
  border-bottom: 0;
}
.menu button.ativo {
  color: var(--c-destaque);
  font-weight: 600;
}

/* Grade 2x2 */
.grade {
  flex: 1;
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 0.75rem;
  padding: 0.75rem;
  align-content: start;
}

/* Mensagens de carregamento e erro ocupam a largura toda */
.aviso,
.erro {
  grid-column: 1 / -1;
  margin: 0;
  text-align: center;
}
.aviso {
  color: var(--c-suave);
}
.erro {
  color: var(--c-negativo);
}

/* Caixas com conteúdo centralizado */
.caixa {
  padding: 0.9rem;
  background: var(--c-caixa);
  border: 1px solid var(--c-borda);
  border-radius: 12px;
  text-align: center;
}
.caixa h2 {
  margin: 0 0 0.5rem;
  font-size: 0.95rem;
  font-weight: 600;
  color: var(--c-suave);
}
.valor {
  margin: 0;
  font-size: 1.4rem;
  font-weight: 700;
}
.valor.negativo {
  color: var(--c-negativo);
}

/* Gráfico e legenda */
.grafico svg {
  display: block;
  width: 100%;
  max-width: 9rem;
  margin: 0 auto;
}
.legenda {
  list-style: none;
  margin: 0.6rem 0 0;
  padding: 0;
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  gap: 0.25rem 1rem;
  font-size: 0.85rem;
}
.legenda i {
  display: inline-block;
  width: 0.7rem;
  height: 0.7rem;
  margin-right: 0.4rem;
  border-radius: 50%;
}

/* Linhas: alinhado à esquerda */
.linhas {
  text-align: left;
}
.linhas ul {
  list-style: none;
  margin: 0;
  padding: 0;
  display: grid;
  gap: 0.5rem;
}
.linhas li {
  display: grid;
  gap: 0.25rem;
  padding-bottom: 0.5rem;
  border-bottom: 1px solid var(--c-borda);
  font-size: 0.9rem;
}
.linhas li:last-child {
  border-bottom: 0;
  padding-bottom: 0;
}
.linhas li.ganho span:nth-child(2) {
  color: var(--c-pago);
}
.vazio {
  margin: 0;
  color: var(--c-suave);
  font-size: 0.9rem;
}
.marcar {
  justify-self: start;
  padding: 0.25rem 0.6rem;
  font: inherit;
  font-size: 0.8rem;
  color: var(--c-destaque);
  background: none;
  border: 1px solid var(--c-destaque);
  border-radius: 6px;
  cursor: pointer;
}
.marcar:disabled {
  opacity: 0.6;
  cursor: default;
}

/* Rodapé centralizado */
.rodape {
  display: flex;
  justify-content: center;
  gap: 1.5rem;
  padding: 0.75rem 1rem;
  font-size: 0.8rem;
  color: var(--c-suave);
  border-top: 1px solid var(--c-borda);
  text-align: center;
}

button:focus-visible {
  outline: 2px solid var(--c-destaque);
  outline-offset: 2px;
}

@media (max-width: 520px) {
  .grade {
    grid-template-columns: 1fr;
  }
}
</style>