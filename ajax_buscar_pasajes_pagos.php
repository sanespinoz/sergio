<?php
// Buscamos los viajes segun la fecha y/o salidas 
include_once("seguridad.php");
include_once("template.php");
include_once("conexion.php");
include_once("funciones.php");


$db = conectar_al_servidor();

// obtenemos los datos pasados por parametros por ajax
$fech_d = $_REQUEST["f_d"];
$fech_h  = $_REQUEST["f_h"];
$loc_o = $_REQUEST["lo"];
$loc_d  = $_REQUEST["ld"];
$tip_pasaje = $_REQUEST["tip_p"];
$form_pago = $_REQUEST["fo_p"];


$mifecha = explode('/','/'.$fech_d);
$fecha_desde = $mifecha[3]."-".$mifecha[1]."-".$mifecha[2];

$mifechah = explode('/','/'.$fech_h);
$fecha_hasta = $mifechah[3]."-".$mifechah[1]."-".$mifechah[2];
                          
$sql = "SELECT
            v.nropasaje,
            v.FECHA,
            v.NOMBRE_Y_APELLIDO,
            v.dni,
            v.DIRECCION_DESTINO,
            v.NOMBRE_PERSONA_SEGURO,
            v.DNI_PERSONA_SEGURO,
            v.nro_cheque,
            v.importe_chq,
            v.total
            FROM v_pasajes_vendidos_con_pagos AS v
               WHERE (v.FECHA >= '".$fecha_desde."' and v.FECHA <= '".$fecha_hasta."') and 
               v.loc_origen = '".$loc_o."' and v.loc_destino='".$loc_d."' and v.precio = '".$tip_pasaje."' and v.forma_pago = '".$form_pago."'; ";   
//order by v.fecha asc;
$res = ejecutar_sql($db,$sql);
/*
echo $res;
die();
*/

if (!$res){  
    mensaje('Error accediendo a la tabla viaje...'); 
    die();
}else{ 
    $cant = $res -> RecordCount();
    $pasajes = ''; 
    if ($cant >= 1) {

         
    while (!$res->EOF){
        // nropasaje | fecha| nombreYapellido | dni |dir Destino | Pasajero aseg |dni pas aseg|nro cheque |importe cheque |total  
        $pasajes = $pasajes.$res->fields[0]."|".$res->fields[1]."|".$res->fields[2]."|".$res->fields[3]."|".$res->fields[4]."|".$res->fields[5]
                ."|".$res->fields[6]."|".$res->fields[7]."|".$res->fields[8]."|".$res->fields[9]."|";
        
        $v_total_lis = $v_total_lis + ($res -> fields[9]);
        
        $res->MoveNext();
         $pasajes = $pasajes."@";
        
    }
    }

}
                                    
set_var("v_cant_total",$cant);
set_var("v_total_listado",$v_total_lis);
//echo $pasajes; // No me mostraba porque tenia una columna de mas fields[10]
//die();

echo $pasajes;
desconectar($db);
die();
?>