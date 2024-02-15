<?php
require_once("../includes/lib.php");

if (check_auth($usrid, $perid) && check_permission($permiso, "usuarios.php")) {
// permiso ok

$accion = $_POST["accion"];

$v_id = $_POST["id"];
$v_password = $_POST["nueva"];

$conn->StartTrans();
try {
$sql = "Update USUARIO " .
	"Set USR_CAMBIAR_PASS = " . sqlboolean(true) . "," .
	"	USR_PASSWORD = " . sqlstring(md5($v_password)) . " " .
	"Where USR_ID = " . sqlint($v_id);
$conn->Execute($sql);

// volver
header("location: usuarios.php");
} catch (exception $e) {
	dbhandleerror($e);
}

$conn->CompleteTrans();

} // fin autorizacion
?>