<?php

$mensagemSalva = ($_GET['msg'] ?? '') === 'salvo';
$configuracao = $configuracao ?? [];
$configuracaoPersonalizada = $configuracaoPersonalizada ?? $configuracao;
$configuracaoLocal = $configuracaoLocal ?? [];

$modoCores = $configuracao['modo_cores_email'] ?? 'personalizado';

if (!in_array($modoCores, ['marca', 'personalizado'], true)) {
    $modoCores = 'personalizado';
}

$usarPersonalizadoAtual = !empty($configuracao['usar_personalizado']);

$coresPadrao = [
    'cor_email_fundo' => '#0b0b0b',
    'cor_email_cartao' => '#151515',
    'cor_email_destaque' => '#ffb000',
    'cor_email_botao' => '#ffb000',
    'cor_email_texto_botao' => '#151515',
];

$coresEmail = [];

foreach ($coresPadrao as $campo => $padrao) {
    $valor = $configuracao[$campo] ?? $padrao;

    $coresEmail[$campo] = (
        is_string($valor) &&
        preg_match('/^#[0-9a-fA-F]{6}$/', $valor)
    ) ? strtolower($valor) : $padrao;
}

$escapar = static function ($valor): string {
    if (!is_string($valor) && !is_numeric($valor)) {
        $valor = '';
    }

    return htmlspecialchars(
        (string) $valor,
        ENT_QUOTES,
        'UTF-8'
    );
};

$valorSMTP = static function (
    string $campo,
    $padrao = ''
) use (
    $configuracao,
    $configuracaoPersonalizada,
    $configuracaoLocal
) {
    $local = $configuracaoLocal[$campo] ?? $padrao;
    $personalizado = $configuracaoPersonalizada[$campo] ?? $padrao;

    return [
        'padrao' => $local,
        'personalizado' => $personalizado,
        'atual' => $configuracao[$campo] ?? $local,
    ];
};

include __DIR__ . '/../shared/header.php';
include __DIR__ . '/../shared/sidebar.php';

?>

<style>

/* ==========================================
   GYMFLOW - CONFIGURAÇÕES DE E-MAIL
========================================== */

/* 1. ESTRUTURA DA PÁGINA */

.config-email-page {
    width: 100%;
    min-width: 0;
    max-width: 1200px;
    margin: 0 auto;
    padding: 30px;
    color: var(--ui-texto);
}

/* 2. CABEÇALHO */

.config-email-page .config-email-topo {
    display: flex;
    align-items: center;
    gap: 16px;
    margin-bottom: 26px;
}

.config-email-page .config-email-icone {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 54px;
    height: 54px;
    border-radius: 14px;
    background-color: var(--cor-primaria-suave);
    color: var(--cor-primaria);
    font-size: 26px;
    flex-shrink: 0;
}

.config-email-page .config-email-topo h1 {
    margin: 0 0 6px;
    color: var(--ui-texto);
    font-size: 26px;
    font-weight: 700;
}

.config-email-page .config-email-topo p {
    margin: 0;
    color: var(--ui-texto-suave);
}

/* 3. CARDS */

.config-email-page .config-email-card {
    padding: 28px;
    margin-bottom: 22px;
    background-color: var(--ui-fundo-card);
    color: var(--ui-texto);
    border: 1px solid var(--ui-borda);
    border-radius: 14px;
    box-shadow: var(--ui-sombra-card);
}

.config-email-page .config-email-card h2 {
    margin: 0 0 8px;
    color: var(--ui-texto);
    font-size: 18px;
    font-weight: 700;
}

.config-email-page .config-email-descricao {
    margin-bottom: 24px;
    color: var(--ui-texto-suave);
    line-height: 1.6;
}

/* 4. ATIVAÇÃO DO SMTP */

.config-email-page .config-email-ativacao {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 16px;
    margin-bottom: 26px;
    background-color: var(--cor-primaria-suave);
    border: 1px solid var(--cor-primaria-borda);
    border-radius: var(--ui-raio-md);
}

.config-email-page .config-email-ativacao input {
    width: 18px;
    height: 18px;
    margin: 0;
    flex-shrink: 0;
    accent-color: var(--cor-primaria);
    cursor: pointer;
}

.config-email-page .config-email-ativacao label {
    margin: 0;
    color: var(--ui-texto);
    font-weight: 600;
    cursor: pointer;
}

/* 5. FORMULÁRIO */

.config-email-page .config-email-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 20px;
}

.config-email-page .config-email-campo {
    min-width: 0;
}

.config-email-page .config-email-campo label {
    display: block;
    margin-bottom: 8px;
    color: var(--ui-texto);
    font-weight: 600;
    font-size: 14px;
}

/* 6. INPUTS E SELECTS */

.config-email-page .config-email-campo input,
.config-email-page .config-email-campo select {
    display: block;
    width: 100%;
    min-width: 0;
    min-height: 44px;
    padding: 10px 12px;
    background-color: var(--ui-input-fundo);
    color: var(--ui-input-texto);
    border: 1px solid var(--ui-input-borda);
    border-radius: 8px;
    font: inherit;
    transition:
        border-color var(--ui-transicao),
        box-shadow var(--ui-transicao);
}

.config-email-page .config-email-campo input::placeholder {
    color: var(--ui-texto-suave);
}

.config-email-page .config-email-campo select option {
    background-color: var(--ui-input-fundo);
    color: var(--ui-input-texto);
}

.config-email-page .config-email-campo input:hover:not(:disabled),
.config-email-page .config-email-campo select:hover:not(:disabled) {
    border-color: var(--cor-borda-forte);
}

.config-email-page .config-email-campo input:focus,
.config-email-page .config-email-campo select:focus {
    border-color: var(--ui-input-foco);
    outline: 2px solid var(--ui-input-foco);
    outline-offset: 1px;
}

/* 7. SELETORES DE CORES */

.config-email-page .config-email-campo input[type="color"] {
    height: 48px;
    padding: 4px;
    cursor: pointer;
}

/* 8. CAMPOS DESABILITADOS */

.config-email-page .config-email-campo input:disabled,
.config-email-page .config-email-campo select:disabled {
    background-color: var(--cor-superficie-secundaria);
    color: var(--ui-texto-suave);
    border-color: var(--ui-borda);
    cursor: not-allowed;
    opacity: 1;
}

/* 9. TEXTOS AUXILIARES */

.config-email-page .config-email-ajuda {
    display: block;
    margin-top: 6px;
    color: var(--ui-texto-suave);
    font-size: 12px;
    line-height: 1.5;
}

.config-email-page .config-email-separador {
    margin: 28px 0 22px;
    border: 0;
    border-top: 1px solid var(--ui-borda);
}

.config-email-page .config-email-nota {
    padding: 14px 16px;
    margin: 18px 0 0;
    background-color: var(--ui-fundo-pagina);
    color: var(--ui-texto-suave);
    border: 1px solid var(--ui-borda);
    border-radius: 8px;
    font-size: 14px;
    line-height: 1.6;
}

/* 10. BOTÃO SALVAR */

.config-email-page .config-email-acoes {
    display: flex;
    justify-content: flex-end;
    margin-top: 28px;
    padding-top: 22px;
    border-top: 1px solid var(--ui-borda);
}

.config-email-page .config-email-salvar {
    min-height: 44px;
    padding: 12px 22px;
    background-color: var(--ui-botao-fundo);
    color: var(--ui-botao-texto);
    border: none;
    border-radius: 8px;
    font-family: inherit;
    font-weight: 600;
    cursor: pointer;
    transition: background-color var(--ui-transicao);
}

.config-email-page .config-email-salvar:hover {
    background-color: var(--ui-botao-fundo-hover);
    color: var(--ui-botao-texto);
}

.config-email-page .config-email-salvar:focus-visible {
    outline: 2px solid var(--cor-primaria);
    outline-offset: 3px;
}

/* 11. MENSAGEM DE SUCESSO */

.config-email-page .config-email-sucesso {
    padding: 14px 18px;
    margin-bottom: 22px;
    background-color: var(--cor-sucesso-fundo);
    color: #047857;
    border: 1px solid #a7f3d0;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
}

:root[data-theme="dark"]
.config-email-page .config-email-sucesso {
    background-color: #15372a;
    color: #86efac;
    border-color: #286344;
}

/* 12. ELEMENTOS OCULTOS */

.config-email-page [hidden] {
    display: none !important;
}

/* 13. RESPONSIVIDADE */

@media (max-width: 700px) {

    .config-email-page {
        padding: 18px;
    }

    .config-email-page .config-email-card {
        padding: 20px;
    }

    .config-email-page .config-email-grid {
        grid-template-columns: 1fr;
        gap: 16px;
    }

    .config-email-page .config-email-topo h1 {
        font-size: 22px;
    }

    .config-email-page .config-email-acoes {
        justify-content: stretch;
    }

    .config-email-page .config-email-salvar {
        width: 100%;
    }
}

</style>

<main class="conteudo config-email-page">

    <div class="config-email-topo">

        <div class="config-email-icone" aria-hidden="true">
            ✉
        </div>

        <div>
            <h1>Configurações de e-mail</h1>
            <p>Configure o envio e a aparência dos e-mails do GymFlow.</p>
        </div>

    </div>

    <?php if ($mensagemSalva): ?>

        <div class="config-email-sucesso" role="status">
            Configurações salvas com sucesso!
        </div>

    <?php endif; ?>

    <form
        method="POST"
        action="<?= $escapar(BASE_URL) ?>/app/controllers/ConfiguracaoEmailController.php?acao=salvar">

        <input
            type="hidden"
            name="csrf_token"
            value="<?= $escapar($_SESSION['csrf_config_email'] ?? '') ?>">

        <!-- SERVIDOR SMTP -->

        <section class="config-email-card">

            <h2>Servidor de e-mail</h2>

            <p class="config-email-descricao">
                Configure o remetente e os dados do servidor SMTP da sua empresa.
            </p>

            <div class="config-email-ativacao">

                <input
                    type="checkbox"
                    id="usar_personalizado"
                    name="usar_personalizado"
                    value="1"
                    <?= $usarPersonalizadoAtual ? 'checked' : '' ?>>

                <label for="usar_personalizado">
                    Usar servidor de e-mail personalizado
                </label>

            </div>

            <div class="config-email-grid">

                <?php

                $camposTexto = [
                    [
                        'id' => 'email_remetente',
                        'label' => 'E-mail remetente',
                        'placeholder' => 'exemplo@empresa.com',
                        'type' => 'email',
                        'maxlength' => 255,
                        'padrao' => $valorSMTP('email_remetente', ''),
                    ],
                    [
                        'id' => 'nome_remetente',
                        'label' => 'Nome do remetente',
                        'placeholder' => 'Ex.: GymFlow',
                        'type' => 'text',
                        'maxlength' => 100,
                        'padrao' => $valorSMTP('nome_remetente', 'GymFlow'),
                    ],
                    [
                        'id' => 'smtp_host',
                        'label' => 'Servidor SMTP',
                        'placeholder' => 'smtp.gmail.com',
                        'type' => 'text',
                        'maxlength' => 255,
                        'padrao' => $valorSMTP('smtp_host', ''),
                    ],
                    [
                        'id' => 'smtp_usuario',
                        'label' => 'Usuário SMTP',
                        'placeholder' => 'Usuário de autenticação',
                        'type' => 'text',
                        'maxlength' => 255,
                        'padrao' => $valorSMTP('smtp_usuario', ''),
                    ],
                ];

                ?>

                <?php foreach ($camposTexto as $campo): ?>

                    <div class="config-email-campo">

                        <label for="<?= $escapar($campo['id']) ?>">
                            <?= $escapar($campo['label']) ?>
                        </label>

                        <input
                            type="<?= $escapar($campo['type']) ?>"
                            id="<?= $escapar($campo['id']) ?>"
                            name="<?= $escapar($campo['id']) ?>"
                            maxlength="<?= (int) $campo['maxlength'] ?>"
                            value="<?= $escapar($campo['padrao']['atual']) ?>"
                            placeholder="<?= $escapar($campo['placeholder']) ?>"
                            data-smtp-field
                            data-smtp-padrao="<?= $escapar($campo['padrao']['padrao']) ?>"
                            data-smtp-personalizado="<?= $escapar($campo['padrao']['personalizado']) ?>"
                            <?= $usarPersonalizadoAtual ? 'required' : 'disabled' ?>>

                    </div>

                <?php endforeach; ?>

                <!-- PORTA SMTP -->

                <?php $portaSMTP = $valorSMTP('smtp_porta', 587); ?>

                <div class="config-email-campo">

                    <label for="smtp_porta">
                        Porta SMTP
                    </label>

                    <input
                        type="number"
                        id="smtp_porta"
                        name="smtp_porta"
                        min="1"
                        max="65535"
                        value="<?= $escapar($portaSMTP['atual']) ?>"
                        data-smtp-field
                        data-smtp-padrao="<?= $escapar($portaSMTP['padrao']) ?>"
                        data-smtp-personalizado="<?= $escapar($portaSMTP['personalizado']) ?>"
                        <?= $usarPersonalizadoAtual ? 'required' : 'disabled' ?>>

                </div>

                <!-- SEGURANÇA SMTP -->

                <?php $segurancaSMTP = $valorSMTP('smtp_seguranca', 'tls'); ?>

                <div class="config-email-campo">

                    <label for="smtp_seguranca">
                        Segurança da conexão
                    </label>

                    <select
                        id="smtp_seguranca"
                        name="smtp_seguranca"
                        data-smtp-field
                        data-smtp-padrao="<?= $escapar($segurancaSMTP['padrao']) ?>"
                        data-smtp-personalizado="<?= $escapar($segurancaSMTP['personalizado']) ?>"
                        <?= $usarPersonalizadoAtual ? 'required' : 'disabled' ?>>

                        <?php foreach ([
                            'tls' => 'TLS / STARTTLS',
                            'ssl' => 'SSL',
                            'none' => 'Sem criptografia',
                        ] as $valor => $rotulo): ?>

                            <option
                                value="<?= $escapar($valor) ?>"
                                <?= $segurancaSMTP['atual'] === $valor ? 'selected' : '' ?>>

                                <?= $escapar($rotulo) ?>

                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>

                <!-- SENHA SMTP -->

                <div class="config-email-campo">

                    <label for="smtp_senha">
                        Senha SMTP
                    </label>

                    <input
                        type="password"
                        id="smtp_senha"
                        name="smtp_senha"
                        maxlength="1024"
                        autocomplete="new-password"
                        placeholder="Deixe em branco para manter a senha salva"
                        data-smtp-field
                        <?= $usarPersonalizadoAtual ? '' : 'disabled' ?>>

                    <small class="config-email-ajuda">
                        Por segurança, a senha salva não será exibida aqui.
                        Preencha somente para cadastrar ou trocar a senha.
                    </small>

                </div>

            </div>

            <p class="config-email-nota">
                Ao desativar o SMTP personalizado, os campos mostram os dados
                padrão do GymFlow. Suas configurações personalizadas salvas
                permanecem no banco de dados.
            </p>

        </section>

        <!-- APARÊNCIA DOS E-MAILS -->

        <section class="config-email-card">

            <h2>Aparência dos e-mails</h2>

            <p class="config-email-descricao">
                Escolha se os e-mails de recuperação de senha vão acompanhar
                as cores da sua marca ou terão uma identidade visual própria.
            </p>

            <div class="config-email-grid">

                <div class="config-email-campo">

                    <label for="modo_cores_email">
                        Modo de cores
                    </label>

                    <select
                        id="modo_cores_email"
                        name="modo_cores_email">

                        <option
                            value="marca"
                            <?= $modoCores === 'marca' ? 'selected' : '' ?>>

                            Usar cores da minha marca

                        </option>

                        <option
                            value="personalizado"
                            <?= $modoCores === 'personalizado' ? 'selected' : '' ?>>

                            Personalizar cores do e-mail

                        </option>

                    </select>

                </div>

            </div>

            <div
                id="cores_email_personalizadas"
                <?= $modoCores === 'personalizado' ? '' : 'hidden' ?>>

                <hr class="config-email-separador">

                <div class="config-email-grid">

                    <?php

                    $camposCores = [
                        'cor_email_fundo' => 'Fundo do e-mail',
                        'cor_email_cartao' => 'Fundo do cartão',
                        'cor_email_destaque' => 'Cor de destaque',
                        'cor_email_botao' => 'Fundo do botão',
                        'cor_email_texto_botao' => 'Texto do botão',
                    ];

                    ?>

                    <?php foreach ($camposCores as $campo => $rotulo): ?>

                        <div class="config-email-campo">

                            <label for="<?= $escapar($campo) ?>">
                                <?= $escapar($rotulo) ?>
                            </label>

                            <input
                                type="color"
                                id="<?= $escapar($campo) ?>"
                                name="<?= $escapar($campo) ?>"
                                value="<?= $escapar($coresEmail[$campo]) ?>">

                        </div>

                    <?php endforeach; ?>

                </div>

            </div>

            <p class="config-email-nota" id="nota_modo_cores">

                <?php if ($modoCores === 'marca'): ?>

                    O modo marca está selecionado.
                    A aplicação das cores da marca será conectada
                    ao envio do e-mail na próxima etapa.

                <?php else: ?>

                    Você pode escolher cores exclusivas para os e-mails.
                    Elas não devem alterar o portfólio público.

                <?php endif; ?>

            </p>

        </section>

        <!-- AÇÕES -->

        <div class="config-email-acoes">

            <button
                type="submit"
                class="config-email-salvar">

                Salvar configurações

            </button>

        </div>

    </form>

</main>

<script>
(function () {

    const checkboxSMTP =
        document.getElementById('usar_personalizado');

    const camposSMTP =
        document.querySelectorAll('[data-smtp-field]');

    const seletorModo =
        document.getElementById('modo_cores_email');

    const camposPersonalizados =
        document.getElementById('cores_email_personalizadas');

    const nota =
        document.getElementById('nota_modo_cores');

    let modoSMTPAtual =
        checkboxSMTP && checkboxSMTP.checked
            ? 'personalizado'
            : 'padrao';

    const valoresSMTP = new WeakMap();

    camposSMTP.forEach(function (campo) {

        if (campo.id === 'smtp_senha') {
            return;
        }

        valoresSMTP.set(campo, {
            padrao: campo.dataset.smtpPadrao ?? '',
            personalizado: campo.dataset.smtpPersonalizado ?? ''
        });

    });

    function atualizarCamposSMTP() {

        if (!checkboxSMTP) {
            return;
        }

        const novoModo = checkboxSMTP.checked
            ? 'personalizado'
            : 'padrao';

        camposSMTP.forEach(function (campo) {

            if (campo.id === 'smtp_senha') {

                campo.disabled =
                    novoModo !== 'personalizado';

                campo.required = false;
                campo.value = '';

                return;
            }

            const valores = valoresSMTP.get(campo);

            if (novoModo !== modoSMTPAtual && valores) {

                valores[modoSMTPAtual] = campo.value;

                campo.value = valores[novoModo];

            }

            campo.disabled =
                novoModo !== 'personalizado';

            campo.required =
                novoModo === 'personalizado';

        });

        modoSMTPAtual = novoModo;

    }

    function atualizarModoCores() {

        if (!seletorModo || !camposPersonalizados || !nota) {
            return;
        }

        const usaMarca =
            seletorModo.value === 'marca';

        camposPersonalizados.hidden = usaMarca;

        nota.textContent = usaMarca

            ? 'O modo marca está selecionado. A aplicação das cores da marca será conectada ao envio do e-mail na próxima etapa.'

            : 'Você pode escolher cores exclusivas para os e-mails. Elas não devem alterar o portfólio público.';

    }

    if (checkboxSMTP) {

        checkboxSMTP.addEventListener(
            'change',
            atualizarCamposSMTP
        );

        atualizarCamposSMTP();

    }

    if (seletorModo) {

        seletorModo.addEventListener(
            'change',
            atualizarModoCores
        );

        atualizarModoCores();

    }

})();
</script>

<?php include __DIR__ . '/../shared/footer.php'; ?>