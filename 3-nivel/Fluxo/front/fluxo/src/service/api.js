const API_URL = 'http://localhost:8080/financia/src/index.php'
const LOGIN_URL = 'http://localhost:8080/financia/src/loginIndex.php'

async function enviar(url, type, action, data = {}) {
  const resposta = await fetch(url, {
    method: 'POST',
    credentials: 'include',
    headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
    body: JSON.stringify({ type, action, data }),
  })

  const texto = await resposta.text()
  let json

  try {
    json = JSON.parse(texto)
  } catch {
    const falha = new Error('A API retornou uma resposta inválida.')
    falha.status = resposta.status
    throw falha
  }

  if (!resposta.ok || json.success !== true) {
    const falha = new Error(json.errors?.[0] || 'Não foi possível concluir a operação.')
    falha.status = resposta.status
    throw falha
  }

  return json
}

// index.php: ganhos e transações
export const chamar = (type, action, data = {}) => enviar(API_URL, type, action, data)

// loginIndex.php: usuários (login, logout, cadastro)
export const chamarLogin = (type, action, data = {}) => enviar(LOGIN_URL, type, action, data)