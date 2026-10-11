<?php
include_once(__DIR__."/../util/Connection.php");
include_once(__DIR__."/../model/Curso.php");
    class CursoDao{
        private PDO $conn;

        public function __construct() {
            $this->conn = Connection::getConnection();
        }
        
        public function list(){
            $sql = "SELECT * FROM cursos ORDER BY nome";
            $stm = $this->conn->prepare($sql);
            $stm->execute();
            $result = $stm->fetchAll();

            return $this->map($result);
        }

        private function map(array $result){
            $curso = array();
            foreach($result as $r){
                $cursos = new Curso();
                $cursos->setId($r['id']);
                $cursos->SetNome($r['nome']);
                $cursos->setTurno($r['turno']);

                array_push($curso, $cursos);
            }
            return $curso;
        }
    }
    