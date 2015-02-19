<?php
//------------------------------------------------------------------------------
//// Realiza la carga de los datos de pasajes  por adelantados.
//------------------------------------------------------------------------------
include_once("seguridad.php");
include_once("template.php");
include_once("conexion.php");
include_once("funciones.php");
include_once("propia.php");

set_file("pasajes_adelantados", "pasajes_adelantados.html");

$db = conectar_al_servidor();

//----------------------------------------------------------------------------------------------------
//----------------------------------------------------------------------------------------------------
// Cargamos el comboBOX de TIPO DE PASAJES saque campo_interno en res[2] por tipo_pasaje
//----------------------------------------------------------------------------------------------------
$q = "SELECT codigo, CONCAT(tipo_pasaje,' - $:',precio) AS tipo_pas, tipo_pasaje  FROM tipo_pasajes WHERE activo='S' ORDER BY tipo_pasaje";
$res = ejecutar_sql($db, $q);
if (!$res) {
    echo $db->ErrorMsg(); //die();
} else {
    $combobox_tipo_pasaje = "<option value=0>Seleccione uno...</option>";
    $tipo_pasaje_operaciones =[];
    while (!$res->EOF) {//r$res->fields[2] vendria a ser tipo pasaje(comun, comun con carga o especial) , $res->fields[1]  seria tipo_pasaje-$:precio
         $combobox_tipo_pasaje =  $combobox_tipo_pasaje . "<option value=".$res->fields[2].">".$res->fields[1]."</option>";
         $res->MoveNext();
    }    
}
set_var("v_listado_tipo_pasaje",  $combobox_tipo_pasaje);

set_var("v_color_cabezera_tabla",    COLOR_ENCOMIENDAS_CABEZERA_TABLA);
set_var("v_color_cabezera_columna",  COLOR_ENCOMIENDAS_CABEZERA_COLUMNA);

set_var("v_color_origen",COLOR_FONDO_CARGA_DATOS_PASAJE_ORIGEN);
set_var("v_color_destino",COLOR_FONDO_CARGA_DATOS_PASAJE_DESTINO); 

$fecha = date('m/j/Y');
$nuevafecha = strtotime ( '+1 month' , strtotime ( $fecha ) ) ;
$nuevafecha = date ( 'm/j/Y' , $nuevafecha );


set_var("v_fecha_desde",  $fecha);



set_var("v_nombre", "");
set_var("v_cantidad", "1");
set_var("v_total", "0.00");
set_var("v_total_pago", "0.00");
set_var("v_cantidad_pago", "0.00");
set_var("v_detalle_pago", "");
set_var("v_total_pago", "0.00");
set_var("v_observaciones", "");
               

parse ('pasajes_adelantados');
pparse('pasajes_adelantados');

desconectar($db);
?>