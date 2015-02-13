<?php
//------------------------------------------------------------------------------
// Insertamos los registros en las siguientes tablas:
//          1) CHEQUES
//          2) PAGOS
//          5) CTACTES
// Modifiucamos el contenido en los siguientes          
//          6) PASAJES_ADELANTADO
//          7) pasajes
//          
//          
//          
//          
//          
/*         f_h    : $("#e_fecha_hasta").val(),                           
                           id_cli : $("#h_id_cliente").val(),
                           observ : $("#e_observaciones").val(),
                           cant   : $("#e_cantidad").val(),
                           total  : $("#e_mostrar_pago").val(),
                          raz : $("#e_nombre_pasaje").val(),
                           tipo_pas :$("#e_tipo_pasaje option:selected").attr('value')   
                                //id_viaje         : $("#h_id_viaje").val(),
                                //id               : $("#h_id_pasaje").val(),
                                //tipo             : 0, // indica que se esta pagando un pasaje reservado.
                                e_pagos          : pagos // datos del pago*/
//------------------------------------------------------------------------------
include_once("seguridad.php");
include_once("template.php");
include_once("conexion.php");
include_once("funciones.php");


$total      = $_POST["total"];
$cant       = $_POST["cant"];
$observ     = $_POST["observ"];

$razon      = $_POST["raz"];

$res            = $_POST['b_aceptar'];
$detalle_pagos  = $_POST["e_pagos"];    // Detalle de pago de la encomienda.
$id_cliente     = $_POST["id_cli"]; // id del cliente al que se le asignara el pago en la cta. cte.
$id_suc         = $_SESSION['id_sucursal'];
//$id             = $_POST['id'];        // id generico para que se pase el id de psaje o id encomienda o el id de pasajes adelantados
$tipo           = $_POST['tipo_pas']; 

$db = conectar_al_servidor_e_iniciar_transaccion();

//******************************************************************************
//                        Escritura del  PAGO
//******************************************************************************
$detalle_pagos1 = $detalle_pagos; // explode(",", $detalle_pagos);  

// cantidad de campos a escribir en cada tabla
$cant_campo2 = 10;
$cant_campo3 = 9; /* 4 */
$cant_campo4 = 3;
    
$id_cheque           = "";
$id_ctacte           = "";
$id_pasaje_reservado = "";
$id_tarjeta          = "";
$interes_tarjeta     = 0;

//------------------------------------------------------------------------------
// campos para inserciones de tablas 
$cheque_campos = " nro_cheque, id_banco, importe, fecha_emision, fecha_cobro, operacion_ingreso, propio, propietario, entregado_por, eliminado ";    
$pago_campos   = " importe, forma_pago, fecha, id_cheque, nro_sucursal, nro_recibo, id_tarjeta, porcentaje_tarjeta, id_reserva ";
$ctacte_campos = " id_cliente, id_pago, fecha ";


if($tipo==0){ // sies un pasaje 
$pago_campos = $pago_campos.', id_pasaje';
}
if($tipo==1){ // sies un pasaje adelantado
$pago_campos = $pago_campos.', id_reserva';
}
if($tipo==2){ // sies un encomienda
$pago_campos = $pago_campos.', id_encomienda';
}

//------------------------------------------------------------------------------
// campos  para modificaciones de tablas TERMINARRRRRRRRRRRRRRR QUE ES CANTIDAD
$pasaje_adelantado_campos =  " cantidad "; 


$mifecha = explode('/','/'.$f_d);
$fecha_desde = $mifecha[3]."-".$mifecha[1]."-".$mifecha[2];

$mifechah = explode('/','/'.$f_h);
$fecha_hasta = $mifechah[3]."-".$mifechah[1]."-".$mifechah[2];
$est= 'S';

$sql = "INSERT INTO pasajes_adelantados (fecha_emision, fecha_vensimiento, observaciones, estado, cantidad,id_cliente) VALUES ('".$fecha_desde."','".$fecha_hasta."','".$observ."','".$est."','".$cant."','".$id_cliente."');";
//$sql = "CALL crear_pasajes_adelantados('".$fecha_desde."','".$fecha_hasta."','".$observ."','".$id_cliente."','".$cant."');";
$res = ejecutar_sql($db,$sql);

//------------------------------------------------------------------------------
// Incrementamos el nro de recibo que esta asociado a la sucursal
//------------------------------------------------------------------------------
$tabla_nro_recibo  = "sucursales";
$campos_nro_recibo = "nro_recibo";
$rs = mysql_query("SELECT MAX(".$campos_nro_recibo.") AS id FROM ".$tabla_nro_recibo." WHERE codigo=".$id_suc);

if ($row = mysql_fetch_row($rs)) {
    $nro_recibo = trim($row[0]) + 1;
    mysql_query("UPDATE ".$tabla_nro_recibo ." SET ".$campos_nro_recibo."=".$nro_recibo." where codigo = ".$id_suc);     
}                
//--------------------- Fin de incremento de nro recibo ------------------------

//******************************************************************************
// Entrada: [cadena1,cadena2,...]
//           cadena1 : tipo pago|dato1|dato2|...
//           dato1: detalle de pago|importe|datos adicion1|dato adicion2|...
//              tipo pago = 1: contado, 2: cheque, 3: Cta. cte., 4: pago destino, 5: tarjeta, 6: reserva de pagos
// *****************************************************************************
for ($k=0; $k<=sizeof($detalle_pagos1)-1; $k += 1) {           

    $detalle_pagos12 = explode('#',$detalle_pagos1[$k]);
    
    for ($i=0; $i<=sizeof($detalle_pagos12)-1; $i += $cant_campo2) {           
        
        //**********************************************************************
        //completamos el registro de pago con los ID de cheque si corresponde
        // ---------------------------------------------------------------------
        if ($detalle_pagos12[0 + $i]==2){ // si es igual a CHEQUE (2)
            // armamos el sql para el registro de pago
            //                         nro_cheque            id_banco               importe             fecha_emision
            $cheque_datos = $detalle_pagos12[4 + $i].",".$detalle_pagos12[8 + $i].",".$detalle_pagos12[1 + $i].",'".$detalle_pagos12[2 + $i]."','".
            //                         fecha_cobro       operacion_ingreso          propio              propietario
            $detalle_pagos12[3 + $i]."','".$detalle_pagos12[5 + $i]."','".$detalle_pagos12[9 + $i]."','".$detalle_pagos12[6 + $i]."','".
            //                        entregado_por  eliminado  
            $detalle_pagos12[7 + $i]."','N'";
            $id_cheque = ejecutar_sql_y_dar_id_con_transaccion($db, 'cheques',$cheque_campos,$cheque_datos); // obtenemos el ID del cheque   
                
        }else{
             $id_cheque = 'NULL';                
        } // fin del IF  de CHEQUES    
        // ---------------------------------------------------------------------
        //------ Fin de la carga del cheque ------------------------------------
        //----------------------------------------------------------------------        
        if($detalle_pagos12[0 + $i]==5){ // si es un pago con tarjeta
            $id_tarjeta = $detalle_pagos12[2 + $i]; // obtenemos el id de la tarjeta
            $interes_tarjeta = $detalle_pagos12[3 + $i]; // obtenemos el interes de la tarjeta
        }else{
            $id_tarjeta = 'NULL';  
            $interes_tarjeta = 'NULL';
        }
        
        if($detalle_pagos12[0 + $i]==6){ // si es reserva de pasaje el pago
            $id_pasaje_reservado = $detalle_pagos12[2 + $i]; // obtenemos el id de donde se desconto el pasaje            //

            // Restamos 1 pasaje a la cuenta referenciada por ID            
             actualizar_registro_con_transaccion($db,'pasajes_adelantados','cantidad = (cantidad - 1)','id_cliente = '.$id_pasaje_reservado );
             
        }else{
            $id_pasaje_reservado = 'NULL';  
        }
        
        
        //----------------------------------------------------------------------
        // Insertamos el REGISTRO DE PAGO         
        // CAMPOS ---->       importe,                 forma_pago,                 fecha,       id_cheque,    id_sucursal    nro_recibo      tarjeta          interes,            id_reserva de pasaje                                  
        $pagos_datos = $detalle_pagos12[1 + $i].",".$detalle_pagos12[0 + $i].", CURRENT_DATE, ".$id_cheque.", ".$id_suc.", ".$nro_recibo.", ".$id_tarjeta.", ".$interes_tarjeta.", ".$id_pasaje_reservado.", ".$id;
        
        $id_pago = ejecutar_sql_y_dar_id_con_transaccion($db, 'pagos', $pago_campos, $pagos_datos);
        
        if($id_pago!=-1){// SI no devuelve -1 es porque se realizo el pago y se marca como pagado en pasaje.
            mysql_query("UPDATE pasajes SET PAGADO = 'V' where codigo = ".$id);                 
            
        }
            
        //----------------------------------------------------------------------
        // Se ejecuta despues, ya que se necesita el id de pago para el insert de CTA CTE
        $ctacte_datos = $id_cliente.",".$id_pago.",CURRENT_DATE";
        $id_ctacte = ejecutar_sql_y_dar_id_con_transaccion($db, 'ctactes', $ctacte_campos, $ctacte_datos); // obtenemos el ID del ctacte   
  
       }
    }    
    //$res3=ejecutar_sql($db, $sql_b); 
//    $id_pago = ejecutar_sql_y_dar_id_con_transaccion($db,'pagos',$pagos_campos, $pagos_datos);
// echo 'pagos ----- '.$pago_campos."<<--->>".$pagos_datos;
commit_transaccion($db); 
    
?>  