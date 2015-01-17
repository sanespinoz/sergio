<?php
include_once("seguridad.php");
include_once("conexion.php");
include_once("template.php");
include_once("funciones.php");

// parametros recibidos desde el javascript 
$nro_asiento = $_REQUEST["code"]; // patente del vehiculo seleccionado.
$id_viaje    = $_REQUEST["id_v"]; // clave unica del viaje seleccionado para agregar el vehiculo.
$deuda       = $_REQUEST["deuda"]; // importe de la deuda de la reserva
$importe_pasaje       = $_REQUEST["importe_pasaje"]; // importe de la deuda de la reserva
$importe_coseguro_pasaje = $_REQUEST["importe_coseguro"];

set_file("pagar_pasaje", "pagar_pasaje.html");

set_var('v_color_cabezera_columna', COLOR_ENCOMIENDAS_CABEZERA_COLUMNA);
set_var('v_total_pago','0.00');

set_var("v_deuda_pasaje",   $deuda); 
set_var("v_importe_pasaje", $deuda);
set_var("v_importe_coseguro_pasaje", $importe_coseguro_pasaje);

parse ('pagar_pasaje');
pparse('pagar_pasaje');

desconectar($db);

?>
