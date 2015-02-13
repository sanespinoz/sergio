<?php
include_once("seguridad.php");
include_once("conexion.php");

$db = conectar_al_servidor();

$f_d        = $_POST["f_d"];
$f_h        = $_POST["f_h"];
$id_cliente = $_POST["id_cli"];
$total      = $_POST["total"];
$cant       = $_POST["cant"];
$observ     = $_POST["observ"];

$mifecha = explode('/','/'.$f_d);
$fecha_desde = $mifecha[3]."-".$mifecha[1]."-".$mifecha[2];

$mifechah = explode('/','/'.$f_h);
$fecha_hasta = $mifechah[3]."-".$mifechah[1]."-".$mifechah[2];
$est= 'S';

$sql = "INSERT INTO pasajes_adelantados (fecha_emision, fecha_vensimiento, observaciones, estado, cantidad,id_cliente) VALUES ('".$fecha_desde."','".$fecha_hasta."','".$observ."','".$est."','".$cant."','".$id_cliente."');";
//$sql = "CALL crear_pasajes_adelantados('".$fecha_desde."','".$fecha_hasta."','".$observ."','".$id_cliente."','".$cant."');";
$res = ejecutar_sql($db,$sql);

if (!$res){
echo 'Error accediendo a las pasajes adelantados...'.$sql;
}else{
echo 'OK';    
}

desconectar($db);
?>
