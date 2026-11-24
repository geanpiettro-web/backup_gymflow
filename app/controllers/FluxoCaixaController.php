<?php
// app/controllers/FluxoCaixaController.php

require_once __DIR__ . '/../../config/sessao.php';

verificarRole(['Admin']);

$acao = $_GET['acao'] ?? 'listar';

// Valida as datas recebidas pelos filtros
function validarDataFluxo($valor, $formato)
{
    if (!is_string($valor)) {
        return false;
    }

    $data = DateTimeImmutable::createFromFormat(
        '!' . $formato,
        $valor
    );

    return $data !== false
        && $data->format($formato) === $valor;
}


// Listar fluxo de caixa
if ($acao === 'listar') {

    $fuso = new DateTimeZone('America/Sao_Paulo');
    $hoje = new DateTimeImmutable('now', $fuso);

    $periodoAtual = $_GET['periodo'] ?? 'mensal';

    $periodosPermitidos = [
        'diario',
        'mensal',
        'personalizado',
        'todos'
    ];

    if (!in_array($periodoAtual, $periodosPermitidos, true)) {
        $periodoAtual = 'mensal';
    }

    // Datas padrão
    $diaSelecionado = $hoje->format('Y-m-d');
    $mesSelecionado = $hoje->format('Y-m');

    $dataInicio = $hoje->format('Y-m-01');
    $dataFim = $hoje->format('Y-m-d');

    $inicioConsulta = null;
    $fimConsulta = null;

    $erroFiltro = '';
    $periodoDescricao = '';

    $meses = [
        1 => 'Janeiro',
        2 => 'Fevereiro',
        3 => 'Março',
        4 => 'Abril',
        5 => 'Maio',
        6 => 'Junho',
        7 => 'Julho',
        8 => 'Agosto',
        9 => 'Setembro',
        10 => 'Outubro',
        11 => 'Novembro',
        12 => 'Dezembro'
    ];


    // Consulta diária
    if ($periodoAtual === 'diario') {

        $dia = $_GET['dia'] ?? $diaSelecionado;

        if (validarDataFluxo($dia, 'Y-m-d')) {
            $diaSelecionado = $dia;
        } else {
            $erroFiltro = 'Data inválida.';
        }

        $data = new DateTimeImmutable(
            $diaSelecionado,
            $fuso
        );

        $inicioConsulta = $data->format('Y-m-d');

        $fimConsulta = $data
            ->modify('+1 day')
            ->format('Y-m-d');

        $periodoDescricao = $data->format('d/m/Y');
    }


    // Consulta mensal
    elseif ($periodoAtual === 'mensal') {

        $mes = $_GET['mes'] ?? $mesSelecionado;

        if (validarDataFluxo($mes, 'Y-m')) {
            $mesSelecionado = $mes;
        } else {
            $erroFiltro = 'Mês inválido.';
        }

        $data = new DateTimeImmutable(
            $mesSelecionado . '-01',
            $fuso
        );

        $inicioConsulta = $data->format('Y-m-d');

        $fimConsulta = $data
            ->modify('+1 month')
            ->format('Y-m-d');

        $periodoDescricao =
            $meses[(int) $data->format('n')]
            . ' de '
            . $data->format('Y');
    }


    // Consulta personalizada
    elseif ($periodoAtual === 'personalizado') {

        $inicio = $_GET['data_inicio'] ?? $dataInicio;
        $fim = $_GET['data_fim'] ?? $dataFim;

        if (
            validarDataFluxo($inicio, 'Y-m-d') &&
            validarDataFluxo($fim, 'Y-m-d') &&
            $inicio <= $fim
        ) {
            $dataInicio = $inicio;
            $dataFim = $fim;
        } else {
            $erroFiltro = 'Intervalo de datas inválido. Foi utilizado o período padrão.';
        }

        $inicioConsulta = $dataInicio;

        $fimConsulta = (new DateTimeImmutable(
            $dataFim,
            $fuso
        ))
            ->modify('+1 day')
            ->format('Y-m-d');

        $periodoDescricao =
            date('d/m/Y', strtotime($dataInicio))
            . ' a '
            . date('d/m/Y', strtotime($dataFim));
    }


    // Todo o histórico
    else {

        $periodoDescricao = 'Todo o histórico';
    }


    // Executa o Model
    $operacao = 'listar_fluxo';

    require __DIR__ . '/../models/FluxoCaixa.php';

    // Carrega a View
    $tituloPagina = "Fluxo de Caixa";

    require __DIR__ . '/../views/fluxo_caixa/index.php';

    exit;
}


// Lançar custo
if ($acao === 'lancar') {

    $tituloPagina = "Lançar custo";

    require __DIR__ . '/../views/fluxo_caixa/lancar.php';

    exit;
}


// Confirmar lançamento
if ($acao === 'confirmar_lancamento') {

    $descricao = trim($_POST['descricao'] ?? '');
    $categoria = trim($_POST['categoria'] ?? '');
    $valor = $_POST['valor'] ?? '';
    $data = trim($_POST['data'] ?? '');

    if (
        $descricao === '' ||
        $categoria === '' ||
        $valor === '' ||
        $data === ''
    ) {
        header(
            "Location: " . BASE_URL .
            "/app/controllers/FluxoCaixaController.php"
        );

        exit;
    }

    $operacao = 'criar_custo';

    require __DIR__ . '/../models/FluxoCaixa.php';

    header(
        "Location: " . BASE_URL .
        "/app/controllers/FluxoCaixaController.php"
    );

    exit;
}