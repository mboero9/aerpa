<?
set_time_limit(0);

require_once("../includes/lib.php");
require_once("inc_registros.php");

if (check_auth($usrid, $perid, $eleid, $ctrid) && check_permission($permiso, "registros-import.php")) {
// permiso ok

// control de errores
$ok = true; $errdetail = array();
//echo "<br>archivo:".$_FILES["regimport"]["tmp_name"];
// verificar si subio el archivo
if (is_uploaded_file($_FILES["regimport"]["tmp_name"])) {

	// obtener delimitador
	$delim = $_POST["delimitador"];

	// abrir archivo (solo lectura)
	$fp = fopen($_FILES["regimport"]["tmp_name"], "r");

	// recorrer el archivo
	$pwrx = "/^(.*[A-Za-z]{1,}.*[0-9]{1,}.*|.*[0-9]{1,}.*[A-Za-z]{1,}.*)$/";
	$lnum = 0;
	$rimport = 0;	
	$regiones = 0;
	while (($ln = fgetcsv($fp, CSV_MAX_LINE, $delim)) !== FALSE) {
			  $lnum++;	$lineok = true;

		// verificar cantidad de items
		if (count($ln) < USR_COL_MIN) {
			$lineok = false;
			$errdetail[$lnum] = "Cantidad incorrecta de columnas, se esperan " .
				xlscol(NIS_COL_MAX) . " y se recibieron " . count($ln);

		} else {
			// validar longitudes de campo, etc.
/*			$buf = $ln[xlscol(USR_COL_LOGIN)];
			if (strlen($buf) == 0) {
				$errdetail[$lnum] .= ($lineok ? "" : "\n<br>") .
					"Login no especificado";
				$lineok = false;
			}

			if (strlen($buf) > 20) {
				$errdetail[$lnum] .= ($lineok ? "" : "\n<br>") .
					"Login demasiado largo" .
					" (valor: " . htmlentities($buf) . ")";
				$lineok = false;
			}

			$buf = $ln[xlscol(USR_COL_PASSWORD)];
			if ((strlen($buf) < 8) || (!preg_match($pwrx, $buf))) {
				$errdetail[$lnum] .= ($lineok ? "" : "\n<br>") .
					"Contrase&ntilde;a demasiado simple";
				$lineok = false;
			}

			$buf = $ln[xlscol(USR_COL_DOCUMENTO)];
			if ((!is_numeric($buf)) || (strlen($buf) > 8)) {
				$errdetail[$lnum] .= ($lineok ? "" : "\n<br>") .
					"Documento incorrecto" .
					" (valor: $buf)";
				$lineok = false;
			}

			$buf = $ln[xlscol(USR_COL_NOMBRE)];
			if (strlen($buf) == 0) {
				$errdetail[$lnum] .= ($lineok ? "" : "\n<br>") .
					"Nombre no especificado";
				$lineok = false;
			}

			if (strlen($buf) > 20) {
				$errdetail[$lnum] .= ($lineok ? "" : "\n<br>") .
					"Nombre demasiado largo" .
					" (valor: " . htmlentities($buf) . ")";
				$lineok = false;
			}

			$buf = $ln[xlscol(USR_COL_APELLIDO)];
			if (strlen($buf) == 0) {
				$errdetail[$lnum] .= ($lineok ? "" : "\n<br>") .
					"Apellido no especificado";
				$lineok = false;
			}

			if (strlen($buf) > 20) {
				$errdetail[$lnum] .= ($lineok ? "" : "\n<br>") .
					"Apellido demasiado largo" .
					" (valor: " . htmlentities($buf) . ")";
				$lineok = false;
			}

			if (!array_key_exists(strtolower($ln[xlscol(USR_COL_CENTRO)]), $ctr)) {
				$errdetail[$lnum] .= ($lineok ? "" : "\n<br>") .
					"Centro de transferencia no encontrado" .
					" (valor: " . $ln[xlscol(USR_COL_CENTRO)] . ")";
				$lineok = false;
			} // end if ctr

			if (!array_key_exists(strtolower($ln[xlscol(USR_COL_PERFIL)]), $prf)) {
				$errdetail[$lnum] .= ($lineok ? "" : "\n<br>") .
					"Perfil no encontrado" .
					" (valor: " . $ln[xlscol(USR_COL_PERFIL)] . ")";
				$lineok = false;
			} // end if ctr
*/
		} // end validacion


		if (!$lineok) {
			$ok = false;
		} else {
//actualizo el numerador para la tabla de registros automotor		
			$sql = "select max(reg_codigo) as maxreg_codigo from REG_AUTOM";
			$rs = $conn->execute($sql);			
			if (!$rs->EOF) {
			   if ($rs->fields["maxreg_codigo"] > 0) {
				   $maxregcodigo=$rs->fields["maxreg_codigo"];
			   }else{
				   $maxregcodigo=0;			   
			   }
			}else{
			   $maxregcodigo=0;			
			}
			$sql = "Update NUMERADOR Set NUM_ULTIMO = ".$maxregcodigo." Where Lower(NUM_TABLA) = 'reg_autom'";		
			$rs = $conn->execute($sql);						
			$sql = "Select reg_codigo From REG_AUTOM " .
				"Where Lower(reg_cod_int) = Lower(" . sqlstring($ln[xlscol(REG_CODIGO)]) . ") " ;
//echo "<br>".$sql;				
			$rs = $conn->execute($sql);
			if (!$rs->EOF) {
				$ok = false;
				$errdetail[$lnum] .= ($lineok ? "" : "\n<br>") .
					"Registro duplicado, Codigo=".$ln[xlscol(REG_CODIGO)];

			} else {
				// registro OK, insertarlo
/*				$sql = "Insert Into USUARIOS (USRID,PERID,USRUSERNAME,USRPASSWORD," .
					"	USRDOCUMENTO,USRNOMBRE,USRAPELLIDO,USRCAMBIARPASS," .
					(trim($ln[xlscol(USR_COL_HABILITADO)]) == "" ? "" : "	USRHABILITADO,") .
					"	USRUSUARIOALTA,USRTIPOALTA) " .
					"Values (USRID.NEXTVAL, " .
					sqlint($ln[xlscol(USR_COL_PERFIL)]) . "," .
					sqlstring($ln[xlscol(USR_COL_LOGIN)]) . "," .
					sqlstring(md5($ln[xlscol(USR_COL_PASSWORD)])) . "," .
					sqlint($ln[xlscol(USR_COL_DOCUMENTO)]) . "," .
					sqlstring($ln[xlscol(USR_COL_NOMBRE)]) . "," .
					sqlstring($ln[xlscol(USR_COL_APELLIDO)]) . "," .
					sqlstring($changepwdf) . "," .
					(trim($ln[xlscol(USR_COL_HABILITADO)]) == "" ?
						"" : sqlstring($ln[xlscol(USR_COL_HABILITADO)]) . ",") .
					sqlint($usrid) . "," .
					sqlstring(ALTAUSR_LOTE) . ")";
				$conn->execute($sql);*/

	$reg_descrip= $ln[xlscol(REG_DEPENDENCIA)];
	$calle      = $ln[xlscol(REG_DOMICILIO)];
	$localidad  = $ln[xlscol(REG_LOCALIDAD)];
// USUARIO
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
//echo "<br>Usuario:".$id_usuario;
	
//PROVINCIA	
		$Sql="select PRO_CODIGO
					  from PROVINCIA WHERE PRO_DESCRIP=".sqlstring($ln[xlscol(REG_PROVINCIA)]);	
//echo "<br>Busco Provincia:".$Sql;   	
		$provincia = $conn->execute($Sql);		
		$pro_codigo="";
		if ($provincia->EOF) {		
				$ok = false;
				$errdetail[$lnum] .= ($lineok ? "" : "\n<br>") .
					"Provincia Inexistente:".$ln[xlscol(REG_PROVINCIA)];		
		}else{
			$pro_codigo=$provincia->fields["PRO_CODIGO"];
//echo "<br>encontro la provincia:".$pro_codigo;			
//REGION
/*si es AMBA     rgi_codigo = MET, 
si es BSAS INT rgi_codigo = PBA, 
si es ESTE     rgi_codigo = EST, 
si es OESTE    rgi_codigo = OES, 
si es CENTRO   rgi_codigo = CEN, 
si es SUR      rgi_codigo = SUR */
				switch(strtoupper($ln[xlscol(REG_REGION)])) {
					case 'AMBA': $rgi_codigo='MET';
					break;
					case 'BSAS INT': $rgi_codigo='PBA';
					break;
					case 'ESTE': $rgi_codigo='EST';
					break;
					case 'OESTE': $rgi_codigo='OES';
					break;
					case 'CENTRO': $rgi_codigo='CEN';
					break;
					case 'SUR': $rgi_codigo='SUR';
					break;
					default: $rgi_codigo=substr(strtoupper($ln[xlscol(REG_REGION)]),0,3);
				}
		$Sql="select RGI_CODIGO
					  from REGION WHERE RGI_CODIGO=".sqlstring($rgi_codigo);	
//echo "<br>Busco region:".$Sql;   	
		$region = $conn->execute($Sql);		
		if ($region->EOF) {				
				try {			
				$migrar="insert into REGION(rgi_codigo,
											   rgi_descrip)
									  values (".sqlstring($rgi_codigo).",".
												sqlstring($ln[xlscol(REG_REGION)]).")";
//	echo "<br>migrando region:".$migrar;	
					$regiones++;									
					$conn->execute($migrar);
				} catch (exception $e) {
					dbhandleerror($e);
				}
		}						
	$reg_cod_int =  $ln[xlscol(REG_CODIGO)];
    $reg_tipo    = ($ln[xlscol(REG_ABONADO)] == 'SI' ? 'A' : 'D');		
	$reg_cpa     =  $ln[xlscol(REG_CPA)];
		try {			   	
	$migrar="insert into REG_AUTOM(reg_codigo,
								   reg_descrip,
								   reg_calle,
								   reg_cpa,
								   reg_localidad,
								   pro_codigo,
								   rgi_codigo,
								   reg_cod_int,
								   reg_tipo,
								   usrid,
								   reg_fecha_act)
						  values (".sqlint(numerador('REG_AUTOM')).",".
						          sqlstring($reg_descrip).",".
								  sqlstring($calle).",".
								  sqlstring($reg_cpa).",".
								  sqlstring($localidad).",".
								  sqlstring($pro_codigo).",".
								  sqlstring($rgi_codigo).",".
								  sqlstring($reg_cod_int).",".
								  sqlstring($reg_tipo).",
								  $id_usuario,
								  ".sqldate(dbtime()).")";
//echo "<br>$migrar";			
	  $rimport++;	  							  
	  $conn->execute($migrar);	
		} catch (exception $e) {
			dbhandleerror($e);
		}		  
		} // end busqueda de provincia		
			} // end registro duplicado

		} // end verificacion

	} // end while

	// cerrar transaccion
/*	if ($ok) {
		$conn->commit();
	} else {
		$conn->rollback();
	} // end if ok*/

	// cerrar y borrar archivo
	fclose($fp);
	unlink($_FILES["regimport"]["tmp_name"]);
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

<p class=titulo1>Importaci&oacute;n de registros</p>
<?
if ($ok) {
?>
<p align=center class=texto>Se han importado <b><? echo($rimport); ?></b> registros.</p>
<p align=center class=texto>Se han importado <b><? echo($regiones); ?></b> regiones.</p>
<?
} else {
?>
<p align=center class=textoerror>Se han producido errores al importar los registros.<br>
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

<input type=button class=botonout value="Volver" onClick="window.location = 'registros-import.php';">

</p>
<p align=center class=texto>Registros erroneos: <b><? echo($rconerror); ?></b> registros.</p>
<p align=center class=texto>Se han importado <b><? echo($rimport); ?></b> registros.</p>
<p align=center class=texto>Se han importado <b><? echo($regiones); ?></b> regiones.</p>
<?
} // end if ok
?>
<? require_once("../includes/inc_bottom.php"); ?>
</body>

</html>

<?
} // fin autorizacion
?>