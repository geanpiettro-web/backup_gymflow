<?php
// app/views/login/redefinir_senha.php

if (!defined('BASE_URL')) {
    http_response_code(403);
    exit('Acesso não permitido.');
}

require_once __DIR__ .
    '/../../models/IdentidadeAutenticacao.php';

$erro = $erro ?? '';
$sucesso = $sucesso ?? '';
$token = (string) ($token ?? '');

$identidade = gymflowIdentidadeRecuperacao($token);

$esc = static function ($valor): string {
    return htmlspecialchars(
        (string) $valor,
        ENT_QUOTES,
        'UTF-8'
    );
};

$loginUrl = $identidade['login_url'];

$actionUrl = BASE_URL .
    '/app/controllers/LoginController.php?acao=redefinir_senha' .
    '&token=' . rawurlencode($token);

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

    <title>Redefinir senha | <?= $esc($identidade['nome']) ?></title>

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

            <!-- IDENTIDADE VISUAL -->
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
                        Um novo começo
                        <span>para seu acesso.</span>
                    </h1>

                    <p class="login-description">
                        Defina uma nova senha para continuar
                        utilizando os serviços da sua academia.
                    </p>

                    <div class="login-features">

                        <div class="login-feature">
                            <div class="feature-icon">✓</div>
                            <div>
                                <strong>Proteção</strong>
                                <span>
                                    Redefinição por link temporário
                                </span>
                            </div>
                        </div>

                        <div class="login-feature">
                            <div class="feature-icon">◇</div>
                            <div>
                                <strong>Acesso</strong>
                                <span>
                                    Continue de onde parou
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
                            Criar nova senha
                        </h2>

                        <p class="login-form-description">
                            Escolha uma nova senha para acessar
                            sua conta com segurança.
                        </p>

                    </header>

                    <?php if ($sucesso !== ''): ?>

                        <div
                            class="auth-message auth-message-success"
                            role="status">
                            Sua senha foi redefinida com sucesso!
                            Você já pode entrar na sua conta.
                        </div>

                        <div class="auth-success-action">
                            <a
                                href="<?= $esc($loginUrl) ?>"
                                class="login-submit-button">
                                <span>Ir para o login</span>
                                <span class="button-arrow">→</span>
                            </a>
                        </div>

                    <?php else: ?>

                        <?php if ($erro !== ''): ?>
                            <div
                                class="auth-message auth-message-error"
                                role="alert">
                                <?= $esc($erro) ?>
                            </div>
                        <?php endif; ?>

                        <form
                            method="POST"
                            action="<?= $esc($actionUrl) ?>"
                            class="login-form">
                            <div class="form-group">

                                <label
                                    for="nova_senha"
                                    class="form-label">
                                    Nova senha
                                </label>

                                <div class="input-wrapper">

                                    <input
                                        type="password"
                                        id="nova_senha"
                                        name="nova_senha"
                                        class="form-input auth-input"
                                        placeholder="Digite sua nova senha"
                                        autocomplete="new-password"
                                        minlength="6"
                                        required>

                                </div>

                                <p class="auth-hint">
                                    Utilize pelo menos 6 caracteres.
                                </p>

                            </div>

                            <button
                                type="submit"
                                class="login-submit-button">
                                <span>Salvar nova senha</span>
                                <span class="button-arrow">→</span>
                            </button>

                        </form>

                    <?php endif; ?>

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