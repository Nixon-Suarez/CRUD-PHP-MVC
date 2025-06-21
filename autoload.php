<?php
    spl_autoload_register(function($clase){
        $archivo = __DIR__ ."/".$clase.".php";

    }); # spl_autoload_register -> permite cargar automáticamente las clases cuando se instancian, sin necesidad de require_once