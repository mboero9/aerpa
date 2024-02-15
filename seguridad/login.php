<?php
require_once("../includes/lib.php");

if ($_POST['_return_to_'] == "") {
	$returnto = "../home/";
} else {
	$returnto = $_POST['_return_to_'];
} // end if returnto vacio
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>

<head>
<?php require_once("../includes/inc_header.php"); ?>
<script type="text/javascript">
function doFocus() {
	document.frm._usuario_.focus();
} // end doFocus
</script>
</head>

<body onLoad="doFocus();">
<?php require_once('../includes/inc_topleft2.php'); ?>

<form action="login-do.php" method=post name=frm>
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
<!--
<br>
<table border=0 cellpadding=5 cellspacing=0 align=center>
<tr><td align="center" valign="bottom" colspan=2><img src="../imagenes/aerpa.gif"></td></tr>
</table>
-->
<br>

<table border=0 cellpadding=5 cellspacing=0 align=center>
<tr><th class=celdatitulo colspan=2><h2>Ingrese Usuario y Password</h2></th></tr>
<tr><td class=celdatexto2><b>Usuario:</b></td>
	<td><input type=text name="_usuario_" class=texto></td></tr>
<tr><td class=celdatexto2><b>Password:</b></td>
	<td><input type=password name="_password_" class=texto></td></tr>
<tr><td colspan=2 align=center><input type=submit class="botonout" value="Login"></td></tr>
</table>
<!--
<table border=0 cellpadding=0 cellspacing=0 align=center>
<tr><td align=center><img src="../imagenes/logohome.jpg" align="middle"></td></tr>
</table>
-->
</form>
<?php require_once("../includes/inc_bottom.php"); ?>
</body>

</html>
