<?php

$origemPermitida = 'http://localhost:5173';

header('Access-Control-Allow-Origin: ' . $origemPermitida);
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Accept, Authorization');
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

$sendError = static function (int $status, string $message): void {
    http_response_code($status);

    echo json_encode([
        'success' => false,
        'errors' => [$message]
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    exit;
};

$body = file_get_contents('php://input');
$data = json_decode($body, true);

if (!is_array($data)) {
    $sendError(400, 'Invalid JSON.');
}

try {
    require_once __DIR__ . '/../config/DatabaseConfig.php';
    require_once __DIR__ . '/controller/loginController.php';
    require_once __DIR__ . '/jwt/ConfigJWT.php';

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
                            if (
                                isset($datas['password']) &&
                                is_string($datas['password'])
                            ) {
                                $datas['password'] = password_hash(
                                    $datas['password'],
                                    PASSWORD_DEFAULT
                                );
                            }

                            $result = $controller->CreateUser($datas);
                            break;

                        case 'updatePassword':
                            if (
                                isset($datas['password']) &&
                                is_string($datas['password'])
                            ) {
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
    http_response_code(500);

    echo json_encode([
        'success' => false,
        'error' => 'Database error',
        'message' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    exit;

} catch (Throwable $e) {
    http_response_code(500);

    echo json_encode([
        'success' => false,
        'error' => 'Internal server error',
        'message' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    exit;
}