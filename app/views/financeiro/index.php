<?php
if (!isset($tituloPagina)) {
    header("Location: " . BASE_URL . "/app/controllers/FinanceiroController.php");
    exit;
}

$tituloPagina = "Financeiro";
$cssEspecifico = BASE_URL . '/assets/css/jorge-financeiro.css?v=2';

include __DIR__ . '/../shared/header.php';
include __DIR__ . '/../shared/sidebar.php';

$totalGeral = ($totalAberto ?? 0) + ($totalRecebido ?? 0);
?>

<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/jorge-financeiro.css?v=2">

<main class="conteudo financeiro-page contas-receber-page">

    <!-- Cabeçalho da página -->
    <div class="fin-page-header">
        <div class="fin-header-info">
            <h1>Contas a Receber</h1>
            <p>Acompanhe e gerencie as mensalidades e pagamentos dos alunos da academia.</p>
        </div>
    </div>

    <!-- Cards de Resumo Financeiro -->
    <section class="fin-metrics-grid">

        <!-- Total em Aberto -->
        <div class="fin-metric-card warning">

            <div class="fin-metric-top">
                <span class="fin-metric-label">Total em Aberto</span>
            </div>

            <div class="fin-metric-value valor-em-aberto">
                R$ <?= number_format($totalAberto ?? 0, 2, ',', '.') ?>
            </div>

            <p class="fin-metric-subtext">
                Pendências a receber
            </p>

        </div>

        <!-- Total Recebido -->
        <div class="fin-metric-card success">

            <div class="fin-metric-top">
                <span class="fin-metric-label">Total Recebido</span>
            </div>

            <div class="fin-metric-value valor-recebido">
                R$ <?= number_format($totalRecebido ?? 0, 2, ',', '.') ?>
            </div>

            <p class="fin-metric-subtext">
                Mensalidades liquidadas
            </p>

        </div>

        <!-- Total Previsto -->
        <div class="fin-metric-card info">

            <div class="fin-metric-top">
                <span class="fin-metric-label">Total Previsto</span>
            </div>

            <div class="fin-metric-value valor-previsto">
                R$ <?= number_format($totalGeral, 2, ',', '.') ?>
            </div>

            <p class="fin-metric-subtext">
                Receita total projetada
            </p>

        </div>

    </section>

    <!-- Tabela de Contas -->
    <section class="fin-table-card">

        <div class="fin-table-header">
            <h2>Lançamentos de Matrículas</h2>
        </div>

        <div class="fin-table-responsive">

            <table class="fin-table">

                <thead>

                    <tr>
                        <th>Aluno</th>
                        <th>Vencimento</th>
                        <th>Valor</th>
                        <th>Status</th>
                        <th class="contas-coluna-acoes">Ações</th>
                    </tr>

                </thead>

                <tbody>

                    <?php if (empty($contas)): ?>

                        <tr>

                            <td colspan="5" class="contas-vazio">
                                Nenhuma conta a receber encontrada no momento.
                            </td>

                        </tr>

                    <?php else: ?>

                        <?php foreach ($contas as $conta): ?>

                            <?php
                            $contaPaga = $conta['status'] === 'Pago';
                            ?>

                            <tr>

                                <!-- Nome do aluno -->
                                <td>
                                    <strong>
                                        <?= htmlspecialchars($conta['aluno_nome']) ?>
                                    </strong>
                                </td>

                                <!-- Vencimento -->
                                <td>
                                    <?= date('d/m/Y', strtotime($conta['vencimento'])) ?>
                                </td>

                                <!-- Valor -->
                                <td>
                                    <strong>
                                        R$ <?= number_format($conta['valor'], 2, ',', '.') ?>
                                    </strong>
                                </td>

                                <!-- Status -->
                                <td class="<?= $contaPaga ? 'conta-status-pago' : 'conta-status-aberto' ?>">

                                    <?= $contaPaga ? 'Pago' : 'Aberto' ?>

                                </td>

                                <!-- Ações -->
                                <td class="contas-coluna-acoes">

                                    <?php if (!$contaPaga): ?>

                                        <a
                                            href="<?= BASE_URL ?>/app/controllers/FinanceiroController.php?acao=baixar&id=<?= $conta['id'] ?>"
                                            class="fin-btn fin-btn-primary fin-btn-sm"
                                        >
                                            Baixar
                                        </a>

                                    <?php else: ?>

                                        <span class="conta-sem-acao">—</span>

                                    <?php endif; ?>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    <?php endif; ?>

                </tbody>

            </table>

        </div>

    </section>

</main>

<?php include __DIR__ . '/../shared/footer.php'; ?>