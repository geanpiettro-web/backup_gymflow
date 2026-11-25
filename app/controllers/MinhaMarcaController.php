
<?php
require_once __DIR__ . '/../../config/sessao.php';

verificarRole(['Admin']);

$acao = $_GET['acao'] ?? 'index';
$user_id = (int) ($_SESSION['usuario_id'] ?? 0);

if ($user_id <= 0) {
    header('Location: ' . BASE_URL .
        '/app/controllers/LoginController.php?acao=login');
    exit;
}

$urlRetorno = BASE_URL .
    '/app/controllers/MinhaMarcaController.php';

$escaparLogo = static function (string $url) use ($user_id): string {
    $prefixo = BASE_URL . '/assets/uploads/minha-marca/' .
        $user_id . '/';

    // Apenas arquivos da pasta pessoal são aceitos.
    if (
        !str_starts_with($url, $prefixo) ||
        !preg_match(
            '~^' . preg_quote($prefixo, '~') .
                '[a-f0-9]{32}\.(png|jpg|webp)$~',
            $url
        )
    ) {
        return '';
    }

    return $url;
};

if ($acao === 'index') {
    $preferencias = null;
    $operacao = 'buscar';

    require __DIR__ . '/../models/MinhaMarca.php';

    if (!$preferencias) {
        $preferencias = [
            'nome_painel' => 'Gymflow',
            'tema' => 'dark',
            'cor_primaria' => '#ffb000',
            'cor_secundaria' => '#111827',
            'tema_predefinido' => 'padrao',
            'logo_url' => null
        ];
    }

    // Garante que a view não carregue URL externa.
    $preferencias['logo_url'] = $escaparLogo(
        (string) ($preferencias['logo_url'] ?? '')
    );

    if (empty($_SESSION['minha_marca_csrf'])) {
        $_SESSION['minha_marca_csrf'] =
            bin2hex(random_bytes(32));
    }

    $tituloPagina = 'Minha Marca';

    require __DIR__ . '/../views/minha_marca/index.php';
    exit;
}

if ($acao !== 'salvar') {
    header('Location: ' . $urlRetorno);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Método não permitido.');
}

// PROTEÇÃO CSRF
$token = $_POST['csrf_token'] ?? '';
$tokenSessao = $_SESSION['minha_marca_csrf'] ?? '';

if (
    !is_string($token) ||
    !is_string($tokenSessao) ||
    $tokenSessao === '' ||
    !hash_equals($tokenSessao, $token)
) {
    http_response_code(419);
    exit('Sessão expirada. Atualize a página.');
}

// BUSCAR PREFERÊNCIAS ATUAIS
$preferencias = null;
$operacao = 'buscar';
require __DIR__ . '/../models/MinhaMarca.php';

$logoAnterior = $escaparLogo(
    (string) ($preferencias['logo_url'] ?? '')
);

$nome = trim((string) ($_POST['nome_painel'] ?? 'Gymflow'));
$tema = (string) ($_POST['tema'] ?? 'dark');
$primaria = (string) ($_POST['cor_primaria'] ?? '#ffb000');
$secundaria = (string) ($_POST['cor_secundaria'] ?? '#111827');
$preset = (string) ($_POST['tema_predefinido'] ?? 'padrao');

if (
    $nome === '' ||
    mb_strlen($nome, 'UTF-8') > 100 ||
    !in_array($tema, ['light', 'dark'], true) ||
    !preg_match('/^#[0-9a-fA-F]{6}$/', $primaria) ||
    !preg_match('/^#[0-9a-fA-F]{6}$/', $secundaria) ||
    !in_array(
        $preset,
        ['padrao', 'dark', 'forest', 'sunset', 'ocean', 'personalizado'],
        true
    )
) {
    http_response_code(422);
    exit('As preferências informadas são inválidas.');
}

$logoUrl = $logoAnterior;
$novoArquivo = null;
$remover = ($_POST['remover_logo'] ?? '') === '1';

$pasta = dirname(__DIR__, 2) .
    '/assets/uploads/minha-marca/' . $user_id;

$novoUpload = isset($_FILES['logotipo']) &&
    ($_FILES['logotipo']['error'] ?? UPLOAD_ERR_NO_FILE)
    !== UPLOAD_ERR_NO_FILE;

if ($remover && $novoUpload) {
    http_response_code(422);
    exit('Escolha remover ou enviar um novo logotipo.');
}

if ($remover) {
    $logoUrl = '';
}

if ($novoUpload) {
    $arquivo = $_FILES['logotipo'];

    if (
        !is_array($arquivo) ||
        ($arquivo['error'] ?? 1) !== UPLOAD_ERR_OK ||
        !is_uploaded_file($arquivo['tmp_name'] ?? '')
    ) {
        http_response_code(422);
        exit('Não foi possível receber a imagem.');
    }

    // Limite de 2 MB
    if (
        ($arquivo['size'] ?? 0) <= 0 ||
        $arquivo['size'] > 2 * 1024 * 1024
    ) {
        http_response_code(422);
        exit('O logotipo deve ter no máximo 2 MB.');
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($arquivo['tmp_name']);

    $formatos = [
        'image/png' => 'png',
        'image/jpeg' => 'jpg',
        'image/webp' => 'webp'
    ];

    $dimensoes = @getimagesize($arquivo['tmp_name']);

    if (
        !isset($formatos[$mime]) ||
        $dimensoes === false ||
        !in_array($dimensoes['mime'], [$mime], true) ||
        $dimensoes[0] < 1 ||
        $dimensoes[1] < 1 ||
        $dimensoes[0] > 4096 ||
        $dimensoes[1] > 4096
    ) {
        http_response_code(422);
        exit('Utilize PNG, JPG ou WEBP válido, até 4096 pixels.');
    }

    if (!is_dir($pasta) && !mkdir($pasta, 0755, true)) {
        http_response_code(500);
        exit('Não foi possível criar a pasta do logotipo.');
    }

    $extensao = $formatos[$mime];
    $nomeArquivo = bin2hex(random_bytes(16)) . '.' . $extensao;

    $destino = $pasta . '/' . $nomeArquivo;

    if (!move_uploaded_file($arquivo['tmp_name'], $destino)) {
        http_response_code(500);
        exit('Não foi possível salvar o arquivo.');
    }

    $novoArquivo = $destino;
    $logoUrl = BASE_URL . '/assets/uploads/minha-marca/' .
        $user_id . '/' . $nomeArquivo;
}

// SALVAR NO BANCO
$dados = [
    'nome_painel' => $nome,
    'tema' => $tema,
    'cor_primaria' => $primaria,
    'cor_secundaria' => $secundaria,
    'tema_predefinido' => $preset,
    'logo_url' => $logoUrl
];

try {
    $operacao = 'salvar';
    require __DIR__ . '/../models/MinhaMarca.php';
} catch (Throwable $e) {
    // Não deixa um arquivo novo órfão se o SQL falhar.
    if ($novoArquivo !== null && is_file($novoArquivo)) {
        unlink($novoArquivo);
    }

    error_log('Minha Marca: ' . $e->getMessage());

    http_response_code(500);
    exit('Erro ao salvar preferências.');
}

// REMOVER ARQUIVO ANTIGO APÓS SALVAR
if ($logoAnterior !== '' && $logoAnterior !== $logoUrl) {
    $nomeAntigo = basename(parse_url(
        $logoAnterior,
        PHP_URL_PATH
    ));

    $caminhoAntigo = $pasta . '/' . $nomeAntigo;

    if (is_file($caminhoAntigo)) {
        unlink($caminhoAntigo);
    }
}

// ATUALIZAR SESSÃO
$_SESSION['nome_painel'] = $nome;
$_SESSION['tema'] = $tema;
$_SESSION['cor_primaria'] = $primaria;
$_SESSION['cor_secundaria'] = $secundaria;
$_SESSION['tema_predefinido'] = $preset;
$_SESSION['logo_url'] = $logoUrl;

// Renovar token após o salvamento.
$_SESSION['minha_marca_csrf'] = bin2hex(random_bytes(32));

header('Location: ' . $urlRetorno . '?msg=salvo');
exit;
