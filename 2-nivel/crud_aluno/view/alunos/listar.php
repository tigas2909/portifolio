<?php

include_once(__DIR__ . "/../../controller/AlunoController.php");

$alunoCont = new AlunoController();
$alunos = $alunoCont->listar();
//print_r($alunos);


include_once(__DIR__ . "/../include/header.php");
?>


<h3>Listagem de Alunos</h3> 

<div>
    <a href="inserir.php">Inserir</a>
</div>

<table>
    <!-- Cabeçalho -->
    <tr>
        <th>ID</th>
        <th>Nome</th>
        <th>Idade</th>
        <th>Estrangeiro</th>
        <th>Curso</th>
        <th>editar</th>
        <th>excluir</th>
    </tr>

    <!-- Dados -->
    <?php foreach($alunos as $a): ?>
        <tr>
            <td><?= $a->getId() ?></td>
            <td><?= $a->getNome() ?></td>
            <td><?= $a->getIdade() ?></td>
            <td><?= $a->getEstrangeiroDesc() ?></td>
            <td><?= $a->getCurso()->getNomeTurno() ?></td>
            <td>
                <a href="editar.php?id=<?php echo $a->getId() ?>">
                    <img src="../../img/btn_editar.png" alt="">
                </a>
            </td>
            <td>
                <a href="excluir.php?id=<?php echo $a->getId() ?>" onclick="return confirm('confirma a exclusão?');">
                    <img src="../../img/btn_excluir.png" alt="">
                </a>
            </td>
        </tr>
    <?php endforeach; ?>
   
</table>

<?php
include_once(__DIR__ . "/../include/footer.php");
?>

