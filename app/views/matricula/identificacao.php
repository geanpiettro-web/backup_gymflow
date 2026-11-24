<?php
// app/views/matricula/identificacao.php

/** @var array|null $planoSelecionado */
/** @var array $planos */
/** @var array $dados */
/** @var string $erro */
/** @var array $config */
/** @var int $company_id */

$tituloPagina = "Identificação do Aluno";
$cssEspecifico = BASE_URL . '/assets/css/matricula.css';
include __DIR__ . '/../shared/portfolio_header.php';
?>

<main class="matricula-wrapper">
    <div class="matricula-container">

        <!-- STEPPER DE PROGRESSO (ETAPA 1 ATIVA) -->
        <nav class="matricula-stepper-container" aria-label="Progresso da matrícula">
            <ol class="matricula-stepper">
                <li class="matricula-step active" aria-current="step">
                    <div class="step-circle" aria-hidden="true">1</div>
                    <div class="step-info">
                        <span class="step-number">Etapa 1</span>
                        <span class="step-name">Identificação</span>
                    </div>
                    <div class="step-line" aria-hidden="true"></div>
                </li>
                <li class="matricula-step">
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
            <h2>Seus Dados Cadastrais</h2>
            <p>Preencha as informações para criar seu acesso e iniciar seus treinos na <?= htmlspecialchars($config['app_name'] ?? 'GymFlow') ?>.</p>
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
            <!-- COLUNA PRINCIPAL: FORMULÁRIO -->
            <section class="matricula-card">
                <h3 class="matricula-card-titulo">Dados Pessoais e Acesso</h3>
                <p class="matricula-card-subtitulo">Campos com <span class="form-label-required">*</span> são de preenchimento obrigatório.</p>

                <form action="<?= BASE_URL ?>/app/controllers/MatriculaController.php?acao=identificacao" method="POST" novalidate>
                    
                    <div class="form-group">
                        <label for="plano_id" class="form-label">
                            <span>Plano Desejado <span class="form-label-required">*</span></span>
                        </label>
                        <select id="plano_id" name="plano_id" class="form-control" required onchange="this.form.submit()">
                            <option value="">Selecione o plano desejado</option>
                            <?php foreach ($planos as $p): ?>
                                <option value="<?= (int) $p['id'] ?>" <?= ((int) ($planoSelecionado['id'] ?? $dados['plano_id'] ?? 0) === (int) $p['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($p['nome']) ?> — R$ <?= number_format((float) $p['valor'], 2, ',', '.') ?> (<?= htmlspecialchars($p['duracao']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <span class="form-help">Você pode alterar o plano a qualquer momento nesta etapa.</span>
                    </div>

                    <div class="form-group">
                        <label for="nome" class="form-label">
                            <span>Nome Completo <span class="form-label-required">*</span></span>
                        </label>
                        <input type="text" id="nome" name="nome" class="form-control" value="<?= htmlspecialchars($dados['nome'] ?? '') ?>" placeholder="Ex: Ana Clara Silva" required autocomplete="name">
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="cpf" class="form-label">
                                <span>CPF <span class="form-label-required">*</span></span>
                            </label>
                            <input type="text" id="cpf" name="cpf" class="form-control" value="<?= htmlspecialchars($dados['cpf'] ?? '') ?>" placeholder="000.000.000-00" required maxlength="14">
                        </div>

                        <div class="form-group">
                            <label for="nascimento" class="form-label">
                                <span>Data de Nascimento <span class="form-label-required">*</span></span>
                            </label>
                            <input type="date" id="nascimento" name="nascimento" class="form-control" value="<?= htmlspecialchars($dados['nascimento'] ?? '') ?>" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="sexo" class="form-label">
                                <span>Sexo <span class="form-label-required">*</span></span>
                            </label>
                            <select id="sexo" name="sexo" class="form-control" required>
                                <option value="">Selecione</option>
                                <option value="Masculino" <?= (($dados['sexo'] ?? '') === 'Masculino') ? 'selected' : '' ?>>Masculino</option>
                                <option value="Feminino" <?= (($dados['sexo'] ?? '') === 'Feminino') ? 'selected' : '' ?>>Feminino</option>
                                <option value="Outro" <?= (($dados['sexo'] ?? '') === 'Outro') ? 'selected' : '' ?>>Outro</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="telefone" class="form-label">
                                <span>WhatsApp / Celular <span class="form-label-required">*</span></span>
                            </label>
                            <input type="tel" id="telefone" name="telefone" class="form-control" value="<?= htmlspecialchars($dados['telefone'] ?? '') ?>" placeholder="(11) 99999-9999" required autocomplete="tel">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="email" class="form-label">
                            <span>E-mail <span class="form-label-required">*</span></span>
                        </label>
                        <input type="email" id="email" name="email" class="form-control" value="<?= htmlspecialchars($dados['email'] ?? '') ?>" placeholder="seu.email@exemplo.com" required autocomplete="email">
                        <span class="form-help">Usado para receber seus comprovantes e links de acesso.</span>
                    </div>

                    <div class="form-group">
                        <label for="senha" class="form-label">
                            <span>Criar Senha de Acesso <span class="form-label-required">*</span></span>
                        </label>
                        <input type="password" id="senha" name="senha" class="form-control" minlength="6" placeholder="Mínimo de 6 caracteres" required autocomplete="new-password">
                        <span class="form-help">Esta senha será usada para você acessar o Portal do Aluno com suas fichas de treino.</span>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="rg" class="form-label">
                                <span>RG <small>(Opcional)</small></span>
                            </label>
                            <input type="text" id="rg" name="rg" class="form-control" value="<?= htmlspecialchars($dados['rg'] ?? '') ?>" placeholder="Número do RG">
                        </div>

                        <div class="form-group">
                            <label for="endereco" class="form-label">
                                <span>Endereço Completo <small>(Opcional)</small></span>
                            </label>
                            <input type="text" id="endereco" name="endereco" class="form-control" value="<?= htmlspecialchars($dados['endereco'] ?? '') ?>" placeholder="Rua, número, bairro, cidade">
                        </div>
                    </div>

                    <div class="matricula-acoes">
                        <a href="<?= BASE_URL ?>/index.php?id=<?= (int) $company_id ?>" class="btn-matricula-secundario">
                            ← Voltar ao Portfólio
                        </a>
                        <button type="submit" class="btn-matricula-principal">
                            Continuar para Escolha da Filial →
                        </button>
                    </div>
                </form>
            </section>

            <!-- COLUNA LATERAL: RESUMO DO PLANO -->
            <aside class="matricula-resumo-card" aria-label="Resumo do plano selecionado">
                <div class="resumo-cabecalho">
                    <span class="resumo-badge">Resumo da Escolha</span>
                    <?php if ($planoSelecionado): ?>
                        <h3 class="resumo-plano-nome"><?= htmlspecialchars($planoSelecionado['nome']) ?></h3>
                        <div class="resumo-preco-box">
                            <span class="resumo-preco-cifrao">R$</span>
                            <span class="resumo-preco-valor"><?= number_format((float) $planoSelecionado['valor'], 2, ',', '.') ?></span>
                            <span class="resumo-preco-periodo">/ <?= htmlspecialchars($planoSelecionado['duracao']) ?></span>
                        </div>
                    <?php else: ?>
                        <h3 class="resumo-plano-nome">Nenhum plano selecionado</h3>
                        <p class="form-help">Selecione um plano no formulário para visualizar os valores.</p>
                    <?php endif; ?>
                </div>

                <?php if ($planoSelecionado): ?>
                    <ul class="resumo-detalhes-lista">
                        <li class="resumo-item">
                            <span>Categoria</span>
                            <strong><?= htmlspecialchars($planoSelecionado['categoria'] ?? 'Padrão') ?></strong>
                        </li>
                        <li class="resumo-item">
                            <span>Taxa de adesão</span>
                            <strong>Grátis</strong>
                        </li>
                        <li class="resumo-item">
                            <span>Taxa de cancelamento</span>
                            <strong>Zero</strong>
                        </li>
                    </ul>

                    <div class="resumo-beneficios">
                        <p class="resumo-beneficios-titulo">Vantagens Inclusas</p>
                        <ul>
                            <li><span>✓</span> Acesso completo aos equipamentos</li>
                            <li><span>✓</span> Fichas e treinos digitais no Portal do Aluno</li>
                            <li><span>✓</span> Acompanhamento por profissionais</li>
                        </ul>
                    </div>
                <?php endif; ?>
            </aside>
        </div>

    </div>
</main>

<?php include __DIR__ . '/../shared/portfolio_footer.php'; ?>
