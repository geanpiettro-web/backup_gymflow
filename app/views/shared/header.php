<?php
// =============================================
// GYMFLOW - HEADER GLOBAL
// =============================================

// 1. INFORMAÇÕES DO SISTEMA

$nomePainel = $_SESSION['nome_painel'] ?? 'Gymflow';
$titulo = $tituloPagina ?? 'Dashboard';

$usuario = $_SESSION['usuario_nome'] ?? 'Usuário';
$role = $_SESSION['usuario_role'] ?? 'Admin';

// 2. IDENTIDADE VISUAL

$corPrimaria = $_SESSION['cor_primaria'] ?? '#ffb000';
$corSecundaria = $_SESSION['cor_secundaria'] ?? '#111827';
$temaPainel = $_SESSION['tema'] ?? 'dark';

function validarCorGymflow($cor, $padrao)
{
    if (!is_string($cor)) {
        return $padrao;
    }

    $cor = trim($cor);

    return preg_match('/^#[0-9a-fA-F]{6}$/', $cor)
        ? $cor
        : $padrao;
}

$corPrimaria = validarCorGymflow(
    $corPrimaria,
    '#ffb000'
);

$corSecundaria = validarCorGymflow(
    $corSecundaria,
    '#111827'
);

// 3. CONTRASTE AUTOMÁTICO

function corTextoContrasteGymflow($hex)
{
    $hex = ltrim($hex, '#');

    $r = hexdec(substr($hex, 0, 2)) / 255;
    $g = hexdec(substr($hex, 2, 2)) / 255;
    $b = hexdec(substr($hex, 4, 2)) / 255;

    $linearizar = static function ($canal) {
        return $canal <= 0.04045
            ? $canal / 12.92
            : (($canal + 0.055) / 1.055) ** 2.4;
    };

    $luminancia =
        0.2126 * $linearizar($r) +
        0.7152 * $linearizar($g) +
        0.0722 * $linearizar($b);

    $contrasteComBranco = 1.05 / ($luminancia + 0.05);
    $contrasteComEscuro = ($luminancia + 0.05) / 0.05;

    return $contrasteComEscuro >= $contrasteComBranco
        ? '#000000'
        : '#ffffff';
}

$textoSobrePrimaria = corTextoContrasteGymflow(
    $corPrimaria
);

$textoSobreSecundaria = corTextoContrasteGymflow(
    $corSecundaria
);

// 4. APARÊNCIA DO PAINEL

$temaPainel = $temaPainel === 'dark'
    ? 'dark'
    : 'light';

if ($temaPainel === 'dark') {

    $corFundoHeader = $corSecundaria;
    $corTextoHeader = $textoSobreSecundaria;
    $corBordaHeader = '#334155';

} else {

    $corFundoHeader = '#ffffff';
    $corTextoHeader = '#111827';
    $corBordaHeader = '#e2e8f0';

}

// 5. FUNÇÕES AUXILIARES

$escapar = static function ($valor) {
    return htmlspecialchars(
        (string) $valor,
        ENT_QUOTES,
        'UTF-8'
    );
};

// 6. VERSIONAMENTO DOS ARQUIVOS CSS
// Aplicado aos CSS compartilhados e específicos.

$versionarCss = static function ($url) {

    $prefixo = BASE_URL . '/assets/css/';

    // Mantém URLs que não pertencem à pasta local.
    if (!str_starts_with($url, $prefixo)) {
        return $url;
    }

    $partes = parse_url($url);

    if ($partes === false) {
        return $url;
    }

    $caminhoUrl = $partes['path'] ?? '';

    $arquivo = substr(
        $caminhoUrl,
        strlen($prefixo)
    );

    // Aceita somente arquivos CSS da pasta.
    if (!preg_match('/^[a-zA-Z0-9_-]+\.css$/', $arquivo)) {
        return $url;
    }

    $caminho = dirname(__DIR__, 3)
        . '/assets/css/'
        . $arquivo;

    if (!is_file($caminho)) {
        return $url;
    }

    $versao = filemtime($caminho);

    if ($versao === false) {
        return $url;
    }

    // Preserva outros parâmetros da URL.
    $parametros = [];

    parse_str(
        $partes['query'] ?? '',
        $parametros
    );

    // Atualiza a versão sem duplicar parâmetros.
    $parametros['v'] = $versao;

    $urlVersionada = $caminhoUrl
        . '?'
        . http_build_query($parametros);

    if (isset($partes['fragment'])) {
        $urlVersionada .= '#' . $partes['fragment'];
    }

    return $urlVersionada;
};

// Carregamento dos CSS compartilhados.

$carregarCss = static function ($arquivo) use ($versionarCss) {

    return $versionarCss(
        BASE_URL . '/assets/css/' . $arquivo
    );
};

// Carregamento do CSS específico da página.

$cssEspecifico = $cssEspecifico ?? null;

if (!empty($cssEspecifico)) {

    $cssEspecifico = $versionarCss($cssEspecifico);

}

?>

<!DOCTYPE html>
<html lang="pt-BR" data-theme="<?= $escapar($temaPainel) ?>">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>
        <?= $escapar($nomePainel) ?> |
        <?= $escapar($titulo) ?>
    </title>

    <!-- CSS GLOBAL -->

    <link
        rel="stylesheet"
        href="<?= $escapar($carregarCss('global.css')) ?>">

    <!-- LAYOUT -->

    <link
        rel="stylesheet"
        href="<?= $escapar($carregarCss('layout.css')) ?>">

    <!-- PORTAL DO ALUNO -->

    <?php if ($role === 'Aluno'): ?>

        <link
            rel="stylesheet"
            href="<?= $escapar($carregarCss('portal.css')) ?>">

        <link
            rel="stylesheet"
            href="<?= $escapar($carregarCss('treinos.css')) ?>">

    <?php endif; ?>

    <!-- CADASTROS -->

    <link
        rel="stylesheet"
        href="<?= $escapar($carregarCss('cadastros.css')) ?>">

    <!-- CSS ESPECÍFICO -->

    <?php if (!empty($cssEspecifico)): ?>

        <link
            rel="stylesheet"
            href="<?= $escapar($cssEspecifico) ?>">

    <?php endif; ?>

    <!-- IDENTIDADE VISUAL DINÂMICA -->

    <style>

        :root {
            --cor-primaria: <?= $corPrimaria ?>;
            --cor-secundaria: <?= $corSecundaria ?>;

            --cor-sobre-primaria: <?= $textoSobrePrimaria ?>;
            --cor-sobre-secundaria: <?= $textoSobreSecundaria ?>;

            --header-fundo: <?= $corFundoHeader ?>;
            --header-texto: <?= $corTextoHeader ?>;
            --header-borda: <?= $corBordaHeader ?>;

            --fin-primary: var(--cor-primaria);
            --fin-primary-dark: var(--cor-primaria-hover);
        }

        body {
            background-color: var(--ui-fundo-pagina);
            color: var(--ui-texto);
        }

        body > .header {
            background-color: var(--header-fundo);
            color: var(--header-texto);
            border-bottom: 1px solid var(--header-borda);
        }

        body > .header h1,
        body > .header .header-direita,
        body > .header .header-direita span,
        body > .header .mobile-toggle {
            color: var(--header-texto);
        }

        body > #appSidebar {
            background-color: var(--cor-secundaria);
            color: var(--cor-sobre-secundaria);
        }

    </style>

</head>

<body>

    <!-- OVERLAY DO MENU MOBILE -->

    <div
        class="mobile-overlay"
        id="mobileOverlay"
        onclick="
            document.getElementById('appSidebar')
                .classList.remove('open');

            this.classList.remove('active');
        ">
    </div>

    <!-- CABEÇALHO -->

    <header class="header">

        <div class="header-esquerda">

            <button
                type="button"
                class="mobile-toggle"
                aria-label="Abrir ou fechar o menu"
                onclick="
                    document.getElementById('appSidebar')
                        .classList.toggle('open');

                    document.getElementById('mobileOverlay')
                        .classList.toggle('active');
                ">
                ☰
            </button>

            <h1>
                <?= $escapar($titulo) ?>
            </h1>

        </div>

        <div class="header-direita">

            <span>
                <?= $escapar($usuario) ?>
                (<?= $escapar($role) ?>)
            </span>

        </div>

    </header>