<?php
// app/views/shared/sidebar.php

$nomePainel = $_SESSION['nome_painel'] ?? 'Gymflow';
$role = $_SESSION['usuario_role'] ?? '';
?>

<aside class="sidebar" id="appSidebar">

    <!-- TOPO -->
    <div class="sidebar-header">

        <h1 class="sidebar-logo">
            <span>
                <?= htmlspecialchars(
                    $nomePainel,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </span>
        </h1>

        <button
            type="button"
            class="sidebar-toggle"
            id="sidebarToggle"
            aria-label="Recolher menu"
            title="Recolher menu">

            <i data-lucide="menu"></i>

        </button>

    </div>

    <!-- MENU PRINCIPAL -->
    <nav class="sidebar-nav">
        <ul>

            <?php if ($role === 'Aluno'): ?>

                <!-- PORTAL DO ALUNO -->

                <li>
                    <a href="<?= BASE_URL ?>/app/controllers/PortalAlunoController.php?acao=aluno"
                        title="Início">

                        <span class="menu-icon">
                            <i data-lucide="house"></i>
                        </span>

                        <span class="menu-text">Início</span>
                    </a>
                </li>

                <li>
                    <a href="<?= BASE_URL ?>/app/controllers/PortalAlunoController.php?acao=treinos"
                        title="Treinos">

                        <span class="menu-icon">
                            <i data-lucide="dumbbell"></i>
                        </span>

                        <span class="menu-text">Treinos</span>
                    </a>
                </li>

                <li>
                    <a href="<?= BASE_URL ?>/app/controllers/PortalAlunoController.php?acao=faturas"
                        title="Faturas">

                        <span class="menu-icon">
                            <i data-lucide="credit-card"></i>
                        </span>

                        <span class="menu-text">Faturas</span>
                    </a>
                </li>

                <li>
                    <a href="<?= BASE_URL ?>/app/controllers/PortalAlunoController.php?acao=contratos"
                        title="Contratos">

                        <span class="menu-icon">
                            <i data-lucide="file-text"></i>
                        </span>

                        <span class="menu-text">Contratos</span>
                    </a>
                </li>

            <?php elseif (in_array(
                $role,
                ['Admin', 'Recepcao', 'Professor'],
                true
            )): ?>

                <!-- DASHBOARD -->
                <li>
                    <a href="<?= BASE_URL ?>/app/controllers/DashboardController.php"
                        title="Dashboard">

                        <span class="menu-icon">
                            <i data-lucide="layout-dashboard"></i>
                        </span>

                        <span class="menu-text">Dashboard</span>
                    </a>
                </li>

                <!-- EXCLUSIVO DO ADMIN -->
                <?php if ($role === 'Admin'): ?>

                    <li>
                        <a href="<?= BASE_URL ?>/app/controllers/FilialController.php?acao=listar"
                            title="Filiais">

                            <span class="menu-icon">
                                <i data-lucide="map-pin"></i>
                            </span>

                            <span class="menu-text">Filiais</span>
                        </a>
                    </li>

                    <li>
                        <a href="<?= BASE_URL ?>/app/controllers/FuncionarioController.php?acao=listar"
                            title="Funcionários">

                            <span class="menu-icon">
                                <i data-lucide="users"></i>
                            </span>

                            <span class="menu-text">Funcionários</span>
                        </a>
                    </li>

                <?php endif; ?>

                <!-- ADMIN + RECEPÇÃO -->
                <?php if (in_array(
                    $role,
                    ['Admin', 'Recepcao'],
                    true
                )): ?>

                    <li>
                        <a href="<?= BASE_URL ?>/app/controllers/AlunoController.php?acao=listar"
                            title="Alunos">

                            <span class="menu-icon">
                                <i data-lucide="graduation-cap"></i>
                            </span>

                            <span class="menu-text">Alunos</span>
                        </a>
                    </li>

                    <li>
                        <a href="<?= BASE_URL ?>/app/controllers/PlanoController.php?acao=listar"
                            title="Planos">

                            <span class="menu-icon">
                                <i data-lucide="layers"></i>
                            </span>

                            <span class="menu-text">Planos</span>
                        </a>
                    </li>

                <?php endif; ?>

                <!-- FINANCEIRO: SOMENTE ADMIN -->
                <?php if ($role === 'Admin'): ?>

                    <li>
                        <a href="<?= BASE_URL ?>/app/controllers/FinanceiroController.php"
                            title="Financeiro">

                            <span class="menu-icon">
                                <i data-lucide="credit-card"></i>
                            </span>

                            <span class="menu-text">Financeiro</span>
                        </a>
                    </li>

                    <li>
                        <a href="<?= BASE_URL ?>/app/controllers/CrmController.php"
                            title="CRM Leads">

                            <span class="menu-icon">
                                <i data-lucide="target"></i>
                            </span>

                            <span class="menu-text">CRM Leads</span>
                        </a>
                    </li>

                    <li>
                        <a href="<?= BASE_URL ?>/app/controllers/FluxoCaixaController.php"
                            title="Fluxo de caixa">

                            <span class="menu-icon">
                                <i data-lucide="chart-no-axes-column-increasing"></i>
                            </span>

                            <span class="menu-text">Fluxo de caixa</span>
                        </a>
                    </li>

                    <li>
                        <a href="<?= BASE_URL ?>/app/controllers/MinhaMarcaController.php"
                            title="Minha marca">

                            <span class="menu-icon">
                                <i data-lucide="tag"></i>
                            </span>

                            <span class="menu-text">Minha marca</span>
                        </a>
                    </li>

                <?php endif; ?>

                <!-- ADMIN + PROFESSOR -->
                <?php if (in_array(
                    $role,
                    ['Admin', 'Professor'],
                    true
                )): ?>

                    <li>
                        <a href="<?= BASE_URL ?>/app/controllers/ExercicioController.php"
                            title="Biblioteca de exercícios">

                            <span class="menu-icon">
                                <i data-lucide="dumbbell"></i>
                            </span>

                            <span class="menu-text">Biblioteca de exercícios</span>
                        </a>
                    </li>

                    <li>
                        <a href="<?= BASE_URL ?>/app/controllers/TreinoController.php"
                            title="Ficha de treino">

                            <span class="menu-icon">
                                <i data-lucide="clipboard-list"></i>
                            </span>

                            <span class="menu-text">Ficha de treino</span>
                        </a>
                    </li>

                <?php endif; ?>

                <!-- PORTFÓLIO E CONFIGURAÇÕES DE E-MAIL: SOMENTE ADMIN -->
                <?php if ($role === 'Admin'): ?>

                    <li>
                        <a href="<?= BASE_URL ?>/app/controllers/PortfolioController.php"
                            title="Site / Portfólio">

                            <span class="menu-icon">
                                <i data-lucide="globe"></i>
                            </span>

                            <span class="menu-text">Site / Portfólio</span>
                        </a>
                    </li>

                    <li>
                        <a href="<?= BASE_URL ?>/app/controllers/ConfiguracaoEmailController.php"
                            title="Configurações de e-mail">

                            <span class="menu-icon">
                                <i data-lucide="mail"></i>
                            </span>

                            <span class="menu-text">Configurações de e-mail</span>
                        </a>
                    </li>

                <?php endif; ?>

            <?php endif; ?>

        </ul>
    </nav>

    <!-- RODAPÉ -->
    <div class="sidebar-footer">

        <a
            class="sidebar-action voltar-site"
            href="<?= BASE_URL ?>/"
            title="Voltar ao Site">

            <span class="menu-icon">
                <i data-lucide="arrow-left"></i>
            </span>

            <span class="menu-text">Voltar ao Site</span>
        </a>

        <a
            class="sidebar-action sair"
            href="<?= BASE_URL ?>/app/controllers/LoginController.php?acao=logout"
            title="Sair">

            <span class="menu-icon">
                <i data-lucide="log-out"></i>
            </span>

            <span class="menu-text">Sair</span>
        </a>

    </div>

</aside>

<!-- LUCIDE ICONS -->
<script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        lucide.createIcons({
            attrs: {
                width: 20,
                height: 20,
                'stroke-width': 1.8
            }
        });
    });
</script>

<script src="<?= BASE_URL ?>/assets/js/sidebar.js"></script>