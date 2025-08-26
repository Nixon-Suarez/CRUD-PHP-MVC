<?php
    require_once "../../config/app.php";
    require_once "../view/inc/session_start.php";
    require_once "../../autoload.php";

    use app\controllers\searchController;
    if(isset($_POST['modulo_buscador'])){
        $insBuscador = new searchController();

        if($_POST['modulo_buscador'] == "buscar")
            echo $insBuscador->buscarDatosControlador();
        if($_POST['modulo_buscador'] == "eliminar")
            echo $insBuscador->eliminarBuscadorControlador();
    }else{
        session_destroy();
        header("Location: ".APP_URL."login.php/");
    }