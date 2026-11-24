<?php
// app/views/portfolio/checkout.php
// Redireciona de forma compatível e transparente para o fluxo de matrícula oficial

if (!defined('BASE_URL')) {
    require_once __DIR__ . '/../../../config/sessao.php';
}

$planoId = isset($_GET['plano_id']) ? (int) $_GET['plano_id'] : 0;
$companyId = isset($_GET['company_id']) ? (int) $_GET['company_id'] : (isset($_GET['id']) ? (int) $_GET['id'] : 0);

$params = ['acao' => 'identificacao'];
if ($companyId > 0) {
    $params['company_id'] = $companyId;
}
if ($planoId > 0) {
    $params['plano_id'] = $planoId;
}

$destino = BASE_URL . '/app/controllers/MatriculaController.php?' . http_build_query($params);

header("Location: $destino");
exit;
