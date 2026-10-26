<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()

const email = ref('')
const senha = ref('')
const mensagem = ref('')
const carregando = ref(false)

async function fazerLogin() {
  mensagem.value = ''
  carregando.value = true

  const dados = {
    type: 'users',
    action: 'login',
    data: {
      email: email.value,
      password: senha.value
    }
  }

  try {
    const resposta = await fetch(
      'http://localhost:8080/financia/src/loginIndex.php',
      {
        method: 'POST',
        credentials: 'include',
        headers: {
          'Content-Type': 'application/json',
          Accept: 'application/json'
        },
        body: JSON.stringify(dados)
      }
    )

    const texto = await resposta.text()

    console.log('Status HTTP:', resposta.status)
    console.log('Resposta da API:', texto)

    if (!texto.trim()) {
      throw new Error(
        `A API retornou uma resposta vazia (HTTP ${resposta.status}).`
      )
    }

    let resultado

    try {
      resultado = JSON.parse(texto)
    } catch (erro) {
      console.error('Resposta não é JSON válido:', texto)
      throw new Error('A API retornou uma resposta inválida.')
    }

    if (!resposta.ok || resultado.success !== true) {
      const erros = resultado.errors

      const mensagemErro = Array.isArray(erros)
        ? erros.join(', ')
        : typeof erros === 'string'
          ? erros
          : erros?.message || 'Não foi possível realizar o login.'

      throw new Error(mensagemErro)
    }

    mensagem.value = 'Login realizado com sucesso!'

    await router.push('/pagina-principal')
  } catch (erro) {
    console.error('Erro no login:', erro)
    mensagem.value = erro.message || 'Erro ao conectar com a API.'
  } finally {
    carregando.value = false
  }
}
</script>

<template>
  <div class="pagina">
    <main>
      <h1>Login</h1>

      <form @submit.prevent="fazerLogin">
        <label>
          E-mail:
          <input
            v-model.trim="email"
            type="email"
            name="email"
            autocomplete="email"
            required
          />
        </label>

        <label>
          Senha:
          <input
            v-model="senha"
            type="password"
            name="senha"
            autocomplete="current-password"
            required
          />
        </label>

        <button type="submit" :disabled="carregando">
          {{ carregando ? 'Entrando...' : 'Entrar' }}
        </button>

        <RouterLink to="/cadastro">Não tenho usuário</RouterLink>

        <p v-if="mensagem" role="status">
          {{ mensagem }}
        </p>
      </form>
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
  color: var(--muted);
  text-align: center;
}
</style>