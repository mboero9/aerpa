<?php
require_once("../includes/lib.php");

if (check_auth($usrid, $perid) && check_permission($permiso, "usuarios.php")) {
// permiso ok

$accion = $_POST["accion"];
$view = ($accion == ABM_VIEW);
$new = ($accion == ABM_NEW);
$edit = (($accion == ABM_NEW) || ($accion == ABM_EDIT));
$ro = (!$edit ? " readonly" : "");
$dis = (!$edit ? " disabled" : "");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>

<head>
<?php
require_once("../includes/inc_header.php");

if ($edit) { ?>
<!-- funcion parseDate -->
<script type="text/javascript" language="JavaScript" src="../includes/fecha.js"></script>

<!-- calendario desplegable -->
<style type="text/css">@import url(../calendar/calendar-win2k-1.css);</style>
<script type="text/javascript" src="../calendar/calendar.js"></script>
<script type="text/javascript" src="../calendar/lang/calendar-es.js"></script>
<script type="text/javascript" src="../calendar/calendar-setup.js"></script>
<?php } // end if edit ?>

<script type="text/javascript">
function validar() {
	var s = "";

	// evitar nuevo submit si haciendo submit
	if (document.frm.enviar.disabled) {
		return false;
	} // end if !reentry

	buf = new String(document.frm.username.value);
	if (buf.length == 0) {
		s += "- No se ha ingresado el login\n";
	} else {
		rx = /^[A-Za-z]+[0-9_]*$/;
		s += (!rx.test(buf) ?
			"- El login contiene caracteres inválidos: sólo A-Z, 0-9 y _ (No se permiten login solo númericos)\n" : "")
	} // end valid username

<?php if ($new) { ?>
	s += (new String(document.frm.pwd.value).length < 8 ?
		"- Password demasiado corta, 8 caracteres mínimo\n" : "");

	rx = /^(.*[A-Za-z]{1,}.*[0-9]{1,}.*|.*[0-9]{1,}.*[A-Za-z]{1,}.*)$/;
	s += (!rx.test(document.frm.pwd.value) ?
		"- La password debe contener al menos una letra y un número\n" : "");

	s += (document.frm.pwd.value != document.frm.pwd2.value ?
		"- Las passwords difieren\n" : "");
<?php } // end if new ?>

<?php if ($edit) { ?>
	buf = new String(document.frm.documento.value);
	if (buf.length == 0) {
		s += "- No se ha ingresado el documento\n";
	} else {
		rx = /^[0-9]+$/;
		s += (!rx.test(buf) ? "El documento no es válido, debe ser numérico\n" : "");
	} // end valid documento

	buf = new String(document.frm.nombre.value);
	if (buf.length == 0) {
		s += "- No se ha ingresado el nombre\n";
	} else {
		rx = /^[A-Za-zñÑáÁéÉíÍóÓúÚüÜ']+[ ]*[A-Za-zñÑáÁéÉíÍóÓúÚüÜ']*$/;
		s += (!rx.test(buf) ? "- El nombre no es válido\n" : "");
	} // end valid nombre

	buf = new String(document.frm.apellido.value);
	if (buf.length == 0) {
		s += "- No se ha ingresado el apellido\n";
	} else {
		rx = /^[A-Za-zñÑáÁéÉíÍóÓúÚüÜ']+[ ]*[A-Za-zñÑáÁéÉíÍóÓúÚüÜ']*$/;
		s += (!rx.test(buf) ? "- El apellido no es válido\n" : "");
	} // end valid apellido

	buf = new String(document.frm.fechabaja.value);
	if (buf.length > 0) {
		dt = checkDate(buf);
		if (!dt) {
			s += "- La fecha de baja es inválida\n";
		} else
			{
			 var diferencia = compareDate(dt, new Array(<?php echo(date("Y,n,j", dbtime())); ?>));
			 
			 if (diferencia != 1 && diferencia !=3) {
					s += "- No puede fijarse la fecha de baja en el pasado";
				}
		} // end if dt
	} // end valid fecha de baja
<?php } // end if edit ?>

<?php if ($usrid == $_POST["id"]) { ?>
	s += (document.frm.bloqueado.checked ?
		"- No puede bloquearse a sí mismo/a\n" : "");

	s += (!document.frm.habilitado.checked ?
		"- No puede inhabilitarse a sí mismo/a\n" : "");
<?php } // end if self-edit ?>

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

<p class=titulo1>Usuario</p>
<?php
$v_id = 0;
$v_perfil = 0;
$v_username = "";
$v_nombre = "";
$v_apellido = "";
$v_fechabaja = null;
$v_cambiarpass = true;
$v_bloqueado = false;
$v_habilitado = true;

if (array_key_exists("username", $_POST)) {
	$v_id = $_POST["id"];
	$v_perfil = $_POST["perfil"];
	$v_username = $_POST["username"];
	$v_documento = $_POST["documento"];
	$v_nombre = $_POST["nombre"];
	$v_apellido = $_POST["apellido"];
	$v_fechabaja = $_POST["fechabaja"];
	$v_cambiarpass = ($_POST["cambiarpass"] <> "");
	$v_bloqueado = ($_POST["bloqueado"] <> "");
	$v_habilitado = ($_POST["habilitado"] <> "");

} elseif ($accion != ABM_NEW) {
	try {
	$sql = "Select USR_ID, PER_ID, USR_USERNAME, USR_DOCUMENTO, USR_NOMBRE, USR_APELLIDO, " .
		"	" . $conn->SQLDate(FMT_DATE_DB, "USR_FECHA_BAJA") . " as USR_FECHA_BAJA, " .
		"	USR_CAMBIAR_PASS, USR_BLOQUEADO, USR_HABILITADO " .
		"From USUARIO " .
		"Where USR_ID = " . sqlint($_POST["id"]);
	$rs = $conn->Execute($sql);
	if (!$rs->EOF) {
		$v_id = $rs->fields["USR_ID"];
		$v_perfil = $rs->fields["PER_ID"];
		$v_username = $rs->fields["USR_USERNAME"];
		$v_documento = $rs->fields["USR_DOCUMENTO"];
		$v_nombre = $rs->fields["USR_NOMBRE"];
		$v_apellido = $rs->fields["USR_APELLIDO"];
		$v_fechabaja = $rs->fields["USR_FECHA_BAJA"];
		$v_cambiarpass = $rs->fields["USR_CAMBIAR_PASS"];
		$v_bloqueado = $rs->fields["USR_BLOQUEADO"];
		$v_habilitado = $rs->fields["USR_HABILITADO"];
	} // end if eof
	} catch (exception $e) {
		dbhandleerror($e);
	}
} else {
	try {
	// ABM_NEW: buscar el perfil por default
	$sql = "Select PER_ID From PERFIL Where PER_DEFAULT = " . sqlboolean(true);
	$rs = $conn->Execute($sql);
	if (!$rs->EOF) {
		$v_perfil = $rs->fields["PER_ID"];
	} // end if !eof
	} catch (exception $e) {
		dbhandleerror($e);
	}
} // end if !ABM_NEW
?>
<form action="usuario-edit-do.php" method=post name=frm onSubmit="return validar();">
<input type=hidden name=accion value="<?php echo($accion); ?>">
<input type=hidden name=reentrante value="1">
<input type=hidden name=id value="<?php echo($v_id); ?>">

<p align=center>
<table class=tablanormal>

<tr><td class=celdatexto>Perfil</td>
	<td class=celdatexto><select name=perfil <?php echo($dis); ?>>
<?php fill_combo("Select PER_ID,PER_DESCRIPCION From PERFIL Order by PER_ID", $v_perfil); ?>
	</select></td></tr>

<tr><td class=celdatexto>Login</td>
	<td class=celdatexto><input type=text name=username maxlength=20
			value="<?php echo(htmlentities($v_username)); ?>" <?php echo($ro); ?>></td></tr>

<?php if ($new) { ?>
<tr><td class=celdatexto>Password</td>
	<td class=celdatexto><input type=password name=pwd maxlength=100></td></tr>

<tr><td class=celdatexto>Confirmaci&oacute;n</td>
	<td class=celdatexto><input type=password name=pwd2 maxlength=100></td></tr>
<?php } // end if new ?>

<tr><td class=celdatexto>Documento</td>
	<td class=celdatexto><input type=text name=documento maxlength=8
			value="<?php echo(htmlentities($v_documento)); ?>" <?php echo($ro); ?>></td></tr>

<tr><td class=celdatexto>Nombre</td>
	<td class=celdatexto><input type=text name=nombre maxlength=20
			value="<?php echo(htmlentities($v_nombre)); ?>" <?php echo($ro); ?>></td></tr>

<tr><td class=celdatexto>Apellido</td>
	<td class=celdatexto><input type=text name=apellido maxlength=20
			value="<?php echo(htmlentities($v_apellido)); ?>" <?php echo($ro); ?>></td></tr>

<tr><td class=celdatexto>Fecha de baja</td>
	<td class=celdatexto><input type=text name=fechabaja id=fechabaja maxlength=10
			value="<?php echo(htmlentities($v_fechabaja)); ?>" <?php echo($ro); ?>
<?php if ($edit) { ?>
			onBlur="parseDate(frm.fechabaja,'<?php echo(FMT_DATE_CAL); ?>',true);"
		><img src="../imagenes/calendario.png" id="fechabajasel"
			title="Calendario" style="cursor:pointer;"
<?php } // end if edit ?>
		></td></tr>

<tr><td class=celdatexto>Cambiar pass</td>
	<td class=celdatexto><input type=checkbox name=cambiarpass value=1
			<?php echo(is_checked($v_cambiarpass)); ?> <?php echo($dis); ?>></td></tr>

<tr><td class=celdatexto>Bloqueado</td>
	<td class=celdatexto><input type=checkbox name=bloqueado value=1
			<?php echo(is_checked($v_bloqueado)); ?> <?php echo($dis); ?>></td></tr>

<tr><td class=celdatexto>Habilitado</td>
	<td class=celdatexto><input type=checkbox name=habilitado value=1
			<?php echo(is_checked($v_habilitado)); ?> <?php echo($dis); ?>></td></tr>

<tr><td colspan=2 class=celdatexto align=center>
<?php if (!$view) { ?>
		<input type=submit name=enviar class="botonout" value="<?php echo(abm_label($accion)); ?>">
<?php } // end if view ?>
		<input type=button class="botonout" value="Volver" onClick="window.location = 'usuarios.php';">
	</td></tr>

</table>
</p>
</form>
<?php require_once("../includes/inc_bottom.php"); ?>
<?php
if ($edit) {
?>
<script type="text/javascript">
	Calendar.setup( { inputField: "fechabaja", ifFormat: "<?php echo(FMT_DATE_CAL); ?>", button: "fechabajasel" } );
</script>
<?php
} // end if edit
?>

</body>

</html>
<?php
} // fin autorizacion
?>