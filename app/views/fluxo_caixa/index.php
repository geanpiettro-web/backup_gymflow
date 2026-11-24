<?php
if (!isset($tituloPagina)) {
    header("Location: " . BASE_URL . "/app/controllers/FluxoCaixaController.php");
    exit;
}

$tituloPagina = "Fluxo de Caixa";

// Carregamento do CSS com controle de versão
$arquivoCss = dirname(__DIR__, 3) . '/assets/css/jorge-financeiro.css';

$versaoCss = is_file($arquivoCss)
    ? filemtime($arquivoCss)
    : '4';

$cssEspecifico = BASE_URL . '/assets/css/jorge-financeiro.css?v=' . $versaoCss;

include __DIR__ . '/../shared/header.php';
include __DIR__ . '/../shared/sidebar.php';

// Valores recebidos do Controller
$periodoAtual = $periodoAtual ?? 'mensal';
$diaSelecionado = $diaSelecionado ?? date('Y-m-d');
$mesSelecionado = $mesSelecionado ?? date('Y-m');
$dataInicio = $dataInicio ?? date('Y-m-01');
$dataFim = $dataFim ?? date('Y-m-d');
$periodoDescricao = $periodoDescricao ?? 'Mês atual';
$erroFiltro = $erroFiltro ?? '';

// Define a cor do resultado
if (($resultado ?? 0) > 0) {
    $classeResultado = 'fluxo-positivo';
} elseif (($resultado ?? 0) < 0) {
    $classeResultado = 'fluxo-negativo';
} else {
    $classeResultado = 'fluxo-neutro';
}
?>

<link rel="stylesheet" href="<?= htmlspecialchars($cssEspecifico) ?>">

<main class="conteudo fluxo-page fluxo-relatorio-page">

    <!-- =====================================
         CABEÇALHO
    ====================================== -->

    <div class="fin-page-header">

        <div class="fin-header-info">

            <h1>Fluxo de Caixa & DRE</h1>

            <p>
                Controle financeiro consolidado com demonstrativo
                de receitas e despesas operacionais.
            </p>

        </div>

        <div class="fin-header-actions">

            <a
                href="<?= BASE_URL ?>/app/controllers/FluxoCaixaController.php?acao=lancar"
                class="fin-btn fin-btn-primary"
            >
                Lançar Custo
            </a>

        </div>

    </div>


    <!-- =====================================
         FILTROS DE CONSULTA
    ====================================== -->

    <section class="fin-table-card fluxo-filtros-card">

        <form
            method="GET"
            action="<?= BASE_URL ?>/app/controllers/FluxoCaixaController.php"
            class="fluxo-filtros-form"
        >

            <input type="hidden" name="acao" value="listar">

            <!-- Tipo de período -->

            <div class="fluxo-filtro-grupo">

                <label for="periodo">
                    Período de consulta
                </label>

                <select
                    name="periodo"
                    id="periodo"
                    class="fin-form-select"
                >

                    <option
                        value="diario"
                        <?= $periodoAtual === 'diario' ? 'selected' : '' ?>
                    >
                        Diário
                    </option>

                    <option
                        value="mensal"
                        <?= $periodoAtual === 'mensal' ? 'selected' : '' ?>
                    >
                        Mensal
                    </option>

                    <option
                        value="personalizado"
                        <?= $periodoAtual === 'personalizado' ? 'selected' : '' ?>
                    >
                        Personalizado
                    </option>

                    <option
                        value="todos"
                        <?= $periodoAtual === 'todos' ? 'selected' : '' ?>
                    >
                        Todo o histórico
                    </option>

                </select>

            </div>


            <!-- Consulta diária -->

            <div
                class="fluxo-filtro-grupo"
                id="filtro-diario"
                <?= $periodoAtual !== 'diario' ? 'hidden' : '' ?>
            >

                <label for="dia">
                    Data de referência
                </label>

                <input
                    type="date"
                    name="dia"
                    id="dia"
                    class="fin-form-input"
                    value="<?= htmlspecialchars($diaSelecionado) ?>"
                    <?= $periodoAtual !== 'diario' ? 'disabled' : '' ?>
                >

            </div>


            <!-- Consulta mensal -->

            <div
                class="fluxo-filtro-grupo"
                id="filtro-mensal"
                <?= $periodoAtual !== 'mensal' ? 'hidden' : '' ?>
            >

                <label for="mes">
                    Mês de referência
                </label>

                <input
                    type="month"
                    name="mes"
                    id="mes"
                    class="fin-form-input"
                    value="<?= htmlspecialchars($mesSelecionado) ?>"
                    <?= $periodoAtual !== 'mensal' ? 'disabled' : '' ?>
                >

            </div>


            <!-- Consulta personalizada: início -->

            <div
                class="fluxo-filtro-grupo"
                id="filtro-inicio"
                <?= $periodoAtual !== 'personalizado' ? 'hidden' : '' ?>
            >

                <label for="data_inicio">
                    Data inicial
                </label>

                <input
                    type="date"
                    name="data_inicio"
                    id="data_inicio"
                    class="fin-form-input"
                    value="<?= htmlspecialchars($dataInicio) ?>"
                    <?= $periodoAtual !== 'personalizado' ? 'disabled' : '' ?>
                >

            </div>


            <!-- Consulta personalizada: fim -->

            <div
                class="fluxo-filtro-grupo"
                id="filtro-fim"
                <?= $periodoAtual !== 'personalizado' ? 'hidden' : '' ?>
            >

                <label for="data_fim">
                    Data final
                </label>

                <input
                    type="date"
                    name="data_fim"
                    id="data_fim"
                    class="fin-form-input"
                    value="<?= htmlspecialchars($dataFim) ?>"
                    min="<?= htmlspecialchars($dataInicio) ?>"
                    <?= $periodoAtual !== 'personalizado' ? 'disabled' : '' ?>
                >

            </div>


            <!-- Botão de consulta -->

            <div class="fluxo-filtro-acoes">

                <button
                    type="submit"
                    class="fin-btn fin-btn-primary"
                >
                    Consultar
                </button>

                <a
                    href="<?= BASE_URL ?>/app/controllers/FluxoCaixaController.php"
                    class="fin-btn fin-btn-secondary"
                >
                    Limpar
                </a>

            </div>

        </form>


        <!-- Mensagem de validação -->

        <?php if ($erroFiltro !== ''): ?>

            <div class="fin-alert fin-alert-danger fluxo-filtro-alerta">
                <?= htmlspecialchars($erroFiltro) ?>
            </div>

        <?php endif; ?>

    </section>


    <!-- =====================================
         RESUMO DO PERÍODO
    ====================================== -->

    <section class="fin-table-card fluxo-resumo-card">

        <div class="fluxo-resumo-header">

            <h2>Resumo do Período</h2>

            <span class="fluxo-periodo-atual">
                <?= htmlspecialchars($periodoDescricao) ?>
            </span>

        </div>

        <div class="fluxo-resumo-grid">

            <!-- Receitas -->

            <div class="fluxo-resumo-item">

                <span class="fluxo-resumo-label">
                    Receitas
                </span>

                <div class="fluxo-resumo-valor fluxo-positivo">
                    R$ <?= number_format($totalReceitas ?? 0, 2, ',', '.') ?>
                </div>

                <p class="fluxo-resumo-descricao">
                    <?= (int) ($quantidadeReceitas ?? 0) ?>
                    pagamentos recebidos
                </p>

            </div>


            <!-- Custos -->

            <div class="fluxo-resumo-item">

                <span class="fluxo-resumo-label">
                    Custos
                </span>

                <div class="fluxo-resumo-valor fluxo-negativo">
                    R$ <?= number_format($totalCustos ?? 0, 2, ',', '.') ?>
                </div>

                <p class="fluxo-resumo-descricao">
                    <?= (int) ($quantidadeCustos ?? 0) ?>
                    despesas lançadas
                </p>

            </div>


            <!-- Resultado líquido -->

            <div class="fluxo-resumo-item">

                <span class="fluxo-resumo-label">
                    Resultado Líquido
                </span>

                <div class="fluxo-resumo-valor <?= $classeResultado ?>">
                    R$ <?= number_format($resultado ?? 0, 2, ',', '.') ?>
                </div>

                <p class="fluxo-resumo-descricao">
                    Saldo: Receitas - Custos
                </p>

            </div>

        </div>

    </section>


    <!-- =====================================
         DRE GERENCIAL SIMPLIFICADA
    ====================================== -->

    <section class="fin-table-card fluxo-dre-card">

        <div class="fluxo-dre-header">

            <h2>Demonstração do Resultado do Exercício (DRE)</h2>

            <p>
                Demonstrativo gerencial simplificado
                — <?= htmlspecialchars($periodoDescricao) ?>
            </p>

        </div>

        <div class="fluxo-dre-tabela-container">

            <table class="fluxo-dre-tabela">

                <tbody>

                    <!-- Receita bruta -->

                    <tr>

                        <th scope="row">
                            Receita Bruta de Mensalidades
                        </th>

                        <td class="fluxo-dre-valor fluxo-positivo">
                            R$ <?= number_format($totalReceitas ?? 0, 2, ',', '.') ?>
                        </td>

                    </tr>


                    <!-- Despesas -->

                    <tr>

                        <th scope="row">
                            Total de Despesas & Custos Operacionais
                        </th>

                        <td class="fluxo-dre-valor fluxo-negativo">
                            R$ <?= number_format($totalCustos ?? 0, 2, ',', '.') ?>
                        </td>

                    </tr>


                    <!-- Resultado líquido -->

                    <tr class="fluxo-dre-resultado <?= $classeResultado ?>">

                        <th scope="row">
                            Resultado Líquido do Período
                        </th>

                        <td class="fluxo-dre-valor <?= $classeResultado ?>">
                            R$ <?= number_format($resultado ?? 0, 2, ',', '.') ?>
                        </td>

                    </tr>

                </tbody>

            </table>

        </div>

        <p class="fluxo-dre-observacao">
            Demonstrativo baseado nos pagamentos recebidos
            e nos custos registrados no período.
        </p>

    </section>


    <!-- =====================================
         LANÇAMENTOS DE CUSTOS
    ====================================== -->

    <section class="fin-table-card">

        <div class="fin-table-header">

            <div class="fluxo-lancamentos-titulo">

                <h2>Lançamentos de Custos</h2>

                <p class="fluxo-lancamentos-periodo">
                    <?= htmlspecialchars($periodoDescricao) ?>
                </p>

            </div>

            <span class="fluxo-registros">
                <?= count($custos ?? []) ?> registro(s)
            </span>

        </div>

        <div class="fin-table-responsive">

            <table class="fin-table">

                <thead>

                    <tr>

                        <th>Data</th>
                        <th>Descrição</th>
                        <th>Categoria</th>

                        <th class="fluxo-coluna-valor">
                            Valor
                        </th>

                    </tr>

                </thead>

                <tbody>

                    <?php if (empty($custos)): ?>

                        <tr>

                            <td
                                colspan="4"
                                class="fluxo-tabela-vazia"
                            >

                                Nenhum custo lançado no período selecionado.

                            </td>

                        </tr>

                    <?php else: ?>

                        <?php foreach ($custos as $custo): ?>

                            <tr>

                                <!-- Data -->

                                <td>
                                    <?= date('d/m/Y', strtotime($custo['data'])) ?>
                                </td>


                                <!-- Descrição -->

                                <td>

                                    <strong>
                                        <?= htmlspecialchars($custo['descricao']) ?>
                                    </strong>

                                </td>


                                <!-- Categoria -->

                                <td>
                                    <?= htmlspecialchars($custo['categoria']) ?>
                                </td>


                                <!-- Valor -->

                                <td class="fluxo-coluna-valor fluxo-custo-valor">

                                    - R$ <?= number_format($custo['valor'], 2, ',', '.') ?>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    <?php endif; ?>

                </tbody>

            </table>

        </div>

    </section>

</main>


<!-- =====================================
     CONTROLE DOS FILTROS
====================================== -->

<script>
document.addEventListener('DOMContentLoaded', function () {

    const periodo = document.getElementById('periodo');

    const filtroDiario = document.getElementById('filtro-diario');
    const filtroMensal = document.getElementById('filtro-mensal');
    const filtroInicio = document.getElementById('filtro-inicio');
    const filtroFim = document.getElementById('filtro-fim');

    const dataInicio = document.getElementById('data_inicio');
    const dataFim = document.getElementById('data_fim');

    function configurarCampo(grupo, ativo) {

        grupo.hidden = !ativo;

        const campo = grupo.querySelector('input');

        if (campo) {
            campo.disabled = !ativo;
            campo.required = ativo;
        }
    }

    function atualizarFiltros() {

        const tipo = periodo.value;

        configurarCampo(
            filtroDiario,
            tipo === 'diario'
        );

        configurarCampo(
            filtroMensal,
            tipo === 'mensal'
        );

        configurarCampo(
            filtroInicio,
            tipo === 'personalizado'
        );

        configurarCampo(
            filtroFim,
            tipo === 'personalizado'
        );
    }

    function atualizarLimiteData() {

        dataFim.min = dataInicio.value;

        if (
            dataInicio.value &&
            dataFim.value &&
            dataFim.value < dataInicio.value
        ) {
            dataFim.value = dataInicio.value;
        }
    }

    periodo.addEventListener(
        'change',
        atualizarFiltros
    );

    dataInicio.addEventListener(
        'change',
        atualizarLimiteData
    );

    atualizarFiltros();
    atualizarLimiteData();

});
</script>

<?php include __DIR__ . '/../shared/footer.php'; ?>