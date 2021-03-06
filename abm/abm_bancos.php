<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=windows-1252">
	<title>Demo abm</title>

	<!-- Estilos -->
	<link href="css/sitio.css" rel="stylesheet" type="text/css">
	<link href="css/abm.css" rel="stylesheet" type="text/css">

	<!-- MooTools -->
	<script type="text/javascript" src="js/mootools-1.2.3-core.js"></script>
	<script type="text/javascript" src="js/mootools-1.2.3.1-more.js"></script>
	
	<!--FormCheck-->
	<script type="text/javascript" src="js/formcheck/lang/es.js"></script>
	<script type="text/javascript" src="js/formcheck/formcheck.js"></script>
	<link rel="stylesheet" href="js/formcheck/theme/classic/formcheck.css" type="text/css" media="screen"/>

	<!--Datepicker-->
	<link rel="stylesheet" href="js/datepicker/datepicker_vista/datepicker_vista.css" type="text/css" media="screen"/>
	<script type="text/javascript" src="js/datepicker/datepicker.js"></script>

</head>
<body>
    
<?php
include_once("../conexion.php");
require_once "comun/class_db.php";
require_once "comun/class_abm.php";
require_once "comun/class_paginado.php";
require_once "comun/class_orderby.php";
//echo ($HOST. $USUARIO. $PASSWORD. $BASE); die();

//conexi�n a la bd
//$db = new class_db("localhost", "root", "123", "prueba");

$db = new class_db(HOST, USUARIO, PASSWORD, BASE);
$db->mostrarErrores = FALSE;
$db->connect();
$abm = new class_abm();
$abm->tabla = "bancos";
$abm->registros_por_pagina = 5;
$abm->campoId = "codigo";
$abm->textoTituloFormularioAgregar = "Agregar Bancos";
$abm->textoTituloFormularioEdicion = "Editar Bancos";
//$abm->adicionalesInsert = ", activo='S'";




$abm->campos = array(
		array("campo" => "banco", 
					"tipo" => "texto", 
					"titulo" => "Banco", 
					"maxLen" => 50,
					"requerido" => true,
					"hint" => "El banco no debe existir"
					),
                array("campo" => "activo", 
					"tipo" => "combo", 
					"titulo" => "Activo", 
					"datos" => array("S"=>"SI", "N"=>"NO"),
					"valorPredefinido" => "S",
					"centrarColumna" => true,
					"hint" => "Indica si el tipo de banco estara activo"
					)
		);					

$abm->generarAbm("", "Administrar banco");
?>
</body>
</html>