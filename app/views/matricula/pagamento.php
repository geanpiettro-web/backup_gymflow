<?php
// app/views/matricula/pagamento.php

/** @var array $plano */
/** @var array $filial */
/** @var array $matriculaTemp */
/** @var string $publicKeyMP */
/** @var bool $isConfiguredMP */
/** @var string $erro */
/** @var array $config */
/** @var int $company_id */

$tituloPagina = "Forma de Pagamento";
$cssEspecifico = BASE_URL . '/assets/css/matricula.css';
include __DIR__ . '/../shared/portfolio_header.php';
?>

<main class="matricula-wrapper">
    <div class="matricula-container">

        <!-- STEPPER DE PROGRESSO (ETAPA 3 ATIVA) -->
        <nav class="matricula-stepper-container" aria-label="Progresso da matrícula">
            <ol class="matricula-stepper">
                <li class="matricula-step completed">
                    <div class="step-circle" aria-hidden="true">✓</div>
                    <div class="step-info">
                        <span class="step-number">Etapa 1</span>
                        <span class="step-name">Identificação</span>
                    </div>
                    <div class="step-line" aria-hidden="true"></div>
                </li>
                <li class="matricula-step completed">
                    <div class="step-circle" aria-hidden="true">✓</div>
                    <div class="step-info">
                        <span class="step-number">Etapa 2</span>
                        <span class="step-name">Unidade</span>
                    </div>
                    <div class="step-line" aria-hidden="true"></div>
                </li>
                <li class="matricula-step active" aria-current="step">
                    <div class="step-circle" aria-hidden="true">3</div>
                    <div class="step-info">
                        <span class="step-number">Etapa 3</span>
                        <span class="step-name">Pagamento</span>
                    </div>
                    <div class="step-line" aria-hidden="true"></div>
                </li>
                <li class="matricula-step">
                    <div class="step-circle" aria-hidden="true">4</div>
                    <div class="step-info">
                        <span class="step-number">Etapa 4</span>
                        <span class="step-name">Conclusão</span>
                    </div>
                </li>
            </ol>
        </nav>

        <!-- CABEÇALHO DA SEÇÃO -->
        <header class="matricula-header-secao">
            <span class="matricula-tag-etapa">Finalização da Matrícula</span>
            <h2>Como Deseja Pagar?</h2>
            <p>Escolha o método de pagamento seguro para ativar sua matrícula na <?= htmlspecialchars($config['app_name'] ?? 'GymFlow') ?>.</p>
        </header>

        <?php if (!empty($erro)): ?>
            <div class="matricula-alerta-erro" role="alert">
                <span aria-hidden="true">⚠️</span>
                <div>
                    <strong>Atenção:</strong> <?= htmlspecialchars($erro) ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="matricula-grid-layout">
            
            <!-- COLUNA PRINCIPAL: MÉTODO DE PAGAMENTO -->
            <section class="matricula-card">
                <h3 class="matricula-card-titulo">Método de Pagamento</h3>
                <p class="matricula-card-subtitulo">Processamento direto e seguro via Mercado Pago.</p>

                <form action="<?= BASE_URL ?>/app/controllers/MatriculaController.php?acao=pagamento" method="POST" id="form-pagamento">
                    
                    <div class="metodo-pagamento-selector" role="radiogroup" aria-label="Selecione o método de pagamento">
                        <label class="metodo-opcao is-selected" id="label-pix">
                            <input 
                                type="radio" 
                                name="metodo" 
                                value="pix" 
                                id="radio-pix" 
                                checked 
                                onchange="alternarMetodo('pix')"
                            >
                            <div class="metodo-opcao-info">
                                <strong>PIX</strong>
                                <small>Aprovação instantânea</small>
                            </div>
                        </label>

                        <label class="metodo-opcao" id="label-cartao">
                            <input 
                                type="radio" 
                                name="metodo" 
                                value="cartao" 
                                id="radio-cartao" 
                                onchange="alternarMetodo('cartao')"
                            >
                            <div class="metodo-opcao-info">
                                <strong>Cartão de Crédito</strong>
                                <small>Até 12x no cartão</small>
                            </div>
                        </label>
                    </div>

                    <!-- PAINEL PIX -->
                    <div id="painel-pix" class="painel-metodo-conteudo">
                        <h4 class="painel-metodo-titulo">
                            <span>⚡ Pagamento Instantâneo via PIX</span>
                        </h4>
                        <p class="painel-metodo-descricao">
                            Ao clicar no botão de confirmação abaixo, o sistema gerará o <strong>QR Code</strong> e o código <strong>Pix Copia e Cola</strong> para você pagar pelo aplicativo do seu banco.
                        </p>
                        <p class="form-help">
                            ✓ Confirmação em poucos segundos pelo Banco Central.<br>
                            ✓ Seu acesso ao Portal do Aluno é liberado assim que o pagamento for registrado.
                        </p>
                    </div>

                    <!-- PAINEL CARTÃO -->
                    <div id="painel-cartao" class="painel-metodo-conteudo" style="display: none;">
                        <h4 class="painel-metodo-titulo">
                            <span>💳 Cartão de Crédito</span>
                        </h4>
                        <p class="painel-metodo-descricao">
                            Ambiente protegido para processamento seguro via Mercado Pago. Nenhum dado do seu cartão é gravado em nossos servidores.
                        </p>

                        <div id="paymentBrick_container" style="margin-bottom: 20px;">
                            <p class="form-help">🔒 Transação protegida por criptografia de ponta a ponta.</p>
                        </div>

                        <div class="form-group">
                            <label for="parcelas" class="form-label">
                                <span>Número de Parcelas</span>
                            </label>
                            <select name="parcelas" id="parcelas" class="form-control">
                                <option value="1">1x de R$ <?= number_format((float) $plano['valor'], 2, ',', '.') ?> (à vista)</option>
                                <option value="2">2x de R$ <?= number_format((float) ($plano['valor'] / 2), 2, ',', '.') ?></option>
                                <option value="3">3x de R$ <?= number_format((float) ($plano['valor'] / 3), 2, ',', '.') ?></option>
                            </select>
                        </div>
                    </div>

                    <div class="matricula-acoes">
                        <a href="<?= BASE_URL ?>/app/controllers/MatriculaController.php?acao=filial" class="btn-matricula-secundario">
                            ← Voltar para Filiais
                        </a>
                        <button type="submit" class="btn-matricula-principal" id="btn-submit-pagamento">
                            Confirmar e Pagar R$ <?= number_format((float) $plano['valor'], 2, ',', '.') ?> →
                        </button>
                    </div>
                </form>
            </section>

            <!-- COLUNA LATERAL: RESUMO COMPLETO -->
            <aside class="matricula-resumo-card" aria-label="Resumo detalhado da matrícula">
                <div class="resumo-cabecalho">
                    <span class="resumo-badge">Resumo da Contratação</span>
                    <h3 class="resumo-plano-nome"><?= htmlspecialchars($plano['nome'] ?? 'Plano') ?></h3>
                    <div class="resumo-preco-box">
                        <span class="resumo-preco-cifrao">R$</span>
                        <span class="resumo-preco-valor"><?= number_format((float) ($plano['valor'] ?? 0), 2, ',', '.') ?></span>
                        <span class="resumo-preco-periodo">/ <?= htmlspecialchars($plano['duracao'] ?? 'mês') ?></span>
                    </div>
                </div>

                <ul class="resumo-detalhes-lista">
                    <li class="resumo-item">
                        <span>Aluno:</span>
                        <strong><?= htmlspecialchars($matriculaTemp['nome'] ?? '') ?></strong>
                    </li>
                    <li class="resumo-item">
                        <span>CPF:</span>
                        <strong><?= htmlspecialchars($matriculaTemp['cpf'] ?? '') ?></strong>
                    </li>
                    <li class="resumo-item">
                        <span>Unidade:</span>
                        <strong><?= htmlspecialchars($filial['nome'] ?? '') ?></strong>
                    </li>
                    <?php if (!empty($filial['telefone'])): ?>
                        <li class="resumo-item">
                            <span>Telefone:</span>
                            <strong><?= htmlspecialchars($filial['telefone']) ?></strong>
                        </li>
                    <?php endif; ?>
                </ul>

                <div class="resumo-beneficios">
                    <p class="resumo-beneficios-titulo">Segurança Garantida</p>
                    <ul>
                        <li><span>✓</span> Matrícula imediata no sistema</li>
                        <li><span>✓</span> Acesso ao Portal do Aluno</li>
                        <li><span>✓</span> Pagamento criptografado</li>
                    </ul>
                </div>
            </aside>

        </div>

    </div>
</main>

<script>
function alternarMetodo(metodo) {
    const painelPix = document.getElementById('painel-pix');
    const painelCartao = document.getElementById('painel-cartao');
    const labelPix = document.getElementById('label-pix');
    const labelCartao = document.getElementById('label-cartao');

    if (metodo === 'pix') {
        painelPix.style.display = 'block';
        painelCartao.style.display = 'none';
        labelPix.classList.add('is-selected');
        labelCartao.classList.remove('is-selected');
    } else {
        painelPix.style.display = 'none';
        painelCartao.style.display = 'block';
        labelPix.classList.remove('is-selected');
        labelCartao.classList.add('is-selected');
    }
}
</script>

<?php include __DIR__ . '/../shared/portfolio_footer.php'; ?>
