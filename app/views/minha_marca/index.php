<?php
if (!isset($tituloPagina)) {
    header('Location: ' . BASE_URL . '/app/controllers/MinhaMarcaController.php');
    exit;
}

$preferencias = $preferencias ?? [];

$nome = $preferencias['nome_painel'] ?? 'Gymflow';
$tema = $preferencias['tema'] ?? 'dark';
$primaria = $preferencias['cor_primaria'] ?? '#ffb000';
$secundaria = $preferencias['cor_secundaria'] ?? '#000000';
$preset = $preferencias['tema_predefinido'] ?? 'padrao';
$logo = $preferencias['logo_url'] ?? '';

$esc = static function ($valor) {
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
};

if (!preg_match('/^#[0-9a-fA-F]{6}$/', $primaria)) {
    $primaria = '#ffb000';
}

if (!preg_match('/^#[0-9a-fA-F]{6}$/', $secundaria)) {
    $secundaria = '#000000';
}

$temLogo = !empty($logo);

$arquivoCss = dirname(__DIR__, 3) . '/assets/css/minha-marca.css';

$versaoCss = is_file($arquivoCss)
    ? filemtime($arquivoCss)
    : '4';

$cssEspecifico = BASE_URL . '/assets/css/minha-marca.css?v=' . $versaoCss;

include __DIR__ . '/../shared/header.php';
include __DIR__ . '/../shared/sidebar.php';
?>

<link rel="stylesheet" href="<?= $esc($cssEspecifico) ?>">

<main class="conteudo minha-marca-page">

    <!-- Cabeçalho da página -->
    <div class="mm-topo">

        <div>
            <h1>Minha Marca</h1>
            <p>Personalize a aparência do seu painel.</p>
        </div>

    </div>

    <!-- Mensagem de confirmação -->
    <?php if (($_GET['msg'] ?? '') === 'salvo'): ?>

        <div class="mm-alert" role="status">
            Suas preferências foram salvas com sucesso.
        </div>

    <?php endif; ?>


    <div class="mm-grid">

        <!-- FORMULÁRIO -->

        <form
            class="mm-form"
            id="formMinhaMarca"
            method="POST"
            enctype="multipart/form-data"
            action="<?= BASE_URL ?>/app/controllers/MinhaMarcaController.php?acao=salvar">

            <input
                type="hidden"
                name="csrf_token"
                value="<?= $esc($_SESSION['minha_marca_csrf'] ?? '') ?>">


            <!-- IDENTIDADE DO PAINEL -->

            <section class="mm-card">

                <div class="mm-card-top">
                    <h3>Identidade do painel</h3>
                </div>

                <label for="nome_painel">
                    Nome do painel
                </label>

                <input
                    type="text"
                    id="nome_painel"
                    name="nome_painel"
                    maxlength="100"
                    required
                    value="<?= $esc($nome) ?>"
                    placeholder="Ex.: Minha Academia">

            </section>


            <!-- APARÊNCIA -->

            <section class="mm-card">

                <div class="mm-card-top">
                    <h3>Aparência</h3>
                </div>

                <div class="mm-temas">

                    <!-- Tema claro -->

                    <label class="mm-opcao">

                        <input
                            type="radio"
                            name="tema"
                            value="light"
                            <?= $tema === 'light' ? 'checked' : '' ?>>

                        <span class="mm-opcao-cor mm-claro"></span>

                        <strong>Tema claro</strong>

                    </label>


                    <!-- Tema escuro -->

                    <label class="mm-opcao">

                        <input
                            type="radio"
                            name="tema"
                            value="dark"
                            <?= $tema !== 'light' ? 'checked' : '' ?>>

                        <span class="mm-opcao-cor mm-escuro"></span>

                        <strong>Tema escuro</strong>

                    </label>

                </div>

            </section>


            <!-- PALETA DE CORES -->

            <section class="mm-card">

                <div class="mm-card-top">
                    <h3>Paleta de cores</h3>
                </div>

                <label for="tema_predefinido">
                    Combinação predefinida
                </label>

                <select
                    id="tema_predefinido"
                    name="tema_predefinido">

                    <?php
                    $opcoes = [
                        'padrao' => 'GymFlow padrão',
                        'dark' => 'Violeta',
                        'forest' => 'Forest',
                        'sunset' => 'Sunset',
                        'ocean' => 'Ocean',
                        'personalizado' => 'Personalizado'
                    ];

                    foreach ($opcoes as $valor => $rotulo):
                    ?>

                        <option
                            value="<?= $esc($valor) ?>"
                            <?= $preset === $valor ? 'selected' : '' ?>>

                            <?= $esc($rotulo) ?>

                        </option>

                    <?php endforeach; ?>

                </select>


                <div class="mm-cores">

                    <!-- Cor primária -->

                    <div>

                        <label for="cor_primaria">
                            Cor primária
                        </label>

                        <div class="mm-cor-input">

                            <input
                                type="color"
                                id="cor_primaria"
                                name="cor_primaria"
                                value="<?= $esc($primaria) ?>">

                            <output id="codigoPrimaria">
                                <?= $esc($primaria) ?>
                            </output>

                        </div>

                    </div>


                    <!-- Cor secundária -->

                    <div>

                        <label for="cor_secundaria">
                            Cor secundária
                        </label>

                        <div class="mm-cor-input">

                            <input
                                type="color"
                                id="cor_secundaria"
                                name="cor_secundaria"
                                value="<?= $esc($secundaria) ?>">

                            <output id="codigoSecundaria">
                                <?= $esc($secundaria) ?>
                            </output>

                        </div>

                    </div>

                </div>

            </section>


            <!-- LOGOTIPO -->

            <section class="mm-card">

                <div class="mm-card-top">
                    <h3>Logotipo</h3>
                </div>

                <div class="mm-logo-area">

                    <div class="mm-logo-imagem">

                        <img
                            id="mmLogoImagem"
                            src="<?= $temLogo ? $esc($logo) : '' ?>"
                            alt="Prévia do logotipo"
                            <?= $temLogo ? '' : 'hidden' ?>>

                        <span
                            id="mmLogoPlaceholder"
                            <?= $temLogo ? 'hidden' : '' ?>>

                            Sem imagem

                        </span>

                    </div>


                    <div class="mm-logo-detalhes">

                        <label
                            class="mm-logo-upload"
                            for="logotipo">

                            Escolher imagem

                        </label>

                        <input
                            type="file"
                            name="logotipo"
                            id="logotipo"
                            accept="image/png,image/jpeg,image/webp"
                            class="mm-logo-file">

                        <p id="mmLogoNome">

                            <?= $temLogo
                                ? 'Logotipo atual'
                                : 'Nenhuma imagem selecionada' ?>

                        </p>

                        <small>
                            PNG, JPG ou WEBP · Até 2 MB
                        </small>

                    </div>

                </div>


                <label class="mm-logo-remover">

                    <input
                        type="checkbox"
                        name="remover_logo"
                        id="remover_logo"
                        value="1"
                        <?= $temLogo ? '' : 'disabled' ?>>

                    Remover logotipo atual

                </label>

            </section>


            <!-- SALVAR ALTERAÇÕES -->

            <button
                class="mm-salvar"
                type="submit">

                Salvar alterações

            </button>

        </form>


        <!-- PRÉ-VISUALIZAÇÃO -->

        <aside class="mm-lateral">

            <div class="mm-card mm-preview-card">

                <div class="mm-preview-topo">

                    <h3>Pré-visualização</h3>

                </div>


                <div id="mmPreview" class="mm-preview">

                    <!-- Menu lateral da prévia -->

                    <div class="mm-preview-menu">

                        <div
                            class="mm-preview-logo"
                            id="mmNomePreview">

                            <?= $esc($nome) ?>

                        </div>

                        <div class="mm-preview-item ativo">
                            Visão geral
                        </div>

                        <div class="mm-preview-item">
                            Alunos
                        </div>

                        <div class="mm-preview-item">
                            Treinos
                        </div>

                        <div class="mm-preview-item">
                            Financeiro
                        </div>

                    </div>


                    <!-- Conteúdo da prévia -->

                    <div class="mm-preview-painel">

                        <div class="mm-preview-header">

                            <span>Dashboard</span>

                        </div>

                        <div class="mm-preview-body">

                            <h4>Visão geral</h4>

                            <div class="mm-preview-stat">

                                <small>Alunos ativos</small>

                                <strong>128</strong>

                            </div>

                            <div class="mm-preview-botao">
                                Nova matrícula
                            </div>

                        </div>

                    </div>

                </div>

                <p class="mm-nota">
                    As alterações serão aplicadas ao salvar.
                </p>

            </div>

        </aside>

    </div>

</main>


<!-- JAVASCRIPT COMPLETO -->

<script>
    (() => {
        'use strict';

        // ELEMENTOS DO FORMULÁRIO
        const nome = document.getElementById('nome_painel');
        const primaria = document.getElementById('cor_primaria');
        const secundaria = document.getElementById('cor_secundaria');
        const preset = document.getElementById('tema_predefinido');
        const preview = document.getElementById('mmPreview');

        // ELEMENTOS DO LOGOTIPO
        const inputLogo = document.getElementById('logotipo');
        const imagemLogo = document.getElementById('mmLogoImagem');
        const placeholder = document.getElementById('mmLogoPlaceholder');
        const nomeArquivo = document.getElementById('mmLogoNome');
        const removerLogo = document.getElementById('remover_logo');

        const logoOriginal = imagemLogo.getAttribute('src') || '';
        let urlTemporaria = null;

        const temas = {
            padrao: ['#ffb000', '#000000', 'dark'],
            dark: ['#8b5cf6', '#111827', 'dark'],
            forest: ['#22c55e', '#14532d', 'dark'],
            sunset: ['#f97316', '#7c2d12', 'dark'],
            ocean: ['#0ea5e9', '#0c4a6e', 'dark']
        };

        // CONTRASTE DAS CORES
        function textoContrastante(hex) {
            const rgb = [1, 3, 5].map(i =>
                parseInt(hex.substring(i, i + 2), 16) / 255
            );

            const linear = rgb.map(v =>
                v <= 0.04045 ?
                v / 12.92 :
                Math.pow((v + 0.055) / 1.055, 2.4)
            );

            const lum =
                linear[0] * 0.2126 +
                linear[1] * 0.7152 +
                linear[2] * 0.0722;

            return lum > 0.179 ? '#111827' : '#ffffff';
        }

        // ATUALIZAÇÃO DA PRÉVIA
        function atualizarPreview() {
            const modo = document.querySelector(
                'input[name="tema"]:checked'
            )?.value || 'dark';

            preview.dataset.theme = modo;

            preview.style.setProperty(
                '--mm-cor1',
                primaria.value
            );

            preview.style.setProperty(
                '--mm-cor2',
                secundaria.value
            );

            preview.style.setProperty(
                '--mm-texto1',
                textoContrastante(primaria.value)
            );

            preview.style.setProperty(
                '--mm-texto2',
                textoContrastante(secundaria.value)
            );

            document.getElementById('mmNomePreview').textContent =
                nome.value.trim() || 'Gymflow';

            document.getElementById('codigoPrimaria').textContent =
                primaria.value.toUpperCase();

            document.getElementById('codigoSecundaria').textContent =
                secundaria.value.toUpperCase();
        }

        // TEMAS PREDEFINIDOS
        preset.addEventListener('change', () => {
            const escolhido = temas[preset.value];

            if (escolhido) {
                primaria.value = escolhido[0];
                secundaria.value = escolhido[1];

                document.querySelector(
                    'input[name="tema"][value="' +
                    escolhido[2] + '"]'
                ).checked = true;
            }

            atualizarPreview();
        });

        // CORES PERSONALIZADAS
        [primaria, secundaria].forEach(campo => {
            campo.addEventListener('input', () => {
                preset.value = 'personalizado';
                atualizarPreview();
            });
        });

        nome.addEventListener('input', atualizarPreview);

        document.querySelectorAll(
            'input[name="tema"]'
        ).forEach(campo => {
            campo.addEventListener('change', atualizarPreview);
        });

        // FUNÇÕES DO LOGOTIPO
        function liberarUrl() {
            if (urlTemporaria) {
                URL.revokeObjectURL(urlTemporaria);
                urlTemporaria = null;
            }
        }

        function mostrarLogo(src) {
            imagemLogo.src = src;
            imagemLogo.hidden = false;
            placeholder.hidden = true;
        }

        function ocultarLogo() {
            imagemLogo.removeAttribute('src');
            imagemLogo.hidden = true;
            placeholder.hidden = false;
        }

        // SELECIONAR IMAGEM
        inputLogo.addEventListener('change', () => {
            liberarUrl();

            const arquivo = inputLogo.files[0];

            if (!arquivo) {
                if (logoOriginal) {
                    mostrarLogo(logoOriginal);
                    nomeArquivo.textContent = 'Logotipo atual';
                } else {
                    ocultarLogo();
                    nomeArquivo.textContent =
                        'Nenhuma imagem selecionada';
                }
                return;
            }

            const formatos = [
                'image/png',
                'image/jpeg',
                'image/webp'
            ];

            if (
                !formatos.includes(arquivo.type) ||
                arquivo.size === 0 ||
                arquivo.size > 2 * 1024 * 1024
            ) {
                inputLogo.value = '';

                if (logoOriginal) {
                    mostrarLogo(logoOriginal);
                } else {
                    ocultarLogo();
                }

                alert(
                    'Escolha uma imagem PNG, JPG ou WEBP ' +
                    'de até 2 MB.'
                );
                return;
            }

            removerLogo.checked = false;
            removerLogo.disabled = false;

            urlTemporaria = URL.createObjectURL(arquivo);

            mostrarLogo(urlTemporaria);

            nomeArquivo.textContent = arquivo.name;
        });

        // REMOVER LOGOTIPO
        removerLogo.addEventListener('change', () => {
            if (removerLogo.checked) {
                inputLogo.value = '';
                liberarUrl();
                ocultarLogo();

                nomeArquivo.textContent =
                    'Logotipo será removido ao salvar';

            } else if (logoOriginal) {
                mostrarLogo(logoOriginal);
                nomeArquivo.textContent = 'Logotipo atual';

            } else {
                ocultarLogo();
                nomeArquivo.textContent =
                    'Nenhuma imagem selecionada';
            }
        });

        // LIBERA MEMÓRIA UTILIZADA PELA PRÉVIA
        window.addEventListener('pagehide', liberarUrl);

        // INICIALIZAÇÃO
        atualizarPreview();

    })();
</script>

<?php include __DIR__ . '/../shared/footer.php'; ?>