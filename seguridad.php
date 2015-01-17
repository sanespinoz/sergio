<?php
session_start();
// $_SESSION['usuario'] nombre del usuario logueado
// en el caso de no estarlo no vale nada
if(!isset($_SESSION['usuario'])){
     // lo redirijo al login.php
	header("Location:login.php");
	// impido la ejecucion
	die();
}
?>
