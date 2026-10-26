<?php

date_default_timezone_set('America/Sao_Paulo');

/*
 * CORS: com cookies, a origem precisa ser exata (nada de "*").
 */
$allowedOrigins = ['http://localhost:5173'];
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';

if (in_array($origin, $allowedOrigins, true)) {
    header('Access-Control-Allow-Origin: ' . $origin);
    header('Access-Control-Allow-Credentials: true');
    header('Vary: Origin');
}

header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Accept');
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

require_once __DIR__ . '/../config/DatabaseConfig.php';
require_once __DIR__ . '/controller/Controller.php';
require_once __DIR__ . '/jwt/ConfigJWT.php';
require_once __DIR__ . '/controller/loginController.php';

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
    $token = $_COOKIE['token'] ?? '';

    if (!is_string($token) || $token === '') {
        $sendError(401, 'Token missing, invalid or expired.');
    }

    if (ConfigJWT::validateRefreshToken($token) === false) {
        $sendError(401, 'Token missing, invalid or expired.');
    }

    LoginController::RefreshToken();

} catch (Throwable $e) {
    error_log('Token processing error: ' . $e->getMessage());

    $sendError(500, 'An internal error occurred while processing the token.');
}

/*
 * Leitura da requisição.
 * GET: type, action, id e month vêm da URL.
 * POST: o corpo é um JSON { "type": "...", "action": "...", "data": { ... } }.
 */
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $data = [
        'type' => $_GET['type'] ?? null,
        'action' => $_GET['action'] ?? null,
        'data' => array_intersect_key($_GET, array_flip(['id', 'month']))
    ];
} else {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!is_array($data)) {
        $sendError(400, 'Invalid JSON.');
    }
}

$type = is_string($data['type'] ?? null) ? $data['type'] : null;
$action = is_string($data['action'] ?? null) ? $data['action'] : null;
$datas = is_array($data['data'] ?? null) ? $data['data'] : [];

// Aceita snake_case e camelCase (select_all e selectAll, pay_installment e payInstallment).
if ($action !== null) {
    $action = lcfirst(str_replace('_', '', ucwords($action, '_')));
}

/*
 * Métodos permitidos: tudo por POST, e as leituras também por GET.
 */
$isRead = in_array($action, ['selectAll', 'selectById', 'selectInstallments'], true);

if ($method !== 'POST' && !($method === 'GET' && $isRead)) {
    header('Allow: GET, POST');
    $sendError(405, 'HTTP method not allowed.');
}

/*
 * Conexão com o banco, Controller e roteamento por tipo e ação.
 */
try {
    $conn = DatabaseConfig::conectar();

    $controller = new Controller($conn);

    $result = null;

    switch ($type) {

        case 'income':

            switch ($action) {
                case 'selectAll':
                    $result = $controller->IncomeSelectAll($datas);
                    break;

                case 'selectById':
                    $result = $controller->IncomeSelectById((int) ($datas['id'] ?? 0));
                    break;

                case 'create':
                    $result = $controller->IncomeCreate($datas);
                    break;

                case 'update':
                    $result = $controller->IncomeUpdate($datas);
                    break;

                case 'delete':
                    $result = $controller->IncomeDelete($datas);
                    break;

                default:
                    $sendError(400, 'Invalid action.');
            }

            break;

        case 'transaction':

            switch ($action) {
                case 'selectAll':
                    $result = $controller->TransactionSelectAll($datas);
                    break;

                case 'selectById':
                    $result = $controller->TransactionSelectById((int) ($datas['id'] ?? 0));
                    break;

                case 'selectInstallments':
                    $result = $controller->TransactionInstallments($datas);
                    break;

                case 'create':
                    $result = $controller->TransactionCreate($datas);
                    break;

                case 'update':
                    $result = $controller->TransactionUpdate($datas);
                    break;

                case 'pay':
                    $result = $controller->TransactionPay($datas);
                    break;

                case 'payInstallment':
                    $result = $controller->TransactionPayInstallment($datas);
                    break;

                case 'delete':
                    $result = $controller->TransactionDelete($datas);
                    break;

                default:
                    $sendError(400, 'Invalid action.');
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