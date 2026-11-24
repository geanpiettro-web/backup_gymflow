<?php
// app/views/matricula/sucesso.php

/** @var array $resultado */
/** @var array $config */
/** @var int $company_id */

$tituloPagina = "Matrícula Realizada com Sucesso!";
$cssEspecifico = BASE_URL . '/assets/css/matricula.css';
include __DIR__ . '/../shared/portfolio_header.php';

$empresaRetorno = (int) ($resultado['company_id'] ?? $company_id ?? 1);
?>

<main class="matricula-wrapper">
    <div class="matricula-container">

        <!-- STEPPER DE PROGRESSO (ETAPA 4 CONCLUÍDA) -->
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
                <li class="matricula-step completed">
                    <div class="step-circle" aria-hidden="true">✓</div>
                    <div class="step-info">
                        <span class="step-number">Etapa 3</span>
                        <span class="step-name">Pagamento</span>
                    </div>
                    <div class="step-line" aria-hidden="true"></div>
                </li>
                <li class="matricula-step active" aria-current="step">
                    <div class="step-circle" aria-hidden="true">✓</div>
                    <div class="step-info">
                        <span class="step-number">Etapa 4</span>
                        <span class="step-name">Conclusão</span>
                    </div>
                </li>
            </ol>
        </nav>

        <!-- CABEÇALHO DA SEÇÃO -->
        <header class="matricula-header-secao">
            <span class="matricula-tag-etapa">Tudo Pronto!</span>
            <h2>Seja Bem-vindo(a) à <?= htmlspecialchars($config['app_name'] ?? 'GymFlow') ?>!</h2>
            <p>Sua matrícula foi registrada com sucesso no sistema.</p>
        </header>

        <div class="matricula-grid-layout is-single">
            <section class="matricula-card">
                
                <div class="sucesso-badge-topo" aria-hidden="true">
                    ✓
                </div>

                <div class="sucesso-card-grid">
                    <div class="sucesso-item">
                        <span>Número da Matrícula</span>
                        <strong>#<?= (int) ($resultado['matricula_id'] ?? 0) ?></strong>
                    </div>

                    <div class="sucesso-item">
                        <span>Aluno Cadastrado</span>
                        <strong><?= htmlspecialchars($resultado['aluno_nome'] ?? '') ?></strong>
                    </div>

                    <div class="sucesso-item">
                        <span>E-mail de Acesso</span>
                        <strong><?= htmlspecialchars($resultado['aluno_email'] ?? '') ?></strong>
                    </div>

                    <div class="sucesso-item">
                        <span>Plano Contratado</span>
                        <strong><?= htmlspecialchars($resultado['plano_nome'] ?? '') ?></strong>
                    </div>

                    <div class="sucesso-item">
                        <span>Valor do Plano</span>
                        <strong>R$ <?= number_format((float) ($resultado['plano_valor'] ?? 0), 2, ',', '.') ?></strong>
                    </div>

                    <div class="sucesso-item">
                        <span>Situação do Pagamento</span>
                        <strong style="color: <?= (($resultado['status'] ?? '') === 'aprovado') ? '#10b981' : '#f59e0b' ?>;">
                            <?= (($resultado['status'] ?? '') === 'aprovado') ? '✓ Aprovado e Confirmado' : '⏳ Aguardando Confirmação' ?>
                        </strong>
                    </div>
                </div>

                <?php if (!empty($resultado['pix_copia_cola'])): ?>
                    <div class="pix-copia-box">
                        <h4>Código PIX Copia e Cola</h4>
                        <p>Copie o código abaixo e utilize a opção "Pix Copia e Cola" no aplicativo do seu banco para efetuar o pagamento:</p>
                        <textarea readonly rows="3" class="pix-textarea" onclick="this.select();"><?= htmlspecialchars($resultado['pix_copia_cola']) ?></textarea>
                    </div>
                <?php endif; ?>

                <div class="proximo-passo-box">
                    <p>
                        <strong>Próximo passo:</strong> Acesse o <strong>Portal do Aluno</strong> com o seu e-mail e a senha que você acabou de cadastrar. Lá você terá acesso imediato à sua ficha de treinos, histórico financeiro e horários de aulas.
                    </p>
                </div>

                <div class="matricula-acoes is-centered">
                    <a href="<?= BASE_URL ?>/app/controllers/LoginController.php?acao=login&amp;empresa=<?= $empresaRetorno ?>" class="btn-matricula-principal">
                        Entrar no Portal do Aluno →
                    </a>
                    <a href="<?= BASE_URL ?>/index.php?id=<?= $empresaRetorno ?>" class="btn-matricula-secundario">
                        Voltar para a Página Inicial
                    </a>
                </div>

            </section>
        </div>

    </div>
</main>

<?php include __DIR__ . '/../shared/portfolio_footer.php'; ?>
