<nav class="navbar">
    <div class="navbar-brand">
        <a class="navbar-item" href="<?php echo APP_URL;?>?view=dashboard">
            <img src="<?php echo APP_URL;?>app/view/img/Captura de pantalla 2025-06-20 191843.png" alt="Bulma" width="112" height="28">
        </a>
        <div class="navbar-burger" data-target="navbarExampleTransparentExample">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </div>

    <div id="navbarExampleTransparentExample" class="navbar-menu">

        <div class="navbar-start">
            <a class="navbar-item" href="<?php echo APP_URL;?>?view=dashboard">
                Dashboard
            </a>

            <div class="navbar-item has-dropdown is-hoverable">
                <a class="navbar-link" href="<?php echo APP_URL;?>?view=userUpdate/">
                    Usuarios
                </a>
                <div class="navbar-dropdown is-boxed">

                    <a class="navbar-item" href="<?php echo APP_URL;?>?view=userNew/">
                        Nuevo
                    </a>
                    <a class="navbar-item" href="<?php echo APP_URL;?>?view=userList">
                        Lista
                    </a>
                    <a class="navbar-item" href="<?php echo APP_URL;?>?view=userSearch">
                        Buscar
                    </a>

                </div>
            </div>
        </div>

        <div class="navbar-end">
            <div class="navbar-item has-dropdown is-hoverable">
                <a class="navbar-link">
                    ** <?php echo $_SESSION['usuario'];?>** 
                </a>
                <div class="navbar-dropdown is-boxed">

                    <a class="navbar-item" href="<?php echo APP_URL."?view=userUpdate/".$_SESSION['id']."/";?>">
                        Mi cuenta
                    </a>
                    <a class="navbar-item" href="<?php echo APP_URL."?view=userPhoto/".$_SESSION['id']."/";?>">
                        Mi foto
                    </a>
                    <hr class="navbar-divider">
                    <a class="navbar-item" href="<?php echo APP_URL;?>?view=logout/" id="btn_exit" >
                        Salir
                    </a>

                </div>
            </div>
        </div>

    </div>
</nav>