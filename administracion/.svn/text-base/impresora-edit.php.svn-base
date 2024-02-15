<?php
require_once("../includes/lib.php");

if (check_auth($usrid, $perid) && check_permission($permiso, "impresoras.php")) {
// permiso ok
	// validacion AJAX - back end
	if (isset($_GET["ajax"])) {
		try {
		if (isset($_GET["nombre"])) {
			$sql =
				"Select IMP_NOMBRE From IMPRESORA
				Where Lower(IMP_NOMBRE) != " . sqlstring(strtolower($_GET["id"])) . "
				And Lower(IMP_NOMBRE) = " . sqlstring(strtolower($_GET["nombre"]));
			$rs = $conn->Execute($sql);
			$out = "nombre\n" . (!$rs->EOF ? "1" : "0");
		}

		header("content-type: text/plain; charset=iso-8859-1");
		echo($out);
		return;
		} catch (exception $e) {
			dbhandleerror();
		}
	} // end if AJAX


$accion = $_POST["accion"];
$view = ($accion == ABM_VIEW);
$new = ($accion == ABM_NEW);
$edit = (($accion == ABM_NEW) || ($accion == ABM_EDIT));
$del = ($accion == ABM_DEL);
$ro = (!$edit ? " readonly" : "");
$dis = (!$edit ? " disabled" : "");

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>

<head>
<?php require_once("../includes/inc_header.php"); ?>
<script type="text/javascript">
<?php if ($edit) { ?>
<?php require_once("../includes/ajaxobjt.js"); ?>

var updating = false;

// Callback para el GET
// validacion AJAX - front end
function getHttp() {
	if (http.readyState == 4) {
		// obtener respuesta como texto
		r = new String(http.responseText);
		if (r.indexOf("invalid") == -1) {
			i = r.indexOf("\n");
			entidad = r.substr(0,i);
			r = r.substr(i+1);
			if (entidad == "nombre") {
				document.frm.nombre_existe.value = r;
			}
		}
		document.getElementById("loading").style.display = "none";
		updating = false;
	}
} // end getHttp
<?php } // end if edit ?>

function validar() {
<?php if ($edit) { ?>
	var s = "";

	// evitar nuevo submit si haciendo submit
	if (document.frm.enviar.disabled) {
		return false;
	} // end if !reentry

	buf = new String(document.frm.nombre.value);
	if (buf.length == 0) {
		s += "- Debe ingresar el nombre\n";
	} else {
		// Validacion AJAX sincronica para detectar duplicado
		document.frm.nombre_existe.value = "";
		updating = true;
		document.getElementById("loading").style.display = "";
		http.open("GET", "?ajax&id=" + escape(document.frm.id.value) + "&nombre=" + escape(document.frm.nombre.value), false);
		http.send(null);
		getHttp();
		s += (document.frm.nombre_existe.value == "1" ?
			"- El nombre de impresora ya existe\n" : "");
	} // end valid nombre

	buf = new String(document.frm.host.value);
	if (buf.length == 0) {
		s += "- Debe ingresar el host\n";
	} // end valid calle

	buf = new String(document.frm.port.value);
	if (buf.length == 0) {
		s += "- Debe ingresar el puerto\n";
	} else if (isNaN(parseInt(buf))) {
		s += "- El puerto es incorrecto\n";
	} // end valid altura

	buf = new String(document.frm.path.value);
	if (buf.length == 0) {
		s += "- Debe ingresar la ruta\n";
	} // end valid localidad

	// Si datos ok, agregar fila
	if (s != "") {
		alert("Se han encontrado errores:\n" + s);
		return false;
	} else {
		document.frm.enviar.disabled = true;
		return true;
	} // end if errores
<?php } // end if edit ?>

<?php if ($del) { ?>
	return confirm("Esta seguro que desea eliminar esta impresora?");
<?php } // end if delete ?>
} // end validar

</script>
</head>

<body>
<?php require_once('../includes/inc_topleft.php'); ?>

<p class=titulo1>Impresora</p>
<?php
if ($accion != ABM_NEW) {
	try {
	$sql = "Select IMP_NOMBRE,IMP_HOST,IMP_PORT,IMP_PATH,IMP_PAPEL " .
		"From IMPRESORA " .
		"Where IMP_NOMBRE = " . sqlstring($_POST["id"]);
	$rs = $conn->Execute($sql);
	if (!$rs->EOF) {
		$v_id = $rs->fields["IMP_NOMBRE"];
		$v_host = $rs->fields["IMP_HOST"];
		$v_port = $rs->fields["IMP_PORT"];
		$v_path = $rs->fields["IMP_PATH"];
		$v_papel= $rs->fields["IMP_PAPEL"];		
	} // end if eof
	} catch (exception $e) {
		dbhandleerror($e);
	}
} // end if !ABM_NEW
?>
<form action="impresora-edit-do.php" method=post name=frm onSubmit="return validar();">
<input type=hidden name=accion value="<?php echo($accion); ?>">
<input type=hidden name=reentrante value="1">
<input type=hidden name=id value="<?php echo($v_id); ?>">

<p align=center>
<table class=tablanormal>

<tr><td class=celdatexto>Nombre</td>
	<td class=celdatexto><input type=text name=nombre maxlength=50
			value="<?php echo(htmlentities($v_id)); ?>" <?php echo($ro); ?>>
		<input type=hidden name=nombre_existe></td></tr>

<tr><td class=celdatexto>Host</td>
	<td class=celdatexto><input type=text name=host maxlength=100
			value="<?php echo(htmlentities($v_host)); ?>" <?php echo($ro); ?>></td></tr>

<tr><td class=celdatexto>Puerto</td>
	<td class=celdatexto><input type=text name=port maxlength=5
			value="<?php echo($v_port); ?>" <?php echo($ro); ?>></td></tr>

<tr><td class=celdatexto>Ruta</td>
	<td class=celdatexto><input type=text name=path maxlength=100
			value="<?php echo(htmlentities($v_path)); ?>" <?php echo($ro); ?>></td></tr>
<tr><td class="celdatexto">Papel</td>
	<td>
		<select name="papel">
		<option value="RPT_A4" <?php echo("RPT_A4"==$v_papel ? 'selected' : ''); ?> >A4</option>
		<option value="RPT_LETTER" <?php echo("RPT_LETTER"==$v_papel ? 'selected' : ''); ?>>Carta</option>
		<option value="RPT_LEGAL" <?php echo("RPT_LEGAL"==$v_papel ? 'selected' : ''); ?>>Oficio</option>				
		</select>
	</td>
</tr>
<tr><td colspan=2 class=celdatexto align=center>
<?php if (!$view) { ?>
		<input type=submit name=enviar class=botonout value="<?php echo(abm_label($accion)); ?>">
<?php } // end if view ?>
		<input type=button class=botonout value="Volver" onClick="window.location = 'impresoras.php';">
		<img src="../imagenes/loading.gif" id=loading style="display: none;">
	</td></tr>

</table>
</p>
</form>
<?php require_once("../includes/inc_bottom.php"); ?>

</body>

</html>
<?php
} // fin autorizacion
?>