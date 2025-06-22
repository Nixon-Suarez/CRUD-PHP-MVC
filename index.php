<?php
    require_once "./config/app.php";
    require_once "./app/views/inc/session_start.php";
    require_once "autoload.php";

    if(isset($_GET['views'])){
        $url = explode("/",$_GET['views']); # explode -> splits a string usinga a delimiter
    }else{
        $url=["login"];
    }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <?php require_once "./app/views/inc/head.php";?>
</head>
<body>
    <?php
        use app\controllers\viewsController;

        $viewsController= new viewsController();
        $vista=$viewsController->obtenerVistasControlador($url[0]);
        if($vista=="login" || $vista=="404"){
            require_once "./app/views/content/".$vista."_view.php";
        }else{
            require_once $vista;
        }
        
        require_once "./app/view/inc/script.php";
    ?>
</body>
</html>