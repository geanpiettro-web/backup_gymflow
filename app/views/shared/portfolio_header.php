<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        <?= htmlspecialchars(
            $tituloPagina ?? 'GymCore',
            ENT_QUOTES,
            'UTF-8'
        ) ?>
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

    <?php
    // Identificação do site público atualmente carregado.
    $portfolioEmpresaId = (int) ($company_id ?? 0);

    $portfolioQuery = $portfolioEmpresaId > 0
        ? '?id=' . $portfolioEmpresaId
        : '';

    $portfolioUrl = BASE_URL . '/index.php' . $portfolioQuery;

    $loginUrl = BASE_URL .
        '/app/controllers/LoginController.php?acao=login';

    if ($portfolioEmpresaId > 0) {
        $loginUrl .= '&empresa=' . $portfolioEmpresaId;
    }

    $portfolioEsc = static function ($valor): string {
        return htmlspecialchars(
            (string) $valor,
            ENT_QUOTES,
            'UTF-8'
        );
    };

    $portfolioPrimaria = $config['primary_color'] ?? '#C9A227';
    $portfolioSecundaria = $config['secondary_color'] ?? '#000000';

    if (!preg_match('/^#[0-9a-fA-F]{6}$/', $portfolioPrimaria)) {
        $portfolioPrimaria = '#C9A227';
    }

    if (!preg_match('/^#[0-9a-fA-F]{6}$/', $portfolioSecundaria)) {
        $portfolioSecundaria = '#000000';
    }
    ?>

    <style>
        :root {
            --portfolio-primary: <?= $portfolioPrimaria ?>;
            --portfolio-secondary: <?= $portfolioSecundaria ?>;
        }
    </style>

    <link
        rel="stylesheet"
        href="<?= BASE_URL ?>/assets/css/portfolio-mapa.css">
</head>

<body>

    <header class="portfolio-header">

        <div class="portfolio-logo">
            <h1>
                <?= $portfolioEsc($tituloPagina ?? 'GymCore') ?>
            </h1>
        </div>

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

                <a href="<?= $portfolioEsc($loginUrl) ?>">
                    Entrar
                </a>

                <a
                    href="<?= BASE_URL ?>/app/controllers/MatriculaController.php">
                    Matricule-se
                </a>

            <?php endif; ?>

        </div>

    </header>