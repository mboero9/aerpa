<?
set_time_limit(0);

require_once("../includes/lib.php");
require_once("inc_usuarios.php");

if (check_auth($usrid, $perid) && check_permission($permiso, "usuarios-import.php")) {
// permiso ok

// control de errores
$ok = true; $errdetail = array();

// verificar si subio el archivo
if (is_uploaded_file($_FILES["usrimport"]["tmp_name"])) {
	// obtener eleccion
	$v_eleid = $_POST["eleccion"];

	try {
	// parametros
	$changepwdf = getParametro(PAR_USRIMPORT_CAMBIARPW);
	if (!in_array($changepwdf, array(0,1))) {
		$changepwdf = 1;
	} // end if 1/0
	} catch (exception $e) {
		dbhandleerror($e);
	}
	try {
	// obtener perfiles, cargarlos en el array prf
	$prf = array();
	$sql = "Select PER_ID,PER_DESCRIPCION From PERFIL";
	$rs = $conn->Execute($sql);
	while (!$rs->EOF) {
		$prf[$rs->fields["PER_ID"]] = strtolower($rs->fields["PER_DESCRIPCION"]);
		$rs->MoveNext();
	} // end while !eof
	} catch (exception $e) {
		dbhandleerror($e);
	}

	// obtener delimitador
	$delim = $_POST["delimitador"];

	// abrir archivo (solo lectura)
	$fp = fopen($_FILES["usrimport"]["tmp_name"], "r");

	// abrir transaccion
	$conn->StartTrans();

	// recorrer el archivo
	$pwrx = "/^(.*[A-Za-z]{1,}.*[0-9]{1,}.*|.*[0-9]{1,}.*[A-Za-z]{1,}.*)$/";
	$lnum = 0;
	while (($ln = fgetcsv($fp, CSV_MAX_LINE, $delim)) !== FALSE) {
		$lnum++; $lineok = true;

		// verificar cantidad de items
		if (count($ln) < USR_COL_MIN) {
			$lineok = false;
			$errdetail[$lnum] = "Cantidad incorrecta de columnas, se esperan " .
				xlscol(USR_COL_MAX) . " y se recibieron " . count($ln);

		} else {
			// validar longitudes de campo, etc.
			$buf = $ln[xlscol(USR_COL_LOGIN)];
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
/*			if ((strlen($buf) < 8) || (!preg_match($pwrx, $buf))) {
				$errdetail[$lnum] .= ($lineok ? "" : "\n<br>") .
					"Contrase&ntilde;a demasiado simple";
				$lineok = false;
			}*/

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

			if (!array_key_exists(strtolower($ln[xlscol(USR_COL_PERFIL)]), $prf)) {
				$errdetail[$lnum] .= ($lineok ? "" : "\n<br>") .
					"Perfil no encontrado" .
					" (valor: " . $ln[xlscol(USR_COL_PERFIL)] . ")";
				$lineok = false;
			} // end if ctr

		} // end validacion


		if (!$lineok) {
			$ok = false;
		} else {
			try {
			$sql = "Select USR_ID From USUARIO " .
				"Where USR_BAJA = " . sqlboolean(false) . " " .
				"	And Lower(USR_USERNAME) = Lower(" . sqlstring($ln[0]) . ")";
			$rs = $conn->Execute($sql);
			if (!$rs->EOF) {
				$ok = false;
				$errdetail[$lnum] .= ($lineok ? "" : "\n<br>") .
					"Usuario duplicado";

			} else {
				// registro OK, insertarlo
				$sql = "Insert Into USUARIO (USR_ID,PER_ID,USR_USERNAME,USR_PASSWORD," .
					"	USR_DOCUMENTO,USR_NOMBRE,USR_APELLIDO,USR_CAMBIAR_PASS," .
					(trim($ln[xlscol(USR_COL_HABILITADO)]) == "" ? "" : "	USR_HABILITADO,") .
					"	USR_USUARIO_ALTA,USR_TIPO_ALTA) " .
					"Values (" . sqlint(numerador("USUARIO")) . ", " .
					sqlint($ln[xlscol(USR_COL_PERFIL)]) . "," .
					sqlstring($ln[xlscol(USR_COL_LOGIN)]) . "," .
					sqlstring(md5($ln[xlscol(USR_COL_PASSWORD)])) . "," .
					sqlint($ln[xlscol(USR_COL_DOCUMENTO)]) . "," .
					sqlstring($ln[xlscol(USR_COL_NOMBRE)]) . "," .
					sqlstring($ln[xlscol(USR_COL_APELLIDO)]) . "," .
					sqlboolean($changepwdf) . "," .
					(trim($ln[xlscol(USR_COL_HABILITADO)]) == "" ?
						"" : sqlboolean($ln[xlscol(USR_COL_HABILITADO)]) . ",") .
					sqlint($usrid) . "," .
					sqlstring(ALTAUSR_LOTE) . ")";
				$conn->Execute($sql);
			} // end if eof
			} catch (exception $e) {
				dbhandleerror($e);
			}

		} // end verificacion

	} // end while

	// cerrar transaccion
	if ($ok) $ok = !dberror();
	$conn->CompleteTrans();

	// cerrar y borrar archivo
	fclose($fp);
	unlink($_FILES["usrimport"]["tmp_name"]);
} else {
	$ok = false;
	$errdetail[] = "El archivo no fue subido";
} // end if is_uploaded
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>

<head>
<?php require_once("../includes/inc_header.php"); ?>
</head>

<body>
<?php require_once('../includes/inc_topleft.php');?>

<p class=titulo1>Importaci&oacute;n de usuarios</p>
<?php
if ($ok) {
?>
<p align=center class=texto>Se han importado <b><? echo($lnum); ?></b> usuarios.</p>
<?php
} else {
?>
<p align=center class=textoerror>Se han producido errores al importar los usuarios.<br>
	Corrija el archivo y vuelva a importarlo</p>

<p align=center class=texto>
<table class=tablanormal>

<thead>
<tr><th class=celdatitulo>L&iacute;nea</th>
	<th class=celdatitulo>Error</th></tr>
</thead>

<tbody>
<?php
foreach($errdetail as $lnum => $errdescription) {
?>
<tr><td class=celdatexto align=center><? echo($lnum); ?></td>
	<td class=celdatexto><? echo($errdescription); ?></td></tr>
<?php
} // end foreach
?>

</tbody>

</table>

<input type=button class="botonout" value="Volver" onClick="window.location = 'usuarios-import.php';">

</p>
<?php
} // end if ok
?>
<?php require_once("../includes/inc_bottom.php");?>
</body>

</html>

<?
} // fin autorizacion
?>