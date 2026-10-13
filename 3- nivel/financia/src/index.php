<?php

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/DatabaseConfig.php';
require_once __DIR__ . '/controller/Controller.php';
require_once __DIR__ . '/jwt/ConfigJwt.php';
require_once __DIR__ . '/controller/LoginController.php';

/**
 * Retorna um erro padronizado e encerra a execução.
 */
$sendError = static function (int $status, string $message): void {
    http_response_code($status);

    echo json_encode([
        'success' => false,
        'errors' => [$message]
    ], JSON_UNESCAPED_UNICODE);

    exit;
};

/*
 * Verificação global do token.
 */
try {
    if (ConfigJWT::validateRefreshToken($_COOKIE['token'] ?? null) === false) {
        $sendError(401, 'Token missing, invalid or expired.');
    }

    LoginController::RefreshToken();

} catch (Throwable $e) {
    error_log('Token processing error: ' . $e->getMessage());

    $sendError(500, 'An internal error occurred while processing the token.');
}

/*
 * Leitura e decodificação do JSON.
 */
$body = file_get_contents('php://input');
$data = json_decode($body, true);

if (!is_array($data)) {
    $sendError(400, 'Invalid JSON.');
}

/*
 * Conexão com o banco e inicialização do Controller.
 */
try {
    $conn = DatabaseConfig::conectar();

    $controller = new Controller($conn);

    $method = $_SERVER['REQUEST_METHOD'];
    $type = $data['type'] ?? null;
    $action = $data['action'] ?? null;
    $datas = $data['data'] ?? [];

    $result = null;

    switch ($type) {

        case 'income':

            switch ($method) {

                case 'POST':

                    switch ($action) {

                        case 'create':
                            $result = $controller->IncomeCreate($datas);
                            break;

                        case 'update':
                            $sendError(501, 'Income update is not implemented yet.');

                        case 'delete':
                            $sendError(501, 'Income deletion is not implemented yet.');

                        default:
                            $sendError(400, 'Invalid action.');
                    }

                    break;

                case 'GET':

                    switch ($action) {

                        case 'selectAll':
                            $result = $controller->IncomeSelectALL();
                            break;

                        case 'selectById':
                            $result = $controller->IncomeSelectById($datas['id']);
                            break;

                        default:
                            $sendError(400, 'Invalid action.');
                    }

                    break;

                default:
                    header('Allow: GET, POST');
                    $sendError(405, 'HTTP method not allowed for income.');
            }

            break;

        case 'transaction':

            switch ($method) {

                case 'POST':

                    switch ($action) {

                        case 'create':
                            $result = $controller->TransactionCreate($datas);
                            break;

                        case 'update':
                            $sendError(501, 'Transaction update is not implemented yet.');

                        case 'delete':
                            $sendError(501, 'Transaction deletion is not implemented yet.');

                        default:
                            $sendError(400, 'Invalid action.');
                    }

                    break;

                case 'GET':

                    switch ($action) {

                        case 'selectAll':
                            $result = $controller->TransactionSelectALL();
                            break;

                        case 'selectById':
                            $result = $controller->TransactionSelectById($datas['id']);
                            break;

                        default:
                            $sendError(400, 'Invalid action.');
                    }

                    break;

                default:
                    header('Allow: GET, POST');
                    $sendError(405, 'HTTP method not allowed for transaction.');
            }

            break;

        default:
            $sendError(400, 'Invalid type.');
    }

    /*
     * Retorna o resultado produzido pelo Controller.
     */
    echo json_encode($result, JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {
    error_log('Database error: ' . $e->getMessage());

    $sendError(500, 'A database error occurred.');

} catch (Throwable $e) {
    error_log('API error: ' . $e->getMessage());

    $sendError(500, 'An internal server error occurred.');
}