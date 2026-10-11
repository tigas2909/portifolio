<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>tigas eletrick</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            background-color: #F5F5F5;
            color: #333333;
            padding: 0;
            margin: auto;
            max-width: 900px;
            font-family: Arial, sans-serif;
        }
        .header {
            display: grid;
            grid-template-areas: "menu cabecario";
            grid-template-columns: 100px auto;
            background-color: #EFEFEF;
            border-bottom: 1px solid #888888;
        }
        .men {
            padding: 0;
            height: 75px;
            width: 100px;
            font-size: 40px;
            grid-area: menu;
            background-color: #EFEFEF;
            color: #333333;
            border: none;
            cursor: pointer;
        }
        .cabecario {
            grid-area: cabecario;
            text-align: center;
            color: #333333;
            height: 70px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .menu {
            width: 100px;
            height: 150px;
            display: none;
            background-color: #EFEFEF;
            border: 1px solid #888888;
            padding: 10px;
        }
        .b1, .b2 {
            margin-top: 20px;
            width: 75px;
            height: 20px;
            background-color: #8d8e8fff;
            color: #FFFFFF;
            border: none;
            cursor: pointer;
        }
        .b1:hover, .b2:hover {
            background-color: #02488fff;
        }
        a {
            text-decoration: none;
        }
        
    </style>
</head>
<body>
    <div class="header">
        <div><button class="men">≡</button></div>
        <div class="cabecario"><h1>tigas eletrônicos</h1></div>
    </div>
    <div class="menu">
        <div>
            <a href="http://localhost/trabalho/cadastro.php"><button class="b1">cadastro</button></a>
        </div>
        <div>
            <a href="http://localhost/trabalho/lista.php"><button class="b2">lista</button></a>
        </div>
    </div>

    <script>
        const btn = document.querySelector('.men');
        const menu = document.querySelector('.menu');

        btn.addEventListener("click", () => {
            if (menu.style.display === "none" || menu.style.display === "") {
                menu.style.display = "block";
            } else {
                menu.style.display = "none";
            }
        });
    </script>
</body>
</html>