<?php
require_once("../includes/lib.php");

if (check_auth($usrid, $perid) && check_permission($permiso, "usuarios.php")) {
// permiso ok

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>

<head>
<?php require_once("../includes/inc_header.php"); ?>

<script type="text/javascript">
function validar() {
	var s = "";

	s += (new String(document.frm.nueva.value).length < 8 ?
		"- Password demasiado corta, 8 caracteres mínimo\n" : "");

	s += (document.frm.nueva.value != document.frm.nueva2.value ?
		"- La password nueva difiere de la confirmación\n" : "");

	var pwrx = /^(.*[A-Za-z]{1,}.*[0-9]{1,}.*|.*[0-9]{1,}.*[A-Za-z]{1,}.*)$/;
	s += (!pwrx.test(document.frm.nueva.value) ?
		"- La password debe contener al menos una letra y un número\n" : "");

	if (s != "") {
		alert("Se han encontrado errores:\n" + s);
		return false;
	} else {
		return true;
	} // end if err
} // end validar
</script>
</head>

<body>
<?php require_once('../includes/inc_topleft.php'); ?>

<p class=titulo1>Cambiar password</p>
<?php
$v_id = $_POST["id"];

try {
$sql = "Select USR_USERNAME, USR_NOMBRE, USR_APELLIDO " .
	"From USUARIO " .
	"Where USR_ID = " . sqlint($v_id);
$rs = $conn->Execute($sql);
if (!$rs->EOF) {
	$v_username = $rs->fields["USR_USERNAME"];
	$v_nombre = $rs->fields["USR_NOMBRE"];
	$v_apellido = $rs->fields["USR_APELLIDO"];
} // end if eof
} catch (exception $e) {
	dbhandleerror($e);
}
?>
<form action="usuario-pw-do.php" method=post name=frm onsubmit="return validar();">
<input type=hidden name=accion value="<?php echo($accion); ?>">
<input type=hidden name=id value="<?php echo($v_id); ?>">

<p align=center>
<table class=tablanormal>

<tr><td class=celdatexto>Usuario</td>
	<td class=celdatexto><b><?php echo(htmlentities($v_apellido . ", " . $v_nombre .
		" (" . $v_username . ")")); ?></b></td></tr>

<tr><td class=celdatexto>Nueva</td>
	<td><input type=password name="nueva" class=texto></td></tr>

<tr><td class=celdatexto>Confirmaci&oacute;n</td>
	<td><input type=password name="nueva2" class=texto></td></tr>

<tr><td colspan=2 class=celdatexto>
		<input type=submit class="botonout" value="Cambiar pass">
		<input type=button class="botonout" value="Volver" onclick="window.location = 'usuarios.php';">
	</td></tr>

</table>
</p>
</form>

<?php require_once("../includes/inc_bottom.php"); ?>
</body>

</html>
<?php
} // fin autorizacion
?>