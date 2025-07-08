<?php
    require_once "./config/app.php";
    require_once "./app/view/inc/session_start.php";
    require_once "autoload.php";

    if(isset($_GET['view'])){
        $url = explode("/",$_GET['view']); # explode -> splits a string usinga a delimiter
    }else{
        $url=["login"];
    }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <?php require_once "./app/view/inc/head.php";?>
</head>
<body>
    <?php
        use app\controllers\viewsController;
        use app\controllers\loginController;

        $insLogin = new loginController();

        $viewsController= new viewsController();
        $vista=$viewsController->obtenerVistasControlador($url[0]);
        if($vista=="login" || $vista=="404"){
            require_once "./app/view/content/".$vista."_view.php";
        }else{
            require_once "./app/view/inc/navbar.php";
            require_once $vista;
        }
        require_once "./app/view/inc/script.php";
    ?>
</body>
</html>