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

        public function limpiarCadena($cadena){
            $palabras = ["<script>", "</script>", "<script src>", "<script type=", "SELECT * FROM",  "DELETE FROM",  "INSERT INTO",  "DROP TABLE",  "DROP DATABASE", "TRUNCATE TABLA",   "SHOW TABLES;", "SHOW DATABASE;", "<?php", "?>", "--", "^", "<", ">", "[", "]", "==", ";", "::"];
            $cadena = trim($cadena); //quita espacios en blanco
            $cadena = stripslashes($cadena); //quita barras invertidas
            foreach ($palabras as $palabra) {
                $cadena = str_ireplace($palabra, "", $cadena); //reemplaza las palabras prohibidas por "" y lo guarda en $cadena
            }
            $cadena = trim($cadena);
            $cadena = stripslashes($cadena);
            $cadena = htmlspecialchars($cadena); //convierte caracteres especiales en entidades HTML
            return $cadena;
        }
        protected function verificarDatos($filtro, $cadena){
            if(preg_match("/^".$filtro."$/", $cadena)){
                return false; //si no coincide con el filtro devuelve false
            }else{
                return true; //si coincide con el filtro devuelve true
            }
        }
    }