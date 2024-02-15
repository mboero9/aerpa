<?php
require_once("../includes/lib.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<?php require_once("../includes/inc_header.php"); ?>
</head>
<body>
<?php require_once('../includes/inc_topleft.php'); ?>
<br>
<p class=titulo1 align="center">Acceso denegado</p>
<br>
<p class=textoerror align="center">No tiene acceso a la p&aacute;gina especificada.</p>
<br>
<form action="../home/index.php" method=post name=frm>
<input type=hidden name="_return_to_" value="<?php echo($_POST['_return_to_']); ?>">
<?php
foreach($_POST as $key => $value) {
	if (($key{0} != "_") || ($key{strlen($key)-1} != "_")) {
?>
<input type=hidden name="<?php echo($key); ?>" value="<?php echo($value) ?>">
<?php
	} // end if empieza y termina con "_"
} // end foreach
?>
<p align=center><input type=submit class="botonout" value="Volver"></p>
</form>
<?php require_once("../includes/inc_bottom.php"); ?>
</body>
</html>
