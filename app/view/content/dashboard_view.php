<div class="container is-fluid">
	<h1 class="title">Home</h1>
  	<div class="columns is-flex is-justify-content-center">
    	<figure class="image is-128x128">
			<?php 
				if(is_file("./app/view/fotos/".$_SESSION['foto'])){
					echo '<img class="is-rounded" src="'.APP_URL.'app/view/fotos/'.$_SESSION['foto'].'">';
				}else{
					echo '<img class="is-rounded" src="'.APP_URL.'app/view/img/Captura de pantalla 2025-06-20 191843.png">';
				}
			?>
		</figure>
  	</div>
  	<div class="columns is-flex is-justify-content-center">
  		<h2 class="subtitle">¡Bienvenido <?php echo $_SESSION['nombre'] ." ". $_SESSION['apellido'];?>!</h2>
  	</div>
</div>