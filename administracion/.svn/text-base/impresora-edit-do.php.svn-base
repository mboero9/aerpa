<?php
require_once("../includes/lib.php");

if (check_auth($usrid, $perid) && check_permission($permiso, "impresoras.php")) {
// permiso ok

$conn->StartTrans();
try {
$accion = $_POST["accion"];

$v_id = $_POST["id"];
$v_nombre = $_POST["nombre"];
$v_host = $_POST["host"];
$v_port = $_POST["port"];
$v_path = $_POST["path"];
$v_papel = $_POST["papel"];

if ($accion == ABM_NEW) {
	$sql = "Insert Into IMPRESORA (" .
		"	IMP_NOMBRE,IMP_HOST,IMP_PORT,IMP_PATH,IMP_DEFAULT,IMP_PAPEL) " .
		"Values (" .
		sqlstring($v_nombre) . "," .
		sqlstring($v_host) . "," .
		sqlint($v_port) . "," .
		sqlstring($v_path) . "," .
		sqlboolean(false) . "," .
		sqlstring($v_papel). ")";
	$conn->Execute($sql);

} elseif ($accion == ABM_EDIT) {
	$sql = "Update IMPRESORA " .
		"Set IMP_NOMBRE = " . sqlstring($v_nombre) . "," .
		"	IMP_HOST = " . sqlstring($v_host) . "," .
		"	IMP_PORT = " . sqlstring($v_port) . "," .
		"	IMP_PATH = " . sqlstring($v_path) . "," .
		"	IMP_PAPEL = " . sqlstring($v_papel) . " " .
		"Where IMP_NOMBRE = " . sqlstring($v_id);
	$conn->Execute($sql);

} elseif ($accion == ABM_SETDEFAULT) {
	$sql = "Update IMPRESORA " .
		"Set IMP_DEFAULT = " . sqlboolean(false) . " " .
		"Where IMP_DEFAULT = " . sqlboolean(true);
	$conn->Execute($sql);
	$sql = "Update IMPRESORA " .
		"Set IMP_DEFAULT = " . sqlboolean(true) . " " .
		"Where IMP_NOMBRE = " . sqlstring($v_id);
	$conn->Execute($sql);

} elseif ($accion == ABM_DEL) {
	$sql = "Delete From IMPRESORA " .
		"Where IMP_NOMBRE = " . sqlstring($v_id);
	$conn->Execute($sql);


} // end if ABM_NEW/EDIT/DEL
	// volver
	header("location: impresoras.php");
} catch (exception $e) {
	dbhandleerror($e);
}
$conn->CompleteTrans();

} // fin autorizacion
?>