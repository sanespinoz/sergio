<?php

include_once("seguridad.php");
include_once("conexion.php");
include_once("template.php");
include_once("funciones.php");

// parametros recibidos desde el javascript 
$nro_asiento = $_REQUEST["code"]; // Nro de asiento.
$id_viaje    = $_REQUEST["id_v"]; // clave unica del viaje seleccionado para agregar el vehiculo.
$deuda       = $_REQUEST["deuda"]; // importe de la deuda de la reserva
$importe_pasaje       = $_REQUEST["importe_pasaje"]; // importe de la deuda de la reserva
$importe_coseguro_pasaje = $_REQUEST["importe_coseguro"];
$destino   = $_REQUEST["destino"]; // cantidad de asientos reservados
$fecha =  $_REQUEST["fecha"]; // fecha del viaje
$hora =  $_REQUEST["hora"];   // hora del viaje     
$nro_pasaje = $_REQUEST["nro_pasaje"];   // hora del viaje     

set_var('v_fecha_viaje', $fecha);
set_var('v_hora_viaje', $hora);
set_var("v_pasaje", $nro_pasaje);

set_file("pagar_pasaje", "pagar_pasaje.html");

set_var('v_color_cabezera_columna', COLOR_ENCOMIENDAS_CABEZERA_COLUMNA);
set_var('v_total_pago','0.00');

set_var("v_deuda_pasaje",   "0.00"); 
set_var("v_importe_pasaje", "0.00");
set_var("v_importe_coseguro_pasaje","0.00");

set_var("v_destino", $destino);
set_var('v_id_viaje', $id_viaje);

set_var("v_color_cabezera_tabla",    COLOR_ENCOMIENDAS_CABEZERA_TABLA);
set_var("v_color_cabezera_columna",  COLOR_ENCOMIENDAS_CABEZERA_COLUMNA);
set_var("v_interes_tarjeta", PORCENTAJE_INTERES_TARJETA);
set_var("v_color_mando_botonera_mando",'#4d4d4d');
set_var("v_color_fondo_boton_mando",'#333');
set_var("v_color_texto_boton_mando",'#fff');

set_var("v_interes_tarjeta",0);
set_var("v_id_tarjeta","");
set_var("v_id_banco","");

set_var("v_importe_con_interes_pago", $deuda);

set_var("v_deuda_pasaje",   $deuda); 
set_var("v_importe_pasaje", $deuda);

set_var("v_importe_coseguro_pasaje", $importe_coseguro_pasaje);
set_var("v_cantidad_pasaje_reservados", 0);

$db = conectar_al_servidor();

//----------------------------------------------------------------------------------------------------
//----------------------------------------------------------------------------------------------------
// Cargamos el comboBOX de tarjetas de creditos con las cuotas
//----------------------------------------------------------------------------------------------------
$q = " SELECT tc.codigo, tc.nombre, tc.porcentaje, tc.cuotas
       FROM prueba.tarjetas_creditos AS tc WHERE tc.activa='S'         ORDER BY tc.nombre, tc.cuotas";
$res = ejecutar_sql($db, $q);
if (!$res) {
    echo $db->ErrorMsg(); //die();
} else {
    $combobox_tarjetas = "<option value='0' selected=true>Seleccionar uno...</option>";
    while (!$res->EOF) {
        // destino -------------------------------------------------------------
        $combobox_tarjetas = $combobox_tarjetas . "<option value=".$res->fields[0]."@".$res->fields[2].">"
            .$res->fields[3]." Cuotas en ".$res->fields[1]."</option>";
        $res->MoveNext();
    }
}
set_var("v_comboBox_tarjeta_credito", $combobox_tarjetas);

//----------------------------------------------------------------------------------------------------
//----------------------------------------------------------------------------------------------------
// Cargamos el comboBOX de bancos
//----------------------------------------------------------------------------------------------------
$q = "select codigo, banco from bancos where activo='S' order by banco";
$res = ejecutar_sql($db, $q);
if (!$res) {
    echo $db->ErrorMsg(); //die();
} else {
    $combobox_bancos = "<option value=0>Seleccione uno...</option>";
    while (!$res->EOF) {
        $combobox_bancos = $combobox_bancos . "<option value=".$res->fields[0] . ">" . $res->fields[1]."</option>";
        $res->MoveNext();
    }
}
set_var("v_comboBox_banco", $combobox_bancos);
 				


//----------------------------------------------------------------------------------------------------
//----------------------------------------------------------------------------------------------------
// Cargamos el comboBOX de Cuentas de pasajes adelantados
//----------------------------------------------------------------------------------------------------
$q = "SELECT pa.codigo, pa.cantidad, pa.fecha_vensimiento, cl.dni, cl.razon_social
      FROM pasajes_adelantados AS pa
      INNER JOIN clientes AS cl ON pa.id_cliente = cl.codigo
      ORDER BY cl.razon_social ASC ";
$res = ejecutar_sql($db, $q);
if (!$res) {
    echo $db->ErrorMsg(); //die();
} else {
    $combobox_cuenta = "<option value=0>Seleccione uno...</option>";
    while (!$res->EOF) {
        $combobox_cuenta = $combobox_cuenta . "<option value=".$res->fields[0]."@".$res->fields[1]. ">" .$res->fields[4]." - ".$res->fields[1]."</option>";
        $res->MoveNext();
    }
}
set_var("v_comboBox_cuenta", $combobox_cuenta);
 				





parse ('pagar_pasaje');
pparse('pagar_pasaje');

desconectar($db);

?>