<?php
require_once("../includes/lib.php");

if (check_auth($usrid, $perid) && check_permission($permiso)) {
	$ordencolumna = (array_key_exists("ordencolumna", $_POST) ? $_POST["ordencolumna"] : "1");
// permiso ok
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>

<head>
<?php require_once("../includes/inc_header.php"); ?>
<script type="text/javascript">
var submitting = false;
function go(accion) {
	if (submitting) return (false);

	submitting = true;
	if ((accion != <?php echo(ABM_NEW); ?>) && (document.frm.id.value == "")) {
		alert("Debe seleccionar una fila");
	} else {
		if (accion == <?php echo(ABM_SETDEFAULT); ?>) {
			document.frm.action = "impresora-edit-do.php";
		}
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

<p class=titulo1>Impresoras</p>

<form action="impresora-edit.php" method=post name=frm>
<input type=hidden name=id>
<input type=hidden name=cls>
<input type=hidden name=accion>
<input type=hidden name=ordencolumna value="<?php echo($ordencolumna); ?>">
</form>
<?php
try {
$sql = "Select IMP_NOMBRE,IMP_HOST,IMP_PORT,IMP_PATH,IMP_DEFAULT,IMP_PAPEL " .
	"From IMPRESORA " .
	"Order by " . $ordencolumna;
$rs = $conn->Execute($sql);

if ($rs->EOF) {
?>
<p align=center class=texto>No hay registros cargados.</p>
<?php
} else {
?>
<p align=center>
<table class=tablanormal>

<thead>
<tr><th class=celdatituloColumna><span style="cursor: pointer" onClick="ordenar('IMP_NOMBRE');">Nombre</span></th>
	<th class=celdatituloColumna><span style="cursor: pointer" onClick="ordenar('IMP_HOST');">Host</span></th>
	<th class=celdatituloColumna><span style="cursor: pointer" onClick="ordenar('IMP_PORT');">Puerto</span></th>
	<th class=celdatituloColumna><span style="cursor: pointer" onClick="ordenar('IMP_PATH');">Ruta</span></th>
	<th class=celdatituloColumna><span style="cursor: pointer" onClick="ordenar('IMP_PAPEL');">Papel</span></th>	
	<th class=celdatituloColumna><span style="cursor: pointer" onClick="ordenar('IMP_DEFAULT');">Predeterminada</span></th>	
</thead>

<tbody>
<?php
	$fondo = 1;
	while (!$rs->EOF) {
		$id = $rs->fields["IMP_NOMBRE"];
		$host = $rs->fields["IMP_HOST"];
		$port = $rs->fields["IMP_PORT"];
		$path = $rs->fields["IMP_PATH"];
		$default = $rs->fields["IMP_DEFAULT"];
		$papel = $rs->fields["IMP_PAPEL"];
		$des_papel=$rs->fields["IMP_PAPEL"];
		switch($papel) {
		 case 'RPT_A4': $des_papel='A4'; break;
		 case 'RPT_LETTER': $des_papel='Carta'; break;
		 case 'RPT_LEGAL': $des_papel='Oficio'; break;
		}		 		 
?>
<tr class=fondotabla<?php echo($fondo); ?> id="row_<?php echo($id); ?>"
	onclick="selrow('<?php echo($id); ?>');" style="cursor: pointer;">
	<td class=celdatexto><?php echo(htmlentities($id)); ?></td>
	<td class=celdatexto><?php echo(htmlentities($host)); ?></td>
	<td class=celdatexto><?php echo($port); ?></td>
	<td class=celdatexto><?php echo(htmlentities($path)); ?></td>
	<td class=celdatexto><?php echo(htmlentities($des_papel)); ?></td>	
	<td class=celdatexto><?php echo($default ? "X" : ""); ?></td>
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
	<input type=button value="<?php echo(ABMLBL_VIEW); ?>" class=botonout
		onclick="go(<?php echo(ABM_VIEW); ?>);">
<?php if ($permiso == PERM_EDIT) { ?>
	<input type=button value="<?php echo(ABMLBL_EDIT); ?>" class=botonout
		onclick="go(<?php echo(ABM_EDIT); ?>);">
	<input type=button value="<?php echo(ABMLBL_DEL); ?>" class=botonout
		onclick="go(<?php echo(ABM_DEL); ?>);">
	<input type=button value="<?php echo(ABMLBL_SETDEFAULT); ?>" class=botonout
		onclick="go(<?php echo(ABM_SETDEFAULT); ?>);">
	<input type=button value="Nuevo" class=botonout onClick="go(<?php echo(ABM_NEW); ?>);">
<?php } // end if PERM_EDIT ?>
</p>
<?php require_once("../includes/inc_bottom.php"); ?>
</body>

</html>
<?php
} // fin autorizacion
?>