<?php
include_once(__DIR__."/../../model/Aluno.php");
include_once(__DIR__. "/../../model/Curso.php");
include_once(__DIR__. "/../../controller/AlunoController.php");

//Verificar se o usuário clicou no gravar
if(isset($_POST['nome'])) {
    //Capturar os valores preechidos no formulário
    $nome = trim($_POST['nome']) ? Trim($_POST['nome']) : NULL;
    $idade = is_numeric($_POST['idade']) ? $_POST['idade'] : NULL;
    $estrangeiro= trim($_POST['estrang'])? trim($_POST['estrang']) : NULL;
    $idCurso = is_numeric($_POST['curso']) ? $_POST['curso'] : NULL;

    //criar um objeto aluno
    $aluno = new Aluno;
    $aluno->setNome($nome);
    $aluno->setIdade($idade);
    $aluno->setEstrangeiro($estrangeiro);

    if($idCurso){
        $curso = new Curso;
        $curso->setId($idCurso);
        $aluno->SetCurso($curso);
    }else{
        $aluno->setCurso(NULL);
    }

    $AlunoCont = new AlunoController;
    $AlunoCont->inserir($aluno);
    
    header("location: listar.php");
}


include_once(__DIR__ . "/form.php");
?>