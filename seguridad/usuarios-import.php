<?
require_once("../includes/lib.php");
require_once("inc_usuarios.php");

if (check_auth($usrid, $perid) && check_permission($permiso)) {
// permiso ok
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>

<head>
<?php require_once("../includes/inc_header.php"); ?>
<script type="text/javascript">
function validar() {
	s = "";

	// evitar nuevo submit si haciendo submit
	if (document.frm.enviar.disabled) {
		return false;
	} // end if !reentry

	s += (new String(document.frm.usrimport.value).length == 0 ?
		"- No se ha especificado el archivo\n" : "");

	if (s == "") {
		document.frm.enviar.disabled = true;
		return true;
	} else {
		alert("Se han encontrado errores:\n" + s);
		return false;
	} // end if
} // end validar
</script>
</head>

<body>
<?php require_once('../includes/inc_topleft.php');?>

<p class=titulo1>Importaci&oacute;n de usuarios</p>

<form name=frm method=post enctype="multipart/form-data" action="usuarios-import-do.php"
	onsubmit="return validar();">
<p align=center>
<table class=tablanormal>

<tr><td class=celdatexto>Archivo</td>
	<td class=celdatexto><input type=file class=upload name=usrimport value=""></td></tr>

<tr><td class=celdatexto>Delimitador</td>
	<td class=celdatexto><input type=radio value=";" name=delimitador checked><b>;</b> (punto y coma)<br>
		<input type=radio value="," name=delimitador><b>,</b> (coma)</td></tr>

<tr><td class=celdatexto colspan=2 align=center><input type=submit name=enviar class="botonout" value="Importar"></td></tr>

</table>
</p>
</form>

<p class=texto>El archivo a importar debe ser un CSV (delimitado por comas o puntos y comas,
	como exporta el Excel) con los siguientes campos:</p>
<ol>
<li class=texto><b><? echo(USR_COL_LOGIN); ?></b>: nombre de usuario (login)</li>
<li class=texto><b><? echo(USR_COL_PASSWORD); ?></b>: contrase&ntilde;a</li>
<li class=texto><b><? echo(USR_COL_DOCUMENTO); ?></b>: n&uacute;mero de documento, sin tipo</li>
<li class=texto><b><? echo(USR_COL_NOMBRE); ?></b>: nombre</li>
<li class=texto><b><? echo(USR_COL_APELLIDO); ?></b>: apellido</li>
<li class=texto><b><? echo(USR_COL_PERFIL); ?></b>: id de perfil</li>
<li class=texto><b>[<? echo(USR_COL_HABILITADO); ?>]</b>: habilitado -
	vac&iacute;o (default: &quot;1&quot;), &quot;1&quot; o &quot;0&quot;</li>
</ol>
<p class=texto><i>Nota: los campos entre corchetes son opcionales</i></p>

<table class=tablanormal align=center>
	<thead>
		<tr><th colspan=2 class=celdatitulo>Perfiles</th></tr>
		<tr><th class=celdatituloColumna>Id</th>
			<th class=celdatituloColumna>Descripci&oacute;n</th></tr>
	</thead>
	<tbody>
<?
	$fondo = 1;
	try {
	$sql =
		"Select PER_ID, PER_DESCRIPCION " .
		"From PERFIL " .
		"Order by 1";
	$rs = $conn->Execute($sql);
	while (!$rs->EOF) {
?>
		<tr class=fondotabla<? echo($fondo); ?>>
			<td class=celdatexto><? echo($rs->fields["PER_ID"]); ?></td>
			<td class=celdatexto><? echo(htmlentities($rs->fields["PER_DESCRIPCION"])); ?></td></tr>
<?
		$fondo = ($fondo == 1 ? 2 : 1);
		$rs->MoveNext();
	} // end while !eof
	} catch (exception $e) {
		dbhandleerror($e);
	}
?>
	</tbody>
</table>

<?php require_once("../includes/inc_bottom.php"); ?>
</body>

</html>
<?
} // fin autorizacion
?>