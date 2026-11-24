<?php
// app/models/FluxoCaixa.php

require_once __DIR__ . '/Database.php';

$operacao = $operacao ?? '';
$company_id = $_SESSION['company_id'] ?? 0;


// ==========================================
// LISTAR FLUXO DE CAIXA
// ==========================================

if ($operacao === 'listar_fluxo') {

    // Período recebido do Controller
    $inicioConsulta = $inicioConsulta ?? null;
    $fimConsulta = $fimConsulta ?? null;

    // Filtros financeiros
    $filtroReceitas = '';
    $filtroCustos = '';

    // Parâmetros das consultas
    $parametrosReceitas = [$company_id];
    $parametrosCustos = [$company_id];


    // Aplica o filtro quando existe um intervalo definido
    if ($inicioConsulta !== null && $fimConsulta !== null) {

        // Receitas: considera a data efetiva do pagamento
        $filtroReceitas = "
            AND c.pago_em >= ?
            AND c.pago_em < ?
        ";

        $parametrosReceitas[] = $inicioConsulta;
        $parametrosReceitas[] = $fimConsulta;


        // Custos: considera a data da despesa
        $filtroCustos = "
            AND cu.data >= ?
            AND cu.data < ?
        ";

        $parametrosCustos[] = $inicioConsulta;
        $parametrosCustos[] = $fimConsulta;
    }


    // ==========================================
    // TOTAL E QUANTIDADE DE RECEITAS
    // ==========================================

    $stmt = $pdo->prepare("
        SELECT
            COALESCE(SUM(c.valor), 0) AS total,
            COUNT(c.id) AS quantidade

        FROM contas c

        INNER JOIN alunos a
            ON a.id = c.aluno_id

        INNER JOIN filiais f
            ON f.id = a.filial_id

        WHERE c.status = 'Pago'
            AND f.company_id = ?

        $filtroReceitas
    ");

    $stmt->execute($parametrosReceitas);

    $receitas = $stmt->fetch(PDO::FETCH_ASSOC);

    $totalReceitas = $receitas['total'] ?? 0;
    $quantidadeReceitas = (int) ($receitas['quantidade'] ?? 0);


    // ==========================================
    // TOTAL E QUANTIDADE DE CUSTOS
    // ==========================================

    $stmt = $pdo->prepare("
        SELECT
            COALESCE(SUM(cu.valor), 0) AS total,
            COUNT(cu.id) AS quantidade

        FROM custos cu

        INNER JOIN filiais f
            ON f.id = cu.filial_id

        WHERE f.company_id = ?

        $filtroCustos
    ");

    $stmt->execute($parametrosCustos);

    $dadosCustos = $stmt->fetch(PDO::FETCH_ASSOC);

    $totalCustos = $dadosCustos['total'] ?? 0;
    $quantidadeCustos = (int) ($dadosCustos['quantidade'] ?? 0);


    // ==========================================
    // RESULTADO LÍQUIDO DO PERÍODO
    // ==========================================

    $resultado = round($totalReceitas - $totalCustos, 2);


    // ==========================================
    // LISTAGEM DOS CUSTOS DO PERÍODO
    // ==========================================

    $stmt = $pdo->prepare("
        SELECT
            cu.id,
            cu.descricao,
            cu.categoria,
            cu.valor,
            cu.data

        FROM custos cu

        INNER JOIN filiais f
            ON f.id = cu.filial_id

        WHERE f.company_id = ?

        $filtroCustos

        ORDER BY cu.data DESC, cu.id DESC
    ");

    $stmt->execute($parametrosCustos);

    $custos = $stmt->fetchAll(PDO::FETCH_ASSOC);
}


// ==========================================
// CRIAR CUSTO
// ==========================================

elseif ($operacao === 'criar_custo') {

    $descricao = trim($descricao ?? '');
    $categoria = trim($categoria ?? '');
    $valor = (float) ($valor ?? 0);
    $data = trim($data ?? '');

    // Filial responsável pelo custo
    $filial_id_custo = (int) ($filial_id ?? 0);


    // Busca a primeira filial ativa da empresa
    // caso nenhuma filial tenha sido informada
    if ($filial_id_custo <= 0) {

        $stmtF = $pdo->prepare("
            SELECT id

            FROM filiais

            WHERE company_id = ?
                AND ativo = 1

            LIMIT 1
        ");

        $stmtF->execute([$company_id]);

        $filial_id_custo = (int) ($stmtF->fetchColumn() ?: 0);
    }


    // Validação dos dados do lançamento
    if (
        $descricao !== '' &&
        $categoria !== '' &&
        $valor > 0 &&
        $data !== '' &&
        $filial_id_custo > 0
    ) {

        // Confirma que a filial pertence à empresa logada
        $stmtF = $pdo->prepare("
            SELECT id
            FROM filiais
            WHERE id = ?
                AND company_id = ?
            LIMIT 1
        ");

        $stmtF->execute([
            $filial_id_custo,
            $company_id
        ]);

        if ($stmtF->fetchColumn()) {

            $stmt = $pdo->prepare("
                INSERT INTO custos (
                    filial_id,
                    descricao,
                    categoria,
                    valor,
                    data
                )

                VALUES (
                    :filial_id,
                    :descricao,
                    :categoria,
                    :valor,
                    :data
                )
            ");

            $stmt->execute([
                ':filial_id' => $filial_id_custo,
                ':descricao' => $descricao,
                ':categoria' => $categoria,
                ':valor' => $valor,
                ':data' => $data
            ]);
        }
    }
}