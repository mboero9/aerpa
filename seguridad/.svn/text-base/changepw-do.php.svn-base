<?php
require_once("../includes/lib.php");
require_once("inc_sesion.php");

$conn->StartTrans();

// Verificar sesion
sesion_verificar($usr, $per, $err);

// Volver
if (($err == ERRL_CAMBIOPASS) || ($err == ERRL_OK)) {
	// Todo ok, arrancar sesion
	sesion_inicio($usr, $per);
	// Cambiar password
	$sql =
		"Update USUARIO " .
		"Set USR_PASSWORD = " . sqlstring(md5($_POST["_nueva_"])) . "," .
		"USR_CAMBIAR_PASS = " . sqlboolean(false) . " " .
		"Where USR_ID = " . sqlint($usr);
	try {
		$conn->Execute($sql);
	} catch (exception $e) {
		dberror($e);
	}
	$action = $_POST["_return_to_"];
} else {
	$action = "badlogin.php";
} // end if CAMBIOPASS
if (!$conn->HasFailedTrans()) {
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<script type="text/javascript">
function go() {
	document.frm.submit();
} // end go
</script>
</head>
<body onload="go();">
<form action="<?php echo($action); ?>" method=post name=frm>
<?php
if ($err != ERRL_CAMBIOPASS) {
?>
<input type=hidden name="_error_type_" value="<?php echo($err); ?>">
<input type=hidden name="_return_to_" value="<?php echo($_POST["_return_to_"]); ?>">
<?php
} // end if ERRL_OK
foreach($_POST as $key => $value) {
	if (($key{0} != "_") || ($key{strlen($key)-1} != "_")) {
?>
<input type=hidden name="<?php echo($key); ?>" value="<?php echo($value) ?>">
<?php
	} // end if empieza y termina con "_"
} // end foreach

} // end if FailedTrans

$conn->CompleteTrans();
?>
</form>
</body>
</html>
