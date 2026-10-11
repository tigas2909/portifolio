<?php
require_once("comandos.php");

$filtro = 0;


if (isset($_POST["filtro"])) {
    $filtro = $_POST["filtro"];
}

if (isset($_POST["excluir_id"])) {
    excluir("banco.json", $_POST["excluir_id"]);
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

$produto = filtrar($filtro);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>lista de produtos</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background-color: #F5F5F5;
            color: #424242;
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            flex-direction: column;
        }

        .header {
            background-color: #9E9E9E;
            color: #FFFFFF;
            padding: 10px;
            text-align: center;
            width: 100%;
            box-sizing: border-box;
        }

        button {
            background-color: #424242;
            color: #FFFFFF;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
        }

        button:hover {
            background-color: #000000;
        }

        .main {
            width: 80%;
            max-width: 10000px;
            margin-top: 20px;
            border: 1px solid #9E9E9E;
            padding: 20px;
            background-color: #FFFFFF;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            
        }

        table {
            width: 100%;
            margin: 20px 0;
            border-collapse: collapse;
            text-align: center;
        }

        th, td {
            padding: 10px;
            border: 1px solid #9E9E9E;
        }

        th {
            background-color: #9E9E9E;
            color: #FFFFFF;
        }

        form {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        label {
            margin: 10px 0;
            font-weight: bold;
        }

        input[type="text"], input[type="number"], select {
            width: 100%;
            max-width: 300px;
            padding: 8px;
            margin-top: 5px;
            border: 1px solid #9E9E9E;
            border-radius: 4px;
        }

        button[type="submit"] {
            width: 150px;
            margin-top: 20px;
            padding: 10px;
            background-color: #424242;
            color: #FFFFFF;
            border: none;
            cursor: pointer;
            border-radius: 4px;
        }

        button[type="submit"]:hover {
            background-color: #000000;
        }

        .filtro {
            margin-top: 20px;
            text-align: center;
        }

        .filtro select, .filtro button {
            padding: 8px;
            margin: 5px;
            border: 1px solid #9E9E9E;
            border-radius: 4px;
        }
        th{
            border: 1px solid #333333;
        }
    </style>
</head>
<body>
    <div class="header">
        <a href="http://localhost/trabalho/index.php
        ">
        <button class="voltar">↩</button>
        </a>
        <div class="ca"><h1>Lista de Produtos</h1></div>
    </div>
    <div class="main">
        <table border 1>
            <tr>
                <th>tipo eletronico</th>
                <th>marca</th>
                <th>modelo</th>
                <th>armazenamento</th>
                <th>memoria ram</th>
                <th>processador</th>
                <th>excluir</th>
            </tr>
            <?php foreach($produto as $index => $l): ?>
                <tr>
                    <td><?= $l['tipo'] ?></td>
                    <td><?= $l['marca'] ?></td>
                    <td><?= $l['modelo'] ?></td>
                    <td><?= $l['armazenamento'] ?></td>
                    <td><?= $l['memoria_ram'] ?></td>
                    <td><?= $l['processador'] ?></td>
                    <td>
                        <form method="POST" onsubmit="return confirm('Tem certeza que deseja excluir este item?');">
                            <input type="hidden" name="excluir_id" value="<?= $l["id"] ?>">
                            <button type="submit">🗑️</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <div class="filtro">
        <form action="" method="post">
        <select name="filtro" id="">
            <option value="0" <?= $filtro=='0'? 'selected' : ''?>>todos os produtos</option>
            <option value="1" <?= $filtro=='1'? 'selected' : ''?>>celular</option>
            <option value="2" <?= $filtro=='2'? 'selected' : ''?>>pc</option>
            <option value="3" <?= $filtro=='3'? 'selected' : ''?>>notebook</option>
            <option value="4" <?= $filtro=='4'? 'selected' : ''?>>video_game</option>
        </select>
        <button type="submit">filtrar</button>
        </form>
    </div>
</body>
</html>