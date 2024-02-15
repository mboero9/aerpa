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
procInicio(PROC_DEPURAR_HIST, dbtime());

$a_alarma = array();
$a_log = array();
$a_log[] = array("Inicio", dbtime());
$conn->StartTrans();
$ok = true;

// Buscando Tramites de acuerdo a la cantidad de dias en parametro
//----------------------------------
//Obtengo la cantidad de dias
$sqlparam = "Select par_valor from parametro where par_nombre='dias_depuracion_hist'";
$rsparam = $conn->execute($sqlparam);
$cantidad_dias = $rsparam->fields['par_valor'];

$a_log[] = array("Buscando tramites en tramite_his con mas de ".$cantidad_dias." dias", dbtime());
//-------------------------------------------------------------------------------
$sql ="select count(*) as cantidad_tramites from tramite_his where ".$conn->DiffDate("HIS_FECHA_RETIRO",$conn->sysDate) . " > " . sqlint($cantidad_dias);
//echo("\n".$sql."");
$rs = $conn->Execute($sql);

//Borrando
//----------------------------------
$a_log[] = array($rs->fields['cantidad_tramites']." tramites encontrados.", dbtime());
	if($rs->fields['cantidad_tramites']!=0)
			{
				try{

					$sql2="delete from tramite_his where ".$conn->DiffDate("HIS_FECHA_RETIRO",$conn->sysDate) . " > " . sqlint($cantidad_dias);;

					//echo(sqlint($rs->fields["tra_codigo"])."<br>");
					$rs2 = $conn->execute($sql2);
				}
				catch (exception $e)
				{
					dbhandleerror($e);
					$msg = "Error al borrar datos en la tabla TRAMITE_HIS:\n" . $conn->error;
					$a_alarma[] = $msg;
					$a_log[] = array($msg, dbtime());
					$ok = false;
				} // end try/catch
//--------------------------------------------------------------------------------------------------------------------------------
//Checkeando
			$rs = $conn->Execute($sql);		
				
// Fin transaccion
//----------------------------------
// ok=>commit, error=>rollback
		if ($ok) {
			$a_log[] = array("Tramites con mas de ".$cantidad_dias." dias en TRAMITE_HIS = ".$rs->fields['cantidad_tramites']." .Fin.", dbtime());
		} else {
			$a_log[] = array("Fin de tarea CON ERRORES, ejecutando rollback", dbtime());
		} // end if ok
	}//fin if !=0
	else
	{
		$a_log[] = array("Tramites con mas de ".$cantidad_dias." dias en TRAMITE_HIS = ".$rs->fields['cantidad_tramites']." .Fin.", dbtime());
	}
$conn->CompleteTrans();
		

// Volcar log/alarmas en base de datos
//----------------------------------
foreach($a_alarma as $aitem) {
	alarmaAdd(PROC_DEPURAR_HIST, $aitem, PALAR_SISTEMA);

} // end foreach a_alarma
foreach($a_log as $logitem) {
	procLog(PROC_DEPURAR_HIST, $logitem[0], $logitem[1]);
} // end foeach a_log
procFin(PROC_DEPURAR_HIST, dbtime());
?>

