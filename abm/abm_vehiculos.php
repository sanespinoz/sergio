<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=windows-1252">
	<title>Administrador Vehiculos</title>
	
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
$abm->tabla = 'vehiculos'; 
$abm->campoId = 'PATENTE'; 
$abm->orderByPorDefecto = 'PATENTE DESC'; 
$abm->registros_por_pagina = 5; 
$abm->textoTituloFormularioAgregar = "Agregar"; 
$abm->textoTituloFormularioEdicion = "Editar"; 
$abm->campos = array( 
    array('campo' => 'NOMBRE', 
		'tipo' => 'texto',
		'hint' => "Identificacion por nombre de la unidad.",
		'tituloListado' => 'Denominación',
		'requerido' => true,
		'maxLen' => 50,		
		'titulo' => 'Nombre'
	), 
    
	array('campo' => 'INTERNO', 
		'tipo' => 'texto',
		'tituloListado' => 'Nro Interno',
		'maxLen' => 3,
		'titulo' => 'Nro. Interno'
	), 

	array('campo' => 'PATENTE', 
		'tipo' => 'texto',
		'hint' => "Nro de dominio del vehículo.",
		'tituloListado' => 'Patente',
		'requerido' => true,
		'maxLen' => 7,
		'titulo' => 'Patente'
	), 
    
	array('campo' => 'MODELO', 
		'tipo' => 'texto',
		'hint' => "Año de inscripción de la unidad.",
		'tituloListado' => 'Modelo',
		'maxLen' => 4,
		'titulo' => 'Modelo'
	), 
    
	array('campo' => 'NRO_ASIENTOS', 
		'tipo' => 'texto',
		'hint' => "Cantidad de asientos de pasajeros",
		'tituloListado' => 'Nro Asientos P.B.',
		'requerido' => true,
		'maxLen' => 3,
		'centrarColumna' => true,
		'titulo' => 'Nro. Asientos'
	), 
    
	array('campo' => 'CANTIDAD_CHOFERES', 
		'tipo' => 'texto',
		'hint' => "Cantidad de choferes que debe cargar la unidad.",
		'tituloListado' => 'Cant. Chofer',
		'maxLen' => 2,
		'titulo' => 'Cant. Choferes'
	), 
	array('campo' => 'ACTIVO', 
		'tipo' => 'bit',
		'datos' => Array('S' => 'SI', 'N' => 'No'),
		'valorPredefinido' => "S",
		'tituloListado' => 'Esta Activo',
		'maxLen' => 1,
		'centrarColumna' => true,
		'titulo' => 'Activo'
	), 
    
    	array('campo' => 'DOBLE_PISO', 
		'tipo' => 'bit',
		'datos' => Array('S' => 'SI', 'N' => 'No'),
		'tituloListado' => 'Es doble piso',
		'maxLen' => 1,
		'centrarColumna' => true,
		'noOrdenar' => true,
		'titulo' => 'Doble Piso'
	), 
    
	array('campo' => 'CANTIDAD_ASIENTOS_PISO_2', 
		'tipo' => 'texto',
		'tituloListado' => 'Nro Asientos P.A.',
		'maxLen' => 3,
		'centrarColumna' => true,
		'noOrdenar' => true,
		'titulo' => 'Cant. Asiento 2do piso'
	), 
    
	array('campo' => 'FECHA_VENCIMIENTO_TECNICA', 
		'tipo' => 'fecha',
		'tituloListado' => 'Vencimiento de Técnica',
		'titulo' => 'Fecha Vto. Técnica'
	),

	array('campo' => 'NRO_RUEDAS', 
		'tipo' => 'texto',
		'hint' => "cantidad de rueda de la unidad.",
		'tituloListado' => 'Cant. Ruedas',
		'maxLen' => 2,
		'titulo' => 'Nro. Ruedas'
	), 
    
	array('campo' => 'VIDEOS', 
		'tipo' => 'bit',
		'datos' => Array('S' => 'SI', 'N' => 'No'),
		'valorPredefinido' => "S",
		'hint' => "Indique si la unidad posee video abordo.",
		'tituloListado' => 'Posee Video',
		'maxLen' => 1,
		'centrarColumna' => true,
		'titulo' => 'Videos'
	), 
	
        array('campo' => 'COLUMNA_PB_11', 
		'tipo' => 'bit',
		'datos' => Array('S' => 'SI', 'N' => 'No'),
		'hint' => "Fila de asientos de la planta baja del lado ventanilla.",
		'tituloListado' => 'Dist. Asientos P.B. Vent Izq',
		'maxLen' => 1,
		'centrarColumna' => true,
		'noOrdenar' => true,
		'nolistar' => true,
		'titulo' => 'Fila de Asientos PB [0-] [--]'
	), 
	array('campo' => 'COLUMNA_PB_12', 
		'tipo' => 'bit',
		'datos' => Array('S' => 'SI', 'N' => 'No'),
		'hint' => "Fila de asientos de la planta baja del lado pasillo. ",
		'tituloListado' => 'Dist. Asientos P.B. Pasillo Izq',
		'maxLen' => 1,
		'centrarColumna' => true,
		'noOrdenar' => true,
		'titulo' => 'Fila de Asientos PB [-0] [--]'
	), 
	array('campo' => 'COLUMNA_PB_21', 
		'tipo' => 'bit',
		'datos' => Array('S' => 'SI', 'N' => 'No'),
		'hint' => "Fila de asientos de la planta baja del lado ventanilla.",
		'tituloListado' => 'Dist. Asientos P.B. Pasillo Der',
		'maxLen' => 1,
		'centrarColumna' => true,
		'noOrdenar' => true,
		'titulo' => 'Fila de Asientos PB [--] [0-]'
	), 
	array('campo' => 'COLUMNA_PB_22', 
		'tipo' => 'bit',
		'datos' => Array('S' => 'SI', 'N' => 'No'),
		'tituloListado' => 'Dist. Asientos P.B. Vent Der',
		'maxLen' => 1,
		'centrarColumna' => true,
		'noOrdenar' => true,
		'titulo' => 'Fila de Asientos PB [--] [-0]'
	), 
	array('campo' => 'COLUMNA_PA_11', 
		'tipo' => 'bit',
		'datos' => Array('S' => 'SI', 'N' => 'No'),
		'tituloListado' => 'Dist. Asientos P.A. Vent Izq',
		'maxLen' => 1,
		'centrarColumna' => true,
		'noOrdenar' => true,
		'titulo' => 'Fila de Asientos PA [0-] [--]'
	), 
	array('campo' => 'COLUMNA_PA_12', 
		'tipo' => 'bit',
		'datos' => Array('S' => 'SI', 'N' => 'No'),
		'tituloListado' => 'Dist. Asientos P.A. Pasillo Izq',
		'maxLen' => 1,
		'centrarColumna' => true,
		'noOrdenar' => true,
		'titulo' => 'Fila de Asientos PA [-0] [--]'
	), 
	array('campo' => 'COLUMNA_PA_21', 
		'tipo' => 'bit',
		'datos' => Array('S' => 'SI', 'N' => 'No'),
		'tituloListado' => 'Dist. Asientos P.A. Pasillo Der',
		'maxLen' => 1,
		'centrarColumna' => true,
		'noOrdenar' => true,
		'titulo' => 'Fila de Asientos PA [--] [0-]'
	), 
	array('campo' => 'COLUMNA_PA_22', 
		'tipo' => 'bit',
		'datos' => Array('S' => 'SI', 'N' => 'No'),
		'tituloListado' => 'Dist. Asientos P.A. Vent Der',
		'maxLen' => 1,
		'centrarColumna' => true,
		'noOrdenar' => true,
		'titulo' => 'Fila de Asientos PA [--] [-0]'
	)    
); 
$abm->generarAbm('', 'Administrar vehiculos');

?>
</body>
</html>
 