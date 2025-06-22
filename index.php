<?php
    require_once "./config/app.php";
    require_once "./app/view/inc/session_start.php";
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
    <?php require_once "./app/view/inc/head.php";?>
</head>
<body>
    <?php require_once "./app/view/inc/script.php";?>
</body>
</html>