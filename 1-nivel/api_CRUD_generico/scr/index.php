<?php

require_once __DIR__ . '/config/Connection.php';
require_once __DIR__ . '/controller/APIController.php';
require_once 'Router.php';

header('Content-Type: application/json');

$body = file_get_contents('php://input');

$data = json_decode($body, true);

$connection = Connection::create(
    $data['connection']
);

$array = Router::handle();

$cont = new APIController($connection);


// GET
if ($array['method'] == "GET") {

    // GET ALL
    if ($array['id'] == null) {

        // SELECT ALL
        if ($array['acao'] == null) {

            try {

                $dados = $cont->selectAll(
                    $data['info']['table']
                );

                http_response_code(200);

                echo json_encode([
                    "result" => true,
                    "data" => $dados
                ]);

            } catch (Throwable $e) {

                http_response_code(500);

                echo json_encode([
                    "result" => false,
                    "erro" => $e->getMessage()
                ]);
            }
        }

        // SELECT ATIVO
        else if ($array['acao'] == "selectAtivo") {

            try {

                $dados = $cont->selectByActive(
                    $data['info']['table']
                );

                http_response_code(200);

                echo json_encode([
                    "result" => true,
                    "data" => $dados
                ]);

            } catch (Throwable $e) {

                http_response_code(500);

                echo json_encode([
                    "result" => false,
                    "erro" => $e->getMessage()
                ]);
            }
        }

        // SELECT INATIVO
        else if ($array['acao'] == "selectInativo") {

            try {

                $dados = $cont->selectByDeactive(
                    $data['info']['table']
                );

                http_response_code(200);

                echo json_encode([
                    "result" => true,
                    "data" => $dados
                ]);

            } catch (Throwable $e) {

                http_response_code(500);

                echo json_encode([
                    "result" => false,
                    "erro" => $e->getMessage()
                ]);
            }
        }
    }

    // GET BY ID
    else {

        try {

            $dados = $cont->selectById(
                $data['info']['table'],
                $array['id']
            );

            if ($dados === null) {

                http_response_code(404);

                echo json_encode([
                    "result" => false,
                    "erro" => "Registro não encontrado"
                ]);

            } else {

                http_response_code(200);

                echo json_encode([
                    "result" => true,
                    "data" => $dados
                ]);
            }

        } catch (Throwable $e) {

            http_response_code(500);

            echo json_encode([
                "result" => false,
                "erro" => $e->getMessage()
            ]);
        }
    }
}


// POST
else if ($array['method'] == "POST") {

    try {

        $table = $data['info']['table'];

        $fields = $data['info'];

        unset($fields['table']);

        $resultado = $cont->insert(
            $table,
            $fields
        );

        if ($resultado) {

            http_response_code(201);

            echo json_encode([
                "result" => true
            ]);

        } else {

            http_response_code(500);

            echo json_encode([
                "result" => false
            ]);
        }

    } catch (Throwable $e) {

        http_response_code(500);

        echo json_encode([
            "result" => false,
            "erro" => $e->getMessage()
        ]);
    }
}


// PUT
else if ($array['method'] == "PUT") {

    // UPDATE
    if ($array['acao'] == "atualizar") {

        try {

            $table = $data['info']['table'];

            $fields = $data['info'];

            unset($fields['table']);

            $resultado = $cont->update(
                $array['id'],
                $table,
                $fields
            );

            if ($resultado) {

                http_response_code(200);

                echo json_encode([
                    "result" => true,
                    "data" => $cont->selectById(
                        $table,
                        $array['id']
                    )
                ]);

            } else {

                http_response_code(404);

                echo json_encode([
                    "result" => false,
                    "erro" => "Registro não encontrado"
                ]);
            }

        } catch (Throwable $e) {

            http_response_code(500);

            echo json_encode([
                "result" => false,
                "erro" => $e->getMessage()
            ]);
        }
    }

    // DELETE LÓGICO
    else if ($array['acao'] == "delete") {

        try {

            $table = $data['info']['table'];

            $resultado = $cont->delete(
                $table,
                $array['id']
            );

            if ($resultado) {

                http_response_code(200);

                echo json_encode([
                    "result" => true
                ]);

            } else {

                http_response_code(404);

                echo json_encode([
                    "result" => false,
                    "erro" => "Registro não encontrado"
                ]);
            }

        } catch (Throwable $e) {

            http_response_code(500);

            echo json_encode([
                "result" => false,
                "erro" => $e->getMessage()
            ]);
        }
    }

    // PUT SEM AÇÃO VÁLIDA
    else {

        http_response_code(405);

        echo json_encode([
            "result" => false,
            "erro" => "Ação não permitida"
        ]);
    }
}


// MÉTODO NÃO SUPORTADO
else {

    http_response_code(405);

    echo json_encode([
        "result" => false,
        "erro" => "Método HTTP não permitido"
    ]);
}