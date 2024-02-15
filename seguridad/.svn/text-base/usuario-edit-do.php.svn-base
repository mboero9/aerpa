<?php
require_once("../includes/lib.php");

if (check_auth($usrid, $perid) && check_permission($permiso, "usuarios.php")) {
// permiso ok

$conn->StartTrans();
try {
$usuario_existente=false;
$accion = $_POST["accion"];

$v_id = $_POST["id"];
$v_perfil = $_POST["perfil"];
$v_username = $_POST["username"];
$v_password = $_POST["pwd"];
$v_documento = $_POST["documento"];
$v_nombre = $_POST["nombre"];
$v_apellido = $_POST["apellido"];
$tmp = $_POST["fechabaja"];
if ($tmp != "") {
	$tmp = new Date($tmp);
	$v_fechabaja = $tmp->format(FMT_DATE_ISO);
} // end if fechabaja
$v_cambiarpass = ($_POST["cambiarpass"] != "");
$v_bloqueado = ($_POST["bloqueado"] != "");
$v_habilitado = ($_POST["habilitado"] != "");

if ($accion == ABM_NEW) {
	//checkeo que no exista el mismo login para el perfil
	$sql = "SELECT u.usr_username, Case When USR_FECHA_BAJA <= " . sqldate(dbtime()) . " " .
		" Then 1 Else 0 End as USR_VENCIDO FROM usuario u where per_id=".sqlint($v_perfil)." and usr_username=".sqlstring($v_username);
	//echo($sql."<br>");	
	$rs = $conn->execute($sql);
	//echo((!$rs->EOF) and (!$rs->fields['USR_VENCIDO']));
	if((!$rs->EOF) and (!$rs->fields['USR_VENCIDO']))
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
		<td align="center" class="textoerror">Ya existe el login: "<?php echo($v_username); ?>" para el perfil seleccionado.</td>
		</tr>
		<tr>
		<td align="center"><input type=button class="botonout" name=botvolver     value="<?=VOLVER; ?>"  onMouseOver="this.className = 'botonover';" onMouseOut="this.className = 'botonout';" onClick="goMenu('usuario-edit.php','accion',<?php echo(ABM_NEW); ?>);"></td>
		</tr>
		</table>
		<?php require_once("../includes/inc_bottom.php"); ?>
		</body>
		</html>
	<?php
		$usuario_existente=true;
	}//fin if !EOF
	else{
	
	
		$sql = "Insert Into USUARIO (USR_ID,PER_ID,USR_USERNAME,USR_PASSWORD," .
			"	USR_DOCUMENTO,USR_NOMBRE,USR_APELLIDO," .
			"	USR_FECHA_BAJA,USR_CAMBIAR_PASS,USR_BLOQUEADO,USR_HABILITADO," .
			"	USR_USUARIO_ALTA,USR_TIPO_ALTA) " .
			"Values (" . sqlint(numerador("USUARIO")) . "," .
			sqlint($v_perfil) . "," .
			sqlstring($v_username) . "," .
			sqlstring(md5($v_password)) . "," .
			sqlint($v_documento) . "," .
			sqlstring($v_nombre) . "," .
			sqlstring($v_apellido) . "," .
			sqldate($v_fechabaja) . "," .
			sqlboolean($v_cambiarpass) . "," .
			sqlboolean($v_bloqueado) . "," .
			sqlboolean($v_habilitado) . "," .
			sqlint($usrid) . "," .
			sqlstring(ALTAUSR_MANUAL) . ")";
		$conn->Execute($sql);
		}//Cierro else

} elseif ($accion == ABM_EDIT) {
	$sql = "Update USUARIO " .
		"Set PER_ID = " . sqlint($v_perfil) . "," .
		"	USR_USERNAME = " . sqlstring($v_username) . "," .
		"	USR_DOCUMENTO = " . sqlint($v_documento) . "," .
		"	USR_NOMBRE = " . sqlstring($v_nombre) . "," .
		"	USR_APELLIDO = " . sqlstring($v_apellido) . "," .
		"	USR_FECHA_BAJA = " . sqldate($v_fechabaja) . "," .
		"	USR_CAMBIAR_PASS = " . sqlboolean($v_cambiarpass) . "," .
		"	USR_BLOQUEADO = " . sqlboolean($v_bloqueado) . "," .
		"	USR_HABILITADO = " . sqlboolean($v_habilitado) . " " .
		"Where USR_ID = " . sqlint($v_id);
	$conn->Execute($sql);

} else {
	// Solo baja logica
	$sql = "Update USUARIO " .
		"Set USR_BAJA = " . sqlboolean(true) . "," .
		"	USR_FECHA_BAJA = " . sqldate(dbtime()) . " " .
		"Where USR_ID = " . sqlint($v_id);
	$conn->Execute($sql);

} // end if ABM_NEW/EDIT/DEL

} catch (exception $e) {
	dbhandleerror($e);
}
$conn->CompleteTrans();

if (!dberror()) {
// volver
	if(!$usuario_existente)
	{
		header("location: usuarios.php");
	}
} // end if dberror

} // fin autorizacion
?>