<?php
require_once("../includes/lib.php");
require_once("../report/report.php");

$copies = $_POST["copies"];
if (!is_numeric($copies)) { $copies = 1; }
$sql = stripslashes($_POST["sql"]);
$xml = $_POST["xml"];
$propiedadesreport = $_POST["propiedadesreport"];
$returnto = $_POST["returnto"];
?>
<html><head>
<title>Imprimir</title>
<?php require_once("../includes/inc_header.php"); ?>
</head>
<script type="text/javascript">
function validar() {
	var s = "";

	s += (document.frm.printer.value == "" ?
		"- No se ha seleccionado la impresora\n" : "");

	s += (document.frm.papersize.value == "" ?
		"- No se ha seleccionado el papel\n" : "");

	s += (isNaN(parseInt(document.frm.copies.value)) ?
		"- La cantidad de copias es incorrecta\n" : "");

	if (s != "") {
		alert("Se han encontrado errores:\n" + s);
		return false;
	} else {
		return true;
	} // end if errores
} // end validar
</script>
<body>
<?php require_once('../includes/inc_topleft.php'); ?>
<p class=titulo1>Imprimir</p>

<form action="imprime-do.php" method="post" onsubmit="return validar();" name="frm">
<p align=center>
<table class=tablaconbordes>

<tr><th colspan="2" class="celdatitulo">Seleccionar impresora</th></tr>

<tr><td class="celdatexto">Impresora</td>
	<td class="celdatexto"><select name="printer" class="txtbox">
		<option value="">- Seleccionar impresora -</option>
<?php fill_combo_vec(printers()); ?>
		</select></td></tr>

<tr><td class="celdatexto">Papel</td>
	<td class="celdatexto"><select name="papersize" class="txtbox">
		<option value="">- Seleccionar papel -</option>
<?php fill_combo_arr(array(RPT_A4 => "A4", RPT_LETTER => "Carta", RPT_LEGAL => "Oficio"), RPT_A4); ?>
		</select></td></tr>

<tr><td class="celdatexto">Copias</td>
	<td class="celdatexto"><input type="text" class="txtbox" size="2" maxlength="3"
		name="copies" value="<?php echo($copies); ?>"></td></tr>

<tr><td class="celdatexto" colspan="2" align="center">
	<input type="submit" value="Imprimir" class="botonout">
	</td></tr>

</table>
</p>
<input type="hidden" name="sql" value="<?php echo($sql); ?>">
<input type="hidden" name="xml" value="<?php echo($xml); ?>">
<input type="hidden" name="propiedadesreport" value="<?php echo($propiedadesreport); ?>">
<input type="hidden" name="returnto" value="<?php echo($returnto); ?>">
</form>

<?php require_once("../includes/inc_bottom.php"); ?>

</body>
</html>