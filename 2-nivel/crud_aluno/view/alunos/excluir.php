<?php
include_once(__DIR__. "/../../controller/AlunoController.php" );

$id_aluno=0;

if(isset($_GET['id']))
    $id_aluno= $_GET['id'];

$AlunoCont = new AlunoController;
$AlunoCont->excluir($id_aluno);

header("location: listar.php");
