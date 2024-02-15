<?php

//actualiza la fecha  de inicio y pone la de fin en null
//procInicio("IMPOSICION","2003-08-28 17:20:20");
function procInicio($procNombre,$fechaini)
{
	global $conn;
	$conn->StartTrans();
	$sql = "Update PROCESO
		Set PROC_INI = " . sqldate($fechaini). ",
			PROC_FIN = Null
		Where PROC_NOMBRE = " . sqlstring($procNombre);
	try {
		$conn->Execute($sql);
	} catch (exception $e) {
		dbhandleerror($e);
	}
	$conn->CompleteTrans();
	return(!dberror());
}

//actualiza la fecha de fin de un proceso
//procFin("IMPOSICION",$condate);
function procFin($procNombre,$fechafin)
{
	global $conn;
	$conn->StartTrans();
	$sql = "Update PROCESO
		Set PROC_FIN = " . sqldate($fechafin) . "
		Where PROC_NOMBRE = " . sqlstring($procNombre);
	try {
		$conn->Execute($sql);
	} catch (exception $e) {
		dbhandleerror($e);
	}
	$conn->CompleteTrans();
	return(!dberror());
}

//inserta un registro en el log de procesos
//procLog("IMPOSICION","Este es un evento de prueba",$condate);
function procLog($procNombre,$evento,$fecha)
{
	global $conn;
	$logged = false;
	$conn->StartTrans();
	try {
		// Confirmar si el proceso tiene activado el logging
		$sql = "Select PROC_ID
			From PROCESO
			Where PROC_NOMBRE = " . sqlstring($procNombre) . "
				And PROC_LOGUEAR = " . sqlboolean(true);
		$rs = $conn->Execute($sql);
		if (!$rs->EOF) {
			$id = $rs->fields["PROC_ID"];
			$sql = "Insert Into LOGPROCESO (LOGP_ID,PROC_ID,LOGP_FECHA,LOGP_EVENTO
				) Values (" . sqlint(numerador("LOGPROCESO")) . "," . sqlint($id) . "," .
				sqldate($fecha) . "," . sqlstring(substr($evento,0,254)) . ")";
			$conn->Execute($sql);
			$logged = true;
		}
	} catch (exception $e) {
		dbhandleerror($e);
	}
	$conn->CompleteTrans();
	return($logged);
}

//verifica que el proceso no este corriendo actualmente
function procVerificar($procNombre)
{
	global $conn;

	$avail = false;
	try {
		$sql = "Select PROC_ID
			From PROCESO
			Where PROC_NOMBRE = " . sqlstring($procNombre) . "
				And ((PROC_INI Is Null And PROC_FIN Is Null)
					Or (PROC_INI Is Not Null And PROC_FIN Is Not Null))";
		$rs = $conn->Execute($sql);
		$avail = !$rs->EOF;
	} catch (exception $e) {
		dbhandleerror();
	}
	return ($avail);
}

function alarmaAdd($proceso,$mensaje,$param)
{
	global $conn;

	$mensaje = substr($mensaje, 0, 4000);

	try {
		$sql = "Select PAAL_VALOR From PARAMALARMA Where PAAL_NOMBRE = " . sqlstring($param);
		$rs = $conn->Execute($sql);
		if (!$rs->EOF) {
			$email = $rs->fields["PAAL_VALOR"];
			mail($email, "Alarma proceso [$proceso]",
				$mensaje, "From: " . getParametro(PAR_ALARMA_FROM));
		}

		$sql = "Select PROC_ID From PROCESO Where PROC_NOMBRE = " . sqlstring($proceso);
		$rs = $conn->Execute($sql);
		if (!$rs->EOF) {
			$procid = $rs->fields["PROC_ID"];
		}

		$sql = "Insert into ALARMA (
				ALM_ID,TIPAL_ID,ALM_FEC_GEN,ALM_FEC_ESTADO,ALM_DESCRIPCION,PROC_ID
			) Values (" .
			sqlint(numerador("ALARMA")) . "," .
			sqldate(dbtime()) . "," .
			sqldate(dbtime()) . "," .
			sqlstring($mensaje) . "," .
			sqlint($procid) . ")";
		$conn->Execute($sql);
	} catch (exception $e) {
		dbhandleerror();
	}
	$conn->CompleteTrans();
	return(!dberror());
}
?>
