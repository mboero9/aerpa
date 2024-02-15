<?
header('Content-type: text/html; charset=ISO-8859-1');
require_once("../includes/lib.php");
//nueva version
if (!empty($_GET['tipo'])) {
	switch($_GET['tipo']) {
	case 'desregistro':
		if (!empty($_GET['numero'])) {
				$Sql="Select REG_DESCRIP, REG_TIPO, REG_CPA 
					    From REG_AUTOM
					   where REG_COD_INT=".sqlstring($_GET['numero']);		  
				$rs = $conn->execute($Sql);
				 if (!$rs->EOF) {
				  	echo utf8_encode($rs->fields["REG_DESCRIP"])."|".
				  	     $rs->fields["REG_TIPO"]."|".$rs->fields["REG_CPA"];					
				 }else{ 
				  	echo "Inexistente";
				 }
		}//if vino parametro eleccion
		break;
	case 'grabar':	
			$conn->StartTrans();
			try {			
				$Sql="select REG_CODIGO from REG_AUTOM where REG_COD_INT=".sqlstring($_GET['regOrigen']);			
				$rs = $conn->Execute($Sql);		
				$id_origen=$rs->fields["REG_CODIGO"];		
//echo "<br>".$Sql;										  				
				$Sql="select REG_CODIGO from REG_AUTOM where REG_COD_INT=".sqlstring($_GET['regDestino']);							
				$rs = $conn->Execute($Sql);				
				$id_destino=$rs->fields["REG_CODIGO"];		
//echo "<br>".$Sql;										  								
				$tmp = new Date($_GET["FecRetiro"]);
				$Sql="Insert into TRAMITE (TRA_CODIGO,
										   TRA_FECHA_RETIRO,
										   REG_CODIGO_ORI,
										   REG_CODIGO_DES,
										   TRA_DOMINIO,
										   TRA_NRO_VOUCHER, 
										   TRA_FECHA_CARGA, 
										   USR_ID_CARGA
										  )
								   VALUES (".sqlint(numerador('TRAMITE')).",
										   ".sqldate($tmp->format(FMT_DATE_ISO)).",
										   ".sqlint($id_origen).", 
										   ".sqlint($id_destino).", 										   
										   ".sqlstring($_GET['NroTramite']).",								   								   
										   ".sqlstring(sprintf("%08s", $_GET['NroVoucher'])).",
										   ".sqldate(dbtime()).", 
										   ".sqlint($_GET['usuario'])."								   								   								   
										  )";
//echo "<br>.$Sql;					  
				$rs = $conn->Execute($Sql);
				echo 'ok';
			} catch (exception $e) {
				dbhandleerror($e);
			}				
			$conn->CompleteTrans();
		break;	
	case 'borrar':	
			$conn->StartTrans();
            
            // Antes de eliminar el registro hago LOG en la tabla BAJA_TRAMITE
            $sql_datos_tramite = "SELECT * FROM TRAMITE WHERE tra_dominio =".sqlstring($_GET['NroTramite'])." AND tra_nro_voucher =".sqlstring(sprintf("%08s", $_GET['NroVoucher']));
            
            $rs_tramite = $conn->Execute($sql_datos_tramite);

            $sql_insert_baja = "INSERT INTO BAJA_TRAMITE
                               (baja_usuario_id
                               ,baja_fecha
                               ,tra_codigo
                               ,tra_fecha_retiro
                               ,reg_codigo_ori
                               ,reg_codigo_des
                               ,tra_dominio
                               ,tra_nro_voucher
                               ,tra_fecha_carga
                               ,tra_fecha_entrega
                               ,mot_codigo
                               ,usr_id_carga
                               ,rem_id_ori
                               ,rem_id_des
                               ,usr_id_ent_dev
                               ,usr_id_act
                               ,tra_fecha_act
                               ,tra_nro_imp
                               ,tra_fecha_proceso) VALUES (
                               ".$_GET['usuario'].",
                               ".sqldate(dbtime()).", ".$rs_tramite->fields["tra_codigo"].",
                               ".sqldate($rs_tramite->fields["tra_fecha_retiro"]).",
                               ".sqlint($rs_tramite->fields["reg_codigo_ori"]).",
                               ".sqlint($rs_tramite->fields["reg_codigo_des"]).",
                               ".sqlstring($rs_tramite->fields["tra_dominio"]).",
                               ".sqlint($rs_tramite->fields["tra_nro_voucher"]).",
                               ".sqldate($rs_tramite->fields["tra_fecha_carga"]).",
                               ".sqldate($rs_tramite->fields["tra_fecha_entrega"]).",
                               ".sqlint($rs_tramite->fields["mot_codigo"]).",
                               ".sqlint($rs_tramite->fields["usr_id_carga"]).",
                               ".sqlint($rs_tramite->fields["rem_id_ori"]).",
                               ".sqlint($rs_tramite->fields["rem_id_des"]).",
                               ".sqlint($rs_tramite->fields["usr_id_ent_dev"]).",
                               ".sqlint($rs_tramite->fields["usr_id_act"]).",
                               ".sqldate($rs_tramite->fields["tra_fecha_act"]).",
                               ".sqlint($rs_tramite->fields["tra_nro_imp"]).",
                               ".sqldate($rs_tramite->fields["tra_fecha_proceso"]).")";
        
                    $conn->Execute($sql_insert_baja);
            
			try {			
				$Sql="delete from tramite where tra_dominio = ".sqlstring($_GET['NroTramite'])." and tra_nro_voucher = ".sqlstring(sprintf("%08s", $_GET['NroVoucher']));								   								   								   												  
				$conn->Execute($Sql);
				echo 'ok';
			} catch (exception $e) {
				dbhandleerror($e);
			}				
			$conn->CompleteTrans();
		break;
	case 'buscar2':
		if ((!empty($_GET['NroTramite']))||(!empty($_GET['NroVoucher']))) {
			
				if (!empty($_GET['NroVoucher']))  $_GET['NroVoucher'] = sprintf("%08s", $_GET['NroVoucher']);
				$Sql="Select a.TRA_CODIGO, 
							 ".$conn->SQLDate(FMT_DATE_DB, "a.TRA_FECHA_RETIRO")." as TRA_FECHA_RETIRO,	
							 a.REG_CODIGO_ORI,
							 a.REG_CODIGO_DES,
							 b.REG_DESCRIP as DES_ORIGEN,
							 c.REG_DESCRIP as DES_DESTINO,
							 ".$conn->SQLDate(FMT_DATE_DB, "a.TRA_FECHA_ENTREGA")." as TRA_FECHA_ENTREGA,
							 a.MOT_CODIGO, 
							 a.TRA_DOMINIO,
							 a.TRA_NRO_VOUCHER,
							 a.REM_ID_ORI 
					    From TRAMITE a
				  inner join REG_AUTOM b on a.REG_CODIGO_ORI = b.REG_CODIGO
				  inner join REG_AUTOM c on a.REG_CODIGO_DES = c.REG_CODIGO
					   where a.TRA_DOMINIO=".sqlstring($_GET['NroTramite'])." 
					      or a.TRA_NRO_VOUCHER=".sqlstring($_GET['NroVoucher'])."
					order by a.TRA_FECHA_RETIRO desc";		  
//echo $Sql;					   
				$rs = $conn->execute($Sql);
				if ($rs->EOF) { echo "Inexistente"; }else
				{
					$Sql="Select a.TRA_CODIGO, 
							 ".$conn->SQLDate(FMT_DATE_DB, "a.TRA_FECHA_RETIRO")." as TRA_FECHA_RETIRO,	
							 a.REG_CODIGO_ORI,
							 a.REG_CODIGO_DES,
							 b.REG_DESCRIP as DES_ORIGEN,
							 c.REG_DESCRIP as DES_DESTINO,
							 ".$conn->SQLDate(FMT_DATE_DB, "a.TRA_FECHA_ENTREGA")." as TRA_FECHA_ENTREGA,
							 a.MOT_CODIGO, 
							 a.TRA_DOMINIO,
							 a.TRA_NRO_VOUCHER,
							 a.REM_ID_ORI 
					    From TRAMITE a
				  inner join REG_AUTOM b on a.REG_CODIGO_ORI = b.REG_CODIGO
				  inner join REG_AUTOM c on a.REG_CODIGO_DES = c.REG_CODIGO
					   where /*(a.rem_id_ori IS NULL and a.rem_id_des IS NULL) and*/ 
					   (a.TRA_DOMINIO=".sqlstring($_GET['NroTramite'])." 
					      or a.TRA_NRO_VOUCHER=".sqlstring(sprintf("%08s", $_GET['NroVoucher'])).") 
					order by a.TRA_FECHA_RETIRO desc";		  
//echo $Sql;
				/*	   
					$rs = $conn->execute($Sql);
				if ($rs->EOF) { echo "Con remito"; }
				*/
				while (!$rs->EOF) {
				  	echo $rs->fields["TRA_CODIGO"].";".
				  	     rtrim($rs->fields["REG_CODIGO_ORI"]).";".
					     utf8_encode($rs->fields["DES_ORIGEN"]).";".
					     rtrim($rs->fields["REG_CODIGO_DES"]).";".
					     utf8_encode($rs->fields["DES_DESTINO"]).";".
					     $rs->fields["TRA_FECHA_RETIRO"].";".
					     $rs->fields["TRA_FECHA_ENTREGA"].";".
					     $rs->fields["MOT_CODIGO"].";".
					     $rs->fields["TRA_DOMINIO"].";".
					     $rs->fields["TRA_NRO_VOUCHER"].";".						 						 
					     $rs->fields["REM_ID_ORI"]."|";						 
					$rs->movenext();						 
				}//fin while
				}
		}//if vino parametro eleccion
		break;
	case 'validacion_baja':
		if ((!empty($_GET['NroTramite']))&&(!empty($_GET['NroVoucher']))) {
	
			$Sql="Select a.REM_ID_ORI as REM_ID_ORI, a.REM_ID_DES as REM_ID_DES,a.MOT_CODIGO as MOT_CODIGO, ".
					$conn->SQLDate(FMT_DATE_DB, "a.TRA_FECHA_ENTREGA")." as TRA_FECHA_ENTREGA
				From TRAMITE a
				where 
					(a.TRA_DOMINIO=".sqlstring($_GET['NroTramite'])." 
					      and a.TRA_NRO_VOUCHER=".sqlstring(sprintf("%08s", $_GET['NroVoucher'])).") 
				order by a.TRA_FECHA_RETIRO desc"; 
								   
				$rs = $conn->execute($Sql);
				
				$devolucion = ($rs->fields["MOT_CODIGO"]!=NULL)?"devolucion":"";
				
				if ($rs->fields["REM_ID_ORI"]!=NULL){
					echo "con remito origen"."|".$devolucion;
				}elseif ($rs->fields["TRA_FECHA_ENTREGA"]!=NULL){
					echo "con fecha entrega"."|".$devolucion;					
				}elseif ($rs->fields["REM_ID_DES"]!=NULL){
					$rem_id_des=$rs->fields["REM_ID_DES"];
					echo "con remito destino"."|".$devolucion;
				}else{					
					echo "ok"."|".$devolucion;
				}
		}//if vino parametro eleccion
		break;
	case 'anula_remito':
		if (!empty($_GET['NroRemitoDes'])){
			$remito_des= $_GET['NroRemitoDes'];
				$conn->StartTrans();
                
                    //Antes de poner el remito en ANULADO hago el LOG correspondiente de la operacion
                     //Primero junto los ID de los tramites que pueda tener
                     $sql_lista_tramites = "SELECT tra_codigo FROM tramite WHERE (rem_id_ori = '".sqlint($remito_des)."' or  rem_id_des = '".sqlint($remito_des)."')";
                     $rs_lista_tramites = $conn->Execute($sql_lista_tramites);
                     
                     $lista_tramites = "";
                     while ( !$rs_lista_tramites->EOF ){
                        
                        $lista_tramites.= $rs_lista_tramites->fields["tra_codigo"].",";
                        
                        $rs_lista_tramites->MoveNext();
                     }
                     $lista_tramites = substr ($lista_tramites, 0, strlen($lista_tramites) - 1);
                    
                     //Traigo el registro completo del REMITO
                     $sql_remito = "SELECT * FROM REMITO WHERE rem_id = ".sqlint($remito_des);
                     
                     $rs_remito = $conn->execute($sql_remito);
                   
                     //Hago el Insert en la tabla de log de BAJA_REMITO
                     $sql_insert_remito = "INSERT INTO BAJA_REMITO
                                               (baja_usuario_id
                                               ,baja_fecha
                                               ,rem_id
                                               ,rem_numero
                                               ,rem_tipo
                                               ,rem_fecha_generacion
                                               ,rem_fecha_cierre
                                               ,rem_nombre_conformidad
                                               ,rem_estado
                                               ,usr_id
                                               ,rem_fecha_act
                                               ,rem_tramites)
                                         VALUES
                                               (".trim($_GET['usuario'])."
                                               ,".sqldate(dbtime())."
                                               ,".$remito_des."
                                               ,".sqlint($rs_remito->fields["rem_numero"])."
                                               ,".sqlstring($rs_remito->fields["rem_tipo"])."
                                               ,".sqldate($rs_remito->fields["rem_fecha_generacion"])."
                                               ,".sqldate($rs_remito->fields["rem_fecha_cierre"])."
                                               ,".sqlstring($rs_remito->fields["rem_nombre_conformidad"])."
                                               ,".sqlstring($rs_remito->fields["rem_estado"])."
                                               ,".sqlint($rs_remito->fields["usr_id"])."
                                               ,".sqldate($rs_remito->fields["rem_fecha_act"])."
                                               ,".sqlstring($lista_tramites).")";
                                               
                     $conn->execute($sql_insert_remito);
                
			try{					
				//pongo el remito en estado anulado
				$sql="select rem_numero from remito where rem_id = ".sqlint($remito_des);					
				$rs = $conn->Execute($sql);
				$sql="UPDATE remito set rem_estado= "."'".ANULADO."'"." where rem_id = ".sqlint($remito_des);					
				$rs1 = $conn->Execute($sql);
				
				echo $rs->fields["rem_numero"];
				
			} catch (exception $e) {
				dbhandleerror($e);
			}				
			$conn->CompleteTrans();
		}	
		break;
	case 'nuevo_remito':		
		if ($_GET['usuario']){
		
				$usrid=$_GET['usuario'];
				$id_remito = numerador('REMITO');
				$estado=PAR_REMITO_ESTADO_GEN;	
				$nro_remito=remito_nro('DESTINO');

				$conn->StartTrans();
				try{						
					$sql="Insert into REMITO (REM_ID,
											  		REM_NUMERO,
											   		REM_TIPO, 
											   		REM_FECHA_GENERACION,
											   		REM_ESTADO,
											   		USR_ID,
											   		REM_FECHA_ACT)
									   VALUES (".sqlint($id_remito).",
												   ".sqlint($nro_remito).",
												   "."'".DESTINO."'".",												   
												   ".sqldate(dbtime()).",										   						   
											   	   ".sqlstring($estado).",										   						   										   
											       ".sqlint($usrid).",
											       ".sqldate(dbtime()).")";	
						$conn->Execute($sql);		
						echo 'ok'."|".$id_remito."|".$nro_remito;
				} catch (exception $e) {
						dbhandleerror($e);
				}				
			
				$conn->CompleteTrans();		
		}			
		break;	
	case 'actualizo_tramite':
		if(($_GET['id_nuevo_remito'])&&($_GET['NroRemitoDes'])){
				$id_remito = $_GET['id_nuevo_remito'];
				$remito_des= $_GET['NroRemitoDes'];

				$conn->StartTrans();
				try{		
					$sql = "Update TRAMITE Set REM_ID_DES = ". sqlint($id_remito) . " Where REM_ID_DES = ". sqlint($remito_des);
							$conn->Execute($sql);
					echo 'ok';						
				} catch (exception $e) {
						dbhandleerror($e);
				}							
				$conn->CompleteTrans();	
		}							
		break;

	case 'tramitesxrem':
		if($_GET['NroRemitoDes']){
			$remito_des=$_GET['NroRemitoDes'];
			$sql ="select count(*) from tramite Where REM_ID_DES = ". sqlint($remito_des);
			$rs = $conn->Execute($sql);
			echo $rs;			
		}	
		break;
		
	case 'buscar':
		if ((!empty($_GET['NroTramite']))||(!empty($_GET['NroVoucher']))) {
				$Sql="Select a.TRA_CODIGO, 
							 ".$conn->SQLDate(FMT_DATE_DB, "a.TRA_FECHA_RETIRO")." as TRA_FECHA_RETIRO,	
							 a.REG_CODIGO_ORI,
							 a.REG_CODIGO_DES,
							 b.REG_DESCRIP as DES_ORIGEN,
							 c.REG_DESCRIP as DES_DESTINO,
							 ".$conn->SQLDate(FMT_DATE_DB, "a.TRA_FECHA_ENTREGA")." as TRA_FECHA_ENTREGA,
							 a.MOT_CODIGO, 
							 a.TRA_DOMINIO,
							 a.TRA_NRO_VOUCHER,
							 a.REM_ID_ORI 
					    From TRAMITE a
				  inner join REG_AUTOM b on a.REG_CODIGO_ORI = b.REG_CODIGO
				  inner join REG_AUTOM c on a.REG_CODIGO_DES = c.REG_CODIGO
					   where a.TRA_DOMINIO=".sqlstring($_GET['NroTramite'])." 
					      or a.TRA_NRO_VOUCHER=".sqlstring(sprintf("%08s", $_GET['NroVoucher']))."
					order by a.TRA_FECHA_RETIRO desc";		  
//echo $Sql;					   
				$rs = $conn->execute($Sql);
				if ($rs->EOF) { echo "Inexistente"; }else
				{
					$Sql="Select a.TRA_CODIGO, 
							 ".$conn->SQLDate(FMT_DATE_DB, "a.TRA_FECHA_RETIRO")." as TRA_FECHA_RETIRO,	
							 a.REG_CODIGO_ORI,
							 a.REG_CODIGO_DES,
							 b.REG_DESCRIP as DES_ORIGEN,
							 c.REG_DESCRIP as DES_DESTINO,
							 ".$conn->SQLDate(FMT_DATE_DB, "a.TRA_FECHA_ENTREGA")." as TRA_FECHA_ENTREGA,
							 a.MOT_CODIGO, 
							 a.TRA_DOMINIO,
							 a.TRA_NRO_VOUCHER,
							 a.REM_ID_ORI 
					    From TRAMITE a
				  inner join REG_AUTOM b on a.REG_CODIGO_ORI = b.REG_CODIGO
				  inner join REG_AUTOM c on a.REG_CODIGO_DES = c.REG_CODIGO
					   where (a.REM_ID_ORI IS NULL OR a.TRA_FECHA_ENTREGA IS NOT NULL) and 
					   (a.TRA_DOMINIO=".sqlstring($_GET['NroTramite'])." 
					      or a.TRA_NRO_VOUCHER=".sqlstring(sprintf("%08s", $_GET['NroVoucher'])).")
					order by a.TRA_FECHA_RETIRO desc";		  
					//echo $Sql."";					   
					$rs = $conn->execute($Sql);
					if ($rs->EOF) { echo "Sin fecha"; }
				while (!$rs->EOF) {
				  	echo $rs->fields["TRA_CODIGO"].";".
				  	     rtrim($rs->fields["REG_CODIGO_ORI"]).";".
					     utf8_encode($rs->fields["DES_ORIGEN"]).";".
					     rtrim($rs->fields["REG_CODIGO_DES"]).";".
					     utf8_encode($rs->fields["DES_DESTINO"]).";".
					     $rs->fields["TRA_FECHA_RETIRO"].";".
					     $rs->fields["TRA_FECHA_ENTREGA"].";".
					     $rs->fields["MOT_CODIGO"].";".
					     $rs->fields["TRA_DOMINIO"].";".
					     $rs->fields["TRA_NRO_VOUCHER"].";".						 						 
					     $rs->fields["REM_ID_ORI"]."|";						 
					$rs->movenext();						 
				}//fin while
				}
		}//if vino parametro eleccion
		break;	
		
	case 'buscar_2':
		if( isset( $_GET['NroTramite'] ) ) $tramite = $_GET['NroTramite'];
		if( isset( $_GET['numeroTramite'] ) ) $tramite = $_GET['numeroTramite'];
		if( isset( $_GET['NroVoucher'] ) ) $voucher = $_GET['NroVoucher'];
		if( isset( $_GET['numeroVoucher'] ) ) $voucher = $_GET['numeroVoucher'];
		if ( !empty($tramite) || !empty($voucher) ) {

			$whereTramite =(!empty($tramite)) ? $tramite : '';
			$whereVoucher =(!empty($voucher)) ? sprintf("%08s", $voucher) : '';
			
			
			$Sql="Select a.TRA_CODIGO, 
							 ".$conn->SQLDate(FMT_DATE_DB, "a.TRA_FECHA_RETIRO")." as TRA_FECHA_RETIRO,	
							 a.REG_CODIGO_ORI,
							 a.REG_CODIGO_DES,
							 b.REG_DESCRIP as DES_ORIGEN,
							 c.REG_DESCRIP as DES_DESTINO,
							 ".$conn->SQLDate(FMT_DATE_DB, "a.TRA_FECHA_ENTREGA")." as TRA_FECHA_ENTREGA,
							 a.MOT_CODIGO, 
							 a.TRA_DOMINIO,
							 a.TRA_NRO_VOUCHER,
							 a.REM_ID_DES 
					    From TRAMITE a
				  inner join REG_AUTOM b on a.REG_CODIGO_ORI = b.REG_CODIGO
				  inner join REG_AUTOM c on a.REG_CODIGO_DES = c.REG_CODIGO
					   where a.TRA_DOMINIO='".$whereTramite."' 
					      or a.TRA_NRO_VOUCHER='".$whereVoucher."'
					order by a.TRA_FECHA_RETIRO desc";		  
//echo $Sql;	
//die();				
				
				$rs = $conn->execute($Sql);
				if ($rs->EOF) { echo "Inexistente"; }else
				{
					if (trim($rs->fields["REM_ID_DES"]) == '')
					{
						echo "sinremitodestino";
					}else{
							$Sql="Select a.TRA_CODIGO, 
									 ".$conn->SQLDate(FMT_DATE_DB, "a.TRA_FECHA_RETIRO")." as TRA_FECHA_RETIRO,	
									 a.REG_CODIGO_ORI,
									 a.REG_CODIGO_DES,
									 b.REG_DESCRIP as DES_ORIGEN,
									 c.REG_DESCRIP as DES_DESTINO,
									 ".$conn->SQLDate(FMT_DATE_DB, "a.TRA_FECHA_ENTREGA")." as TRA_FECHA_ENTREGA,
									 a.MOT_CODIGO, 
									 a.TRA_DOMINIO,
									 a.TRA_NRO_VOUCHER,
									 a.REM_ID_DES 
								From TRAMITE a
						  inner join REG_AUTOM b on a.REG_CODIGO_ORI = b.REG_CODIGO
						  inner join REG_AUTOM c on a.REG_CODIGO_DES = c.REG_CODIGO
							   where (a.REM_ID_DES IS NOT NULL AND a.TRA_FECHA_ENTREGA IS NULL) and 
							   (a.TRA_DOMINIO='".$whereTramite."' 
								  or a.TRA_NRO_VOUCHER='".$whereVoucher."')
							order by a.TRA_FECHA_RETIRO desc";		  
							//echo $Sql."";					   
							$rs = $conn->execute($Sql);
							if ($rs->EOF) { echo "tramitecargado"; }
						while (!$rs->EOF) {
							echo $rs->fields["TRA_CODIGO"].";".
								 rtrim($rs->fields["REG_CODIGO_ORI"]).";".
								 utf8_encode($rs->fields["DES_ORIGEN"]).";".
								 rtrim($rs->fields["REG_CODIGO_DES"]).";".
								 utf8_encode($rs->fields["DES_DESTINO"]).";".
								 $rs->fields["TRA_FECHA_RETIRO"].";".
								 $rs->fields["TRA_FECHA_ENTREGA"].";".
								 $rs->fields["MOT_CODIGO"].";".
								 $rs->fields["TRA_DOMINIO"].";".
								 $rs->fields["TRA_NRO_VOUCHER"].";".						 						 
								 $rs->fields["REM_ID_DES"]."|";						 
							$rs->movenext();						 
						}//fin while
					}
				}
		}//if vino parametro eleccion
		break;	
		
	case 'buscarxid':
		if (!empty($_GET['traCodigo'])) {
				$Sql="Select a.TRA_CODIGO, 
						     a.TRA_DOMINIO, 
							 ".$conn->SQLDate(FMT_DATE_DB, "a.TRA_FECHA_RETIRO")." as TRA_FECHA_RETIRO,	
							 b.REG_COD_INT as COD_INT_ORIGEN,
							 c.REG_COD_INT as COD_INT_DESTINO,							 
							 b.REG_DESCRIP as DES_ORIGEN,
							 c.REG_DESCRIP as DES_DESTINO,
							 ".$conn->SQLDate(FMT_DATE_DB, "a.TRA_FECHA_ENTREGA")." as TRA_FECHA_ENTREGA,
							 a.MOT_CODIGO,
							 a.TRA_NRO_VOUCHER,
							 a.REM_ID_ORI,							 
							 a.REM_ID_DES,
							 ".$conn->SQLDate(FMT_DATE_DB, "d.REM_FECHA_CIERRE")." as TRA_FECHA_CIERRE 
					    From TRAMITE a
				  inner join REG_AUTOM b on a.REG_CODIGO_ORI = b.REG_CODIGO
				  inner join REG_AUTOM c on a.REG_CODIGO_DES = c.REG_CODIGO
				  left  join REMITO d    on a.REM_ID_ORI = d.REM_ID 
					   where a.TRA_CODIGO=".sqlint($_GET['traCodigo']);		  
//echo $Sql;					   
				$rs = $conn->execute($Sql);
				if ($rs->EOF) { echo "Inexistente";
				}else{
				  	echo $rs->fields["TRA_CODIGO"].";".
				  	     rtrim($rs->fields["COD_INT_ORIGEN"]).";".
					     utf8_encode($rs->fields["DES_ORIGEN"]).";".
					     rtrim($rs->fields["COD_INT_DESTINO"]).";".
					     utf8_encode($rs->fields["DES_DESTINO"]).";".						 						 
					     $rs->fields["TRA_FECHA_RETIRO"].";".
					     $rs->fields["TRA_FECHA_ENTREGA"].";".
					     $rs->fields["MOT_CODIGO"].";".
					     rtrim($rs->fields["TRA_DOMINIO"]).";".						 
					     $rs->fields["TRA_NRO_VOUCHER"].";".						 
					     $rs->fields["REM_ID_ORI"].";".						 						 
					     $rs->fields["REM_ID_DES"].";".						 
					     $rs->fields["TRA_FECHA_CIERRE"];						 						 
				}
		}//if vino parametro eleccion
		break;		
				
	case 'modificar':	
			$conn->StartTrans();
			try {		
				$Sql="select REG_CODIGO from REG_AUTOM where REG_COD_INT=".sqlstring($_GET['regOrigen']);			
				$rs = $conn->Execute($Sql);		
				$id_origen=$rs->fields["REG_CODIGO"];		
//echo "<br>".$Sql;										  				
				$Sql="select REG_CODIGO from REG_AUTOM where REG_COD_INT=".sqlstring($_GET['regDestino']);							
				$rs = $conn->Execute($Sql);				
				$id_destino=$rs->fields["REG_CODIGO"];		
//echo "<br>".$Sql;										  								
				$fecretiro     = new Date($_GET["FecRetiro"]);
				$fecentrega    = new Date($_GET["FecEntrega"]);
				$Sql="update TRAMITE set  TRA_FECHA_RETIRO=".sqldate($fecretiro->format(FMT_DATE_ISO)).",
										  REG_CODIGO_ORI=".sqlint($id_origen).",
										  REG_CODIGO_DES=".sqlint($id_destino).",
										  TRA_DOMINIO=".sqlstring($_GET['NroTramite']).",
										  TRA_NRO_VOUCHER=".sqlstring(sprintf("%08s", $_GET['NroVoucher'])).",										  										  
										  TRA_FECHA_ENTREGA=".($_GET["FecEntrega"]!="" ? sqldate($fecentrega->format(FMT_DATE_ISO)) : 'null').",
										  MOT_CODIGO=".($_GET["MotDevolucion"]!="0" ? sqlstring($_GET['MotDevolucion']) : 'null').",
										  TRA_FECHA_ACT=".sqldate(dbtime()).", 
										  USR_ID_ACT=".sqlint($_GET['usuario'])."
								where TRA_CODIGO=".sqlint($_GET['idregmod']);
//echo $Sql;								
				$rs = $conn->Execute($Sql);
				echo 'ok';
			} catch (exception $e) {
				dbhandleerror($e);
			}				
			$conn->CompleteTrans();
		break;		
	case 'verificar':
				$out="";
				$fecretiro     = new Date($_GET["FecRetiro"]);	
				$Sql="SELECT a.tra_codigo
						FROM   TRAMITE a 
						INNER JOIN REG_AUTOM b ON a.reg_codigo_ori = b.reg_codigo AND b.reg_cod_int = ".sqlstring($_GET['regOrigen'])." 
						INNER JOIN REG_AUTOM c ON a.reg_codigo_des = c.reg_codigo AND c.reg_cod_int = ".sqlstring($_GET['regDestino'])." 
						WHERE a.tra_dominio = ".sqlstring($_GET['NroTramite'])." AND 
							  a.tra_fecha_retiro =".sqldate($fecretiro->format(FMT_DATE_ISO));
				$rs = $conn->execute($Sql);
				$out= (!$rs->EOF ?  "existe|" : "no|");
				$Sql="Select TRA_CODIGO
					    From TRAMITE
					   where TRA_NRO_VOUCHER=".sqlstring(sprintf("%08s", $_GET['NroVoucher']));						 
				$rs = $conn->execute($Sql);
				$out.= (!$rs->EOF ?  "existe" : "no");				
				echo $out;				
		break;
	case 'verificar2':
				$Sql="Select TRA_CODIGO
					    From TRAMITE
					   where TRA_NRO_VOUCHER=".sqlstring(sprintf("%08s", $_GET['NroVoucher'])).
					   " and TRA_CODIGO!=".sqlint($_GET['idtramite']);						 
				$rs = $conn->execute($Sql);
				echo (!$rs->EOF ?  "voucher" : "no")."|";
				$fecretiro     = new Date($_GET["FecRetiro"]);									
				$Sql="Select a.TRA_CODIGO
					    From TRAMITE a
						WHERE a.tra_dominio = ".sqlstring($_GET['NroTramite'])." AND 
							  a.tra_fecha_retiro =".sqldate($fecretiro->format(FMT_DATE_ISO))." AND 
							  a.tra_codigo!=".sqlint($_GET['idtramite']);
				$rs = $conn->execute($Sql);
				echo (!$rs->EOF ?  "tramite" : "no")."|";				
		break;
	 case 'remito':
				$Sql="SELECT  a.rem_id, a.rem_estado, 
							 ".$conn->SQLDate(FMT_DATE, "a.rem_fecha_generacion")." as rem_fecha_generacion 
						FROM  REMITO a 
						WHERE a.rem_numero = ".sqlint($_GET['remDestino'])." AND 
							  a.rem_tipo = '".DESTINO."'";
				$rs = $conn->execute($Sql);
//				echo $Sql;
				if($rs->EOF) { echo "inexistente";
				}else{
					echo $rs->fields["rem_id"]."|".
					     $rs->fields["rem_estado"]."|".
						 $rs->fields["rem_fecha_generacion"];					
				}
	    break;
		
	 case 'remito_2':
				
				$Sql="select ".$conn->SQLDate(FMT_DATE, "a.rem_fecha_generacion")." as rem_fecha_generacion 
					    from remito a inner join tramite b on a.rem_id = b.rem_id_des
					   where b.tra_dominio = '" . trim($_GET['numeroTramite']) . "' and b.tra_nro_voucher = " . sqlint(trim($_GET['numeroVoucher']));
				
				$rs = $conn->execute($Sql);
//				echo $Sql;
				if($rs->EOF) { echo "inexistente";
				}else{
					echo $rs->fields["rem_fecha_generacion"] . "|FIN";					
				}
	    break;
		
	 case 'fecentrega': 
		if ($_GET['idremito']!="")	  {
			$conn->StartTrans();
			try {				
	       
				$Sql="UPDATE  REMITO
						SET   rem_nombre_conformidad = ".sqlstring($_GET['conformidad']).", REM_ESTADO=".sqlstring(ENTREGADO). 
						"WHERE rem_id = ".sqlint($_GET['idremito']);
				$conn->execute($Sql);						
	 		
				$fecentrega     = new Date($_GET["FecEntrega"]);			
				$Sql="UPDATE TRAMITE 
					     SET TRA_FECHA_ENTREGA=".($_GET["FecEntrega"]!="" ? sqldate($fecentrega->format(FMT_DATE_ISO)) : 'null')." 
						WHERE rem_id_des = ".sqlint($_GET['idremito'])." AND TRA_FECHA_ENTREGA IS NULL";
				$conn->execute($Sql);				
				
				echo 'ok';	 
			} catch (exception $e) {
				dbhandleerror($e);
			}				
			$conn->CompleteTrans();				
		}
	    break;
		
	 case 'fecentrega_2': 
		if ($_GET['numeroVoucher']!="" && $_GET['numeroTramite']!="" && $_GET['FecEntrega']!="")	  {
			$conn->StartTrans();
			try {				
				//PRIMERO HAGO EL UPDATE DE LA FECHA DE ENTREGA PARA EL TRAMITE				
				$fecentrega = new Date($_GET["FecEntrega"]);
				
				$Sql="update tramite
					     set tra_fecha_entrega = ".($_GET["FecEntrega"]!="" ? sqldate($fecentrega->format(FMT_DATE_ISO)) : 'null')."
					   where tra_dominio = '" . trim($_GET['numeroTramite']) . "' and tra_nro_voucher = ".sqlint(trim($_GET['numeroVoucher']));
				$conn->execute($Sql);				
				
				//AHORA ME FIJO QUE SI HAY QUE CAMBIAR EL ESTADO DEL REMITO A GENERADO
				$Sql = "select rem_id_des from tramite a 
						 where a.tra_dominio = '" . trim($_GET['numeroTramite']) . "' 
						   and a.tra_nro_voucher = ".sqlint(trim($_GET['numeroVoucher']));
				$rs = $conn->execute($Sql);	
				
				$varCodigoRemito = trim($rs->fields["rem_id_des"]);
				
				$Sql = "select * from tramite a where a.rem_id_des = " .sqlint($varCodigoRemito). " and a.tra_fecha_entrega IS NULL";
				$rs = $conn->execute($Sql);	
				
				if ($rs->EOF)
				{
					//tengo que cambiar el estado del remito a entregado
					$Sql="UPDATE  REMITO
							SET   REM_ESTADO=".sqlstring(ENTREGADO). 
							"WHERE rem_id = ".sqlint($varCodigoRemito);
					$conn->execute($Sql);	
				}
				
				echo 'ok';	 
			} catch (exception $e) {
				dbhandleerror($e);
			}				
			$conn->CompleteTrans();				
		}
	    break;			
		
		
	case 'devolucion':	
			$conn->StartTrans();
			try {		
				
				//Si no ingreso la fecha de carga
				if (trim($_GET["fechaEntrega"]) == '')
				{
					$fecentrega = new Date(date('Y/m/d'));
					
					$Sql="select REM_ID_ORI, REM_ID_DES
							from TRAMITE
						   where TRA_CODIGO=".sqlint($_GET['idtramite'])." and TRA_FECHA_ENTREGA IS NOT NULL";
					$rs = $conn->Execute($Sql);			    
					if($rs->EOF){echo("Sin fecha de entrega");}
					else
					{
						if($rs->fields["REM_ID_DES"]=="") {
							echo 'Sin Remito Destino';
						}else{
							$Sql="update TRAMITE set  MOT_CODIGO=".($_GET["MotDevolucion"]!="0" ? sqlstring($_GET['MotDevolucion']) : 'null').",
												  TRA_FECHA_ENTREGA=".sqldate($fecentrega ->format(FMT_DATE_ISO)).", 
												  USR_ID_ENT_DEV=".sqlint($_GET['usuario'])."
										where TRA_CODIGO=".sqlint($_GET['idtramite']);
							//echo $Sql;								
							$conn->Execute($Sql);
							echo 'ok';
						}
					}	
				
				}else{
				//Si ingreso fecha de carga
				
					$fecentrega = new Date($_GET["fechaEntrega"]);
				
					$Sql="select REM_ID_ORI, REM_ID_DES
							from TRAMITE
						   where TRA_CODIGO=".sqlint($_GET['idtramite']);
					$rs = $conn->Execute($Sql);			    
					if($rs->EOF){echo("Sin fecha de entrega");}
					else
					{
						if($rs->fields["REM_ID_DES"]=="") {
							echo 'Sin Remito Destino';
						}else{
							$varCodigoRemito = trim($rs->fields["REM_ID_DES"]);
							
							$Sql="update TRAMITE set  MOT_CODIGO=".($_GET["MotDevolucion"]!="0" ? sqlstring($_GET['MotDevolucion']) : 'null').",
												  TRA_FECHA_ENTREGA=".sqldate($fecentrega ->format(FMT_DATE_ISO)).", 
												  USR_ID_ENT_DEV=".sqlint($_GET['usuario'])."
										where TRA_CODIGO=".sqlint($_GET['idtramite']);
							//echo $Sql;								
							$conn->Execute($Sql);
							
							//AHORA ME FIJO QUE SI HAY QUE CAMBIAR EL ESTADO DEL REMITO A GENERADO
							$Sql = "select * from tramite a where a.rem_id_des = " .sqlint($varCodigoRemito). " and a.tra_fecha_entrega IS NULL";
							$rs = $conn->execute($Sql);	
							
							if ($rs->EOF)
							{
								//tengo que cambiar el estado del remito a entregado
								$Sql="UPDATE  REMITO
										SET   REM_ESTADO=".sqlstring(ENTREGADO). 
										"WHERE rem_id = ".sqlint($varCodigoRemito);
								$conn->execute($Sql);	
							}							
							
							echo 'ok';
						}
					}				
				
				}					
					
			} catch (exception $e) {
				dbhandleerror($e);
			}				
			$conn->CompleteTrans();
		break;		
	}//switch		
}//if (!empty($tipo))	
if(!empty($_GET['dia']))
{
	$fecha = split("/",$_GET['dia']);
	$dia_de_semana = date("l", mktime(0, 0, 0, $fecha[1], $fecha[0], $fecha[2]));
	global $dia_a_buscar;
	
	switch($dia_de_semana)
	{
		case "Sunday":
			$dia_a_buscar = "dia_0";
			break;
		case "Monday":
			$dia_a_buscar = "dia_1";
			break;
		case "Tuesday":
			$dia_a_buscar = "dia_2";
			break;
		case "Wednesday":
			$dia_a_buscar = "dia_3";
			break;
		case "Thursday":
			$dia_a_buscar = "dia_4";
			break;
		case "Friday":
			$dia_a_buscar = "dia_5";
			break;
		case "Saturday":
			$dia_a_buscar = "dia_6";
			break;
			
	}//Fin switch
	
	$sql = "SELECT par_nombre FROM parametro WHERE par_nombre = ".sqlstring($dia_a_buscar);
	$rs = $conn->Execute($sql);
	
	if($rs->EOF)
	{
		$posible_feriado = new Date($_GET['dia']);
		$sql ="SELECT fer_fecha,fer_descripcion FROM feriado where fer_fecha = ".sqldate($posible_feriado->format(FMT_DATE_ISO));
		$rs = $conn->Execute($sql);
		if($rs->EOF)
		{
			echo("1");
		}
		else
		{
			echo($rs->fields['fer_descripcion']);		
		}
	
	}
	else
	{
		echo("No es un dia habil");	
	}

}//Fin !empty($_GET['dia'])
?>