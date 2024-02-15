<?php
require_once("../includes/lib.php");
require_once("../includes/funciones.php");
require_once("../includes/sec_mail.php");
set_time_limit(120);


//chequeo si el campo tiene valores validos y devuelvo array de valores validos
function cronChk($field,$minval,$maxval){

	//valido tipos de entrada posibles
	if($maxval==55 || $maxval==23){
		if(!ereg("^((\*{1})|(\*{1}/{1}[0-9]{1,2})|([0-9]{1,2})|([0-9,]+)|([0-9]{1,2}-[0-9]{1,2}))$",$field)){
			return false;
		}
	}else{
		if(!ereg("^((\*{1})|([0-9]{1,2})|([0-9,]+)|([0-9]{1,2}-[0-9]{1,2}))$",$field)){
			return false;
		}
	}

	$arrComa = explode(",",$field);
	$arrGuion = explode("-",$field);
	$arrBarra = explode("/",$field);

	//caso *
	if($field=="*"){
		for($i=$minval,$j=0; $i<=$maxval ;$i++,$j++){
			$arrValues[$j] = $i;
		}
		return $arrValues;
	}

	//caso N
	if(count($arrComa)==1 && count($arrGuion)==1 && count($arrBarra)==1){
		if($field>=$minval && $field<=$maxval){
			$arrValues[0] = intval($field);
			return $arrValues;
		}else{
			return false;
		}
	}

	//caso */N solo habilitado para horas (multiplos de 24) y minutos (multiplos de 60)
	if($maxval==55){
		$maxval_aux = 59;
	}else{
		$maxval_aux = $maxval;
	}
	if(count($arrBarra)==2 && fmod($maxval_aux+1,$arrBarra[1])==0){
		for($i=0,$j=0; $i<=$maxval ;$i=$i+$arrBarra[1],$j++){
			$arrValues[$j] = $i;
		}
		return $arrValues;
	}

	//caso N1-N2
	if(count($arrGuion)==2){
		if($arrGuion[0]<$arrGuion[1] && $arrGuion[0]>=$minval && $arrGuion[1]<=$maxval){
			for($i=$arrGuion[0],$j=0; $i<=$arrGuion[1] ;$i++,$j++){
				$arrValues[$j] = intval($i);
			}
			return $arrValues;
		}else{
			return false;
		}
	}

	//caso N1,N2,N3,...,N
	if(count($arrComa)>1){
		for($i=0,$j=0; $i<count($arrComa) ;$i++,$j++){
			if($arrComa[$i]>=$minval && $arrComa[$i]<=$maxval && $arrComa[$i]<>""){
				if(count($arrValues)>0){
					if(!in_array(intval($arrComa[$i]),$arrValues)){
						//solo lo agrego si no esta
						$arrValues[$j] = intval($arrComa[$i]);
					}
				}else{
					$arrValues[$j] = intval($arrComa[$i]);
				}
			}else{
				return false;
			}
		}
		sort($arrValues);
		return $arrValues;
	}

	return false;
}

//determina si es necesario ejecutar el proceso de acuerdo a la configuracion del cron
function cronExec($runDate,$process,$cronCfg,$lastRun){

	/*fecha en que corre el proceso crontab.php*/
	Global $exec_time;
	$exec_time = $runDate;
	$runDateTime = $runDate;

	$runDayWeek = date("w",$runDateTime);
	$runYear = date("Y",$runDateTime);
	$runMonth = date("n",$runDateTime);
	$runDay = date("j",$runDateTime);
	$runHour = date("G",$runDateTime);
	$runMin = date("i",$runDateTime);

	/*proxima fecha en la que debe correr segun la configuracion del crontab*/
	$proxRunMonth = "";
	$proxRunDay = "";
	$proxRunHour = "";
	$proxRunMin = "";
	//cuando una de las varibles $prox... es < a la equivalente $run...
	//seteo este flag para que de ahi en mas tome siempre el mayor valor posible de arrValues
	$find_end = 0;

	$arrCron = explode(' ',$cronCfg);

	if(count($arrCron)<>5){
		echo(count($arrCron)."\n");
		$error = "Error en la configuracion del crontab ($cronCfg) para el proceso $process";
        alarmaAdd(PROC_CRONTAB,$error,PALAR_SISTEMA);
		//Envio Alerta
		$message = "Error en la configuracion del crontab ($cronCfg) para el proceso $process";
		authSendEmail($message);
		return false;
	}

	//chequeo campo5 del crontab
	unset($arrValues);
	$arrValues = cronChk($arrCron[4],0,6);
	//print_r($arrValues);echo "<br><br>";//debug
	if($arrValues===false){
		$error = "Error en la configuracion del campo5 del crontab (".$arrCron[4].") para el proceso $process";
		alarmaAdd(PROC_CRONTAB,$error,PALAR_SISTEMA);
		//Envio Alerta
		$message = "Error en la configuracion del campo5 del crontab (".$arrCron[4].") para el proceso $process";
		authSendEmail($message);
		return false;
	}else{
		if(!in_array($runDayWeek,$arrValues)){
			return false; //el dia de la semana no corresponde
		}
	}

	//chequeo campo4 del crontab
	unset($arrValues);
	$arrValues = cronChk($arrCron[3],1,12);
	//print_r($arrValues);echo "<br><br>";//debug
	if($arrValues===false){
		$error = "Error en la configuracion del campo4 del crontab (".$arrCron[3].") para el proceso $process";
		alarmaAdd(PROC_CRONTAB,$error,PALAR_SISTEMA);
		//Envio Alerta
		$message = "Error en la configuracion del campo4 del crontab (".$arrCron[3].") para el proceso $process";
		authSendEmail($message);
		return false;
	}else{
		if($arrValues[0]>$runMonth){
			return false;
		}else{
			for($i=0; $arrValues[$i]<=$runMonth && $i<count($arrValues) ;$i++){
				$proxRunMonth = $arrValues[$i];
			}
		}
	}
	if($proxRunMonth===""){return false;}

	//chequeo campo3 del crontab
	unset($arrValues);
	$arrValues = cronChk($arrCron[2],1,31);
	//print_r($arrValues);echo "<br><br>";//debug
	if($arrValues===false){
		$error = "Error en la configuracion del campo3 del crontab (".$arrCron[2].") para el proceso $process";
		alarmaAdd(PROC_CRONTAB,$error,PALAR_SISTEMA);
		//Envio alerta
		$message = "Error en la configuracion del campo3 del crontab (".$arrCron[2].") para el proceso $process";
		authSendEmail($message);
		return false;
	}else{
		if($proxRunMonth<$runMonth){
			$proxRunDay = end($arrValues);
			$find_end = 1;
		}else{
			for($i=0; $arrValues[$i]<=$runDay && $i<count($arrValues) ;$i++){
				$proxRunDay = $arrValues[$i];
			}
		}
	}
	if($proxRunDay===""){return false;}

	//chequeo campo2 del crontab
	unset($arrValues);
	$arrValues = cronChk($arrCron[1],0,23);
	//print_r($arrValues);echo "<br><br>";//debug
	if($arrValues===false){
		$error = "Error en la configuracion del campo2 del crontab (".$arrCron[1].") para el proceso $process";
		alarmaAdd(PROC_CRONTAB,$error,PALAR_SISTEMA);
		//Envio alerta
		$message = "Error en la configuracion del campo2 del crontab (".$arrCron[1].") para el proceso $process";
		authSendEmail($message);
		return false;
	}else{
		if($proxRunDay<$runDay || $find_end==1){
			$proxRunHour = end($arrValues);
			$find_end = 1;
		}else{
			for($i=0; $arrValues[$i]<=$runHour && $i<count($arrValues) ;$i++){
				$proxRunHour = $arrValues[$i];
			}
		}
	}
	if($proxRunHour===""){return false;}

	//chequeo campo1 del crontab
	//nota como este proceso va a estar cronado cada 5 min,
	//si en este campo coloco + de 55 no corre nunca
	unset($arrValues);
	$arrValues = cronChk($arrCron[0],0,55);
	//print_r($arrValues);echo "<br><br>";//debug
	if($arrValues===false){
		$error = "Error en la configuracion del campo1 del crontab (".$arrCron[0].") para el proceso $process";
		alarmaAdd(PROC_CRONTAB,$error,PALAR_SISTEMA);
		//Envio alerta
		$message = "Error en la configuracion del campo1 del crontab (".$arrCron[0].") para el proceso $process";
		authSendEmail($message);
		return false;
	}else{
		if($proxRunHour<$runHour || $find_end==1){
			$proxRunMin = end($arrValues);
		}else{
			for($i=0; $arrValues[$i]<=$runMin && $i<count($arrValues) ;$i++){
				$proxRunMin = $arrValues[$i];
			}
		}
	}
	if($proxRunMin===""){return false;}

	$proxRun = $runYear."-".str_pad($proxRunMonth, 2, "0", STR_PAD_LEFT)."-".str_pad($proxRunDay, 2, "0", STR_PAD_LEFT)." ".str_pad($proxRunHour, 2, "0", STR_PAD_LEFT).":".str_pad($proxRunMin, 2, "0", STR_PAD_LEFT).":00";

	if(strtotime($proxRun)>strtotime($lastRun)){


		if (file_exists("/usr/bin/lynx")) {
			system("/usr/bin/lynx --dump $process </dev/null >/dev/null 2>/dev/null &");
		} else if (file_exists("/usr/local/bin/lynx")) {
			system("/usr/local/bin/lynx --dump $process </dev/null >/dev/null 2>/dev/null &");
		} else if (file_exists("d:\\aerpa\\httpget.exe")) {
			system("d:\\aerpa\\httpget.exe $process");
		}
		echo "<br>$process: corre";
		return true;
	}else{
		echo "<br>$process: no corre";
		//Envio Alerta
		$message = "$process: no corre";
		authSendEmail($message);
		return -1;
	}
}

//carpeta "procesos"
$tmp = explode("/", dirname($_SERVER["PHP_SELF"]));
$fld = implode("/", array_slice($tmp, 0, -1));
$procesos = "http://" . $_SERVER["SERVER_NAME"] . $fld . "/procesos/";

//busco procesos cronados que no estan corriendo menos ALARMASPROC y CRONTAB
$sql = "Select PROC_ID,PROC_SCRIPT,PROC_CRON_CFG,
		".$conn->SQLDate(FMT_DATE_ISO, "PROC_FIN")." As PROC_FIN
	From PROCESO
	Where PROC_CRON = " . sqlboolean(true) . "
		And (PROC_FIN Is Not Null Or (PROC_FIN Is Null And PROC_INI Is Null))
		And PROC_NOMBRE Not In (". sqlstring(PROC_ALARMAS) . "," . sqlstring(PROC_CRONTAB) . ")";

$rsproc = $conn->Execute($sql);

while(!$rsproc->EOF){
	$process = $procesos.trim($rsproc->fields["PROC_SCRIPT"]);
	$cronCfg = trim($rsproc->fields["PROC_CRON_CFG"]);
	$lastRun = trim($rsproc->fields["PROC_FIN"]);
	if($lastRun===""){
		//pongo una fecha vieja para asegurarme que corra seguro
		$lastRun = "2000-01-01 00:00:00";
	}
	if(cronExec(dbtime(),$process,$cronCfg,$lastRun)===true){
		$proc_exec .= $rsproc->fields["PROC_ID"].",";
	}
	$rsproc->MoveNext();
}

//corro el proceso ALARMASPROC
//corro este a lo ultimo para que no de alarmas eroneas de que no corrio
$sql = "Select PROC_ID,PROC_SCRIPT,PROC_CRON_CFG,
	".$conn->SQLDate(FMT_DATE_DB,"PROC_FIN")." As PROC_FIN
	From PROCESO
	Where PROC_CRON = " . sqlboolean(true) . "
		And (PROC_FIN Is Not Null Or (PROC_FIN Is Null And PROC_INI Is Null))
		And PROC_NOMBRE = " . sqlstring(PROC_ALARMAS);
$rsproc = $conn->Execute($sql);

while(!$rsproc->EOF){
	$process = $procesos.trim($rsproc->fields["PROC_SCRIPT"]);
	$cronCfg = trim($rsproc->fields["PROC_CRON_CFG"]);
	$lastRun = trim($rsproc->fields["PROC_FIN"]);
	if($lastRun===""){
		//pongo una fecha vieja para asegurarme que corra seguro
		$lastRun = "2000-01-01 00:00:00";
	}
	if(cronExec(dbtime(),$process,$cronCfg,$lastRun)===true){
		$proc_exec .= $rsproc->fields["PROC_ID"].",";
	}
	$rsproc->MoveNext();
}

if($proc_exec){
	//espero 10 seg y me fijo si los procesos que mande a correr arrancaron
	$espera = 10;
	sleep($espera);
	$sql = "Select PROC_ID,PROC_NOMBRE
		From PROCESO
		Where PROC_INI < ".sqldate($exec_time)."
			And PROC_ID In (".substr($proc_exec,0,-1).")
		Order by PROC_NOMBRE";
	$rsproc = $conn->Execute($sql);
	unset($proc_inf);
	while(!$rsproc->EOF){
		$proc_inf .= $rsproc->fields["PROC_NOMBRE"]."\n";
		$rsproc->MoveNext();
	}

	if($proc_inf){
		$error = "Los siguientes procesos no se han iniciado pasados $espera segundos desde que fueron ejecutados por el proceso de crontab:\n\n".$proc_inf;
		alarmaAdd(PROC_CRONTAB,$error,PALAR_SISTEMA);
	}
}

?>
