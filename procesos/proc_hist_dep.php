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
procInicio(PROC_HISTORICO_DEP, dbtime());

$a_alarma = array();
$a_log = array();
$a_log[] = array("Inicio", dbtime());
$conn->StartTrans();
$ok = true;

// Buscando Tramites de acuerdo a la cantidad de dias en parametro
//----------------------------------
$a_log[] = array("Buscando tramites en TRAMITE", dbtime());

//Obtengo la cantidad de dias
$sqlparam = "Select par_valor from parametro where par_nombre='dias_depuracion'";
$rsparam = $conn->execute($sqlparam);
$cantidad_dias = $rsparam->fields['par_valor'];
//-------------------------------------------------------------------------------
$sql ="Select tr.tra_codigo,"
.$conn->SQLDate(FMT_DATE_DB,"tr.tra_fecha_retiro")." AS tra_fecha_retiro,
				tr.reg_codigo_ori,
				tr.reg_codigo_des,
				tr.tra_dominio,
				tr.tra_nro_voucher, ".
				$conn->SQLDate(FMT_DATE_DB,"tr.tra_fecha_carga")." AS tra_fecha_carga,".
				$conn->SQLDate(FMT_DATE_DB,"tr.tra_fecha_entrega")." AS tra_fecha_entrega,".
				"tr.mot_codigo,".
				"tr.usr_id_carga,".
				"tr.rem_id_ori,".
				"tr.rem_id_des,".
				"tr.usr_id_ent_dev,".
				"tr.usr_id_act,".
				$conn->SQLDate(FMT_DATE_DB,"tr.tra_fecha_act")." AS tra_fecha_act,
				 tr.tra_nro_imp".
					" from tramite tr where ".
						$conn->DiffDate("tr.TRA_FECHA_RETIRO",$conn->sysDate) . " > " . sqlint($cantidad_dias);
//echo("\n".$sql."");
$rs = $conn->Execute($sql);

// Insertando
//----------------------------------
$a_log[] = array("Insertando Tramites en TRAMITE_HIS", dbtime());
	While (!$rs->EOF)
			{
				try{

					$tmp1 = new Date($rs->fields["tra_fecha_retiro"]);
					$tmp2 = new Date($rs->fields["tra_fecha_carga"]);
					$tmp3 = new Date($rs->fields["tra_fecha_entrega"]);
					$tmp6 = new Date($rs->fields["tra_fecha_act"]);

					//Query para insertara los datos en tramite_his
					$sql2="INSERT into TRAMITE_HIS (his_codigo,
					his_fecha_retiro,
					reg_codigo_ori,
					reg_codigo_des,
					his_dominio,
					his_nro_voucher,
					his_fecha_carga,
					his_fecha_entrega,
					mot_codigo,
					usr_id_carga,
					rem_id_ori,
					rem_id_des,
					usr_id_ent_dev,
					usr_id_act,
					his_fecha_act,
					his_nro_imp)".
										" VALUES(".sqlint($rs->fields["tra_codigo"]).",".
											sqldate($tmp1->format(FMT_DATE_ISO)).",".
											sqlint($rs->fields["reg_codigo_ori"]).",".
											sqlint($rs->fields["reg_codigo_des"]).",".
											sqlstring($rs->fields["tra_dominio"]).",".
											sqlstring($rs->fields["tra_nro_voucher"]).",".
											sqldate($tmp2->format(FMT_DATE_ISO)).",".
											sqldate($tmp3->format(FMT_DATE_ISO)).",".
											sqlint($rs->fields["mot_codigo"]).",".
											sqlint($rs->fields["usr_id_carga"]).",".
											sqlint($rs->fields["rem_id_ori"]).",".
											sqlint($rs->fields["rem_id_des"]).",".
											sqlint($rs->fields["usr_id_ent_dev"]).",".
											sqlint($rs->fields["usr_id_act"]).",".
											sqldate($tmp6->format(FMT_DATE_ISO)).",".
											sqlint($rs->fields['tra_nro_imp']).")";

					//echo(sqlint($rs->fields["tra_codigo"])."<br>");
					$rs2 = $conn->execute($sql2);
				}
				catch (exception $e)
				{
					dbhandleerror($e);
					$msg = "Error al INSERTAR datos en la tabla TRAMITE_HIS:\n" . $conn->error;
					$a_alarma[] = $msg;
					$a_log[] = array($msg, dbtime());
					$ok = false;
				} // end try/catch
				try{
					//Query para borrar datos en trabla tramites
					$sql3="DELETE tramite where tra_codigo=".sqlint($rs->fields["tra_codigo"]);
					$rs3= $conn->execute($sql3);
				}//Cierro try 3

				catch (exception $e) {

					dbhandleerror($e);
					$msg = "Error al BORRAR datos en la tabla TRAMITE_HIS:\n" . $conn->error;
					$a_alarma[] = $msg;
					$a_log[] = array($msg, dbtime());
					$ok = false;
				}


				$contador++;
				$rs->movenext();
			}//Cierro While EOF


// Fin transaccion
//----------------------------------
// ok=>commit, error=>rollback
if ($ok) {
	$a_log[] = array("Fin sin errores, $contador tramites transferidos", dbtime());
} else {
	$a_log[] = array("Fin de tarea CON ERRORES, ejecutando rollback", dbtime());
} // end if ok
$conn->CompleteTrans();

// Volcar log/alarmas en base de datos
//----------------------------------
foreach($a_alarma as $aitem) {
	alarmaAdd(PROC_HISTORICO_DEP, $aitem, PALAR_SISTEMA);

} // end foreach a_alarma
foreach($a_log as $logitem) {
	procLog(PROC_HISTORICO_DEP, $logitem[0], $logitem[1]);
} // end foeach a_log
procFin(PROC_HISTORICO_DEP, dbtime());
?>
