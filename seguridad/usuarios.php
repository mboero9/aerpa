<?php
require_once("../includes/lib.php");

if (check_auth($usrid, $perid) && check_permission($permiso)) {
	$ordencolumna = (array_key_exists("ordencolumna", $_POST) ? $_POST["ordencolumna"] : "3");
// permiso ok
$permiso = PERM_EDIT;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>

<head>
<?php require_once("../includes/inc_header.php"); ?>
<script type="text/javascript">
function submitParcial() {
	document.frmfiltro.submit();
} // end submitParcial()

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

function go(accion) {
	if ((accion != <?php echo(ABM_NEW); ?>) && (document.frm.id.value == "")) {
		alert("Debe seleccionar una fila");
	} else if ((accion == <?php echo(ABM_DEL); ?>)
			&& (document.frm.id.value == <?php echo($usrid); ?>)) {
		alert("No puede borrar su propio usuario");
	} else {
		document.frm.accion.value = accion;
		document.frm.submit();
	} // end if id
} // end go

function changepw() {
	if (document.frm.id.value == "") {
		alert("Debe seleccionar una fila");
	} else {
		document.frm.action = "usuario-pw.php";
		document.frm.submit();
	} // end if id
} // end changepw

function ordenar(columna)
{
	var col = document.frmfiltro.ordencolumna;

	if (col.value == columna) {
		col.value = (columna.indexOf(' desc') > -1 ? columna.replace(' desc', '') : columna + ' desc');
	} else {
		col.value = columna;
	}

	document.frmfiltro.submit();
}
</script>
</head>

<body>
<?php require_once("../includes/inc_topleft.php"); ?>

<p class=titulo1>Usuarios</p>

<form action="usuario-edit.php" method=post name=frm>
<input type=hidden name=id>
<input type=hidden name=cls>
<input type=hidden name=accion>
</form>

<form action="" method=post name=frmfiltro>
<input type=hidden name=ordencolumna value="<?php echo($ordencolumna); ?>">
</form>
<?php
try {
$sql = "Select u.USR_ID,p.PER_DESCRIPCION,u.USR_USERNAME," .
	"	u.USR_DOCUMENTO,u.USR_NOMBRE,u.USR_APELLIDO," .
	"	" . $conn->SQLDate(FMT_DATE_DB, "u.USR_FECHA_BAJA") ." as USR_FECHA_BAJA," .
	"	u.USR_HABILITADO " .
	"From USUARIO u " .
	"	Inner Join PERFIL p On u.PER_ID = p.PER_ID " .
	"Where u.USR_BAJA = " . sqlboolean(false) . " " .
	"Order by " . $ordencolumna;
$rs = $conn->Execute($sql);

if ($rs->EOF) {
?>
<p align=center class=texto>No hay usuarios cargados.</p>
<?php
	} else {
?>
<p align=center>
<table class=tablanormal>

<thead>
<tr><th class=celdatituloColumna><span style="cursor: pointer" onClick="ordenar('2');">Perfil</span></th>
	<th class=celdatituloColumna><span style="cursor: pointer" onClick="ordenar('3');">Login</span></th>
	<th class=celdatituloColumna><span style="cursor: pointer" onClick="ordenar('5');">Nombre</span></th>
	<th class=celdatituloColumna><span style="cursor: pointer" onClick="ordenar('6');">Apellido</span></th>
	<th class=celdatituloColumna><span style="cursor: pointer" onClick="ordenar('7');">Fecha baja</span></th>
	<th class=celdatituloColumna><span style="cursor: pointer" onClick="ordenar('8');">Habilit.</span></th>
</thead>

<tbody>
<?php
	$fondo = 1;
	while (!$rs->EOF) {
		$id = $rs->fields["USR_ID"];
		$perfil = $rs->fields["PER_DESCRIPCION"];
		$username = $rs->fields["USR_USERNAME"];
		$nombre = $rs->fields["USR_NOMBRE"];
		$apellido = $rs->fields["USR_APELLIDO"];
		$baja = $rs->fields["USR_FECHA_BAJA"];
		$cambiar = $rs->fields["USR_CAMBIAR_PASS"];
		$habilit = $rs->fields["USR_HABILITADO"];
?>
<tr class=fondotabla<?php echo($fondo); ?> id="row_<?php echo($id); ?>"
	onclick="selrow(<?php echo($id); ?>);" style="cursor: pointer;">
	<td class=celdatexto><?php echo(htmlentities($perfil)); ?></td>
	<td class=celdatexto><?php echo(htmlentities($username)); ?></td>
	<td class=celdatexto><?php echo(htmlentities($nombre)); ?></td>
	<td class=celdatexto><?php echo(htmlentities($apellido)); ?></td>
	<td class=celdatexto><?php echo($baja); ?></td>
	<td class=celdatexto align=center><?php echo($habilit ? "X" : "&nbsp;"); ?></td>
		</tr>
<?php
		$fondo = ($fondo == 1 ? 2 : 1);
		$rs->MoveNext();
	} // end while !eof
?>
</tbody>

</table>
<?php
} // end if eof
} catch (exception $e) {
	dbhandleerror($e);
}
?>
<table class=tablanormal>
<tr><td align=center>
	<input type=button value="<?php echo(ABMLBL_VIEW); ?>" class="botonout"
		onclick="go(<?php echo(ABM_VIEW); ?>);">
<?php if ($permiso == PERM_EDIT) { ?>
	<input type=button value="<?php echo(ABMLBL_EDIT); ?>" class="botonout"
		onclick="go(<?php echo(ABM_EDIT); ?>);">
	<input type=button value="<?php echo(ABMLBL_DEL); ?>" class="botonout"
		onclick="go(<?php echo(ABM_DEL); ?>);">
	<input type=button value="Cambiar pass" class="botonout"
		onclick="changepw();">
	<input type=button value="Nuevo" class="botonout" onClick="go(<?php echo(ABM_NEW); ?>);">
<?php } // end if PERM_EDIT ?>
	</td></tr>
</table>
</p>

<?php require_once("../includes/inc_bottom.php"); ?>
</body>

</html>
<?php
} // fin autorizacion
?>