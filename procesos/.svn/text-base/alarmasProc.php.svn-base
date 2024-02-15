<?php
require_once("../includes/lib.php");
require_once("inc_controlproc.php");

// Evitar reentrada
if(!procVerificar(PROC_ALARMAS))
{
	die();
}

//marco como que empece a correr
procInicio(PROC_ALARMAS,dbtime());
procLog(PROC_ALARMAS,"Inicio de Ejecucion",dbtime());

set_time_limit(0);

//////////////procesos que estan corriendo durante un tiempo superior a procPeriodo
$sqproc = "Select PROC_ID,PROC_NOMBRE,
	".$conn->SQLDate(FMT_DATE_DB,"PROC_INI")." as PROC_INI,PROC_PERIODO
	From PROCESO
	Where PROC_FIN Is Null
		And PROC_CRON = " . sqlboolean(true) . "
		And " . $conn->DiffDate("PROC_INI", $conn->sysTimeStamp) . " >= PROC_PERIODO / 1440.0
	Order by PROC_NOMBRE";
$rsproc = $conn->Execute($sqproc);

unset($proc_inf);
while(!$rsproc->EOF)
{
	$proc_inf .= $rsproc->fields["PROC_NOMBRE"]."\t\t".$rsproc->fields["PROC_INI"]."\t\t".$rsproc->fields["PROC_PERIODO"]."\n";
	$rsproc->MoveNext();
}

if($proc_inf){
	//aca mando mail y pongo la alarma
	$mensaje = "Informe: \nlos siguientes procesos no han finalizado y estan demorando un tiempo superior al maximo tiempo de ejecucion estimado.\n\n";
	$mensaje .= "Proceso\t\tFecha Inicio\t\tMax tiempo Ejec Estimado (min)\n";
	$mensaje .= "-------\t\t----- ------\t\t--- ------ ---- -------- -----\n";
	$mensaje .= $proc_inf;
	alarmaAdd(PROC_ALARMAS,$mensaje,PALAR_PROC);
}
//////////////

procFin(PROC_ALARMAS,dbtime());
procLog(PROC_ALARMAS,"Ejecucion Finalizada",dbtime());

?>
