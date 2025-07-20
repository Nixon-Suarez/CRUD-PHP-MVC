<div class="container is-fluid mb-6">
    <h1 class="title">usuarios</h1>
    <h2 class="subtitle">Lista de Usuarios</h2>
</div>

<div class="container pb-6 pt-6">
    <?php
        use app\controllers\userController;
        $insUsuario = new userController();
        $pagina = isset($url[1]) ? $url[1] : 1;
        echo $insUsuario->listarUsuariosControlador($pagina, 15, $url[0], "");
    ?>
</div>