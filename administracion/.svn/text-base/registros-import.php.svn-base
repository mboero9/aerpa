<?
require_once("../includes/lib.php");
require_once("inc_registros.php");
if (check_auth($usrid, $perid) && check_permission($permiso)) {
// permiso ok
	$p_fil = (array_key_exists("regimport", $_POST) ? $_POST["regimport"] : "");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>

<head>
<? require_once("../includes/inc_header.php"); ?>
<script type="text/javascript">
function submitparcial() {
	document.frm.enctype = "";
	document.frm.action = "";
	document.frm.submit();
} // end submitparcial

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
<? require_once("../includes/inc_topleft.php"); ?>

<p class=titulo1>Importaci&oacute;n de Registros</p>

<form name=frm method=post enctype="multipart/form-data" action="registros-import-do.php"
	onsubmit="return validar();">
<p align=center>
<table class=tablanormal>

<tr><td class=celdatexto>Archivo</td>
	<td class=celdatexto><input type=file class=upload name=regimport value="<? echo($p_fil); ?>"></td></tr>

<tr><td class=celdatexto>Delimitador</td>
	<td class=celdatexto><input type=radio value=";" name=delimitador checked><b>;</b> (punto y coma)<br>
		<input type=radio value="," name=delimitador><b>,</b> (coma)</td></tr>

<tr><td class=celdatexto colspan=2 align=center><input type=submit name=enviar class=botonout value="Importar"></td></tr>

</table>
</p>
</form>

<p class=texto>El archivo a importar debe ser un CSV (delimitado por comas o puntos y comas,
	como exporta el Excel) con los siguientes campos:</p>
<ol>
<li class=texto><b><? echo(REG_CODIGO); ?></b>: codigo</li>
<li class=texto><b><? echo(REG_DEPENDENCIA); ?></b>: dependencia</li>
<li class=texto><b><? echo(REG_DOMICILIO); ?></b>: domicilio</li>
<li class=texto><b><? echo(REG_LOCALIDAD); ?></b>: localidad</li>
<li class=texto><b><? echo(REG_PROVINCIA); ?></b>: provincia</li>
<li class=texto><b><? echo(REG_CPA); ?></b>: cpa</li>
<li class=texto><b><? echo(REG_CPOSTAL); ?></b>: cpostal</li>
<li class=texto><b><? echo(REG_CPOTALEXP); ?></b>: cpotalexp</li>
<li class=texto><b><? echo(REG_ABONADO); ?></b>: abonado</li>
<li class=texto><b><? echo(REG_CTP); ?></b>: ctp</li>
<li class=texto><b><? echo(REG_CIRCUITO); ?></b>: circuito</li>
<li class=texto><b><? echo(REG_REGION); ?></b>: region</li>
<li class=texto><b><? echo(REG_NROREMITO); ?></b>: nroremito</li>
<li class=texto><b><? echo(REG_BAJA); ?></b>: baja</li>
<li class=texto><b><? echo(REG_FECHABAJA); ?></b>: fechabaja</li>
<li class=texto><b><? echo(REG_USUARIOBAJA); ?></b>: usuariobaja</li>
<li class=texto><b><? echo(REG_USUARIO); ?></b>: usuario</li>
<li class=texto><b><? echo(REG_FECHA_US); ?></b>: fecha_us</li>
</ol>
<!--<p class=texto><i>Nota: los campos entre corchetes son opcionales</i></p>-->

</td>
</tr>
</table>

<? require_once("../includes/inc_bottom.php"); ?>
</body>

</html>
<?
} // fin autorizacion
?>