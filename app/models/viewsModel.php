<?php
    namespace app\models;
    class viewsModel{
        protected function obtenerVistasModelo($vistas){
            $listaBlanca = [
                "dashboard",
                "userNew",
                "userList",
                "userSearch",
                "userUpdate",
                "userPhoto",
                "logout"
            ];

            if(in_array($vistas, $listaBlanca)) {
                if (is_file("./app/view/content/{$vistas}_view.php")) {
                    $contenido = "./app/view/content/{$vistas}_view.php";
                }else {
                    $contenido = "404";
                }
            }elseif ($vistas == "login" || $vistas == "index") {
                $contenido = "login";
            }else{
                $contenido = "404";
            }
            return $contenido;
        }
    }