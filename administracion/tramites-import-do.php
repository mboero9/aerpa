<?
set_time_limit(0);

require_once("../includes/lib.php");
require_once("inc_tramites.php");

if (check_auth($usrid, $perid, $eleid, $ctrid) && check_permission($permiso, "tramites-import.php")) {
// permiso ok
//var_dump($_FILES);
// control de errores
$ok = true; $errdetail = array();
//echo "<br>archivo:".$_FILES["traimport"]["tmp_name"];
// verificar si subio el archivo
if (is_uploaded_file($_FILES["traimport"]["tmp_name"])) {

	// obtener delimitador
	$delim = $_POST["delimitador"];

	// abrir archivo (solo lectura)
	$fp = fopen($_FILES["traimport"]["tmp_name"], "r");

	// recorrer el archivo
	$pwrx = "/^(.*[A-Za-z]{1,}.*[0-9]{1,}.*|.*[0-9]{1,}.*[A-Za-z]{1,}.*)$/";
	$lnum = 0;
// actualizo el numerador de id de remitos si se corre por 1ra vez y no hay tramites	
	$Sql="select count(*) as cant_tramites from tramite";
	$rs = $conn->execute($Sql);
	if ($rs->fields['cant_tramites']==0) {		
		$sql = "Update NUMERADOR Set NUM_ULTIMO = 0 Where Lower(NUM_TABLA) = 'remito'";		
		$conn->execute($sql);									
	}	
	$rimport = 0;
	$rem_origen = 0;
	$rem_destino = 0;		
	while (($ln = fgetcsv($fp, CSV_MAX_LINE, $delim)) !== FALSE) {
			  $lnum++;	$lineok = true;

		// verificar cantidad de items
		if (count($ln) < USR_COL_MIN) {
			$lineok = false;
			$errdetail[$lnum] = "Cantidad incorrecta de columnas, se esperan " .
				xlscol(NIS_COL_MAX) . " y se recibieron " . count($ln);
		} // end validacion

		if (!$lineok) {
			$ok = false;
		} else {
			$sql = "Select tra_codigo From TRAMITE " .
				"Where tra_codigo = " . sqlint($ln[xlscol(TRA_MOV)])  ;
//echo "<br>".$sql;				
			$rs = $conn->execute($sql);
			if (!$rs->EOF) {
				$ok = false;
				$errdetail[$lnum] .= ($lineok ? "" : "\n<br>") .
					"Registro duplicado";

			} elseif($rs->EOF) {
					$sql = "Select tra_nro_voucher From TRAMITE " .
				"Where tra_nro_voucher = " . sqlstring($ln[xlscol(TRA_VOUCHER)])  ;
//echo "<br>".$sql;				
			$rs = $conn->execute($sql);
			}
			if (!$rs->EOF) {
				$ok = false;
				$errdetail[$lnum] .= ($lineok ? "" : "\n<br>") .
					"Voucher duplicado";
				
			}else{
				// registro OK, insertarlo
//USUARIO
/*
		$Sql="select usr_id from usuario where Lower(usr_username)= Lower(".sqlstring($ln[xlscol(TRA_USUARIO)]).")";
		$rs = $conn->execute($Sql);
		if ($rs->EOF) {				
		    $id_usuario=numerador('usuario');			
			$Sql="insert into usuario(usr_id,
			                           per_id,
									   usr_username,
									   usr_password)
							values ($id_usuario,
									  1,
									 ".sqlstring($ln[xlscol(TRA_USUARIO)]).",
									 ".sqlstring($ln[xlscol(TRA_USUARIO)]).")";	
			$conn->execute($Sql);
		}else{
		    $id_usuario=$rs->fields["usr_id"];
		}	
*/		
/* Doy de alta un usuario para migrar */		
		$Sql="select usr_id from usuario where usr_username='migracion'";
		$rs = $conn->execute($Sql);
		if ($rs->EOF) {				
		    $id_usuario=numerador('usuario');			
			$Sql="insert into usuario(usr_id,
			                           per_id,
									   usr_username,
									   usr_password)
							values ($id_usuario,
									  1,
									 'migracion',
									 'migracion')";	
			$conn->execute($Sql);
		}else{
		    $id_usuario=$rs->fields["usr_id"];
		}		
						
//				
	   $tra_codigo=$ln[xlscol(TRA_MOV)];
	   $tra_fecha_retiro=new Date($ln[xlscol(TRA_FECHA)]);
	   $id_origen=$ln[xlscol(TRA_ORIGEN)];
	   $id_destino=$ln[xlscol(TRA_DESTINO)];
	   $tra_dominio=str_replace('-', '', $ln[xlscol(TRA_DOMINIO)]);
	   $tra_voucher=$ln[xlscol(TRA_VOUCHER)];	   
	   $tra_fecha_carga=new Date($ln[xlscol(TRA_FECHAHORA)]);	   
	   $tra_fecha_entrega=new Date($ln[xlscol(TRA_ENTREGA)]);	   	   
//	   $tra_fecha_devolucion=  //NO LA VEO EN LA BASE PARA IMPORTAR
//	   $mot_codigo= //IDEM ANTERIOR
	   $usr_id_carga=$id_usuario;
//	   $tra_fecha_remito=
//	   $usr_id_ent_dev=
//	   $usr_id_act=
	   $tra_fecha_act=new Date($ln[xlscol(TRA_FECHA_US)]);	   	   	      								   
	   if ($tra_fecha_retiro->format(FMT_DATE_ISO) < '2006-01-01') {
			$ok = false;
			$errdetail[$lnum] .= ($lineok ? "" : "\n<br>") .
				"Fecha de Retiro es menor al 01/01/2006";					   
	   }else{
					         

//BUSCO CODIGO DE ORIGEN Y DESTINO
		$tra_codigo_ori="";
		$tra_codigo_des="";
		$Sql="select REG_CODIGO from REG_AUTOM where REG_COD_INT=".sqlstring($id_origen);			
		$rs = $conn->Execute($Sql);		
		if ($rs->EOF) {	
			$ok = false;
			$errdetail[$lnum] .= ($lineok ? "" : "\n<br>") .
				"Origen $id_origen Inexistente en tabla registros";				
		}else{
			$tra_codigo_ori=$rs->fields["REG_CODIGO"];		
//echo "<br>".$Sql;										  				
			$Sql="select REG_CODIGO from REG_AUTOM where REG_COD_INT=".sqlstring($id_destino);							
			$rs = $conn->Execute($Sql);				
		if ($rs->EOF) {	
			$ok = false;
			$errdetail[$lnum] .= ($lineok ? "" : "\n<br>") .
				"Destino $id_destino Inexistente en tabla registros";				
		}else{				
			$tra_codigo_des=$rs->fields["REG_CODIGO"];		
//echo "<br>".$Sql;	
// Verificacion que no se viole la FK XAR1TRAMITE 									  								
			$sql = "Select tra_codigo From TRAMITE " .
				"Where tra_fecha_retiro = " .($ln[xlscol(TRA_FECHA)]!="" ? sqldate($tra_fecha_retiro->format(FMT_DATE_ISO)) : 'null')."
				   and reg_codigo_ori = ".sqlstring($tra_codigo_ori)."
				   and reg_codigo_des = ".sqlstring($tra_codigo_des)."
				   and tra_dominio = ".sqlstring($tra_dominio);
			$rs = $conn->execute($sql);
			if (!$rs->EOF) {
				$ok = false;
				$errdetail[$lnum] .= ($lineok ? "" : "\n<br>") .
					"Registro duplicado segun FK XAR1TRAMITE";

			} else {
//Verificar existencia de Remito Destino
//TRA_NROREMITO
			if ($ln[xlscol(TRA_NROREMITO)]=="") {
			    $idremitodestino="";
			}else{
			$sql = "Select rem_id From remito " .
				"Where rem_numero = " .sqlint($ln[xlscol(TRA_NROREMITO)])."
				   and rem_tipo = ".sqlstring(DESTINO);
			$rs = $conn->execute($sql);
			if (!$rs->EOF) {
				$idremitodestino=$rs->fields["rem_id"];		
			} else {
				$idremitodestino =numerador('REMITO');	
			    $tra_fecha=new Date($ln[xlscol(TRA_FECHAHORA)]);
				$tra_fecha_cierre="";
				$estado="";
				if ($ln[xlscol(TRA_CERRADO)]=="CERRADO") {
						$tra_fecha_cierre=new Date($ln[xlscol(TRA_FECHAHORA)]);
						$estado=$ln[xlscol(TRA_CERRADO)];
				}else{
					if ($ln[xlscol(TRA_ENTREGA)]!="") {
						$estado="ENTREGADO";
					}else{
						$estado="GENERADO";					
					}
				}	   	   										
				$sql="insert into remito(rem_id,
										 rem_numero,
										 rem_tipo,
										 rem_fecha_generacion,
										 rem_fecha_cierre,
										 rem_estado,
										 usr_id,
										 rem_fecha_act
										)
								  values(".sqlint($idremitodestino).",".
								           sqlint($ln[xlscol(TRA_NROREMITO)]).",".
										   sqlstring(DESTINO).",".
										   sqldate($tra_fecha->format(FMT_DATE_ISO)).",".
										  ($tra_fecha_cierre!="" ? sqldate($tra_fecha_cierre->format(FMT_DATE_ISO)) : 'null').",".								  										   
										   sqlstring($estado).",".
									  	   sqlint($usr_id_carga).",".								  
										   sqldate(dbtime())."
								  		)";
			    $conn->execute($sql);	
				$rem_destino++;									
			} //verificacion remito	destino	
			} //no tiene nro de remito		
//Verificar existencia de Remito origen
//TRA_NROREMENT
			if ($ln[xlscol(TRA_NROREMENT)]=="") {
			    $idremitoorigen="";
			}else{
			$sql = "Select rem_id From remito " .
				"Where rem_numero = " .sqlint($ln[xlscol(TRA_NROREMENT)])."
				   and rem_tipo = ".sqlstring(ORIGEN);
			$rs = $conn->execute($sql);
			if (!$rs->EOF) {
				$idremitoorigen=$rs->fields["rem_id"];		
			} else {
				$idremitoorigen =numerador('REMITO');	
			    $tra_fecha=new Date($ln[xlscol(TRA_FECHAHORA)]);
				$tra_fecha_cierre="";
				$estado="";
				if ($ln[xlscol(TRA_CERRADO)]=="CERRADO") {
						$tra_fecha_cierre=new Date($ln[xlscol(TRA_FECHAHORA)]);
						$estado=$ln[xlscol(TRA_CERRADO)];
				}else{
					if ($ln[xlscol(TRA_ENTREGA)]!="") {
						$estado="ENTREGADO";
					}else{
						$estado="GENERADO";					
					}
				}	   	   										
				$sql="insert into remito(rem_id,
										 rem_numero,
										 rem_tipo,
										 rem_fecha_generacion,
										 rem_fecha_cierre,
										 rem_estado,
										 usr_id,
										 rem_fecha_act
										)
								  values(".sqlint($idremitoorigen).",".
								           sqlint($ln[xlscol(TRA_NROREMENT)]).",".
										   sqlstring(ORIGEN).",".
										   sqldate($tra_fecha->format(FMT_DATE_ISO)).",".
										  ($tra_fecha_cierre!="" ? sqldate($tra_fecha_cierre->format(FMT_DATE_ISO)) : 'null').",".								  										   
										   sqlstring($estado).",".
									  	   sqlint($usr_id_carga).",".								  
										   sqldate(dbtime())."
								  		)";
			    $conn->execute($sql);			
				$rem_origen++;			
			} //verificacion remito	origen
			} //no tiene nro de remito					   
			$conn->StartTrans();	   
			try {
							
		$migrar="insert into TRAMITE(tra_codigo,
									   tra_fecha_retiro,
									   reg_codigo_ori,
									   reg_codigo_des,
									   tra_dominio,
									   tra_nro_voucher,									   
									   tra_fecha_carga,
									   tra_fecha_entrega,
									   usr_id_carga,
									   rem_id_ori,
									   rem_id_des,									   
									   tra_fecha_act,
									   tra_nro_imp)
							  values (".sqlint($tra_codigo).",".
									  ($ln[xlscol(TRA_FECHA)]!="" ? sqldate($tra_fecha_retiro->format(FMT_DATE_ISO)) : 'null').",".
									  sqlstring($tra_codigo_ori).",".
									  sqlstring($tra_codigo_des).",".
									  sqlstring($tra_dominio).",".
									  sqlstring($tra_voucher).",".									  
									  ($ln[xlscol(TRA_FECHA_US)]!="" ? sqldate($tra_fecha_carga->format(FMT_DATE_ISO)) : 'null').",".								  
									  ($ln[xlscol(TRA_ENTREGA)]!="" ? sqldate($tra_fecha_entrega->format(FMT_DATE_ISO)) : 'null').",".								  								  
									  sqlint($usr_id_carga).",".								  
									  ($idremitoorigen!="" ? sqlint($idremitoorigen) : 'null').",".					
									  ($idremitodestino!="" ? sqlint($idremitodestino) : 'null').",".
									  ($ln[xlscol(TRA_FECHA_US)]!="" ? sqldate($tra_fecha_act->format(FMT_DATE_ISO)) : 'null').",0)";
	//echo "<br>$migrar";
		  $rimport++;	  							  
		  $conn->execute($migrar);	
			} catch (exception $e) {
				dbhandleerror($e);
			}		  
				} // end registro duplicado
	
			} // end verificacion
		   $conn->CompleteTrans();

		   } //duplicacion key XAR1TRAMITE
	   } //eof origen
	   } //eof destino
	   } //if fecha_retiro < 2006-01-01
	} // end while
//actualizo el numerador para la tabla de tramite		
			$sql = "select max(tra_codigo) as maxtra_codigo from TRAMITE";
			$rs = $conn->execute($sql);			
			if (!$rs->EOF) {
			   if ($rs->fields["maxtra_codigo"] > 0) {
				   $maxtracodigo=$rs->fields["maxtra_codigo"];
			   }else{
				   $maxtracodigo=0;			   
			   }
			}else{
			   $maxtracodigo=0;			
			}
			$sql = "select NUM_ULTIMO from NUMERADOR Where Lower(NUM_TABLA) = 'tramite'";		
			$rs = $conn->execute($sql);			
			if (!$rs->EOF) {			
				$sql = "Update NUMERADOR Set NUM_ULTIMO = ".sqlint($maxtracodigo)." Where Lower(NUM_TABLA) = 'tramite'";		
				$rs = $conn->execute($sql);						
			}else{
				$sql = "insert into NUMERADOR(num_tabla,
								   			  num_ultimo)
						  values ('tramite',".
								 sqlint($maxtracodigo).")";
				$rs = $conn->execute($sql);															
			}
//actualizo los maximos de numeradores de remito			
			$sql = "select max(rem_numero) as maxrem_numero_ori from REMITO where rem_tipo=".sqlstring(ORIGEN);
			$rs = $conn->execute($sql);			
			if (!$rs->EOF) {
			   if ($rs->fields["maxrem_numero_ori"] > 0) {
				   $max_rem_ori=$rs->fields["maxrem_numero_ori"];
			   }else{
				   $max_rem_ori=0;			   
			   }
			}else{
			   $max_rem_ori=0;			
			}
			$sql = "Update PARAMETRO set PAR_VALOR = ".sqlint($max_rem_ori)." Where PAR_NOMBRE = 'PAR_NRO_REMITO_ORIGEN'";		
			$conn->execute($sql);						
			$sql = "select max(rem_numero) as maxrem_numero_des from REMITO where rem_tipo=".sqlstring(DESTINO);
			$rs = $conn->execute($sql);			
			if (!$rs->EOF) {
			   if ($rs->fields["maxrem_numero_des"] > 0) {
				   $max_rem_des=$rs->fields["maxrem_numero_des"];
			   }else{
				   $max_rem_des=0;			   
			   }
			}else{
			   $max_rem_des=0;			
			}
			$sql = "Update PARAMETRO set PAR_VALOR = ".sqlint($max_rem_des)." Where PAR_NOMBRE = 'PAR_NRO_REMITO_DESTINO'";		
			$conn->execute($sql);	
	// cerrar transaccion
/*	if ($ok) {
		$conn->commit();
	} else {
		$conn->rollback();
	} // end if ok*/

	// cerrar y borrar archivo
	fclose($fp);
	unlink($_FILES["traimport"]["tmp_name"]);
} else {
	$ok = false;
	$errdetail[] = "El archivo no fue subido";
} // end if is_uploaded
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>

<head>
<? require_once("../includes/inc_header.php"); ?>
</head>

<body>
<?require_once('../includes/inc_topleft.php');?>

<p class=titulo1>Importaci&oacute;n de tramites</p>
<?
if ($ok) {
?>
<p align=center class=texto>Se han generado <b><? echo($rem_destino); ?></b> remitos de destino.</p>
<p align=center class=texto>Se han generado <b><? echo($rem_origen); ?></b> remitos de origen.</p>
<p align=center class=texto>Se han importado <b><? echo($rimport); ?></b> tramites.</p>
<?
} else {
?>
<p align=center class=texto>Se han producido errores al importar los registros.<br>
<!--	Corrija el archivo y vuelva a importarlo--></p>

<p align=center class=texto>
<table class=tablanormal>

<thead>
<tr><th class=celdatitulo>L&iacute;nea</th>
	<th class=celdatitulo>Error</th></tr>
</thead>

<tbody>
<?
$rconerror=0;
foreach($errdetail as $lnum => $errdescription) {
?>
<tr><td class=celdatexto align=center><? echo($lnum); ?></td>
	<td class=celdatexto><? echo($errdescription); ?></td></tr>
<?
$rconerror++;
} // end foreach
?>

</tbody>

</table>

<input type=button class=botonout value="Volver" onClick="window.location = 'tramites-import.php';">

</p>
<p align=center class=texto>Registros erroneos: <b><? echo($rconerror); ?></b> tramites.</p>
<? if ($rimport>0) { ?>
<p align=center class=texto>Se han importado <b><? echo($rimport); ?></b> tramites.</p>
<? } ?>
<?
} // end if ok
?>
<? require_once("../includes/inc_bottom.php"); ?>
</body>

</html>

<?
} // fin autorizacion
?>