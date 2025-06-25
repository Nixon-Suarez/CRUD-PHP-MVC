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

        protected function guardarDatos($tabla, $datos){
            # INSERTANDO LOS DATOS A LA BASE DE DATOS
            $sql ="INSERT INTO $tabla (";
            $Contador = 0;
            foreach($datos as $clave){
                if($Contador>=1){
                    $sql .= ",";
                }
                $sql .= $clave["campo_nombre"]; //agrega el nombre de la columna
                $Contador++;
            }

            $sql = ") VALUES (";

            $Contador = 0;
            foreach($datos as $clave){
                if($Contador>=1 && $Contador < count($datos) - 1){
                    $sql .= ",";
                }
                $sql .= $clave["campo_marcador"]; //agrega el nombre de la columna
                $Contador++;
            }
            foreach($datos as $clave){
                if($Contador>=1 && $Contador < count($datos) - 1){
                    $sql .= ",";
                }
                $sql .= $clave["campo_marcador"]; //agrega el nombre de la columna
                $Contador++;
            }

            $sql .= ")";

            $query = $this->conexion()->prepare($sql);

            foreach($datos as $clave){
                #                                ⬇️NOMBRE DEL MARCADOR            ⬇️Valor real
                $query->bindParam($clave["campo_marcador"], $clave["campo_valor"]); //bindParam -> Método que vincula o sustituye de la consulta SQL un marcador (:name) con el valor real de una variable PHP
            }
            $query->execute(); 
            return $query; //retorna el resultado de la consulta
        }

        public function seleccionarDatos($tipo, $tabla, $campo, $id){
            $tipo = $this->limpiarCadena($tipo);
            $tabla = $this->limpiarCadena($tabla);
            $campo = $this->limpiarCadena($campo);
            $id = $this->limpiarCadena($id);

            if($tipo == "Unico"){
                $sql = $this->conexion()->prepare("SELECT * FROM $tabla WHERE $campo=:id");
                $sql->bindParam(":id", $id);
            }elseif($tipo == "Normal"){
                $sql = $this->conexion()->prepare("SELECT $campo FROM $tabla");
            }
            $sql->execute();
            return $sql;
        }
    }