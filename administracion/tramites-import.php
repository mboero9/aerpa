<?
require_once("../includes/lib.php");
require_once("inc_tramites.php");
if (check_auth($usrid, $perid) && check_permission($permiso)) {
// permiso ok
	$p_fil = (array_key_exists("usrimport", $_POST) ? $_POST["usrimport"] : "");
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

	s += (new String(document.frm.traimport.value).length == 0 ?
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

<p class=titulo1>Importaci&oacute;n de Tramites</p>

<form name=frm method=post enctype="multipart/form-data" action="tramites-import-do.php"
	onsubmit="return validar();">
<p align=center>
<table class=tablanormal>

<tr><td class=celdatexto>Archivo</td>
	<td class=celdatexto><input type=file class=upload name=traimport value="<? echo($p_fil); ?>"></td></tr>

<tr><td class=celdatexto>Delimitador</td>
	<td class=celdatexto><input type=radio value=";" name=delimitador checked><b>;</b> (punto y coma)<br>
		<input type=radio value="," name=delimitador><b>,</b> (coma)</td></tr>

<tr><td class=celdatexto colspan=2 align=center><input type=submit name=enviar class=botonout value="Importar"></td></tr>

</table>
</p>
</form>
<table>
<tr><td>
<p class=texto>El archivo a importar debe ser un CSV (delimitado por comas o puntos y comas,
	como exporta el Excel) con los siguientes campos:</p>
<ol>
<li class=texto><b><? echo(TRA_MOV); ?></b>: mov</li>
<li class=texto><b><? echo(TRA_FECHA); ?></b>: Fecha</li>
<li class=texto><b><? echo(TRA_ORIGEN); ?></b>: Origen</li>
<li class=texto><b><? echo(TRA_DESTINO); ?></b>: Destino</li>
<li class=texto><b><? echo(TRA_DOMINIO); ?></b>: Dominio</li>
<li class=texto><b><? echo(TRA_BOLSIN); ?></b>: Bolsin</li>
<li class=texto><b><? echo(TRA_CPOSTALEXP); ?></b>: cpostalexp</li>
<li class=texto><b><? echo(TRA_FECHABOS); ?></b>: Fechabos</li>
<li class=texto><b><? echo(TRA_FECHAHORA); ?></b>: fechahora</li>
<li class=texto><b><? echo(TRA_NROREMITO); ?></b>: nro de remito</li>
<li class=texto><b><? echo(TRA_ENTREGA); ?></b>: entrega</li>
<li class=texto><b><? echo(TRA_LEGAJO); ?></b>: legajo</li>
<li class=texto><b><? echo(TRA_CODIGO1); ?></b>: codigo 1</li>
<li class=texto><b><? echo(TRA_CODIGO2); ?></b>: codigo 2</li>
<li class=texto><b><? echo(TRA_CODIGO3); ?></b>: codigo 3</li>
<li class=texto><b><? echo(TRA_CODIGO4); ?></b>: codigo 4</li>
<li class=texto><b><? echo(TRA_OBSERVACION); ?></b>: observacion</li>
<li class=texto><b><? echo(TRA_CERRADO); ?></b>: cerrado</li>
<li class=texto><b><? echo(TRA_APLICADO); ?></b>: aplicado</li>
<li class=texto><b><? echo(TRA_USUARIO); ?></b>: usuario</li>
<li class=texto><b><? echo(TRA_FECHA_US); ?></b>: fecha_us</li>
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