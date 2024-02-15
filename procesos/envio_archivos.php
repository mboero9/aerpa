<?
	// Timeout infinito
	//----------------------------------
	set_time_limit(0);
	
	// Includes
	//----------------------------------
	require_once("../includes/lib.php");
	require_once("inc_controlproc.php");
	
	/*********** FUNCIONES **********************************************************************************/
	
	function enviaArchivos($mailto,$mensaje,$atach="",$atach2="",$mailsubj)
	{
		$email = $mailto;
		//Envio archivo por mail								
		$msg = $mensaje."\r\n"; 
		$to = $email;
		$subj = $mailsubj; 

		$fattach = substr($atach,strrpos($atach,"\\")+1);
		//$fattach2 = substr($atach2,strrpos($atach2,"\\")+1); 

		$a_name = "phpmail";
		$timer = time(); 
		$abound = "00-".$a_name."-".$timer.""; 
		$stime = date("r",time()); 
		$mhead = "Date: ".$stime."\r\n"; 
		//$mhead .= "From: ".$from."\r\n"; 
		//$mhead .= "To: ".$to."\r\n"; 
		$mhead .= "X-Priority: 1 (High)\r\n"; 
		$mhead .= "X-Mailer: <PHP MAILER>\r\n"; 
		$mhead .= "MIME-Version: 1.0\r\n"; 
		$mhead .= "Content-Type: multipart/mixed; boundary=\"$abound\"\r\n"; 
		$mhead .= "Content-Transfer-Encoding: 8bit\r\n"; 

		//quito los \r del mensaje porque a algunos servidores no les gusta
		$msg = preg_replace("/\r\n/i", "\n", $msg); 
		$msgbody = "--".$abound.""; 
		$msgbody .= "\r\n"; 
		$msgbody .= "Content-Type: text/plain; charset=\"ISO-8859-1\"\r\n"; 
		$msgbody .= "Content-Transfer-Encoding: 8bit;\r\n\r\n"; 
		$msgbody .= "$msg"; 
		$msgbody .= "\r\n"; 
		$msgbody .= "\r\n"; 
		$msgbody .= "\r\n"; 
		$ahead = "--".$abound.""; 
		$ahead .= "\r\n"; 
		$ahead .= "Content-Type: application/octet-stream"; 
		$ahead .= "\r\n"; 
		$ahead .= "Content-Transfer-Encoding: base64"; 
		$ahead .= "\r\n"; 
		$ahead .= "Content-Disposition: attachment; filename=\"$fattach\""; 
		$ahead .= "\r\n\r\n"; 
		set_magic_quotes_runtime(0); 
		$attachment = fread(fopen("$atach", "rb"), filesize("$atach")); 
		$attachment = chunk_split(base64_encode($attachment)); 

		//quito los \r del attach porque a algunos servidores no les gusta
		$attachment = preg_replace("/\r\n/i", "\n", $attachment); 
		$ahead .= "$attachment"; 
		$ahead .= "\r\n"; 
		
		/*
		$ahead .= "Content-Type: application/octet-stream"; 
		$ahead .= "\r\n"; 
		$ahead .= "Content-Transfer-Encoding: base64"; 
		$ahead .= "\r\n"; 
		$ahead .= "Content-Disposition: attachment; filename=\"$fattach2\""; 
		$ahead .= "\r\n\r\n"; 
		set_magic_quotes_runtime(0); 
		$attachment2 = fread(fopen("$atach2", "rb"), filesize("$atach2")); 
		$attachment2 = chunk_split(base64_encode($attachment2)); 

		//quito los \r del attach porque a algunos servidores no les gusta
		$attachment2 = preg_replace("/\r\n/i", "\n", $attachment2); 
		$ahead .= "$attachment2"; 
		$ahead .= "\r\n"; 		
		*/
		
		
		
		$msgbody .= "$ahead"; 
		set_magic_quotes_runtime(get_magic_quotes_gpc()); 
		$msgbody .= "--".$abound."--"; 

		mail($to, $subj, $msgbody, $mhead);

		/*FIN ENVIO MAIL*/
	}	
	
	
	function dia_habil($diaf, $mesf, $anof)
	{
		$dia_de_semana = date("l", mktime(0, 0, 0, $mesf, $diaf, $anof)); 
		global $conn;
		switch($dia_de_semana)
		{
			case "Sunday":
				$dia_a_buscar = "dia_0";
				break;
			case "Monday":
				$dia_a_buscar = "dia_1";
				break;
			case "Tuesday":
				$dia_a_buscar = "dia_2";
				break;
			case "Wednesday":
				$dia_a_buscar = "dia_3";
				break;
			case "Thursday":
				$dia_a_buscar = "dia_4";
				break;
			case "Friday":
				$dia_a_buscar = "dia_5";
				break;
			case "Saturday":
				$dia_a_buscar = "dia_6";
				break;
				
		}//Fin switch
		
		$sql = "SELECT par_nombre FROM parametro WHERE par_nombre = ".sqlstring($dia_a_buscar);
		$rs = $conn->Execute($sql);
		
		if($rs->EOF)
		{
			$posible_feriado = new Date($anof ."-". $mesf ."-". $diaf);
			$sql ="SELECT fer_fecha,fer_descripcion FROM feriado where fer_fecha = ".sqldate($posible_feriado->format(FMT_DATE_ISO));
			$rs = $conn->Execute($sql);
			if($rs->EOF)
			{
				$retorno = "si";
			}else{
				$retorno = "no";		
			}
		
		}else{
			$retorno = "no";	
		}
		
		return $retorno;	
	}
	
	/*** FIN FUNCIONES **********************************************************************************/
	
	
	//Marco que empezó a correr
	procInicio(PROC_ENVIO_ARCHIVOS, dbtime());
	
	//Busco parametros
	$sql = "select par_valor from parametro where par_nombre = 'PAR_PROCESO_ARCHIVO_LISTA_DISTRIBUCION'";
	$rs_mail = $conn->execute($sql);
	$lista_distribucion = trim($rs_mail->fields("par_valor"));
	$sql = "select par_valor from parametro where par_nombre = 'PAR_FECHA_PROCESO_ARCHIVO'";
	$rs_par = $conn->execute($sql);
	$fecha_corrida = trim($rs_par->fields("par_valor"));
	$sql = "select par_valor from parametro where par_nombre = 'PAR_PATH_PROCESO_ARCHIVO'";
	$rs_path = $conn->execute($sql);
	$ruta_archivo = trim($rs_path->fields("par_valor"));
	$sql = "select par_valor from parametro where par_nombre = 'PAR_RUTA_GZIP'";
	$rs_ruta_gzip = $conn->execute($sql);
	$ruta_gzip = trim($rs_ruta_gzip->fields("par_valor"));		
	
	//Me fijo si tiene que correr el proceso comparando la fecha actual con la fecha guardada en el parametro
	$fecha_corrida_tmp = explode("-",$fecha_corrida);
	$hoy = mktime(0,0,0,date("n"),date("j"),date("Y"));
	$fecha_corrida_unix = mktime(0,0,0,$fecha_corrida_tmp[1],$fecha_corrida_tmp[2],$fecha_corrida_tmp[0]);	
	
	if (!($hoy >= $fecha_corrida_unix))
	{
		procFin(PROC_ENVIO_ARCHIVOS, dbtime());
		die();
	}
	
	//si estoy acá es porque segun el parametro el proceso TIENE que correr
	//rearmo la fecha y busco los datos
	
	$fecha_corrida = $fecha_corrida_tmp[0] ."-". $fecha_corrida_tmp[1] ."-1";
	$sql = "select count(distinct(t.reg_codigo_ori)) as CANT_REGISTROS, count(distinct(tra_codigo)) as CANT_TRAMITES
				from tramite t
			   where t.tra_fecha_retiro < '$fecha_corrida'
				 and t.tra_fecha_proceso is NULL";
	$rs = $conn->execute($sql);
	
	if ($rs->fields("CANT_REGISTROS") == '0' || $rs->fields("CANT_TRAMITES") == '0')
	{
		//si no hay datos para realizar el informe (que es muy poco probable) se envia un mail informando la situacion
		@mail($lista_distribucion,"Archivos Facturación AAERPA - Proceso (" . date("d-m-Y") . ")","No se registraron movimientos de trámites para este proceso.");
		//Preparo la fecha de la proxima corrida para guardar en el parámetro
		$fecha_guardar_parametro = explode("-",$fecha_corrida);
		
		$dia = "0";
		$mes = $fecha_guardar_parametro[1];
		$ano = $fecha_guardar_parametro[0];
		$mes++;
		$contador_dias = 0;
		if ($mes > 12){
			$mes = "1";
			$ano++;
		}
		//ahora me fijo cual es el 3 dia hábil del mes
		while(!($contador_dias == 3)){
			$dia++;
			$dia_habil_temp = dia_habil($dia, $mes, $ano);
			if ($dia_habil_temp=='si'){
				$contador_dias++;
			}
		}
		
		$update_parametro = "update parametro set par_valor = " . sqlstring($ano ."-" . $mes . "-" . $dia) . " where par_nombre = 'PAR_FECHA_PROCESO_ARCHIVO'";
		$conn->execute($update_parametro);		
		
		procFin(PROC_ENVIO_ARCHIVOS, dbtime());
		die();
	}
	
	//sigo ejecutando porque hay datos para armar los archivos
	//busco el tipo de producto
	
	$sql = "select PRO_CODIGO, PRO_DESCRIP from producto
				where " . $rs->fields("CANT_REGISTROS") . " between PRO_ESCALA_INFERIOR AND PRO_ESCALA_SUPERIOR";
	$rs_producto = $conn->execute($sql);

	$archivo_consolidado = $ruta_archivo . date("dmY") . "_CONSOLIDADO.csv";
	$archivo_consolidado_contenido = "Cantidad de Registros, Producto, Cantidad de Tramites\n"
									. $rs->fields("CANT_REGISTROS") ."," . trim($rs_producto->fields("PRO_CODIGO")) . " - " . trim($rs_producto->fields("PRO_DESCRIP")) . "," . $rs->fields("CANT_TRAMITES");
	
	$handle = @fopen($archivo_consolidado, "wb");
	@fwrite($handle, $archivo_consolidado_contenido);
	@fclose($handle);
	
	if (!is_file($archivo_consolidado))
	{
		//Tengo que enviar una alarma
		@alarmaAdd(PROC_ENVIO_ARCHIVOS, "No se pudo crear el archivo " . date("dmY") . "_CONSOLIDADO.csv", PALAR_SISTEMA);
		procFin(PROC_ENVIO_ARCHIVOS, dbtime());
		die();
	}

	/************************************************************************************************************************/
	//AHORA ARMO EL SEGUNDO ARCHIVO, EL DETALLADO
	/************************************************************************************************************************/	
	
	$sql = "select t.tra_fecha_retiro, t.tra_nro_voucher, t.tra_dominio, r.reg_cod_int as registro_origen, r.reg_descrip as registro_origen_desc, 
				   rr.reg_cod_int as registro_destino, rr.reg_descrip as registro_destino_desc
			  from tramite t
			 inner join reg_autom r on t.reg_codigo_ori = r.reg_codigo 
			 inner join reg_autom rr on t.reg_codigo_des = rr.reg_codigo
			 where t.tra_fecha_retiro < '$fecha_corrida'
			   and t.tra_fecha_proceso is NULL
			order by r.reg_cod_int asc, t.tra_fecha_retiro asc";
	$rs_detallado = $conn->execute($sql);

	//$archivo_detallado = $ruta_archivo . date("dmY") . "_DETALLADO.csv";
	$archivo_detallado = $ruta_archivo ."tramites". date("Ym") . ".csv";

	
	$handle = @fopen($archivo_detallado, "wb");
	@fputs($handle, "Fecha de retiro, Nro. de voucher, Nro. de dominio, Nro. de registro origen, Descripcion de registro origen, Nro. de registro destino, Descripcion de registro destino\n");
	
	$detallado_registro = "";
	
	while (!$rs_detallado->EOF)
	{
		$detallado_registro = $rs_detallado->fields("tra_fecha_retiro") . ","
							. $rs_detallado->fields("tra_nro_voucher") . ","
							. $rs_detallado->fields("tra_dominio") . ","
							. $rs_detallado->fields("registro_origen") . ","
							. $rs_detallado->fields("registro_origen_desc") . ","
							. $rs_detallado->fields("registro_destino") . ","
							. $rs_detallado->fields("registro_destino_desc"). "\n";
		
		@fputs($handle, $detallado_registro);
		$rs_detallado->MoveNext();
	}
	
	@fclose($handle);

	if (!is_file($archivo_detallado))
	{
		//Tengo que enviar una alarma
		@alarmaAdd(PROC_ENVIO_ARCHIVOS, "No se pudo crear el archivo " . "tramites". date("Ym") . ".csv", PALAR_SISTEMA);
		procFin(PROC_ENVIO_ARCHIVOS, dbtime());
		die();
	}

	//si el archivo existe entonces lo gzipeo
	$archivo_detallado_temp = $archivo_detallado;
	@copy($archivo_detallado_temp,$archivo_detallado_temp .".bkp");
	@system($ruta_gzip . "gzip.exe -q9n -S .gz " . $archivo_detallado);
	@rename($archivo_detallado_temp . ".bkp",$archivo_detallado_temp);

	$archivo_detallado_zip = $archivo_detallado . ".gz";
	
	if (!is_file($archivo_detallado_zip))
	{
		//Tengo que enviar una alarma
		@alarmaAdd(PROC_ENVIO_ARCHIVOS, "No se pudo crear el archivo " . "tramites". date("Ym") . ".csv.gz", PALAR_SISTEMA);
		procFin(PROC_ENVIO_ARCHIVOS, dbtime());
		die();
	}	
	
	//Si sigo acá es porque ya tengo los dos archivos creados y listo para mandarlos por mail
	@enviaArchivos($lista_distribucion,"Archivo consolidado generado el día " . date("d-m-Y"),$archivo_consolidado,"","Facturación AAERPA Archivo consolidado - Proceso (" . date("d-m-Y") . ")");
	@enviaArchivos($lista_distribucion,"Archivo detallado generado el día " . date("d-m-Y"),$archivo_detallado_zip,"","Facturación AAERPA Archivo detallado - Proceso (" . date("d-m-Y") . ")");

	//Base de Datos - Actualizo la tabla tramites y grabo la corrida del proceso y los archivos en ARCHIVO_PROCESO
	$tra_fecha_proceso_tmp = new Date(date("Y-m-d"));
	$update_tamites = "update tramite set tra_fecha_proceso = ".($tra_fecha_proceso_tmp!="" ? sqldate($tra_fecha_proceso_tmp->format(FMT_DATE_ISO)) : 'null')." where tra_fecha_retiro < '$fecha_corrida' and tra_fecha_proceso is NULL";
	$conn->execute($update_tamites);
	
	//Preparo la fecha de la proxima corrida para guardar en el parámetro
	$fecha_guardar_parametro = explode("-",$fecha_corrida);
	
	$dia = "0";
	$mes = $fecha_guardar_parametro[1];
	$ano = $fecha_guardar_parametro[0];
	$mes++;
	$contador_dias = 0;
	if ($mes > 12){
		$mes = "1";
		$ano++;
	}
	//ahora me fijo cual es el 3 dia hábil del mes
	while(!($contador_dias == 3)){
		$dia++;
		$dia_habil_temp = dia_habil($dia, $mes, $ano);
		if ($dia_habil_temp=='si'){
			$contador_dias++;
		}
	}
	
	$update_parametro = "update parametro set par_valor = " . sqlstring($ano ."-" . $mes . "-" . $dia) . " where par_nombre = 'PAR_FECHA_PROCESO_ARCHIVO'";
	$conn->execute($update_parametro);
	
	//Actualizo ARCHIVO_PROCESO
	
	$handle = fopen($archivo_consolidado, "rb");
	$contenido_consolidado = fread($handle, filesize($archivo_consolidado));
	fclose($handle);
	$handle2 = fopen($archivo_detallado_temp, "rb");
	$contenido_detallado = fread($handle2, filesize($archivo_detallado_temp));
	fclose($handle2);	

	$contenido_consolidado = base64_encode($contenido_consolidado);
	$contenido_detallado = base64_encode($contenido_detallado);
	
	$ARP_FECHA_PROCESO_UPDATE_BLOB = date("Y-m-d H:i:s");
	$ARP_FECHA_PROCESO = new Date($ARP_FECHA_PROCESO_UPDATE_BLOB);
	$conn->Execute("INSERT INTO archivo_proceso (ARP_FECHA_PROCESO, ARP_ARCHIVO_CONSOLIDADO, ARP_ARCHIVO_DETALLADO) 
				                         VALUES (".($ARP_FECHA_PROCESO!="" ? sqldate($ARP_FECHA_PROCESO->format(FMT_DATE_ISO)) : 'null').", null, null)");
	
	$conn->UpdateBlob('archivo_proceso','ARP_ARCHIVO_CONSOLIDADO',$contenido_consolidado,'ARP_FECHA_PROCESO=\'' . $ARP_FECHA_PROCESO_UPDATE_BLOB . '\'');
	$conn->UpdateBlob('archivo_proceso','ARP_ARCHIVO_DETALLADO',$contenido_detallado,'ARP_FECHA_PROCESO=\'' . $ARP_FECHA_PROCESO_UPDATE_BLOB . '\'');	
	
	//Termino todo bien, borro los archivos, y grabo el fin de proceso
	@unlink($archivo_detallado_zip);
	@unlink($archivo_consolidado);
	@unlink($archivo_detallado_temp);	
	procFin(PROC_ENVIO_ARCHIVOS, dbtime());
?>