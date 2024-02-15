<?
require_once("../includes/lib.php");
if ((check_auth($usrid, $perid) && check_permission($permiso))||(!isset($_POST['opcion']))) {
if (isset($_POST["_usuario_"])) {
	$usuario = $_POST["_usuario_"];
	$returnto = $_POST["_return_to_"];
} elseif (isset($_COOKIE[SES_COOKIE])) {
	try {
	$sql = "Select USR_USERNAME From USUARIO " .
		"Where USR_ID = (" .
		"	Select USR_ID From SEGSESION " .
		"	Where SES_RAND = " . sqlint($_COOKIE[SES_COOKIE]) . ")";
	$rs = $conn->Execute($sql);
	if (!$rs->EOF) {
		$usuario = $rs->fields["USR_USERNAME"];
		$returnto = "../";
	} // end if eof
	} catch (exception $e) {
		dbhandleerror($e);
	}
} // end if _usuario_
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>

<head>
<?php
require_once("../includes/inc_header.php");
?>
<script type="text/javascript">
function doFocus() {
	document.frm._password_.focus();
} // end doFocus

function validarForm() {
	var s = "";

	s += (document.frm._password_.value == document.frm._nueva_.value ?
		"- Debe utilizar una nueva password\n" : "");

	s += (document.frm._nueva_.value != document.frm._nueva2_.value ?
		"- La password nueva difiere de la confirmación\n" : "");

	s += (new String(document.frm._nueva_.value).length < 8 ?
		"- Password demasiado corta, 8 caracteres mínimo\n" : "");

	var pwrx = /^(.*[A-Za-z]{1,}.*[0-9]{1,}.*|.*[0-9]{1,}.*[A-Za-z]{1,}.*)$/;
	s += (!pwrx.test(document.frm._nueva_.value) ?
		"- La password debe contener al menos una letra y un número\n" : "");

	if (s != "") {
		alert("Se han encontrado errores:\n" + s);
		return false;
	} else {
		return true;
	} // end if err
} // end validarForm
</script>
</head>

<body onLoad="doFocus();">
<? require_once('../includes/inc_topleft.php'); ?>
<form action="changepw-do.php" method=post onSubmit="return validarForm();" name=frm>
<input type=hidden name="_return_to_" value="<?php echo($returnto); ?>">
<?php
foreach($_POST as $key => $value) {
	if (($key{0} != "_") || ($key{strlen($key)-1} != "_")) {
?>
<input type=hidden name="<?php echo($key); ?>" value="<?php echo($value) ?>">
<?php
	} // end if empieza y termina con "_"
} // end foreach
?>
<table border=0 cellpadding=5 cellspacing=5 align=center>
<thead>
<tr><th class=celdatitulo colspan=2>Login</th></tr>
</thead>
<tbody>
<tr><td class=celdatexto>Usuario</td>
	<td><input type=text name="_usuario_" class=texto value="<?php echo($usuario); ?>"
		readonly></td></tr>
<tr><td class=celdatexto>Anterior</td>
	<td><input type=password name="_password_" class=texto></td></tr>
<tr><td class=celdatexto>Nueva</td>
	<td><input type=password name="_nueva_" class=texto></td></tr>
<tr><td class=celdatexto>Confirmaci&oacute;n</td>
	<td><input type=password name="_nueva2_" class=texto></td></tr>
<tr><td colspan=2 align=center><input type=button class="botonout" value="Volver" onClick="location.href='../home/index.php'"><input type=submit class="botonout" value="Cambiar password"></td></tr>
</tbody>
</table>
</form>

<? require_once("../includes/inc_bottom.php");?>
</body>

</html>
<?
}//Cierro if autorizacion
?>