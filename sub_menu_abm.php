<?php
    include_once("seguridad.php");
    include_once("template.php");
    include_once("conexion.php");
    

    $ADODB_FETCH_MODE=ADODB_FETCH_BOTH;			

    // Cargamos las variables del menu principal
	set_file("sub_menu","sub_menu_abm.html");
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

	
	// Mostramos en el orden deseado
        pparse("sub_menu");

?>

