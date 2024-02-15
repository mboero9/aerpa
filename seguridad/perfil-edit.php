<?php
require_once("../includes/lib.php");

if (check_auth($usrid, $perid) && check_permission($permiso, "perfiles.php")) {
// permiso ok

$accion = $_POST["accion"];
$view = ($accion == ABM_VIEW);
$edit = (($accion == ABM_NEW) || ($accion == ABM_EDIT));
$ro = (!$edit ? " readonly" : "");
$dis = (!$edit ? " disabled" : "");
$dis2 = " disabled";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>

<head>
<?php require_once("../includes/inc_header.php"); ?>
<script type="text/javascript">
function validar() {
	var s = "";

	// evitar nuevo submit si haciendo submit
	if (document.frm.enviar.disabled) {
		return false;
	} // end if !reentry

	buf = new String(document.frm.descripcion.value);
	if (buf.length == 0) {
		s += "- No se ha ingresado la descripción\n";
	} else {
		rx = /^[A-Za-z0-9_ ]+$/;
		s += (!rx.test(buf) ?
			"- La descripción contiene caracteres inválidos: sólo A-Z, 0-9, espacios y _\n" : "")
	} // end valid username

	// Si datos ok, agregar fila
	if (s != "") {
		alert("Se han encontrado errores:\n" + s);
		return false;
	} else {
		document.frm.enviar.disabled = true;
		return true;
	} // end if errores
} // end validar

</script>
</head>

<body>
<?php require_once('../includes/inc_topleft.php'); ?>

<p class=titulo1>Perfil</p>
<?php

$v_id = 0;
$v_descripcion = "";
$v_default = false;
$v_editable = true;

if ($accion != ABM_NEW) {
	try {
	$sql = "Select PER_ID, PER_DESCRIPCION, PER_DEFAULT, PER_EDITABLE " .
		"From PERFIL " .
		"Where PER_ID = " . sqlint($_POST["id"]);
	$rs = $conn->Execute($sql);
	if (!$rs->EOF) {
		$v_id = $rs->fields["PER_ID"];
		$v_descripcion = $rs->fields["PER_DESCRIPCION"];
		$v_default = $rs->fields["PER_DEFAULT"];
		$v_editable = $rs->fields["PER_EDITABLE"];
	} // end if eof
	} catch (exception $e) {
		dbhandleerror($e);
	}
} // end if !ABM_NEW
?>
<form action="perfil-edit-do.php" method=post name=frm onSubmit="return validar();">
<input type=hidden name=accion value="<?php echo($accion); ?>">
<input type=hidden name=id value="<?php echo($v_id); ?>">

<p align=center>
<table class=tablanormal>

<tr><td class=celdatexto>Descripci&oacute;n</td>
	<td class=celdatexto><input type=text name=descripcion maxlength=30
			value="<?php echo(htmlentities($v_descripcion)); ?>" <?php echo($ro); ?>></td></tr>

<tr><td class=celdatexto>Default</td>
	<td class=celdatexto><input type=checkbox name=default value=1
			<?php echo(is_checked($v_default)); ?> <?php echo($dis); ?>></td></tr>

<tr><td class=celdatexto>Editable</td>
	<td class=celdatexto><input type=checkbox name=editable value=1
			<?php echo(is_checked($v_editable)); ?> <?php echo($dis); ?>></td></tr>

<tr><td class=celdatexto>Permisos</td>
	<td class=celdatexto>
	<table class=tablanormal id=tblpermisos>
	<col width=5?>
	<col width=70?>
	<col width=25?>
	<thead>
		<tr><td class=celdatitulo colspan=2>Item</td>
			<td class=celdatitulo>Permiso</td></tr>
	</thead>
	<tbody>
<?php
$tperm = array("" => "- Nada -", PERM_VIEW => PERMLBL_VIEW, PERM_EDIT => PERMLBL_EDIT);

try {
$sql =
	"Select s.ADM_SEC_ID,s.ADM_SEC_NOMBRE,a.ADM_ABM_ID,a.ADM_ABM_NOMBRE," .
	"	a.ADM_ABM_TIPO_PERM,a.ADM_ABM_DEFAULT,a.ADM_ABM_ASIGNABLE,pa.PERM_TIPO " .
	"From SEGADMINSECCION s " .
	"	Inner Join SEGADMINABM a " .
	"		On s.ADM_SEC_ID = a.ADM_SEC_ID " .
	"	Left Join SEGADMINSEGURIDADABM pa " .
	"		On a.ADM_ABM_ID = pa.ADM_ABM_ID And pa.PER_ID = " . sqlint($v_id) . " " .
	"Order by s.ADM_SEC_ORDEN,a.ADM_ABM_ORDEN";
$rs = $conn->Execute($sql);
$secid = null;
$fondo = 1;
while(!$rs->EOF) {
	$v_secid = $rs->fields["ADM_SEC_ID"];
	$v_secnombre = $rs->fields["ADM_SEC_NOMBRE"];
	$v_abmid = $rs->fields["ADM_ABM_ID"];
	$v_abmnombre = $rs->fields["ADM_ABM_NOMBRE"];
	$v_abmtperm = $rs->fields["ADM_ABM_TIPO_PERM"];
	$v_abmdefault = $rs->fields["ADM_ABM_DEFAULT"];
	$v_abmasignable = $rs->fields["ADM_ABM_ASIGNABLE"];
	$v_abmperm = $rs->fields["PERM_TIPO"];

	// corte de control por seccion
	if ($secid != $rs->fields["ADM_SEC_ID"]) {
?>
	<tr><td colspan=3 class=celdatexto><b><?php echo(htmlentities($v_secnombre)); ?></b></td></tr>
<?php
		$secid = $v_secid;
	} // end corte control seccion
?>
	<tr class=fondotabla<?php echo($fondo); ?>>
		<td class=celdatexto />
		<td class=celdatexto><?php echo(htmlentities($v_abmnombre)); ?></td>
		<td class=celdatexto>
<?php
	$v_checked = (($v_id == 0) || !$v_abmasignable ? $v_abmdefault : !is_null($v_abmperm));
	if (!$v_abmasignable && $v_checked) {
?>
	<input type=hidden name="abm[<?php echo($v_abmid); ?>]" value="1">
<?php
	} // end if !asignable

	if (strtolower($v_abmtperm) == strtolower(TPERM_EDIT)) {
		// tipo permiso = editable/vista
		$v_abmperm = (is_null($v_abmperm) ? "" : $v_abmperm);
?>
			<select name="abm[<?php echo($v_abmid); ?>]" <?php echo(!$v_abmasignable ? $dis2 : $dis); ?>>
<?php fill_combo_arr($tperm, $v_abmperm); ?>
			</select>
<?php
	} else {
		// tipo permiso = booleano
?>
			<input type=checkbox name="abm[<?php echo($v_abmid); ?>]" value="1"
				<?php echo(is_checked($v_checked)); ?>
				<?php echo(!$v_abmasignable ? $dis2 : $dis); ?>>
<?php
	} // end if tipo permiso
?>
		</td></tr>
<?php
	$fondo = ($fondo == 1 ? 2 : 1);
	$rs->MoveNext();
} // end while !eof
} catch (exception $e) {
	dbhandleerror($e);
}
?>
	</tbody>
	</table>
	</td></tr>

<tr><td colspan=2 class=celdatexto>
<?php if (!$view) { ?>
		<input type=submit name=enviar class="botonout" value="<?php echo(abm_label($accion)); ?>">
<?php } // end if view ?>
		<input type=button class="botonout" value="Volver" onClick="window.location = 'perfiles.php';">
	</td></tr>

</table>
</p>
</form>

<?php include("../includes/inc_bottom.php"); ?>
</body>

</html>
<?php
} // fin autorizacion
?>