    <template>
    <div class="pagina">
        <main>
        <h1>Ganhos</h1>
        <p class="subtitulo">O que você quer fazer?</p>

        <nav class="opcoes" aria-label="Menu de ganhos">
            <RouterLink to="/transaction/create" class="opcao">
            <span class="titulo">Inserir pagamento</span>
            <span class="descricao">Registrar um novo pagamento</span>
            </RouterLink>

            <RouterLink to="/transaction/list" class="opcao">
            <span class="titulo">Listar pagamentos</span>
            <span class="descricao">Ver os pagamentos cadastrados</span>
            </RouterLink>
        </nav>

        <RouterLink to="/pagina-principal" class="voltar">
            ← Voltar à página principal
        </RouterLink>
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
    margin: 0;
    font-size: 1.4rem;
    text-align: center;
    }

    .subtitulo {
    margin: 6px 0 20px;
    color: var(--muted);
    text-align: center;
    }

    .opcoes {
    display: grid;
    gap: 0.75rem;
    }

    .opcao {
    display: grid;
    gap: 0.15rem;
    padding: 0.85rem 1rem;
    background: var(--bg);
    color: var(--text);
    border: 1px solid var(--line);
    border-radius: 8px;
    text-decoration: none;
    }

    .opcao:hover {
    border-color: var(--accent);
    }

    .titulo {
    font-weight: 600;
    }

    .descricao {
    font-size: 0.85rem;
    color: var(--muted);
    }

    .voltar {
    display: block;
    margin-top: 20px;
    color: var(--accent);
    text-align: center;
    }

    .opcao:focus-visible,
    .voltar:focus-visible {
    outline: 2px solid var(--accent);
    outline-offset: 2px;
    }
    </style>