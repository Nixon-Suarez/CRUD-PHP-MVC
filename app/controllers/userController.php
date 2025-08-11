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
                        $foto = '.jpg'; // si es un archivo jpg
                        break;
                    case 'image/png':
                        $foto = '.png'; // si es un archivo png
                        break;
                    case 'image/jpg':
                        $foto = '.jpg'; // si es un archivo jpg
                        break;
                }

                chmod($img_dir, 0777); // cambia los permisos del directorio a 0777 osea puede leer, escribir y ejecutar
                
                // renombra la foto
                $foto = str_ireplace(" ", "_", $usuario_nombre) . "_" . rand(100, 999) . $foto; // renombra la foto con el nombre del usuario y apellido y un numero aleatorio

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
                $foto = ""; // si no se selecciona una imagen, se deja la variable foto vacia
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

        public function listarUsuariosControlador($pagina, $registros, $url, $busqueda){
            $pagina = $this->limpiarCadena($pagina);
            $registros = $this->limpiarCadena($registros);
            $url = $this->limpiarCadena($url);
            $url= APP_URL ."?view=". $url."/";
            $busqueda = $this->limpiarCadena($busqueda);
            $tabla = "";
            $pagina = (isset($pagina) && $pagina>0) ? (int)$pagina : 1;
            $inicio = ($pagina>0) ? (($registros * $pagina) - $registros) : 0; #ej existen 30 registros y se muestran 10 por pagina, entonces si la pagina es 1, el inicio es 0, si la pagina es 2, el inicio es 10, etc.
            if(isset($busqueda) && $busqueda != ""){ # si la variable busqueda no esta vacia  Genera la consulta que treaera los usuarios y el total de usuarios
                $consulta_datos = "SELECT * FROM usuario 
                                WHERE 
                                    ((usuario_id != ".$_SESSION['id']." AND usuario_id != '1') 
                                    AND (usuario_nombre LIKE '%$busqueda%' OR usuario_apellido LIKE '%$busqueda%' OR usuario_email LIKE '%$busqueda%' OR usuario_usuario LIKE '%$busqueda%'))
                                ORDER BY usuario_nombre ASC LIMIT $inicio,$registros;";

                $consulta_total = "SELECT COUNT(usuario_id) FROM usuario 
                                WHERE 
                                    ((usuario_id != ".$_SESSION['id']." AND usuario_id != '1') 
                                    AND (usuario_nombre LIKE '%$busqueda%' OR usuario_apellido LIKE '%$busqueda%' OR usuario_email LIKE '%$busqueda%' OR usuario_usuario LIKE '%$busqueda%'))
                                ORDER BY usuario_nombre ASC LIMIT $inicio,$registros;";

            }else{
                $consulta_datos = "SELECT * FROM usuario WHERE usuario_id != ".$_SESSION['id']." AND usuario_id != '1' ORDER BY usuario_nombre ASC LIMIT $inicio,$registros;";

                $consulta_total = "SELECT COUNT(usuario_id) FROM usuario WHERE usuario_id != ".$_SESSION['id']." AND usuario_id != '1';";
            }

            $datos = $this->ejecutarConsulta($consulta_datos); #ejecutamos la consulta de los datos
            $datos = $datos->fetchAll(); #fetchAll() devuelve todos los registros de la consulta en un array asociativo
            $total = $this->ejecutarConsulta($consulta_total); #ejecutamos la consulta del total de registros
            $total = (int) $total->fetchColumn(); #fetchColumn() devuelve el valor de la primera columna de la primera fila del resultado de la consulta

            $numeroPaginas = ceil($total / $registros); #ceil redondea hacia arriba
            # abrimos la tabla 
            $tabla .= ' 
                <div class="table-container">
                    <table class="table is-bordered is-striped is-narrow is-hoverable is-fullwidth">
                        <thead>
                            <tr>
		                        <th class="has-text-centered">#</th>
		                        <th class="has-text-centered">Nombre</th>
		                        <th class="has-text-centered">Usuario</th>
		                        <th class="has-text-centered">Email</th>
		                        <th class="has-text-centered">Creado</th>
		                        <th class="has-text-centered">Actualizado</th>
		                        <th class="has-text-centered" colspan="3">Opciones</th>
		                    </tr>
                        </thead>
                        <tbody>
            ';

            if($total>=1 && $pagina<=$numeroPaginas){
				$contador=$inicio+1;
				$pag_inicio=$inicio+1;
				foreach($datos as $rows){
					$tabla.='
						<tr class="has-text-centered" >
							<td>'.$contador.'</td>
							<td>'.$rows['usuario_nombre'].' '.$rows['usuario_apellido'].'</td>
							<td>'.$rows['usuario_usuario'].'</td>
							<td>'.$rows['usuario_email'].'</td>
							<td>'.date("d-m-Y  h:i:s A",strtotime($rows['usuario_creado'])).'</td>
							<td>'.date("d-m-Y  h:i:s A",strtotime($rows['usuario_actualizado'])).'</td>
							<td>
			                    <a href="'.APP_URL.'?view=userPhoto/'.$rows['usuario_id'].'/" class="button is-info is-rounded is-small">Foto</a>
			                </td>
			                <td>
			                    <a href="'.APP_URL.'?view=userUpdate/'.$rows['usuario_id'].'/" class="button is-success is-rounded is-small">Actualizar</a>
			                </td>
			                <td>
			                	<form class="FormularioAjax" action="'.APP_URL.'app/ajax/usuarioAjax.php" method="POST" autocomplete="off" >

			                		<input type="hidden" name="modulo_usuario" value="eliminar">
			                		<input type="hidden" name="usuario_id" value="'.$rows['usuario_id'].'">

			                    	<button type="submit" class="button is-danger is-rounded is-small">Eliminar</button>
			                    </form>
			                </td>
						</tr>
					';
					$contador++;
				}
				$pag_final=$contador-1;
			}else{
                if($total>=1){
					$tabla.='
						<tr class="has-text-centered" >
			                <td colspan="7">
			                    <a href="'.$url.'1/" class="button is-link is-rounded is-small mt-4 mb-4">
			                        Haga clic acá para recargar el listado
			                    </a>
			                </td>
			            </tr>
					';
				}else{
					$tabla.='
						<tr class="has-text-centered" >
			                <td colspan="7">
			                    No hay registros en el sistema
			                </td>
			            </tr>
					';
				}
			}

            $tabla .= '
                        </tbody>
                    </table>
                </div>
            ';

            #si hay registros y la pagina es menor o igual a la cantidad de paginas mostramos los numeros de la paginacion
            if($total>0 && $pagina<=$numeroPaginas){
				$tabla.='<p class="has-text-right">Mostrando usuarios <strong>'.$pag_inicio.'</strong> al <strong>'.$pag_final.'</strong> de un <strong>total de '.$total.'</strong></p>';

				$tabla.=$this->paginadorTablas($pagina,$numeroPaginas,$url,7);
			}
            return $tabla;
        }

        public function eliminarUsuarioControlador(){
            
			$id=$this->limpiarCadena($_POST['usuario_id']);

			if($id==1){
				$alerta=[
					"tipo"=>"simple",
					"titulo"=>"Ocurrió un error inesperado",
					"texto"=>"No podemos eliminar el usuario principal del sistema",
					"icono"=>"error"
				];
				return json_encode($alerta);
		        exit();
			}
            # Verificando usuario #
		    $datos=$this->ejecutarConsulta("SELECT * FROM usuario WHERE usuario_id='$id'");
		    if($datos->rowCount()<=0){
		        $alerta=[
					"tipo"=>"simple",
					"titulo"=>"Ocurrió un error inesperado",
					"texto"=>"No hemos encontrado el usuario en el sistema",
					"icono"=>"error"
				];
				return json_encode($alerta);
		        exit();
		    }else{
		    	$datos=$datos->fetch();
		    }

            $eliminarUsuario=$this->eliminarDatos("usuario","usuario_id",$id);
            if($eliminarUsuario->rowCount()==1){

		    	if(is_file("../views/fotos/".$datos['usuario_foto'])){
		            chmod("../views/fotos/".$datos['usuario_foto'],0777);
		            unlink("../views/fotos/".$datos['usuario_foto']);
		        }
                $alerta=[
					"tipo"=>"recargar",
					"titulo"=>"Usuario eliminado",
					"texto"=>"El usuario ".$datos['usuario_nombre']." ".$datos['usuario_apellido']." ha sido eliminado del sistema correctamente",
					"icono"=>"success"
				];
            }else{
                $alerta=[
					"tipo"=>"simple",
					"titulo"=>"Ocurrió un error inesperado",
					"texto"=>"No hemos podido eliminar el usuario ".$datos['usuario_nombre']." ".$datos['usuario_apellido']." del sistema, por favor intente nuevamente",
					"icono"=>"error"
				];
            }
            return json_encode($alerta);
        }
        
        public function actualizarUsuarioControlador(){
            $usuario_id = $this->limpiarCadena($_POST['usuario_id']);
            $datos=$this->ejecutarConsulta("SELECT * FROM usuario WHERE usuario_id='$usuario_id'");
            if($datos->rowCount()<=0){
                $alerta = [
                    "tipo" => "simple",
                    "titulo" => "Ocurrio un error inesperado",
                    "texto" => "No hemos encontrado el usuario en el sistema",
                    "icono" => "error"
                ];
                return json_encode($alerta);
                exit();
            }else{
                $datos = $datos->fetch();
            }
            $administrador_clave = $this->limpiarCadena($_POST['administrador_clave']);
            $administrador_usuario = $this->limpiarCadena($_POST['administrador_usuario']);
            // Verificando campos obligatorios admin
            if($administrador_clave=="" || $administrador_usuario==""){
                $alerta = [
                    "tipo" => "simple",
                    "titulo" => "Ocurrio un error inesperado",
                    "texto" => "No has llenado todos los campos que son obligatorios",
                    "icono" => "error"
                ];
                return json_encode($alerta);
                exit();
            }
            // Verificando integridad de los datos admin
		    if($this->verificarDatos("[a-zA-Z0-9]{4,20}",$administrador_usuario)){
		        $alerta=[
					"tipo"=>"simple",
					"titulo"=>"Ocurrió un error inesperado",
					"texto"=>"Su USUARIO no coincide con el formato solicitado",
					"icono"=>"error"
				];
				return json_encode($alerta);
		        exit();
		    }
		    elseif($this->verificarDatos("[a-zA-Z0-9$@.-]{7,100}",$administrador_clave)){
		    	$alerta=[
					"tipo"=>"simple",
					"titulo"=>"Ocurrió un error inesperado",
					"texto"=>"Su CLAVE no coincide con el formato solicitado",
					"icono"=>"error"
				];
				return json_encode($alerta);
		        exit();
		    }
            // Verificando administrador 
            $check_admin=$this->ejecutarConsulta("SELECT * FROM usuario WHERE usuario_usuario='$administrador_usuario' AND usuario_id='".$_SESSION['id']."'");
            if($check_admin->rowCount()==1){
                $check_admin = $check_admin->fetch();
                if(!password_verify($administrador_clave,$check_admin['usuario_clave'])) {
                    $alerta=[
						"tipo"=>"simple",
						"titulo"=>"Ocurrió un error inesperado",
						"texto"=>"USUARIO o CLAVE de administrador incorrectos",
						"icono"=>"error"
					];
					return json_encode($alerta);
		        	exit();
                }
            }else{
                $alerta = [
                    "tipo" => "simple",
                    "titulo" => "Ocurrio un error inesperado",
                    "texto" => "USUARIO o CLAVE de administrador incorrectos",
                    "icono" => "error"
                ];
                return json_encode($alerta);
                exit();
            }
            // Almacena Datos
            $usuario_nombre = $this->limpiarCadena($_POST['usuario_nombre']);
            $usuario_apellido = $this->limpiarCadena($_POST['usuario_apellido']);
            $usuario_usuario = $this->limpiarCadena($_POST['usuario_usuario']);
            $usuario_email = $this->limpiarCadena($_POST['usuario_email']);
            $usuario_clave_1 = $this->limpiarCadena($_POST['usuario_clave_1']);
            $usuario_clave_2 = $this->limpiarCadena($_POST['usuario_clave_2']);
            // verificar campos obligatorios
            if($usuario_nombre=="" || $usuario_apellido=="" || $usuario_usuario==""){
                $alerta = [
                    "tipo" => "simple",
                    "titulo" => "Ocurrio un error inesperado",
                    "texto" => "No has llenado todos los campos que son obligatorios",
                    "icono" => "error"
                ];
                return json_encode($alerta);
                exit();
            }
            # Verificando integridad de los datos
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
            }elseif($this->verificarDatos("^[a-zA-Z0-9]{4,20}$", $usuario_usuario)){
                $alerta = [
                    "tipo" => "simple",
                    "titulo" => "Ocurrio un error inesperado",
                    "texto" => "El usuario no coincide con el formato solicitado",
                    "icono" => "error"
                ];
                return json_encode($alerta);
                exit();
            }
            // Verificando email
            if($usuario_email != "" && $usuario_email != $datos['usuario_email']){
                if(filter_var($usuario_email, FILTER_VALIDATE_EMAIL)){ # verifica si el email es valido
                    $check_email = $this->ejecutarConsulta("SELECT usuario_email FROM usuario WHERE usuario_email='$usuario_email'");
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
            # Verificando claves
            if($usuario_clave_1!="" || $usuario_clave_2!=""){
                if($this->verificarDatos("[a-zA-Z0-9$@.-]{7,100}",$usuario_clave_1) || $this->verificarDatos("[a-zA-Z0-9$@.-]{7,100}",$usuario_clave_2)){
                    $alerta = [
                        "tipo" => "simple",
                        "titulo" => "Ocurrio un error inesperado",
                        "texto" => "La clave no coincide con el formato solicitado",
                        "icono" => "error"
                    ];
                    return json_encode($alerta);
                    exit();
                }else{
                    if($usuario_clave_1!=$usuario_clave_2){
						$alerta=[
							"tipo"=>"simple",
							"titulo"=>"Ocurrió un error inesperado",
							"texto"=>"Las nuevas CLAVES que acaba de ingresar no coinciden, por favor verifique e intente nuevamente",
							"icono"=>"error"
						];
						return json_encode($alerta);
						exit();
			    	}else{
			    		$clave=password_hash($usuario_clave_1,PASSWORD_BCRYPT,["cost"=>10]); # encripta la clave
			    	}
                }
            }else{
                $clave = $datos['usuario_clave']; // si no se cambia la clave, se deja la clave actual
            }
            # Verificando usuario
            if($datos['usuario_usuario']!=$usuario_usuario){
                $check_usuario = $this->ejecutarConsulta("SELECT usuario_usuario FROM usuario WHERE usuario_usuario='$usuario_usuario'");
                if($check_usuario->rowCount()>0){
                    $alerta = [
                        "tipo" => "simple",
                        "titulo" => "Ocurrio un error inesperado",
                        "texto" => "El usuario ya se encuentra registrado",
                        "icono" => "error"
                    ];
                    return json_encode($alerta);
                    exit(); 
                }
            }
            $usuario_datos_up=[
				[
					"campo_nombre"=>"usuario_nombre",
					"campo_marcador"=>":Nombre",
					"campo_valor"=>$usuario_nombre
				],
				[
					"campo_nombre"=>"usuario_apellido",
					"campo_marcador"=>":Apellido",
					"campo_valor"=>$usuario_apellido
				],
				[
					"campo_nombre"=>"usuario_usuario",
					"campo_marcador"=>":Usuario",
					"campo_valor"=>$usuario_usuario
				],
				[
					"campo_nombre"=>"usuario_email",
					"campo_marcador"=>":Email",
					"campo_valor"=>$usuario_email
				],
				[
					"campo_nombre"=>"usuario_clave",
					"campo_marcador"=>":Clave",
					"campo_valor"=>$clave
				],
				[
					"campo_nombre"=>"usuario_actualizado",
					"campo_marcador"=>":Actualizado",
					"campo_valor"=>date("Y-m-d H:i:s")
				]
			];
            $condicion=[
				"condicion_campo"=>"usuario_id",
				"condicion_marcador"=>":ID",
				"condicion_valor"=>$usuario_id
			];
            if($this->actualizarDatos("usuario", $usuario_datos_up, $condicion)){ #si los datos se actualizaron
                if($usuario_id==$_SESSION['id']){ # si el usuario actualizado es el mismo que el que esta logueado
                    $_SESSION['usuario_nombre'] = $usuario_nombre; # actualiza el nombre del usuario en la sesion
                    $_SESSION['usuario_apellido'] = $usuario_apellido; # actualiza el apellido del usuario en la sesion
                    $_SESSION['usuario_usuario'] = $usuario_usuario; # actualiza el usuario en la sesion
                }
                $alerta = [
                    "tipo" => "recargar",
                    "titulo" => "Usuario Actualizado",
                    "texto" => "El usuario ".$datos['usuario_nombre']." ". $datos['usuario_apellido']." ha sido Actualizado exitosamente",
                    "icono" => "success"
                ];
            }else{
                $alerta = [
                    "tipo" => "simple",
                    "titulo" => "Ocurrio un error inesperado",
                    "texto" => "No se pudo Actualizar el usuario, intente nuevamente",
                    "icono" => "error"
                ];
            }
            return json_encode($alerta);
        }

        public function actualizarUsuarioFotoControlador(){
            $usuario_id = $this->limpiarCadena($_POST['usuario_id']);
            $datos = $this->ejecutarConsulta("SELECT * FROM usuario WHERE usuario_id='$usuario_id'");
            if($datos->rowCount()<=0){
                $alerta = [
                    "tipo" => "simple",
                    "titulo" => "Ocurrio un error inesperado",
                    "texto" => "No hemos encontrado el usuario en el sistema",
                    "icono" => "error"
                ];
                return json_encode($alerta);
                exit();
            }else{
                $datos = $datos->fetch();
            }
            // Directorio de imagenes
            $img_dir = "../view/fotos/";

            // Validar seleccion de una img
                        #⬇️nombre del imput
                                        // ⬇️ATTRIBUTO DEL ARCHIVO en este caso el nombre
            if($_FILES['usuario_foto']['name'] !="" && $_FILES['usuario_foto']['size']<=0){
                $alerta=[
					"tipo"=>"simple",
					"titulo"=>"Ocurrió un error inesperado",
					"texto"=>"No ha seleccionado una foto para el usuario",
					"icono"=>"error"
				];
				return json_encode($alerta);
		        exit();
            }
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
            
            if($datos['usuario_foto']!= ""){
                $foto = explode(".", $datos['usuario_foto']); // separa el nombre de la foto y la extencion
                $foto = $foto[0]; // toma el nombre de la foto sin la extencion
            }else{
                $foto=str_ireplace(" ","_",$datos['usuario_nombre']);
	            $foto=$foto."_".rand(0,100);
            }
            
            #Extencion del archivo
            switch(mime_content_type($_FILES['usuario_foto']['tmp_name'])) {
                case 'image/jpeg':
                    $foto = $foto.'.jpg'; // si es un archivo jpg
                    break;
                case 'image/png':
                    $foto = $foto.'.png'; // si es un archivo png
                    break;
                case 'image/jpg':
                    $foto = $foto.'.jpg'; // si es un archivo jpg
                    break;
            }
            chmod($img_dir, 0777); // cambia los permisos del directorio a 0777 osea puede leer, escribir y ejecutar
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

            #Eliminar foto anterior
            if(is_file($img_dir.$datos['usuario_foto'])){ #valida si la img existe en el directorio
                chmod($img_dir.$datos['usuario_foto'], 0777);
                unlink($img_dir.$datos['usuario_foto']); #si existe la elimina
            }

            $usuario_datos_up=[
				[
					"campo_nombre"=>"usuario_foto",
					"campo_marcador"=>":Foto",
					"campo_valor"=>$foto
				],
				[
					"campo_nombre"=>"usuario_actualizado",
					"campo_marcador"=>":Actualizado",
					"campo_valor"=>date("Y-m-d H:i:s")
				]
			];
            $condicion=[
				"condicion_campo"=>"usuario_id",
				"condicion_marcador"=>":ID",
				"condicion_valor"=>$usuario_id
			];
            if($this->actualizarDatos("usuario", $usuario_datos_up, $condicion)){ #si los datos se actualizaron
                if($usuario_id==$_SESSION['id']){ # si el usuario actualizado es el mismo que el que esta logueado
                    $_SESSION['foto'] = $foto; # actualiza la variable de session foto
                }
                $alerta = [
                    "tipo" => "recargar",
                    "titulo" => "Foto Actualizado",
                    "texto" => "El usuario ".$datos['usuario_nombre']." ". $datos['usuario_apellido']." ha sido Actualizado exitosamente",
                    "icono" => "success"
                ];
            }else{
                $alerta = [
                    "tipo" => "simple",
                    "titulo" => "Ocurrio un error inesperado",
                    "texto" => "No hemos podido actualizar algunos datos del usuario ".$datos['usuario_nombre']." ". $datos['usuario_apellido']." , sin embargo se actualizo la foto",
                    "icono" => "warning"
                ];
            }
            return json_encode($alerta);
            }

        public function UsuarioEliminarFotoControlador(){
            $usuario_id = $this->limpiarCadena($_POST['usuario_id']);
            $datos = $this->ejecutarConsulta("SELECT * FROM usuario WHERE usuario_id='$usuario_id'");
            if($datos->rowCount()<=0){
                $alerta = [
                    "tipo" => "simple",
                    "titulo" => "Ocurrio un error inesperado",
                    "texto" => "No hemos encontrado el usuario en el sistema",
                    "icono" => "error"
                ];
                return json_encode($alerta);
                exit();
            }else{
                $datos = $datos->fetch();
            }
            // Directorio de imagenes
            $img_dir = "../view/fotos/";
            chmod($img_dir, 0777); // cambia los permisos del directorio a 0777 osea puede leer, escribir y ejecutar
            if(is_file($img_dir.$datos['usuario_foto'])){ #valida si la img existe en el directorio
                chmod($img_dir.$datos['usuario_foto'], 0777);
                if(!unlink($img_dir.$datos['usuario_foto'])){ #si existe la elimina
                    $alerta = [
                        "tipo" => "simple",
                        "titulo" => "Ocurrio un erro inesperado",
                        "texto" => "No hemos podido eliminar la foto del usuario ".$datos['usuario_nombre']." ".$datos['usuario_apellido'].", intente nuevamente",
                        "icono" => "error"
                    ];
                    return json_encode($alerta);
                    exit();
                } 
            }else{
                $alerta = [
                    "tipo" => "simple",
                    "titulo" => "Ocurrio un error inesperado",
                    "texto" => "No hemos encontrado la foto del usuario en el sistema",
                    "icono" => "error"
                ];
                return json_encode($alerta);
                exit();
            }
            $foto = ""; // si se elimina la foto, se deja la variable foto vacia
            $usuario_datos_up=[
				[
					"campo_nombre"=>"usuario_foto",
					"campo_marcador"=>":Foto",
					"campo_valor"=>$foto
				],
				[
					"campo_nombre"=>"usuario_actualizado",
					"campo_marcador"=>":Actualizado",
					"campo_valor"=>date("Y-m-d H:i:s")
				]
			];
            $condicion=[
				"condicion_campo"=>"usuario_id",
				"condicion_marcador"=>":ID",
				"condicion_valor"=>$usuario_id
			];
            if($this->actualizarDatos("usuario", $usuario_datos_up, $condicion)){ #si los datos se actualizaron
                if($usuario_id==$_SESSION['id']){ # si el usuario actualizado es el mismo que el que esta logueado
                    $_SESSION['foto'] = $foto; # actualiza la variable de session foto
                }
                $alerta = [
                    "tipo" => "recargar",
                    "titulo" => "Foto Eliminado",
                    "texto" => "El usuario ".$datos['usuario_nombre']." ". $datos['usuario_apellido']." ha sido eliminada exitosamente",
                    "icono" => "success"
                ];
            }else{
                $alerta = [
                    "tipo" => "simple",
                    "titulo" => "Ocurrio un error inesperado",
                    "texto" => "No hemos podido actualizar algunos datos del usuario ".$datos['usuario_nombre']." ". $datos['usuario_apellido']." , sin embargo se a eliminado la foto",
                    "icono" => "warning"
                ];
            }
            return json_encode($alerta);
        }
    }