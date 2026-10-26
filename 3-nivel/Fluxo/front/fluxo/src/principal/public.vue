<template>
  <div class="home">
    <header class="topo">
      <h1>Fluxo</h1>

      <nav class="acoes" aria-label="Acesso">
        <RouterLink to="/login" class="botao botao-secundario">Acessar conta</RouterLink>
        <RouterLink to="/cadastro" class="botao">Criar conta</RouterLink>
      </nav>
    </header>

    <main class="conteudo">
      <p class="status">Versão beta em desenvolvimento</p>

      <section class="card">
        <h2>Resumo</h2>
        <p>
          Este é o meu primeiro sistema próprio, criado para resolver um problema meu: eu não
          tinha paciência para montar e formatar uma planilha do Excel. Para me desafiar e
          colocar meus conhecimentos em prática, decidi criar o Fluxo, com a ideia de facilitar
          a vida de quem é como eu, ou de quem não sabe usar as ferramentas que a maioria usa,
          para que possam se organizar e ter uma ideia de quanto gastaram e de quanto ainda
          falta pagar.
        </p>
      </section>

      <section class="card">
        <h2>Apresentação pessoal</h2>
        <p>
          Me chamo Tiago e estou cursando Análise e Desenvolvimento de Sistemas no Instituto
          Federal. O Fluxo é o meu primeiro projeto feito para outras pessoas usarem. Ele ainda
          está em desenvolvimento, então algumas funções podem mudar ou ainda não existir.
        </p>
      </section>

      <section class="card">
        <h2>O que o Fluxo vai fazer</h2>
        <ul>
          <li>Registrar ganhos e gastos.</li>
          <li>Acompanhar parcelas pagas e pendentes.</li>
          <li>Mostrar quanto você gastou e quanto ainda falta pagar.</li>
        </ul>
        <p class="nota">Planejado: estas funções ainda estão sendo construídas.</p>
      </section>
    </main>

    <footer class="rodape">
      <small>Criado por tigas2909 · Versão beta em desenvolvimento</small>
    </footer>
  </div>
</template>

<style scoped>
.home {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
  background: var(--bg);
  color: var(--text);
  font-family: system-ui, sans-serif;
}

.home * {
  box-sizing: border-box;
}

.topo {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  padding: 1rem 1.5rem;
  border-bottom: 1px solid var(--line);
}

.topo h1 {
  margin: 0;
  font-size: 1.4rem;
}

.acoes {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
}

.botao {
  padding: 0.6rem 1.2rem;
  border: 1px solid var(--accent);
  border-radius: 8px;
  background: var(--accent);
  color: var(--accent-text);
  font-weight: 600;
  text-decoration: none;
}

.botao-secundario {
  background: transparent;
  border-color: var(--line);
  color: var(--text);
}

.botao:focus-visible {
  outline: 2px solid var(--accent);
  outline-offset: 2px;
}

.conteudo {
  display: flex;
  flex: 1;
  flex-direction: column;
  gap: 1rem;
  width: 100%;
  max-width: 42rem;
  margin: 0 auto;
  padding: 1.5rem;
}

.status {
  align-self: flex-start;
  margin: 0;
  padding: 0.25rem 0.75rem;
  border: 1px solid var(--accent);
  border-radius: 999px;
  color: var(--accent);
  font-size: 0.85rem;
}

.card {
  padding: 1.25rem 1.5rem;
  background: var(--card);
  border: 1px solid var(--line);
  border-radius: 12px;
}

.card h2 {
  margin: 0 0 0.5rem;
  font-size: 1.1rem;
}

.card p,
.card li {
  line-height: 1.6;
}

.card p {
  margin: 0;
}

.card ul {
  margin: 0;
  padding-left: 1.2rem;
}

.nota {
  margin-top: 0.75rem !important;
  color: var(--muted);
  font-size: 0.9rem;
}

.rodape {
  padding: 1rem 1.5rem;
  border-top: 1px solid var(--line);
  color: var(--muted);
  text-align: center;
}
</style>