<?php
include __DIR__ . '/../shared/header.php';
include __DIR__ . '/../shared/sidebar.php';

// Campos que podem vir vazios do banco
$rg = '';
if (isset($aluno['rg'])) {
    $rg = $aluno['rg'];
}

$endereco = '';
if (isset($aluno['endereco'])) {
    $endereco = $aluno['endereco'];
}
?>

<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/jorge-financeiro.css">
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/alunos.css">

<main class="conteudo financeiro-page aluno-visualizar-page">

    <!-- Título e botões de voltar e editar -->
    <div class="fin-page-header">
        <div class="fin-header-info">
            <h1>Dados do Aluno</h1>
            <p>Visualize as informações completas, matrícula e histórico de pagamentos.</p>
        </div>

        <div class="fin-header-actions">
            <a href="<?= $baseUrl ?>?acao=listar"
               class="fin-btn fin-btn-secondary">
                Voltar
            </a>

            <a href="<?= $baseUrl ?>?acao=editar&id=<?= $aluno['id'] ?>"
               class="fin-btn fin-btn-primary">
                Editar Aluno
            </a>
        </div>
    </div>

    <!-- Mensagens de erro e sucesso -->
    <?php if (!empty($erro)): ?>
        <div class="mensagem mensagem-erro">
            <?= htmlspecialchars($erro) ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($sucesso)): ?>
        <div class="mensagem mensagem-sucesso">
            <?= htmlspecialchars($sucesso) ?>
        </div>
    <?php endif; ?>


    <!-- Dados cadastrais -->
    <section class="fin-table-card aluno-secao">

        <h3 class="titulo-secao">Dados Cadastrais</h3>

        <div class="aluno-dados-grid">

            <p>
                <strong>Nome:</strong><br>
                <?= htmlspecialchars($aluno['nome']) ?>
            </p>

            <p>
                <strong>CPF:</strong><br>
                <?= htmlspecialchars($aluno['cpf']) ?>
            </p>

            <p>
                <strong>RG:</strong><br>
                <?= htmlspecialchars($rg) ?>
            </p>

            <p>
                <strong>Sexo:</strong><br>
                <?= htmlspecialchars($aluno['sexo']) ?>
            </p>

            <p>
                <strong>Nascimento:</strong><br>
                <?= date('d/m/Y', strtotime($aluno['nascimento'])) ?>
            </p>

            <p>
                <strong>E-mail:</strong><br>
                <?= htmlspecialchars($aluno['email']) ?>
            </p>

            <p>
                <strong>Telefone:</strong><br>
                <?= htmlspecialchars($aluno['telefone']) ?>
            </p>

            <p>
                <strong>Endereço:</strong><br>
                <?= htmlspecialchars($endereco) ?>
            </p>

            <p>
                <strong>Filial:</strong><br>
                <?= htmlspecialchars($aluno['nome_filial']) ?>
            </p>

            <p>
                <strong>Status:</strong><br>

                <span class="aluno-status <?= $aluno['status'] === 'Ativo' ? 'status-ativo' : 'status-inativo' ?>">
                    <?= htmlspecialchars($aluno['status']) ?>
                </span>
            </p>

        </div>

    </section>


    <!-- Matrícula -->
    <section class="fin-table-card aluno-secao">

        <h3 class="titulo-secao">Matrícula</h3>

        <div class="aluno-secao-conteudo">

            <?php if ($matricula): ?>

                <!-- O aluno já tem matrícula: mostra os dados -->
                <div class="aluno-matricula-dados">

                    <p>
                        <strong>Plano:</strong><br>
                        <?= htmlspecialchars($matricula['nome_plano']) ?>
                    </p>

                    <p>
                        <strong>Período:</strong><br>
                        <?= date('d/m/Y', strtotime($matricula['inicio'])) ?>
                        até
                        <?= date('d/m/Y', strtotime($matricula['fim'])) ?>
                    </p>

                    <p>
                        <strong>Valor:</strong><br>
                        R$ <?= number_format($matricula['valor'], 2, ',', '.') ?>
                    </p>

                </div>

            <?php else: ?>

                <!-- O aluno não possui matrícula ativa -->
                <p class="aluno-sem-matricula">
                    O aluno não possui matrícula ativa.
                </p>

                <form action="<?= $baseUrl ?>?acao=matricular"
                      method="POST"
                      class="fin-form-grid aluno-matricula-form">

                    <input type="hidden"
                           name="aluno_id"
                           value="<?= $aluno['id'] ?>">

                    <div class="fin-form-group aluno-matricula-plano">

                        <label>Plano:</label>

                        <select name="plano_id"
                                class="fin-select"
                                required>

                            <option value="">Selecione o Plano</option>

                            <?php foreach ($planos as $plano): ?>

                                <option value="<?= $plano['id'] ?>">
                                    <?= htmlspecialchars($plano['nome']) ?>
                                    - R$ <?= number_format($plano['valor'], 2, ',', '.') ?>
                                </option>

                            <?php endforeach; ?>

                        </select>

                    </div>

                    <div class="fin-form-group aluno-matricula-campo">

                        <label>Data de início:</label>

                        <input type="date"
                               name="data_inicio"
                               class="fin-input"
                               value="<?= date('Y-m-d') ?>"
                               required>

                    </div>

                    <div class="fin-form-group aluno-matricula-campo">

                        <label>Desconto (R$):</label>

                        <input type="text"
                               name="desconto"
                               class="fin-input"
                               value="0,00">

                    </div>

                    <div class="fin-form-group">

                        <button type="submit"
                                class="fin-btn fin-btn-primary">
                            Matricular
                        </button>

                    </div>

                </form>

            <?php endif; ?>

        </div>

    </section>


    <!-- Mensalidades -->
    <section class="fin-table-card">

        <h3 class="titulo-secao">Mensalidades</h3>

        <div class="fin-table-responsive">

            <table class="fin-table">

                <thead>
                    <tr>
                        <th>Vencimento</th>
                        <th>Valor</th>
                        <th>Status</th>
                        <th>Pagamento</th>
                    </tr>
                </thead>

                <tbody>

                    <?php if (count($contas) == 0): ?>

                        <tr>
                            <td colspan="4"
                                class="aluno-mensalidades-vazio">
                                Nenhuma mensalidade encontrada.
                            </td>
                        </tr>

                    <?php endif; ?>

                    <?php foreach ($contas as $conta): ?>

                        <?php
                        // Forma de pagamento
                        $formaPagamento = 'Pendente';

                        if (isset($conta['forma_pagamento'])) {
                            $formaPagamento = $conta['forma_pagamento'];
                        }
                        ?>

                        <tr>

                            <td>
                                <?= date('d/m/Y', strtotime($conta['vencimento'])) ?>
                            </td>

                            <td>
                                R$ <?= number_format($conta['valor'], 2, ',', '.') ?>
                            </td>

                            <!-- Status da mensalidade -->
                            <td class="<?= $conta['status'] === 'Pago' ? 'status-pago' : 'status-nao-pago' ?>">
                                <?= htmlspecialchars($conta['status']) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($formaPagamento) ?>
                            </td>

                        </tr>

                    <?php endforeach; ?>

                </tbody>

            </table>

        </div>

    </section>

</main>

<?php include __DIR__ . '/../shared/footer.php'; ?>