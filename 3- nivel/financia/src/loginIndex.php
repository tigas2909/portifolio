<?php

require_once __DIR__ . '/../config/DatabaseConfig.php';
require_once __DIR__ . '/controller/LoginController.php';
require_once __DIR__ . '/jwt/ConfigJWT.php';

header('Content-Type: application/json; charset=utf-8');

/**
 * Retorna um erro padronizado e encerra a execução.
 */
$sendError = static function (int $status, string $message): void {
    http_response_code($status);

    echo json_encode([
        'success' => false,
        'errors' => [$message]
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    exit;
};

/*
 * Validação do token antes de executar as funções.
 */
try {
    if (ConfigJWT::validateRefreshToken($_COOKIE['token'] ?? null) === false) {
        $sendError(401, 'Token missing, invalid or expired.');
    }
} catch (Throwable $e) {
    error_log('Token validation error: ' . $e->getMessage());
    $sendError(500, 'An internal error occurred while validating the token.');
}

/*
 * Renovação do token.
 */
try {
    $refreshResult = LoginController::RefreshToken();

    if (($refreshResult['success'] ?? false) === false) {
        http_response_code(401);

        echo json_encode(
            $refreshResult,
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        );

        exit;
    }
} catch (Throwable $e) {
    error_log('Token refresh error: ' . $e->getMessage());
    $sendError(500, 'An internal error occurred while refreshing the token.');
}

/*
 * Recebe e decodifica o JSON.
 */
$body = file_get_contents('php://input');
$data = json_decode($body, true);

if (!is_array($data)) {
    $sendError(400, 'Invalid JSON.');
}

/*
 * Conexão com o banco e inicialização do controller.
 */
try {
    $conn = DatabaseConfig::conectar();
    $controller = new LoginController($conn);

    $method = $_SERVER['REQUEST_METHOD'];
    $type = $data['type'] ?? null;
    $action = $data['action'] ?? null;
    $datas = $data['data'] ?? [];

    $result = null;

    switch ($type) {
        case 'users':

            switch ($method) {
                case 'POST':

                    switch ($action) {
                        case 'login':
                            $result = $controller->Login($datas);
                            break;

                        case 'logout':
                            $result = LoginController::Logout();
                            break;

                        case 'create':
                            if (isset($datas['password']) && is_string($datas['password'])) {
                                $datas['password'] = password_hash(
                                    $datas['password'],
                                    PASSWORD_DEFAULT
                                );
                            }

                            $result = $controller->CreateUser($datas);
                            break;

                        case 'updatePassword':
                            if (isset($datas['password']) && is_string($datas['password'])) {
                                $datas['password'] = password_hash(
                                    $datas['password'],
                                    PASSWORD_DEFAULT
                                );
                            }

                            $result = $controller->UpdatePassword($datas);
                            break;

                        default:
                            $sendError(400, 'Invalid action.');
                    }

                    break;

                default:
                    header('Allow: POST');
                    $sendError(405, 'HTTP method not allowed for users.');
            }

            break;

        default:
            $sendError(400, 'Invalid type.');
    }

    echo json_encode(
        $result,
        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
    );

} catch (PDOException $e) {
    error_log('Database error: ' . $e->getMessage());
    $sendError(500, 'A database error occurred.');

} catch (Throwable $e) {
    error_log('API error: ' . $e->getMessage());
    $sendError(500, 'An internal server error occurred.');
}