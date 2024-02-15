<?php
// Timeout infinito
//----------------------------------
set_time_limit(0);

// Includes
//----------------------------------
require_once("../includes/lib.php");
require_once("inc_controlproc.php");

// Comienzo transaccion
//----------------------------------
procInicio(PROC_DEPURAR_SESIONES, dbtime());

$a_alarma = array();
$a_log = array();
$a_log[] = array("Inicio", dbtime());
$conn->StartTrans();
$ok = true;

// Determinar cantidad de sesiones a borrar
//----------------------------------
$a_log[] = array("Contando sesiones viejas", dbtime());
$sql =
	"Select Count(*) As CANT " .
	"From SEGSESION " .
	"Where " . $conn->DiffDate("SES_FECHA",$conn->sysTimeStamp) . " > " . sqlint(LOG_HISTORIA);
$rs = $conn->Execute($sql);
$cant = $rs->fields["CANT"];

// Borrar
//----------------------------------
$a_log[] = array("Borrando $cant sesiones", dbtime());
$sql =
	"Delete " .
	"From SEGSESION " .
	"Where " . $conn->DiffDate("SES_FECHA",$conn->sysTimeStamp) . " > " . sqlint(LOG_HISTORIA);

try {
	$rs = $conn->Execute($sql);
} catch (exception $e) {
	dbhandleerror($e);
	$msg = "Error al borrar las sesiones viejas:\n" . $conn->error;
	$a_alarma[] = $msg;
	$a_log[] = array($msg, dbtime());
	$ok = false;
} // end try/catch

// Fin transaccion
//----------------------------------
// ok=>commit, error=>rollback
if ($ok) {
	$a_log[] = array("Fin sin errores, borradas $cant sesiones viejas", dbtime());
} else {
	$a_log[] = array("Fin de tarea CON ERRORES, ejecutando rollback", dbtime());
} // end if ok
$conn->CompleteTrans();

// Volcar log/alarmas en base de datos
//----------------------------------
foreach($a_alarma as $aitem) {
	alarmaAdd(PROC_DEPURAR_SESIONES, $aitem, PALAR_SISTEMA);
} // end foreach a_alarma
foreach($a_log as $logitem) {
	procLog(PROC_DEPURAR_SESIONES, $logitem[0], $logitem[1]);
} // end foeach a_log
procFin(PROC_DEPURAR_SESIONES, dbtime());
?>
