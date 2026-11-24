<?php
// app/views/matricula/filial.php

/** @var array $filiais */
/** @var array $plano */
/** @var array $matriculaTemp */
/** @var string $erro */
/** @var array $config */
/** @var int $company_id */

$tituloPagina = "Escolha da Filial";
$cssEspecifico = BASE_URL . '/assets/css/matricula.css';
include __DIR__ . '/../shared/portfolio_header.php';
?>

<main class="matricula-wrapper">
    <div class="matricula-container">

        <!-- STEPPER DE PROGRESSO (ETAPA 2 ATIVA) -->
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
                <li class="matricula-step active" aria-current="step">
                    <div class="step-circle" aria-hidden="true">2</div>
                    <div class="step-info">
                        <span class="step-number">Etapa 2</span>
                        <span class="step-name">Unidade</span>
                    </div>
                    <div class="step-line" aria-hidden="true"></div>
                </li>
                <li class="matricula-step">
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
            <span class="matricula-tag-etapa">Matrícula Online</span>
            <h2>Onde Você Quer Treinar?</h2>
            <p>Selecione a unidade mais conveniente para sua rotina na <?= htmlspecialchars($config['app_name'] ?? 'GymFlow') ?>.</p>
        </header>

        <?php if (!empty($erro)): ?>
            <div class="matricula-alerta-erro" role="alert">
                <span aria-hidden="true">⚠️</span>
                <div>
                    <strong>Atenção:</strong> <?= htmlspecialchars($erro) ?>
                </div>
            </div>
        <?php endif; ?>

        <form action="<?= BASE_URL ?>/app/controllers/MatriculaController.php?acao=filial" method="POST">
            <div class="matricula-grid-layout">
                
                <!-- COLUNA PRINCIPAL: SELEÇÃO DE FILIAL -->
                <section class="matricula-card">
                    <h3 class="matricula-card-titulo">Unidades Disponíveis</h3>
                    <p class="matricula-card-subtitulo">Clique na unidade desejada para selecioná-la.</p>

                    <div class="filiais-grid-selecao" role="radiogroup" aria-label="Unidades de treino disponíveis">
                        <?php if (empty($filiais)): ?>
                            <p class="form-help">Nenhuma unidade disponível no momento para esta rede.</p>
                        <?php else: ?>
                            <?php foreach ($filiais as $f): ?>
                                <?php
                                $isChecked = ((int) ($matriculaTemp['filial_id'] ?? 0) === (int) $f['id']);
                                ?>
                                <label class="filial-card-opcao <?= $isChecked ? 'is-selected' : '' ?>">
                                    <div class="filial-radio-circle">
                                        <input 
                                            type="radio" 
                                            name="filial_id" 
                                            value="<?= (int) $f['id'] ?>" 
                                            class="filial-radio-input"
                                            <?= $isChecked ? 'checked' : '' ?> 
                                            required
                                            onchange="document.querySelectorAll('.filial-card-opcao').forEach(el => el.classList.remove('is-selected')); this.closest('.filial-card-opcao').classList.add('is-selected');"
                                        >
                                    </div>
                                    <div class="filial-card-info">
                                        <div class="filial-card-topo">
                                            <h4 class="filial-nome"><?= htmlspecialchars($f['nome']) ?></h4>
                                            <span class="filial-badge-ativa">Disponível</span>
                                        </div>
                                        <?php if (!empty($f['endereco'])): ?>
                                            <p class="filial-detalhe">
                                                📍 <?= htmlspecialchars($f['endereco']) ?><?= !empty($f['numero']) ? ', ' . htmlspecialchars($f['numero']) : '' ?>
                                            </p>
                                        <?php endif; ?>
                                        <p class="filial-detalhe">
                                            📞 <?= htmlspecialchars($f['telefone'] ?? 'Telefone disponível na recepção') ?>
                                        </p>
                                        <p class="filial-detalhe">
                                            👤 Responsável: <?= htmlspecialchars($f['responsavel'] ?? 'Coordenação Geral') ?>
                                        </p>
                                    </div>
                                </label>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <div class="matricula-acoes">
                        <a href="<?= BASE_URL ?>/app/controllers/MatriculaController.php?acao=identificacao" class="btn-matricula-secundario">
                            ← Voltar para Identificação
                        </a>
                        <button type="submit" class="btn-matricula-principal">
                            Continuar para Pagamento →
                        </button>
                    </div>
                </section>

                <!-- COLUNA LATERAL: RESUMO -->
                <aside class="matricula-resumo-card" aria-label="Resumo da matrícula">
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
                            <span>Aluno</span>
                            <strong><?= htmlspecialchars($matriculaTemp['nome'] ?? '') ?></strong>
                        </li>
                        <li class="resumo-item">
                            <span>E-mail</span>
                            <strong><?= htmlspecialchars($matriculaTemp['email'] ?? '') ?></strong>
                        </li>
                        <li class="resumo-item">
                            <span>Categoria</span>
                            <strong><?= htmlspecialchars($plano['categoria'] ?? 'Geral') ?></strong>
                        </li>
                    </ul>

                    <div class="resumo-beneficios">
                        <p class="resumo-beneficios-titulo">Garantias</p>
                        <ul>
                            <li><span>✓</span> Acesso livre à unidade escolhida</li>
                            <li><span>✓</span> Liberação rápida após confirmação</li>
                        </ul>
                    </div>
                </aside>

            </div>
        </form>

    </div>
</main>

<?php include __DIR__ . '/../shared/portfolio_footer.php'; ?>
