<?php 
    namespace app\controllers;
    use app\models\mainModel;

    class userController extends mainModel {
        public function registrarUsuarioControlador(){
            // Almacenar Datos
            $usuario_nombre = $this->limpiarCadena($_POST['usuario_nombre']);
            $usuario_apellido = $this->limpiarCadena($_POST['usuario_apellido']);
            $usuario = $this->limpiarCadena($_POST['usuario_usuario']);
            $usuario_email = $this->limpiarCadena($_POST['usuario_email']);
            $usuario_clave_1 = $this->limpiarCadena($_POST['usuario_clave_1']);
            $usuario_clave_2 = $this->limpiarCadena($_POST['usuario_clave_2']);

            // verificar campos obligatorios
            if($usuario_nombre=="" || $usuario_apellido=="" || $usuario=="" || $usuario_clave_1=="" || $usuario_clave_2==""){
                $alerta = [
                    "tipo" => "simple",
                    "titulo" => "Ocurrio un error inesperado",
                    "texto" => "No has llenado todos los campos que son obligatorios",
                    "icono" => "error"
                ];
                return json_encode($alerta);
                exit();
            }

            #Verificando integridad de los datos
            if($this->verificarDatos("^[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,40}$", $usuario_nombre)){
                $alerta = [
                    "tipo" => "simple",
                    "titulo" => "Ocurrio un error inesperado",
                    "texto" => "El nombre no coincide con el formato solicitado",
                    "icono" => "error"
                ];
                return json_encode($alerta);
                exit();
            }elseif($this->verificarDatos("^[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,40}$", $usuario_apellido)){
                $alerta = [
                    "tipo" => "simple",
                    "titulo" => "Ocurrio un error inesperado",
                    "texto" => "El apellido no coincide con el formato solicitado",
                    "icono" => "error"
                ];
                return json_encode($alerta);
                exit();
            }elseif($this->verificarDatos("^[a-zA-Z0-9]{4,20}$", $usuario)){
                $alerta = [
                    "tipo" => "simple",
                    "titulo" => "Ocurrio un error inesperado",
                    "texto" => "El usuario no coincide con el formato solicitado",
                    "icono" => "error"
                ];
                return json_encode($alerta);
                exit();
            }elseif($this->verificarDatos("^[a-zA-Z0-9$@.-]{7,100}$", $usuario_clave_1)){
                $alerta = [
                    "tipo" => "simple",
                    "titulo" => "Ocurrio un error inesperado",
                    "texto" => "La clave no coincide con el formato solicitado",
                    "icono" => "error"
                ];
                return json_encode($alerta);
                exit();
            }
            # Verificando si el email es valido y no se encuentra registrado
            if($usuario_email!=""){
                if(filter_var( $usuario_email, FILTER_VALIDATE_EMAIL)){ # verifica si el email es valido
                    $sql = "SELECT usuario_email FROM usuario WHERE usuario_email='$usuario_email'";
                    $check_email = $this->ejecutarConsulta($sql); # selecciona el email de la base de datos
                    if($check_email->rowCount()>0){
                        $alerta = [
                            "tipo" => "simple",
                            "titulo" => "Ocurrio un error inesperado",
                            "texto" => "El email no es valido",
                            "icono" => "error"
                        ];
                        return json_encode($alerta);
                        exit(); 
                    }
                    $check_email->closeCursor(); # cierra la consulta
                }else{
                    $alerta = [
                        "tipo" => "simple",
                        "titulo" => "Ocurrio un error inesperado",
                        "texto" => "El email no es valido",
                        "icono" => "error"
                    ];
                    return json_encode($alerta);
                    exit();
                }
            }
            # Verificando el usuario
            $sql = "SELECT usuario_usuario FROM usuario WHERE usuario_usuario='$usuario'";
            $check_usuaio = $this->ejecutarConsulta($sql); # selecciona el email de la base de datos
            if($check_usuaio->rowCount()>0){
                $alerta = [
                    "tipo" => "simple",
                    "titulo" => "Ocurrio un error inesperado",
                    "texto" => "El usuario ya se encuentra registrado",
                    "icono" => "error"
                ];
                return json_encode($alerta);
                exit(); 
            }
            $check_usuaio->closeCursor(); # cierra la consulta
            
            #Verificando si las claves son iguales
            if($usuario_clave_1!=$usuario_clave_2){
                $alerta = [
                    "tipo" => "simple",
                    "titulo" => "Ocurrio un error inesperado",
                    "texto" => "Las claves no coinciden",
                    "icono" => "error"
                ];
                return json_encode($alerta);
                exit();
            }else{
                $clave_procesada = password_hash($usuario_clave_1, PASSWORD_BCRYPT, ["cost" => 10]); # encripta la clave
            }
            // Directorio de imagenes
            $img_dir = "../view/fotos/";

            // Validar seleccion de una img
                        #⬇️nombre del imput
                                        // ⬇️ATTRIBUTO DEL ARCHIVO en este caso el nombre
            if($_FILES['usuario_foto']['name'] !="" && $_FILES['usuario_foto']['size']>0){
                //  creando directorio si no existe
                if(!file_exists($img_dir)) { 
                    if(!mkdir($img_dir, 0777)){ #0777 puede leer, escribir y ejecutar
                        $alerta = [
                            "tipo" => "simple",
                            "titulo" => "Ocurrio un error inesperado",
                            "texto" => "No se pudo crear el directorio",
                            "icono" => "error"
                        ];
                        return json_encode($alerta);
                        exit();
                    }
                }
                // limitar que tipo de archivo esta entrando (se valida con el tipo de mime)
                if(mime_content_type($_FILES['usuario_foto']['tmp_name']) != "image/jpeg" && mime_content_type($_FILES['usuario_foto']['tmp_name']) != "image/png" && mime_content_type($_FILES['usuario_foto']['tmp_name']) != "image/jpg"){
                    $alerta = [
                        "tipo" => "simple",
                        "titulo" => "Ocurrio un error inesperado",
                        "texto" => "Archivo no permitido, solo se permiten archivos JPG, JPEG y PNG",
                        "icono" => "error"
                    ];
                    return json_encode($alerta);
                    exit();
                }
                # limitar el peso del archivo
                if (($_FILES['usuario_foto']['size'] / 1024) > 5120) {
                    $alerta = [
                        "tipo" => "simple",
                        "titulo" => "Ocurrio un error inesperado",
                        "texto" => "El archivo no puede pesar mas de 5MB",
                        "icono" => "error"
                    ];
                    return json_encode($alerta);
                    exit();
                }
                #Extencion del archivo
                switch(mime_content_type($_FILES['usuario_foto']['tmp_name'])) {
                    case 'image/jpeg':
                        $img_ext = '.jpg'; // si es un archivo jpg
                        break;
                    case 'image/png':
                        $img_ext = '.png'; // si es un archivo png
                        break;
                    case 'image/jpg':
                        $img_ext = '.jpg'; // si es un archivo jpg
                        break;
                }

                chmod($img_dir, 0777); // cambia los permisos del directorio a 0777 osea puede leer, escribir y ejecutar
                
                // renombra la foto
                $foto = str_ireplace(" ", "_", $usuario_nombre) . "_" . rand(100, 999) . $img_ext; // renombra la foto con el nombre del usuario y apellido y un numero aleatorio

                // mover la img al directorio de imagenes
                if(!move_uploaded_file($_FILES['usuario_foto']['tmp_name'], $img_dir . $foto)) {
                    $alerta = [
                        "tipo" => "simple",
                        "titulo" => "Ocurrio un error inesperado",
                        "texto" => "Error al subir el archivo, intente nuevamente",
                        "icono" => "error"
                    ];
                    return json_encode($alerta);
                    exit();
                }
            }else{
                $foto =""; // si no se selecciona una imagen, se deja la variable foto vacia
            }

            $usuario_datos_reg = [
                [
                    "campo_nombre" => "usuario_nombre",
                    "campo_marcador" => ":usuario_nombre",
                    "campo_valor" => $usuario_nombre
                ],
                [
                    "campo_nombre" => "usuario_apellido",
                    "campo_marcador" => ":usuario_apellido",
                    "campo_valor" => $usuario_apellido
                ],
                [
                    "campo_nombre" => "usuario_usuario",
                    "campo_marcador" => ":usuario_usuario",
                    "campo_valor" => $usuario
                ],
                [
                    "campo_nombre" => "usuario_email",
                    "campo_marcador" => ":usuario_email",
                    "campo_valor" => $usuario_email
                ],
                [
                    "campo_nombre" => "usuario_clave",
                    "campo_marcador" => ":usuario_clave",
                    "campo_valor" => $clave_procesada
                ],
                [
                    "campo_nombre" => "usuario_foto",
                    "campo_marcador" => ":usuario_foto",
                    "campo_valor" => $foto
                ],
                [
                    "campo_nombre" => "usuario_creado",
                    "campo_marcador" => ":usuario_creado",
                    "campo_valor" => date("Y-m-d H:i:s") // fecha de creacion del usuario
                ],
                [
                    "campo_nombre" => "usuario_actualizado",
                    "campo_marcador" => ":usuario_actualizado",
                    "campo_valor" => date("Y-m-d H:i:s") // fecha de creacion del usuario
                ]
            ];

            $registro_usuario = $this->guardarDatos("usuario", $usuario_datos_reg); // inserta los datos en la base de datos

            if($registro_usuario->rowCount()==1){
                $alerta = [
                    "tipo" => "limpiar",
                    "titulo" => "Usuario registrado",
                    "texto" => "El usuario ".$usuario_nombre." ha sido registrado exitosamente",
                    "icono" => "success"
                ];
            }else{
                if(is_file($img_dir.$foto)){ #valida si la img existe en el directorio
                    chmod($img_dir.$foto, 0777);
                    unlink($img_dir.$foto); #si existe la elimina
                }
                $alerta = [
                    "tipo" => "simple",
                    "titulo" => "Ocurrio un error inesperado",
                    "texto" => "No se pudo registrar el usuario, intente nuevamente",
                    "icono" => "error"
                ];
            }
            return json_encode($alerta);
        }
    }