<?php
require_once("../includes/lib.php");
require_once("inc_sesion.php");

// Verificar sesion
sesion_verificar($usr, $per, $err);

// Volver
if ($err == ERRL_OK) {
	// Todo ok, arrancar sesion
	sesion_inicio($usr, $per);
	// fijar el action al form original
	$action = $_POST["_return_to_"];
} elseif ($err == ERRL_CAMBIOPASS) {
	$action = "changepw.php";
} else {
	$action = "badlogin.php";
} // end if volver
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
<body onload="go();">
<form action="<?php echo($action); ?>" method=post name=frm>
<?php
if ($err != ERRL_OK) {
	if ($err == ERRL_CAMBIOPASS) {
?>
<input type=hidden name="_usuario_" value="<?php echo($_POST["_usuario_"]); ?>">
<?php
	} // end if CAMBIOPASS
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
?>
</form>
</body>
</html>
