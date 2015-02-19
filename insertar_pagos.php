<?php
//------------------------------------------------------------------------------
// Insertamos los registros en las siguientes tablas:
//          1) CHEQUES
//          2) PAGOS
//          5) CTACTES
//          6) PASAJES_ADELANTADO
// Modifiucamos el contenido en los siguientes          
//          6) PASAJES_ADELANTADO
//          7) pasajes
//------------------------------------------------------------------------------
include_once("seguridad.php");
include_once("template.php");
include_once("conexion.php");
include_once("funciones.php");

    
$db = conectar_al_servidor();                    

//$res            = $_POST['b_aceptar'];
$detalle_pagos  = $_REQUEST["e_pagos"];    // Detalle de pago de la encomienda.
$id_cliente     = $_REQUEST["id_cliente"]; // id del cliente al que se le asignara el pago en la cta. cte.
$id_suc         = $_SESSION["id_sucursal"];
$id             = $_REQUEST["id"];        // id generico para que se pase el id de psaje o id encomienda o el id de pasajes adelantados
$tipo           = $_REQUEST["tipo"]; //termina que pantalla invoco la funcion puede ser 0 reserva o 1 pasajes adelantados
$fecha_desde    = $_REQUEST["f_desde"];
$cant           = $_REQUEST["cantidad"];
$t_pas          = $_REQUEST["tipo_pasaje"];
$raz            = $_REQUEST["razon_s"];
$observ         = $_REQUEST["observ"];


$mifecha = explode('/','/'.$fecha_desde);
$fecha_des = $mifecha[3]."-".$mifecha[1]."-".$mifecha[2];

$est= 'S';

$sql = "INSERT INTO pasajes_adelantados (fecha_emision,observaciones, estado, cantidad,id_cliente,tipo_pas) VALUES ('".$fecha_des."','".$observ."','".$est."','".$cant."','".$id_cliente."','".$t_pas."');";

$res = ejecutar_sql($db,$sql);

if (!$res){
echo 'Error accediendo a las pasajes adelantados...'.$sql;
}else{
echo 'OK';    
}


 desconectar($db);   
?>  

