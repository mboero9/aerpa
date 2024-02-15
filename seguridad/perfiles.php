<?php
require_once("../includes/lib.php");

if (check_auth($usrid, $perid) && check_permission($permiso)) {
	$ordencolumna = (array_key_exists("ordencolumna", $_POST) ? $_POST["ordencolumna"] : "2");
// permiso ok
$permiso = PERM_EDIT;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>

<head>
<?php require_once("../includes/inc_header.php"); ?>
<script type="text/javascript">
function go(accion) {
	if ((accion != <?php echo(ABM_NEW); ?>) && (document.frm.id.value == "")) {
		alert("Debe seleccionar una fila");
	} else {
		document.frm.accion.value = accion;
		document.frm.submit();
	} // end if id
} // end go

function selrow(id) {
	if (document.frm.id.value != "") {
		document.getElementById("row_" + document.frm.id.value).className =
			document.frm.cls.value;
	}
	document.frm.id.value = id;
	rw = document.getElementById("row_" + id);
	document.frm.cls.value = rw.className;
	rw.className = "fondoconfirmacion";
} // end selrow

function ordenar(columna)
{
	var col = document.frm.ordencolumna;

	if (col.value == columna) {
		col.value = (columna.indexOf(' desc') > -1 ? columna.replace(' desc', '') : columna + ' desc');
	} else {
		col.value = columna;
	}

	document.frm.action = "";
	document.frm.submit();
} // end ordenar
</script>
</head>

<body>
<?php require_once('../includes/inc_topleft.php'); ?>

<p class=titulo1>Perfiles</p>

<form action="perfil-edit.php" method=post name=frm>
<input type=hidden name=id>
<input type=hidden name=cls>
<input type=hidden name=accion>
<input type=hidden name=ordencolumna value="<?php echo($ordencolumna); ?>">
</form>
<?php
try {
$sql = "Select PER_ID, PER_DESCRIPCION, PER_DEFAULT, PER_EDITABLE " .
	"From PERFIL " .
	"Order by " . $ordencolumna;
$rs = $conn->execute($sql);

if ($rs->EOF) {
?>
<p align=center class=texto>No hay perfiles cargados.</p>
<?php
} else {
?>
<p align=center>
<table class=tablanormal>

<thead>
<tr><th class=celdatituloColumna><span style="cursor: pointer" onClick="ordenar('2');">Descripci&oacute;n</span></th>
	<th class=celdatituloColumna><span style="cursor: pointer" onClick="ordenar('3');">Default</span></th>
	<th class=celdatituloColumna><span style="cursor: pointer" onClick="ordenar('4');">Editable</span></th>
</thead>

<tbody>
<?php
	$fondo = 1;
	while (!$rs->EOF) {
		$id = $rs->fields["PER_ID"];
		$descripcion = $rs->fields["PER_DESCRIPCION"];
		$default = $rs->fields["PER_DEFAULT"];
		$editable = $rs->fields["PER_EDITABLE"];
?>
<tr class=fondotabla<?php echo($fondo); ?> id="row_<?php echo($id); ?>"
	onclick="selrow(<?php echo($id); ?>);" style="cursor: pointer;">
	<td class=celdatexto><?php echo(htmlentities($descripcion)); ?></td>
	<td class=celdatexto align=center><?php echo($default ? "X" : "&nbsp;"); ?></td>
	<td class=celdatexto align=center><?php echo($editable ? "X" : "&nbsp;"); ?></td>
		</tr>
<?php
		$fondo = ($fondo == 1 ? 2 : 1);
		$rs->MoveNext();
	} // end while !eof
?>
</tbody>

</table>
</p>
<?php
} // end if eof
} catch (exception $e) {
	dbhandleerror($e);
}
?>
<p align=center>
	<input type=button value="<?php echo(ABMLBL_VIEW); ?>" class="botonout"
		onclick="go(<?php echo(ABM_VIEW); ?>);">
<?php if (($permiso == PERM_EDIT) && $editable) { ?>
	<input type=button value="<?php echo(ABMLBL_EDIT); ?>" class="botonout"
		onclick="go(<?php echo(ABM_EDIT); ?>);">
	<input type=button value="Nuevo" class="botonout" onClick="go(<?php echo(ABM_NEW); ?>);">
<?php } // end if PERM_EDIT ?>
</p>
<?php require_once("../includes/inc_bottom.php"); ?>
</body>

</html>
<?php
} // fin autorizacion
?>