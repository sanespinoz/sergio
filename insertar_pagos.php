<?php
//------------------------------------------------------------------------------
// Insertamos los registros en las siguientes tablas:
//          1) CHEQUES
//          2) PAGOS
//          5) CTACTES
// Modifiucamos el contenido en los siguientes          
//          6) PASAJES_ADELANTADO
//------------------------------------------------------------------------------
include_once("seguridad.php");
include_once("template.php");
include_once("conexion.php");
include_once("funciones.php");

$res            = $_REQUEST['b_aceptar'];
$detalle_pagos  = $_REQUEST["e_pagos"];    // Detalle de pago de la encomienda.
$id_cliente     = $_REQUEST["id_cliente"]; // id del cliente al que se le asignara el pago en la cta. cte.
$id_suc         = $_SESSION['id_sucursal'];

echo $detalle_pagos; die();
$db = conectar_al_servidor_e_iniciar_transaccion();
//**************************************************************************
//                        Escritura del  PAGO
//**************************************************************************
$det_pa = explode(",", $detalle_pagos);  

// cantidad de campos a escribir en cada tabla
$cant_campo2 = 10;
$cant_campo3 = 4;
$cant_campo4 = 3;
    
$id_cheque           = "";
$id_ctacte           = "";
$id_pasaje_reservado = "";
$id_tarjeta          = "";
$interes_tarjeta     = 0;
//----------------------------------
// campos para inserciones de tablas 
$cheque_campos = " nro_cheque, id_banco, importe, fecha_emision, fecha_cobro, operacion_ingreso, propio, propietario, entregado_por, eliminado ";    
$pago_campos   = " importe, forma_pago, fecha, id_cheque, nro_sucursal, nro_recibo, id_tarjeta, porcentaje_tarjeta, id_reserva ";
$ctacte_campos = " id_cliente, id_pago, fecha ";
//--------------------------------------
// campos  para modificaciones de tablas
$pasaje_adelantado_campos =  " cantidad "; 

//------------------------------------------------------------------
// Incrementamos el nro de recibo que esta asociado a la sucursal
//------------------------------------------------------------------
$tabla_nro_recibo  = "sucursales";
$campos_nro_recibo = "nro_recibo";
$rs = mysql_query("SELECT MAX(".$campos_nro_recibo.") AS id FROM ".$tabla_nro_recibo." WHERE codigo=".$id_suc);

if ($row = mysql_fetch_row($rs)) {
    $nro_recibo = trim($row[0]) + 1;
    mysql_query("UPDATE ".$tabla_nro_recibo ." SET ".$campos_nro_recibo."=".$nro_recibo." where codigo = ".$id_suc);     
}                
//---------------- Fin de incremento de nro recibo --------------------

//**************************************************************************
// Entrada: [cadena1,cadena2,...]
//           cadena1 : tipo pago|dato1|dato2|...
//           dato1: detalle de pago|importe|datos adicion1|dato adicion2|...
//              tipo pago = 1: contado, 2: cheque, 3: Cta. cte., 4: pago destino, 5: tarjeta, 6: reserva de pagos
// *************************************************************************
for ($k=0; $k<=sizeof($det_pa)-1; $k += 1) {           
    $det_pago = explode('#',$det_pa[$k]);
    for ($i=0; $i<=sizeof($det_pago)-1; $i += $cant_campo2) {           
        //******************************************************************
        //completamos el registro de pago con los ID de cheque si corresponde
        // ------------------------------------------------------------------
        if ($det_pago[0 + $i]==2){ // si es igual a CHEQUE (2)
            // armamos el sql para el registro de pago
            //                         nro_cheque            id_banco               importe             fecha_emision
            $cheque_datos = $det_pago[4 + $i].",".$det_pago[8 + $i].",".$det_pago[1 + $i].",'".$det_pago[2 + $i]."','".
            //                         fecha_cobro       operacion_ingreso          propio              propietario
            $det_pago[3 + $i]."','".$det_pago[5 + $i]."','".$det_pago[9 + $i]."','".$det_pago[6 + $i]."','".
            //                        entregado_por  eliminado  
            $det_pago[7 + $i]."','N'";
            $id_cheque = ejecutar_sql_y_dar_id_con_transaccion($db, 'cheques',$cheque_campos,$cheque_datos); // obtenemos el ID del cheque   
                
        }else{
             $id_cheque = 'NULL';                
        } // fin del IF  de CHEQUES    
        // -----------------------------------------------------------------
        //------ Fin de la carga del cheque --------------------------------
        //------------------------------------------------------------------        
        
        if($det_pago[0 + $i]==5){ // si es un pago con tarjeta
            $id_tarjeta = $det_pago[2 + $i]; // obtenemos el id de la tarjeta
            $interes_tarjeta = $det_pago[3 + $i]; // obtenemos el interes de la tarjeta
        }else{
            $id_tarjeta = 'NULL';  
            $interes_tarjeta = 'NULL';
        }
        
        if($det_pago[0 + $i]==6){ // si es reserva de pasaje el pago
            $id_pasaje_reservado = $det_pago[2 + $i]; // obtenemos el id de donde se desconto el pasaje            //

            // Restamos 1 pasaje a la cuenta referenciada por ID            
             actualizar_registro_con_transaccion($db,'pasajes_adelantados','cantidad = (cantidad - 1)','id_cliente = '.$id_pasaje_reservado );
             
        }else{
            $id_pasaje_reservado = 'NULL';  
        }
              
        //------------------------------------------------------------------
        // Insertamos el REGISTRO DE PAGO         
        // CAMPOS ---->       importe,             forma_pago,            fecha,    id_cheque,  
        $pagos_datos = $det_pago[1 + $i].",".$det_pago[0 + $i].", CURRENT_DATE, ".$id_cheque.", ".$id_suc.", ".$nro_recibo.", ".$id_tarjeta.", ".$interes_tarjeta.", ".$id_pasaje_reservado;
        $id_pago = ejecutar_sql_y_dar_id_con_transaccion($db,'pagos',$pago_campos, $pagos_datos);
            
        //--------------------------------------------------------------------------------
        // Se ejecuta despues, ya que se necesita el id de pago para el insert de CTA CTE
        $ctacte_datos = $id_cliente.",".$id_pago.",CURRENT_DATE";
        $id_ctacte = ejecutar_sql_y_dar_id_con_transaccion($db, 'ctactes', $ctacte_campos, $ctacte_datos); // obtenemos el ID del ctacte   
  
       }
    }    
    //$res3=ejecutar_sql($db, $sql_b); 
//    $id_pago = ejecutar_sql_y_dar_id_con_transaccion($db,'pagos',$pagos_campos, $pagos_datos);
echo 'pagos ----- '.$pagos_campos."---". $pagos_datos;
    //commit_transaccion($db); 
    
//    echo($id_suc."-".$nro_recibo);
    
?>  