<?php 
    namespace app\controllers;
    use app\models\mainModel;

    class searchController extends mainModel {
        public function modulosBusquedaControlador($modulo){
            $listaModulos = [
                'userSearch'
            ];
            if(in_array($modulo, $listaModulos)){
                return false;
            }else{
                return true;
            }
        }
        
        #Controlador iniciar busqueda
        public function buscarDatosControlador(){
            $url = $this->limpiarCadena($_POST['modulo_url']);
            $texto = $this->limpiarCadena($_POST['txt_buscador']);

            if($this->modulosBusquedaControlador($url)){
                $alerta = [
                    "tipo" => "simple",
                    "titulo" => "Ocurrio un error inesperado",
                    "texto" => "No podemos procesar su busqueda, por favor intente nuevamente",
                    "icono" => "error"
                ];
                return json_encode($alerta);
                exit();
            }
            if($texto == ""){
                $alerta = [
                    "tipo" => "simple",
                    "titulo" => "Ocurrio un error inesperado",
                    "texto" => "Ingrese un termino de busqueda",
                    "icono" => "error"
                ];
                return json_encode($alerta);
                exit();
            }
            #Verificando integridad de los datos
            if($this->verificarDatos("[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ ]{1,30}", $texto)){
                $alerta = [
                    "tipo" => "simple",
                    "titulo" => "Ocurrio un error inesperado",
                    "texto" => "El nombre no coincide con el formato solicitado",
                    "icono" => "error"
                ];
                return json_encode($alerta);
                exit();
            }

            $_SESSION[$url] = $texto;
            $alerta = [
                "tipo" => "redireccionar",
                "titulo" => "Busqueda realizada",
                "texto" => "Se ha realizado la busqueda correctamente",
                "icono" => "success",
                "url" => APP_URL."?view=".$url."/1/"
            ];
            return json_encode($alerta);
        }
        #Controlador para eliminar busqueda
        public function eliminarBuscadorControlador(){
            $url = $this->limpiarCadena($_POST['modulo_url']);

            if($this->modulosBusquedaControlador($url)){
                $alerta = [
                    "tipo" => "simple",
                    "titulo" => "Ocurrio un error inesperado",
                    "texto" => "No podemos procesar su peticion, por favor intente nuevamente",
                    "icono" => "error"
                ];
                return json_encode($alerta);
                exit();
            }

            unset($_SESSION[$url]);
            $alerta = [
                "tipo" => "redireccionar",
                "titulo" => "Busqueda eliminada",
                "texto" => "Se ha eliminado la busqueda correctamente",
                "icono" => "success",
                "url" => APP_URL."?view=".$url."/1/"
            ];
            return json_encode($alerta);
        }
    }