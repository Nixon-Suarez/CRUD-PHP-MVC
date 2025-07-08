<?php   
    namespace app\controllers;
    use app\models\mainModel;

    class loginController extends mainModel {
        public function iniciarSesionControlador(){
            #Almacenar Datos
            $usuario = $this->limpiarCadena($_POST['login_usuario']);
            $clave = $this->limpiarCadena($_POST['login_clave']);

            // verificar campos obligatorios
            if($usuario=="" || $clave==""){
                echo "<script>
                            Swal.fire({
                                icon: 'error',
                                title: 'Ocurrió un error inesperado',
                                text: 'No has llenado todos los campos que son obligatorios'
                                });
					</script>";
            }
            else{
                #Verificando integridad de los datos
                if($this->verificarDatos("^[a-zA-Z0-9]{4,20}$", $usuario)){
                    echo 
                    "<script>
                            Swal.fire({
                                icon: 'error',
                                title: 'Ocurrió un error inesperado',
                                text: 'El USUARIO no coincide con el formato solicitado'
                                });
                    </script>";
                }elseif($this->verificarDatos("^[a-zA-Z0-9$@.-]{7,100}$", $clave)){
                    echo 
                    "<script>
                        Swal.fire({
                                icon: 'error',
                                title: 'Ocurrió un error inesperado',
                                text: 'La CLAVE no coincide con el formato solicitado'
                            });
                    </script>";
                }
                else{
                    # Verificando si el usuario es valido
                    $check_user = $this->ejecutarConsulta("SELECT * FROM usuario WHERE usuario_usuario='$usuario'");
                        if($check_user->rowCount()==1){
                            $check_user = $check_user->fetch(); # fetch devuelve un array asociativo de la consulta
                            if($check_user['usuario_usuario'] == $usuario && password_verify($clave, $check_user['usuario_clave'])){ #Valida si concide la clave encriptada con la que se guardo en el array y que el usuario se igual
                                $_SESSION['id'] = $check_user['usuario_id']; #almacena el id del usuario en la sesion
                                $_SESSION['nombre'] = $check_user['usuario_nombre']; #almacena el nombre del usuario en la sesion
                                $_SESSION['apellido'] = $check_user['usuario_apellido']; #almacena el apellido del usuario en la sesion
                                $_SESSION['usuario'] = $check_user['usuario_usuario']; #almacena el usuario en la sesion
                                $_SESSION['foto']=$check_user['usuario_foto'];
                                if(headers_sent()){
                                    echo "<script> window.location.href='".APP_URL."?view=dashboard/'; </script>";
                                }else{
                                    header("Location: ".APP_URL."?view=dashboard/"); # redirige a la pagina de inicio
                                }
                            }else{
                                    echo 
                                    "<script>
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Ocurrió un error inesperado',
                                            text: 'Usuario o clave incorrectos'
                                            });
                                    </script>";
                            }
                        }else{
                                echo 
                                "<script>
                                    Swal.fire({
                                            icon: 'error',
                                            title: 'Ocurrió un error inesperado',
                                            text: 'Usuario o clave incorrectos'
                                            });
                                </script>";
                    }
                }
            }
        }
        public function cerrarSesionControlador(){
            session_destroy(); 
            if(headers_sent()){
                echo "<script> window.location.href='".APP_URL."?view=login/'; </script>";
            }else{
                header("Location: ".APP_URL."?view=login/"); # redirige a la pagina de inicio
            }
        }
    }