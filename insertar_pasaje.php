<?php
//------------------------------------------------------------------------------
// Insertamos los registros en las siguientes tablas:
//          1) PASAJE
//          2) CHEQUES
//          4) PAGOS
//          5) CTACTES
//          6) DESTINOS_COMUNES_CLIENTES
//------------------------------------------------------------------------------
include_once("seguridad.php");
include_once("template.php");
include_once("conexion.php");
include_once("funciones.php");

//---------------------------------------------------------------------------------------------------------------
// modalidad de pasajes como parametros
//---------------------------------------------------------------------------------------------------------------
// // $listado_pasaje = "<pasaje 1>@<pasaje 2>@...@"
// pasaje_n = DNI| NOMBRE_Y_APELLIDO| ID_LOCALIDAD_ORIGEN| ID_LOCALIDAD_DESTINO| DIRECCION_ORIGEN|
//            DIRECCION_DESTINO| TELEFONO_ORIGEN| TELEFONO_DESTINO|CELULAR_ORIGEN| CELULAR_DESTINO| 
//            ID_TIPO_PASAJE| POSEE_SEGURO|DNI_PERSONA_SEGURO| NOMBRE_PERSONA_SEGURO| FECHA_NAC_PERSONA_SEGURO|
//            PRECIO| OBSERVACIONES|FORMA_VIAJE| PAGADO| FECHA| ID_VIAJE| NRO_ASIENTO| USUARIO|pago|
// pago = <pago 1>,<pago 2>,...
// pago n = tipo pago#monto pago######
//---------------------------------------------------------------------------------------------------------------

$lista_pasaje          = $_REQUEST['datos']; 
$forma_salida          = $_REQUEST['fv'];
$id_viaje              = $_REQUEST['id_viaje'];
$tipo                  = $_REQUEST['tipo'];
$detalle_pagos         = $_REQUEST["e_pagos"]; // Detalle de pago de la encomienda.
$destinos_comun        = $_REQUEST["e_destinos_comun"]; // Detalle del destino.
$usuario               = $_SESSION['usuario'];

$bandera_ejecucion_exitosa = TRUE;

$campos = "DNI, NOMBRE_Y_APELLIDO, ID_LOCALIDAD_ORIGEN, ID_LOCALIDAD_DESTINO, DIRECCION_ORIGEN, DIRECCION_DESTINO, TELEFONO_ORIGEN, TELEFONO_DESTINO,
           CELULAR_ORIGEN, CELULAR_DESTINO, ID_TIPO_PASAJE, POSEE_SEGURO,DNI_PERSONA_SEGURO, NOMBRE_PERSONA_SEGURO, FECHA_NAC_PERSONA_SEGURO, PRECIO, OBSERVACIONES,          
           FORMA_VIAJE, PAGADO, FECHA, ID_VIAJE, NRO_ASIENTO, USUARIO";

$lista = explode("@",$lista_pasaje); // generamos el arreglo de pasajes
//$det_pa = explode(",", $detalle_pagos);

$db = conectar_al_servidor_e_iniciar_transaccion();

if ($db){
    //$sql = "INSERT INTO pasajes (".$campos.") VALUES ";
    $imp_pasajs = '';

    // INSERTAMOS LOS PASAJES Y LUEGO EL PAGO PERTINENTE
    for ($i=0; $i<=(sizeof($lista)-2); $i++) {           
        $j++;
        $pasaje = explode("|",$lista[$i]);    
        // DNI, NOMBRE_Y_APELLIDO, ID_LOCALIDAD_ORIGEN, ID_LOCALIDAD_DESTINO, DIRECCION_ORIGEN, 
        // DIRECCION_DESTINO, TELEFONO_ORIGEN, TELEFONO_DESTINO    
        $datos =       " '".$pasaje[1]."','".
                            $pasaje[2]."',".
                            $pasaje[3].",".
                            $pasaje[4].",'".
                            $pasaje[5]."','".
                            $pasaje[6]."','".
                            $pasaje[7]."','".
                            $pasaje[8]."','".
        // CELULAR_ORIGEN, CELULAR_DESTINO, ID_TIPO_PASAJE, POSEE_SEGURO,DNI_PERSONA_SEGURO, 
        // NOMBRE_PERSONA_SEGURO, ,FECHA_NAC_PERSONA_SEGURO, PRECIO, OBSERVACIONES        
                            $pasaje[9]."','".
                            $pasaje[10]."',".
                            $pasaje[11].",'".
                            $pasaje[12]."','".
                            $pasaje[13]."','".
                            $pasaje[14]."','".
                            cambiaf_a_mysql($pasaje[15])."',".
                            $pasaje[16].",'".
                            $pasaje[17]."','".
        // FORMA_VIAJE, PAGADO, FECHA, ID_VIAJE, NRO_ASIENTO,  USUARIO       
                            $forma_salida."','".
                            $tipo."',CURRENT_DATE,".
                            $id_viaje.",".
                            $pasaje[0].",'".
                            $usuario."'";
     
  
        // generamos una lista de nro de asientos
        $imp_pasaje = $imp_pasajs.$pasaje[0]."|";
        
        
        $id_pasaje = ejecutar_sql_y_dar_id_con_transaccion($db, 'pasajes', $campos, $datos);
        
        
        if ($id_pasaje <= 0) 
            $bandera_ejecucion_exitosa = FALSE;
      
        // Si el registro de pasaje se efectuo correctamente
        if($id_pasaje>0){
            // ||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||
            // ||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||
            // ||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||
            //**************************************************************************
            //                              Escritura del  PAGO
            //**************************************************************************
            $det_pa = explode(",", $pasaje[18] ); // obtenemos el pago del pasaje  
    
            $cant_campo2 = 10;
            $cant_campo3 = 5;
            $cant_campo4 = 5;
    
            $id_cheque = "";
            $id_ctacte = "";
    
            $cheque_campos = " nro_cheque, id_banco, importe, fecha_emision, fecha_cobro, operacion_ingreso, propio, propietario, entregado_por, eliminado ";    
            $pago_campos   = " importe, forma_pago, fecha, id_cheque, id_pasaje ";
            $ctacte_campos = " id_cliente, id_encominda, id_pago, fecha ";
            //**************************************************************************
            // Entrada: [cadena1,cadena2,...]
            //           cadena1 : tipo pago|dato1|dato2|...
            //           dato1: detalle de pago|importe|datos adicion1|dato adicion2|...
            // *************************************************************************
            for ($k=0; $k<=sizeof($det_pa)-2; $k += 1) {           
                $det_pago = explode('#',$det_pa[$k]);
                for ($i=0; $i<=sizeof($det_pago)-2; $i += $cant_campo2) {           
         
                    //******************************************************************
                    //completamos el registro de pago con los ID de cheque si corresponde
                    // ------------------------------------------------------------------
                    if ($det_pago[0 + $i]==2){ // si es igaul a CHEQUE (2)
                        // armamos el sql para el registro de pago
                        //                         nro_cheque            id_banco               importe             fecha_emision
                        $cheque_datos = $det_pago[4 + $i].",".$det_pago[8 + $i].",".$det_pago[1 + $i].",'".$det_pago[2 + $i]."','".
                        //                         fecha_cobro       operacion_ingreso          propio              propietario
                                                 $det_pago[3 + $i]."','".$det_pago[5 + $i]."','".$det_pago[9 + $i]."','".$det_pago[6 + $i]."','".
                        //                        entregado_por  eliminado  
                                                 $det_pago[7 + $i]."','N'";
                        $id_cheque = ejecutar_sql_y_dar_id_con_transaccion($db, 'cheques',$cheque_campos,$cheque_datos); // obtenemos el ID del cheque   
                        if ($id_cheque <= 0) $bandera_ejecucion_exitosa = FALSE;
                    }else{
                        $id_cheque = 'NULL';                
                    } // fin del IF  de CHEQUES      
                    // -----------------------------------------------------------------
                    //------ Fin de la carga del cheque --------------------------------
                    //------------------------------------------------------------------

                    //------------------------------------------------------------------
                    // Insertamos el REGISTRO DE PAGO         
                    // CAMPOS ---->       importe,             forma_pago,            fecha,    id_cheque, id_pasaje 
                    $pagos_datos = $det_pago[1 + $i].",".$det_pago[0 + $i].", CURRENT_DATE, ".$id_cheque.",".$id_pasaje;
                    $id_pago = ejecutar_sql_y_dar_id_con_transaccion($db,'pagos',$pago_campos, $pagos_datos);
                    if ($id_pago <= 0) $bandera_ejecucion_exitosa = FALSE;
                    
                    //--------------------------------------------------------------------------------
                    // Se ejecuta despues, ya que se necesita el id de pago para el insert de CTA CTE
                    if ($det_pago[0 + $i]==3){ // si es igaul a Cta. Cte (3)
                        // armamos el sql para el registro de pago
                        //                  id_cliente  id_encomienda  id_pago    fecha_emision
                        $ctacte_datos = $det_pago[1 + $i].",".$id.",".$id_pago.",CURRENT_DATE";
                        $id_ctacte = ejecutar_sql_y_dar_id_con_transaccion($db, 'ctactes', $ctacte_campos, $ctacte_datos); // obtenemos el ID del ctacte   
                        if ($id_ctacte <= 0) $bandera_ejecucion_exitosa = FALSE;

                    }else{
                        $id_ctacte = '';
                    }             
                } // fin del for
            } // fin del for   
            // |||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||
            // ||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||        
            // ||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||
            // ||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||    
        } // fin de id_pasaje > 0
    }// fin del for de pasajes   

    if(!$bandera_ejecucion_exitosa){
        rollback_transaccion($db);
        echo $db->ErrorMsg();
    }else{  
        commit_transaccion($db);  
        echo "Operación exitosa";
    }    
} // fin del if conec normal    
?>  