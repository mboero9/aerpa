<?php
require_once("../includes/lib.php");

/////////////////////////////////////////////////////
// Verificacion de sesion
function sesion_verificar(&$usr, &$per, &$err) {
	global $conn;

	// Levantar login
	$usr = $_POST["_usuario_"];
	$pwd = md5($_POST["_password_"]);
	$err = ERRL_OK;

	// Consultar en base de datos
	try {
	$sql =
		"Select USR_ID,PER_ID,USR_PASSWORD," .
		"	USR_CAMBIAR_PASS,USR_BLOQUEADO," .
		"	USR_HABILITADO,USR_BAJA," .
		"	Case When USR_FECHA_BAJA <= " . sqldate(dbtime()) . " " .
		"		Then 1 Else 0 End as USR_VENCIDO " .
		"From USUARIO " .
		"Where Lower(USR_USERNAME) = Lower(" . sqlstring($usr) . ") " .
		"Order by USR_BAJA";
			// con el order by se logra que si un username esta mas de una vez,
			// intente obtener primero el que no esta dado de baja, y recien despues
			// el que tiene baja logica
	$rs = $conn->Execute($sql);

	// Controlar si login OK
	if ($rs->EOF) {
		// usuario inexistente
		$err = ERRL_USUARIO;
	} else {
		$usr = $rs->fields["USR_ID"];
		$per = $rs->fields["PER_ID"];
		if ($rs->fields["USR_BLOQUEADO"]) {
			// usuario bloqueado
			$err = ERRL_BLOQUEADO;
		} elseif (!$rs->fields["USR_HABILITADO"]) {
			// usuario inhabilitado
			$err = ERRL_INHABILITADO;
		} elseif ($rs->fields["USR_VENCIDO"] || $rs->fields["USR_BAJA"]) {
			// usuario dado de baja
			$err = ERRL_BAJA;
		} elseif ($pwd != $rs->fields["USR_PASSWORD"]) {
			// password invalida

			// verificar login fallidos; si supera el maximo, bloquear el usuario
			$sql = "Select USR_LOGIN_FALLIDOS From USUARIO Where USR_ID = " . sqlint($usr);
			$rs = $conn->Execute($sql);
			$logfail = $rs->fields["USR_LOGIN_FALLIDOS"] + 1;

			$sesbloq = getParametro(PAR_SES_BLOQUEO);
			if (!is_numeric($sesbloq)) {
				$sesbloq = SES_BLOQUEAR;
			} // end if sesbloq

			$sql = "Update USUARIO Set USR_LOGIN_FALLIDOS = " . sqlint($logfail) . "," .
					"USR_BLOQUEADO = " . sqlboolean($logfail >= $sesbloq) . " " .
					"Where USR_ID = " . sqlint($usr);
			$conn->Execute($sql);

			$err = ERRL_PASSWORD;
		} else {
			$sql =
				"Select SES_IP " .
				"From SEGSESION " .
				"Where USR_ID = " . sqlint($usr) .
				" And SES_EXPIRA >= " . sqldate(dbtime()) .
				" And SES_LOGOUT Is Null";
			$rs2 = $conn->Execute($sql);
			if (!$rs2->EOF) {
				if ($rs2->fields["SES_IP"] != $_SERVER["REMOTE_ADDR"]) {
					// tiene una sesion en otra IP
					$err = ERRL_YAINGRESADO;
				} // end if != IP
			} // end if !eof

			if (($err == ERRL_OK) && ($rs->fields["USR_CAMBIAR_PASS"])) {
				// forzar cambio de password
				$err = ERRL_CAMBIOPASS;
			} // end if cambiopass
		} // end if controles
	} // end if eof
	} catch (exception $e) {
		dbhandleerror($e);
	}
} // end sesion_verificar
/////////////////////////////////////////////////////


/////////////////////////////////////////////////////
// Marcar inicio de sesion
function sesion_inicio($usr, $per) {
	global $conn;

	try {
	// generar id de sesion evitando duplicados
	do {
		$sesid = mt_rand();
		$sql = "Select SES_ID From SEGSESION Where SES_RAND = " . sqlint($sesid);
		$rs2 = $conn->Execute($sql);
	} while (!$rs2->EOF);
	// limpiar contador de login invalidos
	$sql = "Update USUARIO " .
		"Set USR_LOGIN_FALLIDOS = " . sqlint(0) . "," .
		"	 USR_FECHA_LOGIN = " . sqldate(dbtime()) . " " .
		"Where USR_ID = " . sqlint($usr);
	$conn->Execute($sql);
	// obtener duracion de sesion
	$sesdur = getParametro(PAR_SES_DURACION);
	if ($sesdur == "") {
		$sesdur = SES_DURACION;
	} // end if sesdur
	// insertar sesion en bd
	$sql =
		"Insert Into SEGSESION (" .
		"	SES_ID,SES_RAND,USR_ID,PER_ID,SES_FECHA,SES_EXPIRA,SES_IP) " .
		"Values (" . sqlint(numerador("SEGSESION")) . "," . sqlint($sesid) . "," .
		sqlint($usr) . "," . sqlint($per) . "," .
		sqldate(dbtime()) . "," . sqldate(dbtime() + $sesdur * 60) . "," .
		sqlstring($_SERVER["REMOTE_ADDR"]) . ")";
	$conn->Execute($sql);
	// enviar cookie con id random sesion al cliente
	setcookie(SES_COOKIE, $sesid, null, raizsitio());
	} catch (exception $e) {
		dbhandleerror($e);
	}
} // end sesion_inicio
/////////////////////////////////////////////////////


/////////////////////////////////////////////////////
// Marcar fin de sesion
function sesion_fin() {
	global $conn;

	// obtener id de sesion
	$sesid = $_COOKIE[SES_COOKIE];

	try {
	// borrar la hora de login
	$sql = "Update USUARIO " .
		"Set USR_FECHA_LOGIN = NULL Where USR_ID = " .
		"(Select USR_ID From SEGSESION Where SES_RAND = " . sqlint($sesid) . ")";
	$conn->Execute($sql);

	// borrar sesion en bd
	$sql = "Update SEGSESION " .
		"Set SES_LOGOUT = " . sqldate(dbtime()) . " " .
		"Where SES_RAND = " . sqlint($sesid);
	$conn->Execute($sql);

	// borrar cookie
	setcookie(SES_COOKIE, $sesid, 0, raizsitio());

	// redireccionar a home
	header("location: ../home/");
	} catch (exception $e) {
		dbhandleerror($e);
	}
} // end sesion_fin
/////////////////////////////////////////////////////
?>
