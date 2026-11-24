<?php
// Se alguém abrir esta página direto, sem passar pelo controller, manda pro controller
if (!isset($funcionarios)) {
    header("Location: " . BASE_URL . "/app/controllers/FuncionarioController.php?acao=listar");
    exit;
}

$tituloPagina = "Funcionários";

include __DIR__ . '/../shared/header.php';
include __DIR__ . '/../shared/sidebar.php';

// Endereço do controller, para não repetir em todos os links
$controller = BASE_URL . "/app/controllers/FuncionarioController.php";
?>

<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/jorge-financeiro.css">

<main class="conteudo financeiro-page">

    <!-- Título da página e botão de cadastrar -->
    <div class="fin-page-header">

        <div class="fin-header-info">

            <h1>
                Gestão de Colaboradores
            </h1>

            <p>
                Gerencie a equipe e defina as permissões de acesso ao sistema.
            </p>

        </div>

        <div class="fin-header-actions">

            <a
                href="<?= $controller ?>?acao=cadastrar"
                class="fin-btn fin-btn-primary"
            >
                + Novo Funcionário
            </a>

        </div>

    </div>

    <!-- Formulário de busca -->
    <section class="fin-table-card">

        <form
            action="<?= $controller ?>"
            method="GET"
            class="fin-form-grid"
        >

            <input
                type="hidden"
                name="acao"
                value="listar"
            >

            <div class="fin-form-group">

                <label>
                    Nome do Colaborador
                </label>

                <input
                    type="text"
                    name="nome_busca"
                    class="fin-input"
                    placeholder="Buscar por nome"
                    value="<?= htmlspecialchars(
                        $nome_busca ?? '',
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>"
                >

            </div>

            <div class="fin-form-group">

                <label>
                    Cargo / Perfil
                </label>

                <select
                    name="role_busca"
                    class="fin-select"
                >

                    <option value="">
                        Todos os cargos
                    </option>

                    <option
                        value="Admin"
                        <?php if (($role_busca ?? '') === 'Admin') echo 'selected'; ?>
                    >
                        Administrador
                    </option>

                    <option
                        value="Professor"
                        <?php if (($role_busca ?? '') === 'Professor') echo 'selected'; ?>
                    >
                        Instrutor / Professor
                    </option>

                    <option
                        value="Recepcao"
                        <?php if (($role_busca ?? '') === 'Recepcao') echo 'selected'; ?>
                    >
                        Recepção
                    </option>

                </select>

            </div>

            <div class="fin-form-group">

                <button
                    type="submit"
                    class="fin-btn fin-btn-secondary"
                >
                    Buscar
                </button>

            </div>

        </form>

    </section>

    <!-- Tabela com os funcionários -->
    <section class="fin-table-card">

        <div class="fin-table-responsive">

            <table class="fin-table">

                <thead>

                    <tr>

                        <th>
                            Nome
                        </th>

                        <th>
                            E-mail
                        </th>

                        <th>
                            Perfil
                        </th>

                        <th>
                            Ações
                        </th>

                    </tr>

                </thead>

                <tbody>

                    <?php if (count($funcionarios) === 0): ?>

                        <tr>

                            <td colspan="4">
                                Nenhum funcionário encontrado.
                            </td>

                        </tr>

                    <?php endif; ?>

                    <?php foreach ($funcionarios as $func): ?>

                        <?php

                        $numeroWhats = '';

                        if (!empty($func['telefone'])) {

                            $numeroWhats = preg_replace(
                                '/\D+/',
                                '',
                                $func['telefone']
                            );
                        }

                        $perfilExibicao = $func['role'] ?? '';

                        if ($perfilExibicao === 'Admin') {
                            $perfilExibicao = 'Administrador';
                        }

                        if ($perfilExibicao === 'Recepcao') {
                            $perfilExibicao = 'Recepção';
                        }

                        ?>

                        <tr>

                            <!-- NOME -->
                            <td>

                                <strong>
                                    <?= htmlspecialchars(
                                        $func['name'] ?? '',
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </strong>

                            </td>

                            <!-- E-MAIL -->
                            <td>

                                <?= htmlspecialchars(
                                    $func['email'] ?? '',
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>

                            </td>

                            <!-- PERFIL -->
                            <td>

                                <?= htmlspecialchars(
                                    $perfilExibicao,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>

                            </td>

                            <!-- AÇÕES -->
                            <td>

                                <div class="icones-acoes">

                                    <!-- WhatsApp -->
                                    <?php if ($numeroWhats !== ''): ?>

                                        <a
                                            href="https://wa.me/55<?= htmlspecialchars(
                                                $numeroWhats,
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            title="WhatsApp"
                                            class="icone"
                                        >

                                            <svg
                                                width="22"
                                                height="22"
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                stroke="#22c55e"
                                                stroke-width="2"
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                            >

                                                <path
                                                    d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"
                                                ></path>

                                            </svg>

                                        </a>

                                    <?php endif; ?>

                                    <!-- E-mail -->
                                    <?php if (!empty($func['email'])): ?>

                                        <a
                                            href="https://mail.google.com/mail/?view=cm&fs=1&to=<?= urlencode(
                                                $func['email']
                                            ) ?>"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            title="E-mail"
                                            class="icone"
                                        >

                                            <svg
                                                width="22"
                                                height="22"
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                stroke="#64748b"
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

                                                <path
                                                    d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"
                                                ></path>

                                            </svg>

                                        </a>

                                    <?php endif; ?>

                                    <!-- Visualizar -->
                                    <a
                                        href="<?= $controller ?>?acao=visualizar&id=<?= urlencode(
                                            $func['id']
                                        ) ?>"
                                        title="Visualizar"
                                        class="icone"
                                    >

                                        <svg
                                            width="22"
                                            height="22"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="#64748b"
                                            stroke-width="2"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        >

                                            <path
                                                d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"
                                            ></path>

                                            <circle
                                                cx="12"
                                                cy="12"
                                                r="3"
                                            ></circle>

                                        </svg>

                                    </a>

                                    <!-- Editar -->
                                    <a
                                        href="<?= $controller ?>?acao=editar&id=<?= urlencode(
                                            $func['id']
                                        ) ?>"
                                        title="Editar"
                                        class="icone"
                                    >

                                        <svg
                                            width="22"
                                            height="22"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="#64748b"
                                            stroke-width="2"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        >

                                            <path
                                                d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"
                                            ></path>

                                            <path
                                                d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"
                                            ></path>

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