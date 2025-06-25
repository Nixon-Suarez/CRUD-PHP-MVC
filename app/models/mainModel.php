<?php
    namespace app\models;
    use \PDO;
    if(file_exists(__DIR__."/../../config/server.php")){
        require_once __DIR__."/../../config/server.php";
    }
    class mainModel{
        private $server = DB_SERVER;
        private $db = DB_NAME;
        private $user = DB_USER;
        private $pass = DB_PASS;

        // conexion BD
        protected function conexion(){
            $connect = new PDO("mysql:host=".$this->server.";dbname=".$this->db, $this->user, $this->pass);
            $connect->exec("SET CHARACTER SET utf8");# define que la codificacion de caracteres sea utf8
            return $connect;
        }

        protected function ejecutarConsulta($consulta){
            $sql = $this->conexion()->prepare($consulta);
            $sql->execute();
            return $sql;
        }
    }