<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once("comandos.php");

$produto = buscar('banco.json');
$marca = "";
$modelo = "";
$tipo = 0;

$arma = "";
$comp_arma = "";

$arma_ram = "";
$comp_ram = "";

$processador = "";

$erros=array();
$cadastro="";

if (isset($_POST["tipoele"])) {
    $tipo = trim($_POST["tipoele"]);
    $outro = $_POST["outro"] ?? "";

    $marca = trim($_POST["marca"]);
    $modelo = trim($_POST["modelo"]);

    $arma = trim($_POST["arma"]);
    $comp_arma = trim($_POST["carma"]);

    $arma_ram = trim($_POST["arma_ram"]);
    $comp_ram = trim($_POST["complemento_ram"]);

    $processador = trim($_POST["processador"]);

    // Montagem dos campos finais
    $armazenamento = trim($arma . " " . $comp_arma);
    $memoria_ram = trim($arma_ram . " " . $comp_ram);
    $tipo_final="";
    if($tipo == 1){
        $tipo_final="celular";
    }if($tipo == 2){
        $tipo_final="pc";
    
    }if($tipo == 3){
        $tipo_final="notebook";
    }
    if($tipo == 4){
        $tipo_final="video_game";
    } if($tipo == 5){
        $tipo_final= $outro;
    }

    if($tipo==""){
        array_push($erros, "selecione o tipo do produto");
    }
    if($marca == ""){
        array_push($erros, "escreva a marca do produto");
    }
    if($modelo ==""){
        array_push($erros, "escreva o modelo do produto");
    }
    if($arma <= 0){
        array_push($erros, "o Armazenamento deve ser maior que 0");
    }
    if($comp_arma == ""){
        array_push($erros, "selecione o tipo de armazenamento (GB)");
    }
    if($arma_ram <= 0){
        array_push($erros, "a memoria ram deve ser maior que zero");
    }
    if($comp_ram == ""){
        array_push($erros, "selecione o tipo de memoria ram (GB)");
    }
    if($processador == ""){
        array_push($erros, "escreva o processador");
    }
    if(empty($erros)){
        
        $novo = [
            "id" => uniqid(),
            "tipo" => $tipo_final,
            "marca" => $marca,
            "modelo" => $modelo,
            "armazenamento" => $armazenamento,
            "processador" => $processador,
            "memoria_ram" => $memoria_ram, 
            "selecao" => $tipo
        ];

        array_push($produto, $novo);

        salvar($produto, "banco.json"); 

        $cadastro="Cadastrado com sucesso";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Produto</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background-color: #F5F5F5;
            font-family: Arial, sans-serif;
            color: #333333;
            max-width: 900px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            display: grid;
            grid-template-areas: "voltar cabecalho";
            grid-template-columns: 50px auto;
            align-items: center;
            border-bottom: 1px solid #D3D3D3;
            padding: 10px 0;
        }

        .voltar {
            font-size: 24px;
            background: none;
            border: none;
            cursor: pointer;
            color: #333333;
        }

        .cabecalho h1 {
            text-align: center;
            font-size: 24px;
            color: #333333;
        }


        .main form {
            background-color: #FFFFFF;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }

        label {
            display: block;
            margin-bottom: 10px;
            font-size: 14px;
            color: #333333;
        }

        input, select, button {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #D3D3D3;
            border-radius: 4px;
            font-size: 16px;
        }

        button {
            background-color: #EAEAEA;
            color: #333333;
            cursor: pointer;
            border: none;
        }

        button:hover {
            background-color: #D3D3D3;
        }


        .erro {
            color: red;
            margin-bottom: 20px;
        }

        .sucesso {
            color: green;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <a href="http://localhost/trabalho/index.php">
            <button class="voltar">↩</button>
        </a>
        <div class="cabecalho">
            <h1>Cadastro de Produto</h1>
        </div>
    </div>
    <div class="erro">
        <?php  foreach($erros as $e): ?>
            <?= $e  ?><br>
        <?php endforeach; ?>
    </div>
    <div class="sucesso">
        <?= $cadastro ?>
    </div>
    <div class="main">
        <form action="" method="post">
            <label for="tipoele">
                Tipo de eletrônico:
                <select name="tipoele" id="tipo">
                    <option value="">--- Selecione ---</option>
                    <option value="1">Celular</option>
                    <option value="2">PC</option>
                    <option value="3">Notebook</option>
                    <option value="4">Video Game</option>
                </select>
            </label>

            <label for="marca">
                Marca:
                <input type="text" name="marca" id="marca" placeholder="Marca do eletrônico">
            </label>

            <label for="modelo">
                Modelo:
                <input type="text" name="modelo" id="modelo" placeholder="Modelo do eletrônico">
            </label>

            <label for="arma">
                Armazenamento:
                <input type="number" name="arma" id="arma" placeholder="64 GB">
                <select name="carma">
                    <option value="">--- Complemento ---</option>
                    <option value="KB">KB</option>
                    <option value="MB">MB</option>
                    <option value="GB">GB</option>
                    <option value="TB">TB</option>
                    <option value="PB">PB</option>
                </select>
            </label>

            <label for="processador">
                Processador:
                <input type="text" name="processador" id="processador" placeholder="Processador">
            </label>

            <label for="arma_ram">
                Memória RAM:
                <input type="number" name="arma_ram" id="arma_ram" placeholder="64 GB">
                <select name="complemento_ram">
                    <option value="">--- Complemento ---</option>
                    <option value="KB">KB</option>
                    <option value="MB">MB</option>
                    <option value="GB">GB</option>
                    <option value="TB">TB</option>
                    <option value="PB">PB</option>
                </select>
            </label>

            <button type="submit">Cadastrar</button>
        </form>
    </div>
</body>
</html>