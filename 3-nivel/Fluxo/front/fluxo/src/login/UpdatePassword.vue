<script setup>
import { ref } from 'vue'

const senha = ref('')
const confirmar = ref('')
const mensagem = ref('')
const carregando = ref(false)


async function atualizarSenha() {
  mensagem.value = ''

  if (senha.value !== confirmar.value) {
    mensagem.value = 'As senhas não coincidem.'
    return
  }

  carregando.value = true

  try {
    // TODO: integrar com o endpoint PHP de atualização de senha.

    console.log('Senhas conferidas.')

    mensagem.value = 'Validação concluída. Falta integrar com a API.'
  } catch (error) {
    mensagem.value = 'Não foi possível atualizar a senha.'
  } finally {
    carregando.value = false
  }
}
</script>

<template>
  <main>
    <h1>Atualizar senha</h1>

    <form @submit.prevent="atualizarSenha">
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

      <label>
        Confirmar senha:
        <input
          v-model="confirmar"
          type="password"
          name="confirmar"
          autocomplete="new-password"
          required
        />
      </label>

      <button type="submit" :disabled="carregando">
        {{ carregando ? 'Salvando...' : 'Salvar' }}
      </button>

      <p v-if="mensagem" role="status">
        {{ mensagem }}
      </p>
    </form>
  </main>
</template>

<style scoped>
:global(:root) {
  --bg: #f2f4f3;
  --card: #fff;
  --text: #1d2321;
  --muted: #5d6764;
  --line: #c9d0cd;
  --accent: #1f6f5c;
  --accent-text: #fff;
}

@media (prefers-color-scheme: dark) {
  :global(:root) {
    --bg: #151a18;
    --card: #1e2523;
    --text: #e8ecea;
    --muted: #9aa5a1;
    --line: #36403d;
    --accent: #3fae95;
    --accent-text: #0e1513;
  }
}

* {
  box-sizing: border-box;
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
button:focus-visible {
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

p {
  color: var(--muted);
  text-align: center;
}
</style>