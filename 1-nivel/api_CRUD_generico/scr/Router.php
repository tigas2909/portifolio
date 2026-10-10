<?php

class Router
{
    public static function handle(): array
    {
        $method = $_SERVER['REQUEST_METHOD'];

        $id = isset($_GET['id'])
            ? (int) $_GET['id']
            : null;

        $acao = $_GET['acao'] ?? null;

        return [
            'method' => $method,
            'id' => $id,
            'acao' => $acao
        ];
    }
}