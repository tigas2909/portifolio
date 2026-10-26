<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()

const senha = ref('')
const nome = ref('')
const email = ref('')
const mensagem = ref('')
const carregando = ref(false)

async function criarUsuario() {
  mensagem.value = ''
  carregando.value = true

  const dados = {
    type: 'users',
    action: 'create',
    data: {
      name: nome.value,
      email: email.value,
      password: senha.value
    }
  }

  try {
    const resposta = await fetch(
      'http://localhost:8080/financia/src/loginIndex.php',
      {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          Accept: 'application/json'
        },
        body: JSON.stringify(dados)
      }
    )

    const resultado = await resposta.json()

    if (!resposta.ok || resultado.success === false) {
      mensagem.value =
        resultado.errors?.[0] || 'Não foi possível criar o usuário.'

      return
    }

    mensagem.value = 'Usuário criado com sucesso!'

    console.log('Resposta da API:', resultado)

    await router.push('/login')
  } catch (erro) {
    console.error('Erro ao criar usuário:', erro)

    mensagem.value = `Erro na requisição: ${erro.message}`
  } finally {
    carregando.value = false
  }
}
</script>

<template>
  <div class="pagina">
    <main>
      <h1>Criar usuário</h1>

      <form @submit.prevent="criarUsuario">
        <label>
          Nome:
          <input
            v-model.trim="nome"
            type="text"
            name="nome"
            autocomplete="name"
            required
          />
        </label>

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
            autocomplete="new-password"
            required
          />
        </label>

        <button type="submit" :disabled="carregando">
          {{ carregando ? 'Salvando...' : 'Salvar' }}
        </button>

        <RouterLink to="/login">Possuo cadastro</RouterLink>

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
  text-align: center;
  color: var(--muted);
}
</style>