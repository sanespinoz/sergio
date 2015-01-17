<?php

//include_once("seguridad.php");

include_once("template.php");
include_once("conexion.php");
include_once("mensajes.php");

$mensaje_error_usuario_incorrecto = " Usuario Incorrecto";


set_file("login","login.html");
	set_var('v_usuario', '');
	set_var('v_contrasenia', '');

$var1= $_REQUEST['usuario'];
$var2= $_REQUEST['contrasenia'];
$var3= $_REQUEST['ok'];

if ((($var1!='')or($var2!=''))and($var3=='Aceptar')){
	
	$db = conectar_al_servidor();
	
	$usuario = "select u.id, u.nombre, u.usuario, u.id_configuracion_usuario, cu.id_loc_destinatario_encomienda, 
                           cu.id_loc_remitente_encomienda, cu.id_provincia_origen_encomienda, s.nombre as sucursal, 
                           cu.id_sucursal
                    from usuarios u 
                        inner join conf_usuario cu on (u.id_configuracion_usuario=cu.codigo) 
                              and u.usuario='".$var1."'AND u.pass='".$var2."'
                        inner join sucursales s on (cu.id_sucursal=s.codigo)";
				
	$us = ejecutar_sql($db, $usuario);    
	$cant = $us->RecordCount();
	if($cant==0){
		mostrar_mensaje((" Error..."), ($mensaje_error_usuario_incorrecto));
	}else{
		session_start();
		$arr = $us->fetchRow();
		$_SESSION['sucursal']        = $arr['sucursal'];
		$_SESSION['id_sucursal']     = $arr['id_sucursal'];
		$_SESSION['usuario']         = $_REQUEST['usuario'];
                $_SESSION['id_usuario']      = $arr['id'];
		$_SESSION['id_conf_usuario'] = $arr['id_configuracion_usuario']; // pasa el identificador de configuracion de arranque para el usuario
		$_SESSION['id_personal']           = $arr['id'];
                $_SESSION['id_loc_orig_encom']     = $arr['id_loc_remitente_encomienda'];
                $_SESSION['id_loc_dest_encom']     = $arr['id_loc_destinatario_encomienda'];
		// lo redirijo al menu
		header("Location:principal.php");
		die();
	} // fin del if cant = 0
}
//desconectar($db);
pparse('login');
?>
