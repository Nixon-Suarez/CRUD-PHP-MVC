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

        protected function actualizarDatos($tabla, $datos, $condicion){
            $sql = "UPDATE $tabla SET ";
            $Contador = 0;
            foreach($datos as $clave){
                if($Contador>=1 && $Contador < count($datos) - 1){
                    $sql .= ",";
                }
                $sql .= $clave["campo_nombre"]."=".$clave["campo_marcador"]; //agrega el nombre de la columna
                $Contador++;
            }
            $sql = " WHERE ".$condicion["condicion_campo"]."=".$condicion["condicion_marcador"]; //agrega la condicion de la consulta
            $query = $this->conexion()->prepare($sql);
            foreach($datos as $clave){
                $query->bindParam($clave["campo_marcador"], $clave["campo_valor"]);
            }
            $query->bindParam($clave["condicion_marcador"], $clave["condicion_valor"]);
            $query->execute(); 
            return $query;
        }

        protected function eliminarDatos($tabla, $campo, $id){
            $sql = $this->conexion()->prepare("DELETE FROM $tabla WHERE $campo=:id");
            $sql->bindParam(":id", $id);
            $sql->execute();
            return $sql; 
        }

        public function paginadorTablas($pagina, $total_paginas, $url, $botones){
            $tabla = '<nav class="pagination is-centered is-rounded" role="navigation" aria-label="pagination">';

            if($pagina <= 1){ # si la pagina es menor o igual a 1 se desactiva el boton de anterior
                $tabla .= '
                <a class="pagination-previous is-disabled" disabled> Anterior</a>
                <ul class="pagination-list">
                ';   
            }else{ # si la pagina es mayor a 1 se activa el boton de anterior
                $tabla .= '
                <a class="pagination-previous" href="'.$url.($pagina-1).'/">Anterior</a>
                <ul class="pagination-list">
                    <li><a class="pagination-link" href="'.$url.'.1/">1</a></li> <!-- se crea un boton con el numero 1 y se le asigna la url que lleva a la pagina uno -->
                    <li><span class="pagination-elliosis">&hellip;</span></li> <!-- son los ... -->
                '; 
            }

            $ContadorI = 0;
            for($i = $pagina; $i <= $total_paginas && $ContadorI < $botones; $i++) { # se inicia un ciclo for que va desde la pagina actual hasta la cantidad de paginas y se limita a la cantidad de botones
                $tabla .= '<li><a class="pagination-link" href="'.$url.$i.'/">'.$i.'</a></li>'; # se crea un boton con el numero de la pagina y se le asigna la url correspondiente
                $ContadorI++;
            }
            
            if($pagina == $total_paginas){ # si la pagina es igual a la cantidad de paginas se desactiva el boton de siguiente
                $tabla .= '
                </ul>
                <a class="pagination-next" is-disabled disabled>Siguiente</a>
                ';   
            }else{ # si la pagina es menor a la cantidad de paginas se activa el boton de siguiente
                $tabla .= '
                    <li><span class="pagination-elliosis">&hellip;</span></li>
                    <li><a class="pagination-link" href="'.$url.$total_paginas.'/">'
                    .$total_paginas.'</a></li> <!-- se crea un boton con el numero maximo de paginas y se le asigna la url que lleva a la pagina final -->
                </ul>
                <a class="pagination-next" href="'.$url.($pagina+1).'/">Siguiente</a>
                '; 
            }

            $tabla .= '</nav>';
            return $tabla;
        }
    }