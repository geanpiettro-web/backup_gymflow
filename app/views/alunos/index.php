<?php
// Se alguém abrir esta página diretamente, redireciona para o controller
if (!isset($alunos)) {
    header("Location: " . BASE_URL . "/app/controllers/AlunoController.php?acao=listar");
    exit;
}

include __DIR__ . '/../shared/header.php';
include __DIR__ . '/../shared/sidebar.php';

// Valores padrão dos filtros
if (!isset($filiais)) {
    $filiais = [];
}

if (!isset($filial_id_filter)) {
    $filial_id_filter = 0;
}
?>

<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/jorge-financeiro.css">
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/alunos.css">

<main class="conteudo financeiro-page alunos-page">

    <!-- Título da página e botão de cadastrar -->
    <div class="fin-page-header">

        <div class="fin-header-info">
            <h1>Alunos</h1>
            <p>Gerencie todos os alunos cadastrados no sistema.</p>
        </div>

        <div class="fin-header-actions">
            <a href="<?= $baseUrl ?>?acao=cadastrar"
               class="fin-btn fin-btn-primary">
                + Novo Aluno
            </a>
        </div>

    </div>

    <!-- Filtros de busca -->
    <section class="fin-table-card alunos-filtros-card">

        <form action="<?= $baseUrl ?>"
              method="GET"
              class="fin-form-grid alunos-filtros">

            <input type="hidden" name="acao" value="listar">

            <!-- Filtro CPF -->
            <div class="fin-form-group alunos-filtro-campo">

                <label>CPF</label>

                <input
                    type="text"
                    name="cpf"
                    class="fin-input"
                    placeholder="Buscar por CPF"
                    value="<?= htmlspecialchars($cpf ?? '') ?>"
                >

            </div>

            <!-- Filtro Status -->
            <div class="fin-form-group alunos-filtro-campo">

                <label>Status</label>

                <select name="status" class="fin-select">

                    <option value="">Todos os status</option>

                    <option value="Ativo"
                        <?= ($status ?? '') == 'Ativo' ? 'selected' : '' ?>>
                        Ativo
                    </option>

                    <option value="Inativo"
                        <?= ($status ?? '') == 'Inativo' ? 'selected' : '' ?>>
                        Inativo
                    </option>

                </select>

            </div>

            <!-- Filtro Filial -->
            <div class="fin-form-group alunos-filtro-campo">

                <label>Filial</label>

                <select name="filial_id" class="fin-select">

                    <option value="">Todas as filiais</option>

                    <?php foreach ($filiais as $filial): ?>

                        <option
                            value="<?= $filial['id'] ?>"
                            <?= $filial_id_filter == $filial['id'] ? 'selected' : '' ?>
                        >
                            <?= htmlspecialchars($filial['nome']) ?>
                        </option>

                    <?php endforeach; ?>

                </select>

            </div>

            <!-- Botão de filtro -->
            <div class="fin-form-group">

                <button type="submit"
                        class="fin-btn fin-btn-secondary">
                    Filtrar
                </button>

            </div>

        </form>

    </section>

    <!-- Tabela de alunos -->
    <section class="fin-table-card">

        <div class="fin-table-responsive">

            <table class="fin-table">

                <thead>

                    <tr>
                        <th class="titulo-coluna">Nome</th>
                        <th class="titulo-coluna">CPF</th>
                        <th class="titulo-coluna">Filial</th>
                        <th class="titulo-coluna">Status</th>

                        <th class="titulo-coluna alunos-coluna-acoes">
                            Ações
                        </th>
                    </tr>

                </thead>

                <tbody>

                    <!-- Nenhum aluno encontrado -->
                    <?php if (count($alunos) == 0): ?>

                        <tr>
                            <td colspan="5" class="alunos-vazio">
                                Nenhum aluno encontrado.
                            </td>
                        </tr>

                    <?php endif; ?>

                    <?php foreach ($alunos as $aluno): ?>

                        <?php
                        // Remove caracteres não numéricos do telefone
                        $numeroWhats = '';

                        if (isset($aluno['telefone'])) {
                            $numeroWhats = preg_replace(
                                '/\D/',
                                '',
                                $aluno['telefone']
                            );
                        }
                        ?>

                        <tr>

                            <!-- Nome -->
                            <td>
                                <strong>
                                    <?= htmlspecialchars($aluno['nome']) ?>
                                </strong>
                            </td>

                            <!-- CPF -->
                            <td>
                                <?= htmlspecialchars($aluno['cpf']) ?>
                            </td>

                            <!-- Filial -->
                            <td>
                                <?= htmlspecialchars($aluno['nome_filial']) ?>
                            </td>

 <!-- Status -->
<td class="<?= $aluno['status'] === 'Ativo' ? 'status-ativo' : 'status-inativo' ?>">
    <?= htmlspecialchars($aluno['status']) ?>
</td>

                            <!-- Ações -->
                            <td class="alunos-coluna-acoes">

                                <div class="fin-actions-dropdown icones-acoes">

                                    <!-- WhatsApp -->
                                    <?php if ($numeroWhats != ''): ?>

                                        <a
                                            href="https://wa.me/55<?= $numeroWhats ?>"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            title="WhatsApp"
                                            aria-label="WhatsApp"
                                            class="icone icone-whats"
                                        >

                                            <svg
                                                width="22"
                                                height="22"
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                stroke="currentColor"
                                                stroke-width="2"
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                            >
                                                <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z">
                                                </path>
                                            </svg>

                                        </a>

                                    <?php endif; ?>

                                    <!-- E-mail -->
                                    <?php if (!empty($aluno['email'])): ?>

                                        <a
                                            href="https://mail.google.com/mail/?view=cm&fs=1&to=<?= urlencode($aluno['email']) ?>"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            title="E-mail"
                                            aria-label="E-mail"
                                            class="icone"
                                        >

                                            <svg
                                                width="22"
                                                height="22"
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                stroke="currentColor"
                                                stroke-width="2"
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                            >
                                                <rect
                                                    x="2"
                                                    y="4"
                                                    width="20"
                                                    height="16"
                                                    rx="2"
                                                ></rect>

                                                <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7">
                                                </path>
                                            </svg>

                                        </a>

                                    <?php endif; ?>

                                    <!-- Visualizar -->
                                    <a
                                        href="<?= $baseUrl ?>?acao=visualizar&id=<?= $aluno['id'] ?>"
                                        title="Visualizar"
                                        aria-label="Visualizar"
                                        class="icone"
                                    >

                                        <svg
                                            width="22"
                                            height="22"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        >
                                            <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z">
                                            </path>

                                            <circle
                                                cx="12"
                                                cy="12"
                                                r="3"
                                            ></circle>
                                        </svg>

                                    </a>

                                    <!-- Editar -->
                                    <a
                                        href="<?= $baseUrl ?>?acao=editar&id=<?= $aluno['id'] ?>"
                                        title="Editar"
                                        aria-label="Editar"
                                        class="icone icone-editar"
                                    >

                                        <svg
                                            width="22"
                                            height="22"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        >
                                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7">
                                            </path>

                                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z">
                                            </path>
                                        </svg>

                                    </a>

                                </div>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                </tbody>

            </table>

        </div>

    </section>

</main>

<?php include __DIR__ . '/../shared/footer.php'; ?>