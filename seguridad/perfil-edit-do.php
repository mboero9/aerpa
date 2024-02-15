<?php
require_once("../includes/lib.php");

if (check_auth($usrid, $perid) && check_permission($permiso, "perfiles.php")) {
// permiso ok

$accion = $_POST["accion"];
$perfil_existente = false;
$v_id = $_POST["id"];
$v_descripcion = $_POST["descripcion"];
$v_default = ($_POST["default"] != "");
$v_editable = ($_POST["editable"]);

$conn->StartTrans();

try {
if ($accion == ABM_NEW) {
	$sql = "SELECT p.per_descripcion FROM perfil p where per_descripcion='".$v_descripcion."'";
	$rs = $conn->execute($sql);
	if(!$rs->EOF)
	{
		?>
		<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
		<html>
		<head>
		<?php require_once("../includes/inc_header.php");?>
		</head>
		<body>
		<?php require_once('../includes/inc_topleft.php'); ?>
		<br>
		<br>
		<table align="center" width="40%" class="tablaconbordes">
		<tr>
		<td align="center" class="textoerror">El perfil: "<?php echo($v_descripcion); ?>" ya existe.</td>
		</tr>
		<tr>
		<td align="center"><input type=button class="botonout" name=botvolver     value="<?=VOLVER; ?>"  onMouseOver="this.className = 'botonover';" onMouseOut="this.className = 'botonout';" onClick="goMenu('perfil-edit.php','accion',<?php echo(ABM_NEW); ?>);"></td>
		</tr>
		</table>
		<?php require_once("../includes/inc_bottom.php"); ?>
		</body>
		</html>
		<?php
		$$perfil_existente =true;
	}//Fin if !EOF
		else{
		
		$sql = "Insert Into PERFIL (PER_ID,PER_DESCRIPCION,PER_DEFAULT,PER_EDITABLE) " .
			"Values (" . sqlint($v_id = numerador("PERFIL")) . "," .
			sqlstring($v_descripcion) . "," .
			sqlboolean($v_default) . "," .
			sqlboolean($v_editable) . ")";
		$conn->Execute($sql);
		
		}//FIn else
	
	} elseif ($accion == ABM_EDIT) {
		$sql = "Update PERFIL " .
			"Set PER_DESCRIPCION = " . sqlstring($v_descripcion) . "," .
			"	PER_DEFAULT = " . sqlstring($v_default) . "," .
			"	PER_EDITABLE = " . sqlstring($v_editable) . " " .
			"Where PER_ID = " . sqlint($v_id);
		$conn->Execute($sql);

} // end if ABM_NEW/EDIT

$sql= "Delete From SEGADMINSEGURIDADABM Where PER_ID = " . sqlint($v_id);
$conn->Execute($sql);

if (is_array($_POST["abm"])) {
	foreach($_POST["abm"] as $abmid => $value) {
		if ($value == "1") {
			$value = " ";
		} // end if value 1

		if ($value != "") {
			$sql = "Insert Into SEGADMINSEGURIDADABM (ADM_ABM_ID, PER_ID, PERM_TIPO) Values (" .
				sqlint($abmid) . "," .
				sqlint($v_id) . "," .
				sqlstring($value) . ")";
			$conn->Execute($sql);
		} // end if value ""
	} // end foreach abm
} // end if array

// volver
if(!$perfil_existente)
{
	header("location: perfiles.php");
}
} catch (exception $e) {
	dbhandleerror($e);
}

// commit
$conn->CompleteTrans();

} // fin autorizacion
?>