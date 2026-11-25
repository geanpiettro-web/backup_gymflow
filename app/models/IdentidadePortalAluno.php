<?php
// =============================================
// GYMFLOW - IDENTIDADE DO PORTAL DO ALUNO
// app/models/IdentidadePortalAluno.php
// =============================================

if (!defined('BASE_URL')) {
    http_response_code(403);
    exit('Acesso não permitido.');
}

// IDENTIDADE PADRÃO

function identidadePadraoPortalAluno(): array
{
    return [
        'nome_painel' => 'GymCore',
        'cor_primaria' => '#C9A227',
        'cor_secundaria' => '#000000',
        'tema' => 'dark',
        'logo_url' => ''
    ];
}

// VALIDAÇÃO DAS CORES

function validarCorPortalAluno(
    $cor,
    string $padrao
): string {
    if (!is_string($cor)) {
        return $padrao;
    }

    $cor = trim($cor);

    return preg_match('/^#[0-9a-fA-F]{6}$/', $cor)
        ? $cor
        : $padrao;
}

// VALIDAÇÃO DO LOGOTIPO INSTITUCIONAL

function validarLogoPortalAluno($logo): string
{
    if (!is_string($logo)) {
        return '';
    }

    $logo = trim($logo);

    if ($logo === '') {
        return '';
    }

    if (str_starts_with(
        $logo,
        'assets/uploads/portfolio/'
    )) {
        $logo = BASE_URL . '/' . $logo;
    }

    if (str_starts_with(
        $logo,
        '/assets/uploads/portfolio/'
    )) {
        $logo = BASE_URL . $logo;
    }

    $prefixo = BASE_URL .
        '/assets/uploads/portfolio/';

    $padrao = '~^' .
        preg_quote($prefixo, '~') .
        '[a-zA-Z0-9_-]+\.(png|jpg|jpeg|webp)$~i';

    if (!preg_match($padrao, $logo)) {
        return '';
    }

    $caminho = dirname(__DIR__, 2) .
        '/assets/uploads/portfolio/' .
        basename($logo);

    return is_file($caminho)
        ? $logo
        : '';
}

// CONTEXTO AUTENTICADO DO ALUNO

function contextoIdentidadeAluno(): array
{
    if (
        ($_SESSION['usuario_role'] ?? '') !== 'Aluno'
    ) {
        throw new RuntimeException(
            'Perfil de usuário inválido.'
        );
    }

    $usuarioId = filter_var(
        $_SESSION['usuario_id'] ?? null,
        FILTER_VALIDATE_INT
    );

    $companyId = filter_var(
        $_SESSION['company_id'] ?? null,
        FILTER_VALIDATE_INT
    );

    $alunoId = filter_var(
        $_SESSION['aluno_id'] ?? null,
        FILTER_VALIDATE_INT
    );

    if (
        !$usuarioId ||
        !$companyId ||
        !$alunoId ||
        $usuarioId <= 0 ||
        $companyId <= 0 ||
        $alunoId <= 0
    ) {
        throw new RuntimeException(
            'Sessão do aluno inválida.'
        );
    }

    return [
        'usuario_id' => (int) $usuarioId,
        'company_id' => (int) $companyId,
        'aluno_id' => (int) $alunoId
    ];
}

// CARREGAR IDENTIDADE INSTITUCIONAL

function carregarIdentidadePortalAluno(
    PDO $pdo
): array {
    $contexto = contextoIdentidadeAluno();

    $identidade = identidadePadraoPortalAluno();

    $stmt = $pdo->prepare("
        SELECT
            pc.app_name,
            pc.primary_color,
            pc.secondary_color,
            pc.theme_mode,
            pc.logo_url,

            p.tema AS tema_aluno

        FROM users u

        INNER JOIN companies c
            ON c.id = u.company_id

        INNER JOIN alunos a
            ON a.id = u.aluno_id

        INNER JOIN filiais f
            ON f.id = a.filial_id
            AND f.company_id = u.company_id

        LEFT JOIN portfolio_config pc
            ON pc.company_id = c.id

        LEFT JOIN preferencias_usuario p
            ON p.user_id = u.id

        WHERE u.id = :usuario_id
          AND u.company_id = :company_id
          AND u.aluno_id = :aluno_id
          AND u.role = 'Aluno'

        LIMIT 1
    ");

    $stmt->execute([
        ':usuario_id' => $contexto['usuario_id'],
        ':company_id' => $contexto['company_id'],
        ':aluno_id' => $contexto['aluno_id']
    ]);

    $config = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$config) {
        throw new RuntimeException(
            'Vínculo institucional não encontrado.'
        );
    }

    // NOME INSTITUCIONAL

    $nome = trim(
        (string) ($config['app_name'] ?? '')
    );

    if ($nome !== '') {
        $identidade['nome_painel'] = $nome;
    }

    // COR PRIMÁRIA

    $identidade['cor_primaria'] =
        validarCorPortalAluno(
            $config['primary_color'] ?? null,
            $identidade['cor_primaria']
        );

    // COR SECUNDÁRIA

    $identidade['cor_secundaria'] =
        validarCorPortalAluno(
            $config['secondary_color'] ?? null,
            $identidade['cor_secundaria']
        );

    // LOGOTIPO

    $identidade['logo_url'] =
        validarLogoPortalAluno(
            $config['logo_url'] ?? null
        );

    // TEMA PADRÃO INSTITUCIONAL

    $temaPortfolio = $config['theme_mode'] ?? null;

    if (in_array(
        $temaPortfolio,
        ['light', 'dark'],
        true
    )) {
        $identidade['tema'] = $temaPortfolio;
    }

    // PREFERÊNCIA INDIVIDUAL DO ALUNO

    $temaAluno = $config['tema_aluno'] ?? null;

    if (in_array(
        $temaAluno,
        ['light', 'dark'],
        true
    )) {
        $identidade['tema'] = $temaAluno;
    }

    return $identidade;
}

// SALVAR SOMENTE O TEMA INDIVIDUAL

function salvarTemaPortalAluno(
    PDO $pdo,
    string $tema
): void {
    if (!in_array(
        $tema,
        ['light', 'dark'],
        true
    )) {
        throw new InvalidArgumentException(
            'Tema inválido.'
        );
    }

    $contexto = contextoIdentidadeAluno();

    // Valida o vínculo do aluno com a empresa.
    carregarIdentidadePortalAluno($pdo);

    $stmt = $pdo->prepare("
        INSERT INTO preferencias_usuario (
            user_id,
            tema
        )
        VALUES (
            :usuario_id,
            :tema
        )

        ON DUPLICATE KEY UPDATE
            tema = VALUES(tema)
    ");

    $stmt->execute([
        ':usuario_id' => $contexto['usuario_id'],
        ':tema' => $tema
    ]);

    $_SESSION['tema'] = $tema;
}
