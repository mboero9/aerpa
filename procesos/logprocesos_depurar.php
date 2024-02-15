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
procInicio(PROC_DEPURAR_LOG, dbtime());

$a_log = array();
$a_log[] = array("Inicio", dbtime());
$conn->StartTrans();
$ok = true;

// Determinar cantidad de entradas de log a borrar
//------------------------------------------------------------------------------------
$a_log[] = array("Contando entradas de log viejas", dbtime());
//Obtengo la cantidad de dias
$sqlparam = "Select par_valor from parametro where par_nombre='dias_log'";
$rsparam = $conn->execute($sqlparam);
$cantidad_dias = $rsparam->fields['par_valor'];
//---------------------------------------------------------------------------------------
$sql =
	"Select Count(*) As CANT " .
	"From LOGPROCESO " .
	"Where " . $conn->DiffDate("LOGP_FECHA",$conn->sysTimeStamp) . " > " . sqlint($cantidad_dias);
$rs = $conn->Execute($sql);
$cant = $rs->fields["CANT"];

// Borrar
//----------------------------------
$a_log[] = array("Borrando $cant entradas de log", dbtime());
$sql =
	"Delete " .
	"From LOGPROCESO " .
	"Where " . $conn->DiffDate("LOGP_FECHA",$conn->sysTimeStamp) . " > " . sqlint($cantidad_dias);

try {
	$rs = $conn->Execute($sql);
} catch (exception $e) {
	dbhandleerror($e);
	$a_log[] = array("Error al borrar las entradas de log viejas", dbtime());
	$ok = false;
} // end try/catch

// Fin transaccion
//----------------------------------
// ok=>commit, error=>rollback
if ($ok) {
	$a_log[] = array("Fin sin errores, borradas $cant entradas de log viejas", dbtime());
} else {
	$a_log[] = array("Fin de tarea CON ERRORES, ejecutando rollback", dbtime());
} // end if ok
$conn->CompleteTrans();

// Volcar el log en base de datos
//----------------------------------
foreach($a_log as $logitem) {
	procLog(PROC_DEPURAR_LOG, $logitem[0], $logitem[1]);
} // end foeach a_log
procFin(PROC_DEPURAR_LOG, dbtime());
?>
