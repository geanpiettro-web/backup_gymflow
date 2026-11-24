
<?php
/**
 * GYMFLOW
 * Identidade visual das páginas de recuperação.
 *
 * Compartilha as cores do Portfólio com:
 * - Esqueci minha senha
 * - Redefinir senha
 *
 * Não utiliza as preferências pessoais do Minha Marca.
 */

if (!defined('BASE_URL')) {
    http_response_code(403);
    exit('Acesso não permitido.');
}

function gymflowIdentidadeRecuperacao(
    ?string $tokenRedefinicao = null
): array {

    /*
     * CORREÇÃO PRINCIPAL:
     * O conexao.php define $pdo no escopo global.
     * Esta declaração permite utilizá-lo na função.
     */
    global $pdo;

    $resultado = [
        'empresa_id' => null,
        'nome' => 'GymFlow',
        'primaria' => '#ffb000',
        'secundaria' => '#0b0b0b',
        'texto_primaria' => '#111827',
        'login_url' => BASE_URL .
            '/app/controllers/LoginController.php?acao=login'
    ];

    $validarId = static function ($valor): ?int {
        $id = filter_var(
            $valor,
            FILTER_VALIDATE_INT,
            ['options' => ['min_range' => 1]]
        );

        return $id === false || $id === null
            ? null
            : (int) $id;
    };

    /*
     * Identificar a academia da URL ou do formulário.
     */
    $empresaId = $validarId(
        $_GET['empresa'] ?? $_POST['empresa'] ?? null
    );

    /*
     * Para redefinição de senha, a empresa é
     * identificada pelo próprio token.
     */
    if ($tokenRedefinicao !== null) {

        if (!preg_match(
            '/^[a-f0-9]{64}$/i',
            $tokenRedefinicao
        )) {
            return $resultado;
        }

        try {
            require_once __DIR__ .
                '/../../config/conexao.php';

            if (!isset($pdo) || !($pdo instanceof PDO)) {
                throw new RuntimeException(
                    'Conexão com o banco indisponível.'
                );
            }

            $stmtToken = $pdo->prepare("
                SELECT company_id
                FROM users
                WHERE reset_token = ?
                  AND reset_token_expires_at > NOW()
                LIMIT 1
            ");

            $stmtToken->execute([$tokenRedefinicao]);

            $empresaToken = $stmtToken->fetchColumn();

            if ($empresaToken === false) {
                return $resultado;
            }

            // A empresa do token tem prioridade.
            $empresaId = $validarId($empresaToken);
        } catch (Throwable $e) {

            error_log(
                'GymFlow: falha ao identificar empresa ' .
                    'do token: ' . $e->getMessage()
            );

            return $resultado;
        }
    } else {

        /*
         * Recuperar empresa de origem do login,
         * quando a URL não contém o parâmetro.
         */
        if ($empresaId === null) {

            $referer = $_SERVER['HTTP_REFERER'] ?? '';

            if ($referer !== '') {

                $partes = parse_url($referer);

                if (is_array($partes)) {

                    $hostAtual = strtolower(
                        explode(
                            ':',
                            $_SERVER['HTTP_HOST'] ?? ''
                        )[0]
                    );

                    $hostReferer = strtolower(
                        (string) ($partes['host'] ?? '')
                    );

                    $caminho = $partes['path'] ?? '';

                    $caminhoLogin = BASE_URL .
                        '/app/controllers/LoginController.php';

                    if (
                        $hostAtual !== '' &&
                        $hostAtual === $hostReferer &&
                        $caminho === $caminhoLogin
                    ) {

                        parse_str(
                            $partes['query'] ?? '',
                            $queryReferer
                        );

                        $empresaId = $validarId(
                            $queryReferer['empresa'] ?? null
                        );
                    }
                }
            }
        }

        /*
         * Recuperar contexto preservado da
         * academia nas etapas de autenticação.
         */
        if ($empresaId === null) {
            $empresaId = $validarId(
                $_SESSION['auth_empresa_id'] ?? null
            );
        }
    }

    /*
     * Sem empresa identificada, utiliza
     * a identidade padrão GymFlow.
     */
    if ($empresaId === null) {
        return $resultado;
    }

    /*
     * Carregar a identidade pública
     * configurada no Portfólio.
     */
    try {

        require_once __DIR__ .
            '/../../config/conexao.php';

        if (!isset($pdo) || !($pdo instanceof PDO)) {
            throw new RuntimeException(
                'Conexão PDO não disponível.'
            );
        }

        $stmtMarca = $pdo->prepare("
            SELECT
                c.id,
                c.nome AS nome_empresa,
                pc.app_name,
                pc.primary_color,
                pc.secondary_color
            FROM companies c
            LEFT JOIN portfolio_config pc
                ON pc.company_id = c.id
            WHERE c.id = ?
            LIMIT 1
        ");

        $stmtMarca->execute([$empresaId]);

        $empresa = $stmtMarca->fetch(PDO::FETCH_ASSOC);

        if (!$empresa) {
            return $resultado;
        }

        $resultado['empresa_id'] = (int) $empresa['id'];

        /*
         * Nome da academia.
         */
        $nome = trim(
            (string) ($empresa['app_name'] ?? '')
        );

        if ($nome === '') {
            $nome = trim(
                (string) ($empresa['nome_empresa'] ?? '')
            );
        }

        if ($nome !== '') {
            $resultado['nome'] = $nome;
        }

        /*
         * Cores públicas do Portfólio.
         */
        $primaria = $empresa['primary_color'] ?? '';
        $secundaria = $empresa['secondary_color'] ?? '';

        if (
            is_string($primaria) &&
            preg_match('/^#[a-f0-9]{6}$/i', $primaria)
        ) {
            $resultado['primaria'] = $primaria;
        }

        if (
            is_string($secundaria) &&
            preg_match('/^#[a-f0-9]{6}$/i', $secundaria)
        ) {
            $resultado['secundaria'] = $secundaria;
        }

        /*
         * Calcular a cor de texto com
         * melhor contraste sobre a primária.
         */
        $hex = ltrim($resultado['primaria'], '#');

        $rgb = [
            hexdec(substr($hex, 0, 2)) / 255,
            hexdec(substr($hex, 2, 2)) / 255,
            hexdec(substr($hex, 4, 2)) / 255
        ];

        $linearizar = static function (float $v): float {
            return $v <= 0.04045
                ? $v / 12.92
                : (($v + 0.055) / 1.055) ** 2.4;
        };

        $luminancia =
            0.2126 * $linearizar($rgb[0]) +
            0.7152 * $linearizar($rgb[1]) +
            0.0722 * $linearizar($rgb[2]);

        $contrastePreto = ($luminancia + 0.05) / 0.05;
        $contrasteBranco = 1.05 / ($luminancia + 0.05);

        $resultado['texto_primaria'] =
            $contrastePreto >= $contrasteBranco
            ? '#000000'
            : '#ffffff';

        /*
         * Link de retorno ao login da
         * academia correta.
         */
        $resultado['login_url'] .=
            '&empresa=' . $resultado['empresa_id'];

        /*
         * Preservar o contexto para as próximas
         * etapas da recuperação.
         */
        $_SESSION['auth_empresa_id'] =
            $resultado['empresa_id'];
    } catch (Throwable $e) {

        error_log(
            'GymFlow: falha ao carregar identidade ' .
                'da recuperação: ' . $e->getMessage()
        );
    }

    return $resultado;
}
