<?php
// app/views/login/esqueci_senha.php

if (!defined('BASE_URL')) {
    http_response_code(403);
    exit('Acesso não permitido.');
}

require_once __DIR__ .
    '/../../models/IdentidadeAutenticacao.php';

$identidade = gymflowIdentidadeRecuperacao();

$erro = $erro ?? '';
$sucesso = $sucesso ?? '';

$esc = static function ($valor): string {
    return htmlspecialchars(
        (string) $valor,
        ENT_QUOTES,
        'UTF-8'
    );
};

$loginUrl = $identidade['login_url'];

$actionUrl = BASE_URL .
    '/app/controllers/LoginController.php?acao=esqueci_senha';

if ($identidade['empresa_id'] !== null) {
    $actionUrl .= '&empresa=' .
        (int) $identidade['empresa_id'];
}

$cssLogin = __DIR__ .
    '/../../../assets/css/login.css';

$cssRecuperacao = __DIR__ .
    '/../../../assets/css/auth-recovery.css';

$vLogin = is_file($cssLogin)
    ? filemtime($cssLogin)
    : 1;

$vRecuperacao = is_file($cssRecuperacao)
    ? filemtime($cssRecuperacao)
    : 1;
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Recuperar acesso | <?= $esc($identidade['nome']) ?></title>

    <link
        rel="stylesheet"
        href="<?= BASE_URL ?>/assets/css/global.css">

    <link
        rel="stylesheet"
        href="<?= BASE_URL ?>/assets/css/login.css?v=<?= $vLogin ?>">

    <link
        rel="stylesheet"
        href="<?= BASE_URL ?>/assets/css/auth-recovery.css?v=<?= $vRecuperacao ?>">
</head>

<body>

    <main
        class="login-page auth-recovery-page"
        style="
        --portfolio-primary: <?= $esc($identidade['primaria']) ?>;
        --portfolio-secondary: <?= $esc($identidade['secundaria']) ?>;
        --portfolio-on-primary: <?= $esc($identidade['texto_primaria']) ?>;
    ">
        <div class="login-container">

            <!-- IDENTIDADE DO LOGIN -->
            <section class="login-first-column">

                <div class="login-brand">
                    GYM<span>FLOW</span>
                </div>

                <div class="login-left-content">

                    <a
                        href="<?= $esc($loginUrl) ?>"
                        class="login-back-link">
                        <span class="back-arrow">←</span>
                        Voltar para o login
                    </a>

                    <div class="login-title-line"></div>

                    <h1 class="login-title">
                        Recupere seu
                        <span>acesso.</span>
                    </h1>

                    <p class="login-description">
                        Acesse novamente sua conta de forma
                        simples e segura.
                    </p>

                    <div class="login-features">

                        <div class="login-feature">
                            <div class="feature-icon">✓</div>
                            <div>
                                <strong>Segurança</strong>
                                <span>
                                    Link de recuperação temporário
                                </span>
                            </div>
                        </div>

                        <div class="login-feature">
                            <div class="feature-icon">◇</div>
                            <div>
                                <strong>Praticidade</strong>
                                <span>
                                    Redefina sua senha pelo e-mail
                                </span>
                            </div>
                        </div>

                    </div>
                </div>
            </section>

            <!-- FORMULÁRIO -->
            <section class="login-second-column">

                <div
                    class="login-background-glow"
                    aria-hidden="true"></div>

                <div class="login-form-container">

                    <div
                        class="login-card-glow"
                        aria-hidden="true"></div>

                    <div class="form-brand">
                        <?= $esc($identidade['nome']) ?>
                    </div>

                    <header class="login-header">

                        <h2 class="login-form-title">
                            Esqueceu sua senha?
                        </h2>

                        <p class="login-form-description">
                            Informe seu e-mail cadastrado.
                            Enviaremos um link para criar uma
                            nova senha caso exista uma conta
                            associada a ele.
                        </p>

                    </header>

                    <?php if ($erro !== ''): ?>
                        <div
                            class="auth-message auth-message-error"
                            role="alert">
                            <?= $esc($erro) ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($sucesso !== ''): ?>
                        <div
                            class="auth-message auth-message-success"
                            role="status">
                            <?= $esc($sucesso) ?>
                        </div>
                    <?php endif; ?>

                    <form
                        class="login-form"
                        method="POST"
                        action="<?= $esc($actionUrl) ?>">
                        <?php if ($identidade['empresa_id'] !== null): ?>
                            <input
                                type="hidden"
                                name="empresa"
                                value="<?= (int) $identidade['empresa_id'] ?>">
                        <?php endif; ?>

                        <div class="form-group">

                            <label
                                class="form-label"
                                for="email">
                                E-mail
                            </label>

                            <div class="input-wrapper">

                                <input
                                    class="form-input auth-input"
                                    type="email"
                                    id="email"
                                    name="email"
                                    placeholder="Digite seu e-mail cadastrado"
                                    value="<?= $esc($email ?? '') ?>"
                                    autocomplete="email"
                                    maxlength="255"
                                    required>

                            </div>
                        </div>

                        <button
                            class="login-submit-button"
                            type="submit">
                            <span>Enviar link de recuperação</span>
                            <span class="button-arrow">→</span>
                        </button>

                    </form>

                    <div class="login-footer">
                        <a href="<?= $esc($loginUrl) ?>">
                            Voltar para o login
                        </a>
                    </div>

                </div>
            </section>

        </div>
    </main>

</body>

</html>