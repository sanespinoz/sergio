<?php
include_once("seguridad.php");
include_once("template.php");
include_once("conexion.php");
include_once("funciones.php");
include_once("propia.php");

/*
$f_d=$_POST['e_fecha_desde'];
$f_h=$_POST['e_fecha_hasta'];
$id_loc_desde    =  $_REQUEST["id_ld"];
$id_loc_hasta    = $_REQUEST["id_lh"];
$tipo_pasaje=$_POST['e_tipo_pasaje'];
 	*/
$id_suc = $_SESSION['id_sucursal'];	
set_file("resumenpasajes", "resumen_pasajes.html");
set_var("v_sucursal", $_SESSION['sucursal']);
  
set_var("v_vendedor",$_SESSION['usuario']  );
        
$db = conectar_al_servidor();

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



//----------------------------------------------------------------------------------------------------
//----------------------------------------------------------------------------------------------------
// Cargamos el comboBOX de TIPO DE PASAJES
//----------------------------------------------------------------------------------------------------



$q = "SELECT precio, tipo_pasaje, campo_interno FROM tipo_pasajes WHERE activo='S' ORDER BY tipo_pasaje";
$res = ejecutar_sql($db, $q);
if (!$res) {
    echo $db->ErrorMsg(); //die();
} else {

    $combobox_tipo_pasaje = "<option value=0>Seleccione uno...</option>";
//    $tipo_pasaje_operaciones =[];

    while (!$res->EOF) {
         $combobox_tipo_pasaje =  $combobox_tipo_pasaje . "<option value=".$res->fields[0].">".$res->fields[1]."</option>";
         //$tipo_pasaje_operaciones[$res->fields[0]] = $res->fields[2]; // guarda la confi de trabajo para el tipo de pasaje.
         $res->MoveNext();
    }    


}

set_var("v_listado_tipo_pasaje",  $combobox_tipo_pasaje);
//set_var("h_operaciones_tipo_pasajes",$tipo_pasaje_operaciones);

set_var("v_color_cabezera_tabla",    COLOR_ENCOMIENDAS_CABEZERA_TABLA);
set_var("v_color_cabezera_columna",  COLOR_ENCOMIENDAS_CABEZERA_COLUMNA);

set_var("v_color_origen",COLOR_FONDO_CARGA_DATOS_PASAJE_ORIGEN);
set_var("v_color_destino",COLOR_FONDO_CARGA_DATOS_PASAJE_DESTINO); 

$fecha = date('m/j/Y');
$nuevafecha = strtotime ( '+1 month' , strtotime ( $fecha ) ) ;
$nuevafecha = date ( 'm/j/Y' , $nuevafecha );


//set_var("v_fecha_desde",  $fecha);
//set_var("v_fecha_hasta",$nuevafecha);


set_var("v_total_listado", "0.00");
set_var("v_cant_total", "0");









/*set_var("v_nombre", "");

set_var("v_total", "0.00");
set_var("v_total_pago", "0.00");
set_var("v_cantidad_pago", "0.00");
set_var("v_detalle_pago", "");

set_var("v_observaciones", "");
   */

parse ('resumenpasajes');
pparse('resumenpasajes');

desconectar($db);
?>