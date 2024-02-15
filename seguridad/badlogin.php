<?php
require_once("../includes/lib.php");

$err = $_POST["_error_type_"];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<?php require_once("../includes/inc_header.php"); ?>
</head>
<body>
<?php require_once('../includes/inc_topleft2.php'); ?>
<br><br>
<p class=titulo1 align="center">Error de inicio de sesi&oacute;n</p>
<br><br>
<p class=textoerror align="center"><?php
if ($err == ERRL_USUARIO) {
	echo("Usuario inexistente");
} elseif ($err == ERRL_PASSWORD) {
	echo("Password incorrecta");
} elseif ($err == ERRL_BLOQUEADO) {
	echo("Usuario bloqueado");
} elseif ($err == ERRL_INHABILITADO) {
	echo("Usuario inhabilitado");
} elseif ($err == ERRL_YAINGRESADO) {
	echo("Ya tiene una sesi&oacute;n en otro equipo");
} elseif ($err == ERRL_BAJA) {
	echo("Usuario dado de baja");
} // end if
?></p>
<br><br>
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
