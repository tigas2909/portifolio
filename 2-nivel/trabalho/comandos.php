<?php
define("DIR_BANCO", "banco"); // Certo: sem barra no início

function salvar($array, $arquivo){
    $json = json_encode($array, JSON_PRETTY_PRINT);
    file_put_contents(DIR_BANCO . "/" . $arquivo, $json); // Correto
}

function buscar($arquivo): array {
    $dados = array();
    $nArquivo = DIR_BANCO . "/" . $arquivo;

    if (file_exists($nArquivo)) {
        $json = file_get_contents($nArquivo);
        $dados = json_decode($json, true);
    }

    return $dados;
}

function excluir($arquivo, $id){
    $dados = buscar($arquivo);

    // array_filter NÃO reindexa, então usamos array_values depois
    $dadosFiltrados = array_filter($dados, function($item) use ($id) {
        return $item["id"] !== $id;
    });

    salvar(array_values($dadosFiltrados), $arquivo);
}

function filtrar($arquivos) {
    $produtos = buscar("banco.json");

    if ($arquivos == 0) {
        return $produtos;
    }
    
    $resultado = [];

    foreach($produtos as $p){
        if(isset($p["selecao"]) && $p["selecao"] == $arquivos){
            $resultado[] = $p;
        }
    }

    return $resultado;
}
