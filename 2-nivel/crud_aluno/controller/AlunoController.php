<?php

include_once(__DIR__ . "/../dao/AlunoDao.php");

class AlunoController {

    private AlunoDao $alunoDao;

    public function __construct() {
        $this->alunoDao = new AlunoDao();        
    }

    public function listar() {
        return $this->alunoDao->list();
    }

    public function inserir($aluno){
        $this->alunoDao->insert($aluno);

    }

    public function excluir($id_aluno){
        $this->alunoDao->drop($id_aluno);
    }
}
