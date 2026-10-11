<?php

require_once __DIR__ . '/../config/DatabaseConfig.php';
require_once __DIR__ . '/controller/Controller.php';
require_once 'Router.php';

header('Content-Type: application/json');

$body = file_get_contents('php://input');
$data = json_decode($body, true);

if (!is_array($data)) {
    http_response_code(400);

    echo json_encode([
        'success' => false,
        'errors' => ['Invalid JSON.']
    ]);

    exit;
}

$conn = DatabaseConfig::conectar();

$controller = new Controller($conn);

$method = $_SERVER['REQUEST_METHOD'];
$type = $data['type'] ?? null;
$action = $data['action'] ?? null;
$datas = $data['data'] ?? [];

$result = null;

switch ($type) {

    case "income":

        switch ($method) {

            case "POST":

                switch ($action) {

                    case 'create':
                        $result = $controller->IncomeCreate($datas);
                        break;

                    case 'update':
                        # code...
                        break;

                    case 'delete':
                        # code...
                        break;

                    default:
                        http_response_code(400);

                        echo json_encode([
                            'success' => false,
                            'errors' => ['Invalid action.']
                        ]);

                        exit;
                }

                break;

            case "GET":

                switch ($action) {

                    case 'selectAll':
                        $result = $controller->IncomeSelectALL();
                        break;

                    case 'selectById':
                        $result = $controller->IncomeSelectById($datas['id']);
                        break;

                    default:
                        http_response_code(400);

                        echo json_encode([
                            'success' => false,
                            'errors' => ['Invalid action.']
                        ]);

                        exit;
                }

                break;

            default:
                http_response_code(400);

                echo json_encode([
                    'success' => false,
                    'errors' => ['Invalid HTTP method.']
                ]);

                exit;
        }

        break;


    case "transaction":

        switch ($method) {

            case "POST":

                switch ($action) {

                    case 'create':
                        $result = $controller->TransactionCreate($datas);
                        break;

                    case 'update':
                        # code...
                        break;

                    case 'delete':
                        # code...
                        break;

                    default:
                        http_response_code(400);

                        echo json_encode([
                            'success' => false,
                            'errors' => ['Invalid action.']
                        ]);

                        exit;
                }

                break;

            case "GET":

                switch ($action) {

                    case 'selectAll':
                        $result = $controller->TransactionSelectALL();
                        break;

                    case 'selectById':
                        $result = $controller->TransactionSelectById($datas['id']);
                        break;

                    default:
                        http_response_code(400);

                        echo json_encode([
                            'success' => false,
                            'errors' => ['Invalid action.']
                        ]);

                        exit;
                }

                break;

            default:
                http_response_code(400);

                echo json_encode([
                    'success' => false,
                    'errors' => ['Invalid HTTP method.']
                ]);

                exit;
        }

        break;


    default:
        http_response_code(400);

        echo json_encode([
            'success' => false,
            'errors' => ['Invalid type.']
        ]);

        exit;
}

echo json_encode($result);