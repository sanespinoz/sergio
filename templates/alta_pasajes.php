<?php
//----------------------------------------------------------------------------------------------------------
// Realiza la carga de los datos del pasaje y el pago del mismo.
//----------------------------------------------------------------------------------------------------------
include_once("seguridad.php");
include_once("template.php");
include_once("conexion.php");
include_once("funciones.php");


// obtenemos el id del viaje...
$venta_o_reserva = $_REQUEST["t"]; // identificamos si es una venta (V) o reserva (R)
$id_viaje        = $_REQUEST["id_viaje"]; // codigo del viaje a donde se asociara el pasaje.
$lista_pasajes   = $_REQUEST["asientos"]; // lista de asientos a los cuales se asociaran con el viaje
$id_loc_desde    =  $_REQUEST["id_ld"];
$id_loc_hasta    = $_REQUEST["id_lh"];
$forma_viaje     = $_REQUEST["fv"];



set_file("altapasajes", "alta_pasajes.html");

$db = conectar_al_servidor();

set_var('v_forma_salida', $forma_viaje);
 
$lp =  explode("|",$lista_pasajes);

$td = '<table>';
$sl = ''; 
for($i=0;$i<=(sizeof($lp)-2); $i++){    
    $td = $td."<tr><td>".$lp[$i]."</td><td>Libre</td></tr>";
    $sl = $sl." <option value=".$lp[$i].">Asiento nro: ".$lp[$i]."</option>";
}
$td = $td."</table>";

set_var("v_listado_asientos_seleccionados", $td);  //
set_var("v_listado_nro_asiento", $sl);
set_var("v_venta_o_reserva",$venta_o_reserva);

//----------------------------------------------------------------------------------------------------
//----------------------------------------------------------------------------------------------------
// Cargamos el comboBOX de TIPO DE PASAJES
//----------------------------------------------------------------------------------------------------
$q = "SELECT codigo, CONCAT(tipo_pasaje,' - $:',precio) AS tipo_pasaje, campo_interno FROM tipo_pasajes WHERE activo='S' ORDER BY tipo_pasaje";
$res = ejecutar_sql($db, $q);
if (!$res) {
    echo $db->ErrorMsg(); //die();
} else {

    $combobox_tipo_pasaje = "<option value=0>Seleccione uno...</option>";
//    $tipo_pasaje_operaciones =[];

    while (!$res->EOF) {
         $combobox_tipo_pasaje =  $combobox_tipo_pasaje . "<option value=".$res->fields[0]."#".$res->fields[2].">".$res->fields[1]."</option>";
         //$tipo_pasaje_operaciones[$res->fields[0]] = $res->fields[2]; // guarda la confi de trabajo para el tipo de pasaje.
         $res->MoveNext();
    }    


}
set_var("v_listado_tipo_pasaje",  $combobox_tipo_pasaje);
//set_var("h_operaciones_tipo_pasajes",$tipo_pasaje_operaciones);


//----------------------------------------------------------------------------------------------------
//----------------------------------------------------------------------------------------------------
// Cargamos el comboBOX de LOCALIDAD REMITENTE y DESTINATARIO
//----------------------------------------------------------------------------------------------------
$q = "select l.codigo, l.localidad  from localidades l order by  l.localidad ";
$res = ejecutar_sql($db, $q);
if (!$res) {
    echo $db->ErrorMsg(); //die();
} else {
    while (!$res->EOF) {

        // remitente -------------------------------------------------------------- 
        if ($id_loc_desde == $res->fields[0]) {
            $combobox_loc_remitente = $combobox_loc_remitente . "<option value=" .
                    $res->fields[0] . " selected=true>" . $res->fields[1] . "</option>";
        } else {
            $combobox_loc_remitente = $combobox_loc_remitente . "<option value=" .
                    $res->fields[0] . ">" . $res->fields[1] . "</option>";
        }
        // destinatario -----------------------------------------------------------
        if ($id_loc_hasta == $res->fields[0]) {
            $combobox_loc_destinatario = $combobox_loc_destinatario . "<option value=" .
                    $res->fields[0] . " selected=true>" . $res->fields[1] . "</option>";
        } else {
            $combobox_loc_destinatario = $combobox_loc_destinatario . "<option value=" .
                    $res->fields[0] . ">" . $res->fields[1] . "</option>";
        }
        $res->MoveNext();
    }
}

set_var("v_listado_localidad_origen", $combobox_loc_remitente);
set_var("v_listado_localidad_destino",$combobox_loc_destinatario);
set_var("v_loc_or", $id_loc_desde); // guardamos la loc origen de la salida
set_var("v_loc_de", $id_loc_hasta); // guardamos la loc destino de la salida

set_var("v_color_cabezera_tabla",    COLOR_ENCOMIENDAS_CABEZERA_TABLA);
set_var("v_color_cabezera_columna",  COLOR_ENCOMIENDAS_CABEZERA_COLUMNA);
set_var("v_datos_de_la_salidas", "");  //

set_var("v_id_viaje", $id_viaje);
set_var("v_nro_asiento",   "");
set_var("v_dni_pasaje",    "");
set_var("v_nombre_pasaje", "");

set_var("v_direccion_origen_pasaje",  "");
set_var("v_direccion_destino_pasaje", "");
set_var("v_telefono_origen_pasaje",   "");
set_var("v_telefono_destino_pasaje",  "");
set_var("v_celular_origen_pasaje",    "");
set_var("v_celular_destino_pasaje",   "");
                        
set_var("v_coseguro_pasaje", "");   
set_var("v_dni_coseguro_pasaje", "");                        
set_var("v_nombre_coseguro_pasaje", "");

set_var("v_importe_pasaje", "0.00");
set_var("v_porsentaje_coseguro", PORCENTAJE_COSEGURO);
set_var("v_importe_coseguro_pasaje", "0.00");
                
set_var("v_nro_siento_cliente", "");    
set_var("v_nombre_cliente", "");    
set_var("v_direccion_cliente", ""); 
set_var("v_tel_cliente", "");   
set_var("v_importe_cliente", "");
set_var("v_direccion_cliente", "");
set_var("v_observaciones", "");


set_var("v_color_origen",COLOR_FONDO_CARGA_DATOS_PASAJE_ORIGEN);
set_var("v_color_destino",COLOR_FONDO_CARGA_DATOS_PASAJE_DESTINO); 

set_var("v_total_pago", "0.00");

set_var("v_lineas",LINEAS);


set_var("v_color_mando_botonera_mando",'#4d4d4d');
set_var("v_color_fondo_boton_mando",'#333');
set_var("v_color_texto_boton_mando",'#333');


parse ('altapasajes');
pparse('altapasajes');

desconectar($db);
?>