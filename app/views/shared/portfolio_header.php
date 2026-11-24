<?php
// ============================================
// GYMFLOW - CABEÇALHO DO PORTFÓLIO PÚBLICO
// app/views/shared/portfolio_header.php
// ============================================

// Empresa do portfólio atualmente acessado.
$portfolioEmpresaId = (int) ($company_id ?? 0);

// Endereço do portfólio da academia.
$portfolioUrl = BASE_URL . '/index.php';

if ($portfolioEmpresaId > 0) {
    $portfolioUrl .= '?id=' . $portfolioEmpresaId;
}

// Endereço do login.
$loginUrl = BASE_URL .
    '/app/controllers/LoginController.php?acao=login';

if ($portfolioEmpresaId > 0) {
    $loginUrl .= '&empresa=' . $portfolioEmpresaId;
}

// Endereço correto da matrícula pública.
$matriculaUrl = BASE_URL .
    '/app/controllers/MatriculaController.php?acao=identificacao';

if ($portfolioEmpresaId > 0) {
    $matriculaUrl .= '&company_id=' . $portfolioEmpresaId;
}

// Escapar valores utilizados no HTML.
$portfolioEsc = static function ($valor): string {
    return htmlspecialchars(
        (string) $valor,
        ENT_QUOTES,
        'UTF-8'
    );
};

// Configurações visuais do Portfólio.
$portfolioPrimaria = $config['primary_color'] ?? '#C9A227';
$portfolioSecundaria = $config['secondary_color'] ?? '#000000';

// Validar cores antes de incluir no CSS.
if (
    !is_string($portfolioPrimaria) ||
    !preg_match('/^#[0-9a-fA-F]{6}$/', $portfolioPrimaria)
) {
    $portfolioPrimaria = '#C9A227';
}

if (
    !is_string($portfolioSecundaria) ||
    !preg_match('/^#[0-9a-fA-F]{6}$/', $portfolioSecundaria)
) {
    $portfolioSecundaria = '#000000';
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>
        <?= $portfolioEsc(!empty($tituloPagina) ? $tituloPagina . ' — ' . ($config['app_name'] ?? 'GymFlow') : ($config['app_name'] ?? 'GymFlow')) ?>
    </title>

    <link
        rel="stylesheet"
        href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">

    <link
        rel="stylesheet"
        href="<?= BASE_URL ?>/assets/css/global.css">

    <link
        rel="stylesheet"
        href="<?= BASE_URL ?>/assets/css/portifolio.css?v=<?= time() ?>">

    <script
        src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        defer></script>

    <style>
        :root {
            --portfolio-primary: <?= $portfolioPrimaria ?>;
            --portfolio-secondary: <?= $portfolioSecundaria ?>;
        }
    </style>

    <link
        rel="stylesheet"
        href="<?= BASE_URL ?>/assets/css/portfolio-mapa.css">

    <?php if (!empty($cssEspecifico)): ?>
        <link
            rel="stylesheet"
            href="<?= $portfolioEsc($cssEspecifico) ?>">
    <?php endif; ?>
</head>

<body>

    <header class="portfolio-header">

        <!-- IDENTIDADE -->
        <div class="portfolio-logo">
            <h1>
                <?= $portfolioEsc($config['app_name'] ?? $tituloPagina ?? 'GymFlow') ?>
            </h1>
        </div>

        <!-- NAVEGAÇÃO -->
        <nav class="portfolio-nav">
            <ul>

                <li>
                    <a href="<?= $portfolioEsc($portfolioUrl) ?>#inicio">
                        Início
                    </a>
                </li>

                <li>
                    <a href="<?= $portfolioEsc($portfolioUrl) ?>#planos">
                        Planos
                    </a>
                </li>

                <li>
                    <a href="<?= $portfolioEsc($portfolioUrl) ?>#unidades">
                        Unidades
                    </a>
                </li>

                <li>
                    <a href="<?= $portfolioEsc($portfolioUrl) ?>#modalidades">
                        Modalidades
                    </a>
                </li>

                <li>
                    <a href="<?= $portfolioEsc($portfolioUrl) ?>#sobre">
                        Sobre Nós
                    </a>
                </li>

                <li>
                    <a href="<?= $portfolioEsc($portfolioUrl) ?>#contato">
                        Contato
                    </a>
                </li>

            </ul>
        </nav>

        <!-- AÇÕES -->
        <div class="portfolio-acoes">

            <?php if ($usuarioLogado ?? false): ?>

                <?php if (($roleUsuario ?? '') === 'Aluno'): ?>

                    <a
                        href="<?= BASE_URL ?>/app/controllers/PortalAlunoController.php?acao=aluno">
                        Área do Aluno
                    </a>

                <?php else: ?>

                    <a
                        href="<?= BASE_URL ?>/app/controllers/DashboardController.php">
                        Painel GymFlow
                    </a>

                <?php endif; ?>

                <a
                    href="<?= BASE_URL ?>/app/controllers/LoginController.php?acao=logout"
                    style="margin-left: 10px;">
                    Sair
                </a>

            <?php else: ?>

                <!-- LOGIN DA ACADEMIA -->
                <a href="<?= $portfolioEsc($loginUrl) ?>">
                    Entrar
                </a>

                <!-- MATRÍCULA DA ACADEMIA -->
                <a href="<?= $portfolioEsc($matriculaUrl) ?>">
                    Matricule-se
                </a>

            <?php endif; ?>

        </div>

    </header>