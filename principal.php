<?php
    include_once("seguridad.php");
    include_once("template.php");
    include_once("conexion.php");

    $ADODB_FETCH_MODE=ADODB_FETCH_BOTH;			

    set_file("cuerpo","cuerpo.html");		

	// Armamos y ejecutamos la consulta de promociones para la pagina central.
	$con  = conectar_al_servidor();
	$res  = ejecutar_sql($con, "SELECT wp.descripcion, wp.foto_der, wp.foto_izq, wp.titulo, wp.precio FROM  web_promociones wp ");
    $cant = $res->RecordCount();
	
    if($cant>0){

	// Cargamos en las variables las promociones
	while (!$res->EOF){
		
		set_var("v_cuerpo",  $res->fields[0]);
		set_var("v_foto_der",$res->fields[1]);
		set_var("v_foto_izq",$res->fields[2]);
		set_var("v_titulo",  $res->fields[3]);
		set_var("v_precio1", $res->fields[4]);
		set_var("v_precio2", $res->fields[4]);
		parse("FilaStock");
		$res->MoveNext();
    }
	desconectar($con);	
     }else{
				set_var("v_cuerpo",  '');
				set_var("v_foto_der",'');
				set_var("v_foto_izq",'');
				set_var("v_titulo",  '');
				set_var("v_precio1",  '');
				set_var("v_precio2",  '');
				parse("FilaStock");
	}; // fin 

	// Cargamos las variables del menu principal
	set_file("menu","menu_principal.html");
		set_var("fecha",date("d/m/Y"));
		set_var("visor",'...Principal...');
		set_var("logo_proyecto",'./imagenes/logo.jpg');
		set_var("titulo_proyecto",'Marciano Tourd SRL');
                
                set_var('v_imagen_fondo', IMAGEN_FONDO);

//------------------------------------------------------------------------------                
//  Seccion para versionado del sistema
//------------------------------------------------------------------------------                
                set_var('v_sis_version', SIS_VERSION);
                set_var('v_fecha_actualizacion_sistema',FECHA_MODI_SISTEMA);
//------------------------------------------------------------------------------                


	// cargamos las variables para el pie de pagina
	set_file("pie","pie_pagina.html");

		set_var("v_usuario",$_SESSION['usuario']);
		set_var("logo_proyecto",'./imagenes/logo.jpg');
		set_var("acerca_de",'HardSoft Sistemas...');

		
	// Mostramos en el orden deseado
        pparse("menu");
	//pparse("cuerpo");
	//pparse("pie");

?>

