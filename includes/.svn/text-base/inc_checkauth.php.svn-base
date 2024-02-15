<?php
require_once("inc_functions.php");
require_once("db.php");

/////////////////////////////////////////////////////
// Verificar si es un usuario autenticado
function check_auth(&$usr, &$per) {
	global $conn;

	if (!array_key_exists(SES_COOKIE, $_COOKIE)) {
		// no hay cookie de sesion, ir a login
		go_login();
	} else {
		// buscar datos de sesion en base de datos
		$ses = $_COOKIE[SES_COOKIE];
		$sql =
			"Select SES_ID,USR_ID,PER_ID,SES_IP " .
			"From SEGSESION " .
			"Where SES_RAND = " . sqlint($ses) .
			" And SES_EXPIRA >= " . sqldate(dbtime()) .
			" And SES_LOGOUT Is Null";
		$rs = $conn->Execute($sql);

		if ($rs->EOF) {
			// no esta la sesion o esta vencida, ir a login
			go_login();
		} elseif ($rs->fields["SES_IP"] != $_SERVER["REMOTE_ADDR"]) {
			// la sesion existe, pero la IP difiere
			go_login();
		} else {
			$usr = $rs->fields["USR_ID"];
			$per = $rs->fields["PER_ID"];

			// mover el vencimiento de la sesion hacia adelante
			// <$sesdur> minutos
			$sesdur = getParametro(PAR_SES_DURACION);
			if ($sesdur == "") {
				$sesdur = SES_DURACION;
			} // end if sesdur
			$sql =
				"Update SEGSESION " .
				"Set SES_EXPIRA = " . sqldate(dbtime() + $sesdur * 60) . " " .
				"Where SES_RAND = " . sqlint($_COOKIE[SES_COOKIE]);
			$conn->Execute($sql);

			// Anti proxy
			anticache();

			// Volver
			return true;
		} // end if eof/IP
	} // end if cookie
} // end function check_auth
/////////////////////////////////////////////////////


/////////////////////////////////////////////////////
// Verificar si el usuario tiene permiso para la pagina especificada
function check_permission(&$permiso, $pag = null) {
	global $conn;
	global $perid;
	global $ignorarpermisos;

	// si no se recibe la pagina por parametro, es la que invoca la funcion
	if (is_null($pag)) {
		$pag = basename($_SERVER["PHP_SELF"]);
	} else {
		$pagpath = dirname($pag);
		$pag = basename($pag);
	} // end if isnull $pag

	if (in_array($pagpath, array("","."))) {
		$pagpath = dirname($_SERVER["PHP_SELF"]);
	} // end if pagpath ""

	// limpiar path
	$tmp = explode("/", $pagpath);
	$pagpath = $tmp[count($tmp) - 1];

	// buscar si tiene permiso
	$sql =
		"Select sa.PERM_TIPO " .
		"From SEGADMINSEGURIDADABM sa " .
		"	Inner Join SEGADMINABM a On a.ADM_ABM_ID = sa.ADM_ABM_ID " .
		"Where sa.PER_ID = " . sqlint($perid) .
		"	And Lower(a.ADM_ABM_LINK_DIR) = " . sqlstring(strtolower($pagpath)) .
		"	And Lower(a.ADM_ABM_LINK) = " . sqlstring(strtolower($pag));
	$rs = $conn->Execute($sql);
	if (!$rs->EOF) {
		$permiso = strtoupper($rs->fields["PERM_TIPO"]);
		return true;
	} else {
		if ($ignorarpermisos) {
			return true;
		} // end if ignorarpermisos

		header("location: ../seguridad/forbidden.php");
	} // end if eof
} // end function check_permission
/////////////////////////////////////////////////////


/////////////////////////////////////////////////////
// Redireccionar al login manteniendo los POST
function go_login() {
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<script type="text/javascript">
function go() {
	document.frm.submit();
}
</script>
</head>
<body onLoad="go();">
<form action="../seguridad/login.php" method=post name=frm>
<input type=hidden name="_return_to_" value="<?php echo($_SERVER['PHP_SELF']); ?>">
<?php
foreach($_POST as $key => $value) {
?>
<input type=hidden name="<?php echo($key); ?>" value="<?php echo($value) ?>">
<?php
} // end foreach
?>
</form>
</body>
</html>
<?php
} // end function go_login
/////////////////////////////////////////////////////
?>
