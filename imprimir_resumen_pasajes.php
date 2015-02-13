<?php

include_once ("seguridad.php");
include_once ("template.php");
include_once ("conexion.php");
include_once ("funciones.php");

include_once('./html2fpdf/html2pdf.class.php');

ob_start();

$fech_d  = $_REQUEST["fdesde"];
$fech_h  = $_REQUEST["fhasta"];
$loc_o = $_REQUEST["locori"];
$loc_d  = $_REQUEST["ldest"];
$tip_pasaje = $_REQUEST["tipo"]; //c, p o E o forma viaje
$for_pago = $_REQUEST["forma_p"]; //1,2,3,4
$id_suc = $_SESSION['id_sucursal'];



//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
//                       Cargamos el membrete
//------------------------------------------------------------------------------

set_file("imprimir_resumenpasajes", "imprimir_resumen_pasajes.html");

/*
set_var("v_fecha_desde", $fecha_desde);
// fecha_desde
set_var("v_fecha_hasta", $fecha_hasta);
// fecha_hasta

*/
set_var("v_path_logo",LOGO);
set_var("v_razon_social",RAZON_SOCIAL);
set_var("v_direc_razon_social",DIREC_RAZON_SOCIAL);
set_var("v_tel_razon_social",TEL_RAZON_SOCIAL);
set_var("v_sucursal", $_SESSION['sucursal']);
// fecha_hasta


// sumatoria de cta cte
set_var("v_vendedor", $_SESSION['usuario']);
// fecha de impresion
set_var("v_fecha", Date(" d/m/Y - H:i") );
set_var("v_forma_pago",$for_pago);
set_var("v_tipo_pasaje",$tip_pasaje);
set_var("v_loc_or",$loc_o);
set_var("v_loc_de",$loc_d);
$db = conectar_al_servidor();

// Indica la cantidad de registros encontrados.
//----------------------------------------------------------------------------------------------------
//----------------------------------------------------------------------------------------------------
//                       MUESTRA TODOS LOS REGISTROS DE Los pasajes pagos
//----------------------------------------------------------------------------------------------------
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
               v.loc_origen = '".$loc_o."' and v.loc_destino='".$loc_d."' and v.precio = '".$tip_pasaje."' and v.forma_pago = '".$for_pago."'; ";   
//order by v.fecha asc; 

//echo $sql;
//die();



$res = ejecutar_sql($db,$sql);
$v_total_lis = 0;
//var_dump($res);
//die();
//----------------------------------------------------------------------------------------------------
// Verificamos el resultado de la busqueda.
//----------------------------------------------------------------------------------------------------
if (!$res) {
    echo $db -> ErrorMsg();
    die();
} else {

    $cant = $res -> RecordCount();
   
    if ($cant >= 1) {
        while (!$res -> EOF) {
          // set_var("v_fecha_entregal", cambiaf_a_normal($res -> fields[12]));
           
            set_var("v_nro_pasaje", $res -> fields[0]);
            set_var("v_fecha", $res -> fields[1]);
            set_var("v_pasajero", $res -> fields[2]);
            set_var("v_dni_pas", $res -> fields[3]);
            set_var("v_dir_d", $res -> fields[4]);
            set_var("v_pasajero_seg", $res -> fields[5]);
            set_var("v_dni_pas_seg", $res -> fields[6]);
            set_var("v_nro_ch", $res -> fields[7]);
            set_var("v_importe_ch", $res -> fields[8]);
            set_var("v_total", $res -> fields[9]);
           
            parse('imprimir_resumenpasajes_pagos');
            $v_total_lis = $v_total_lis + ($res -> fields[9]);
            
            $res -> MoveNext();
	}// fin del while

    }
    
    }
    

desconectar($db);


set_var("v_cant_total", $cant);
set_var("v_total_listado", $v_total_lis);   

pparse("imprimir_resumenpasajes");

// Impresion en PDF

$htmlbuffer=ob_get_contents();
ob_clean();
try{ 
   $fecha = date("ymdhm");   
//   $html2pdf = new HTML2PDF('P', 'A4', 'es');   
   $html2pdf = new HTML2PDF('l','A4','es', false, 'UTF-8', array(5, 5, 5, 10));
   $html2pdf->pdf->SetDisplayMode('fullpage'); 
   $html2pdf->writeHTML($htmlbuffer, isset($_GET['vuehtml']));
   $html2pdf->Output('./resumen_pasajes'.$fecha.'.pdf', 'I');
}
catch(HTML2PDF_exception $e) {
    echo $e;
    exit;
}        

?>

