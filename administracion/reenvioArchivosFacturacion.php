<?
require_once("../includes/lib.php");
if (check_auth($usrid, $perid) && check_permission($permiso)) {
?>
<html><head>
<? require_once("../includes/inc_header.php"); ?>
<?
	function enviaArchivos($mailto,$mensaje,$atach="",$atach2="",$mailsubj)
	{
		$email = $mailto;
		$msg = $mensaje."\r\n"; 
		$to = $email;
		$subj = $mailsubj; 

		$fattach = substr($atach,strrpos($atach,"\\")+1);

		$a_name = "phpmail";
		$timer = time(); 
		$abound = "00-".$a_name."-".$timer.""; 
		$stime = date("r",time()); 
		$mhead = "Date: ".$stime."\r\n"; 
		$mhead .= "X-Priority: 1 (High)\r\n"; 
		$mhead .= "X-Mailer: <PHP MAILER>\r\n"; 
		$mhead .= "MIME-Version: 1.0\r\n"; 
		$mhead .= "Content-Type: multipart/mixed; boundary=\"$abound\"\r\n"; 
		$mhead .= "Content-Transfer-Encoding: 8bit\r\n"; 

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

		$attachment = preg_replace("/\r\n/i", "\n", $attachment); 
		$ahead .= "$attachment"; 
		$ahead .= "\r\n"; 
		$msgbody .= "$ahead"; 
		set_magic_quotes_runtime(get_magic_quotes_gpc()); 
		$msgbody .= "--".$abound."--"; 

		mail($to, $subj, $msgbody, $mhead);
	}	
	
	
	if ($_POST["reenviar"] == 'si')
	{
		$fecha_reenvio = trim(str_replace("/","-",$_POST["fechasArchivos"]));
		$fecha_reenvio_tmp = explode(" ",$fecha_reenvio);
		$fecha_nombre_archivo_tmp = explode("-",$fecha_reenvio_tmp[0]);
		$fecha_nombre_archivo = $fecha_nombre_archivo_tmp[0] . $fecha_nombre_archivo_tmp[1] . $fecha_nombre_archivo_tmp[2];
		$fecha_reenvio_tmp_tmp = explode("-",$fecha_reenvio_tmp[0]);
		$fecha_reenvio = $fecha_reenvio_tmp_tmp[2] ."-". $fecha_reenvio_tmp_tmp[1] ."-". $fecha_reenvio_tmp_tmp[0] ." ". $fecha_reenvio_tmp[1];
		
		//busco los parametros		
		//busco en la BD los archivos y los escribo en el directorio temporal
		$sql = "select par_valor from parametro where par_nombre = 'PAR_PROCESO_ARCHIVO_LISTA_DISTRIBUCION'";
		$rs_mail = $conn->execute($sql);
		$lista_distribucion = trim($rs_mail->fields("par_valor"));
		$sql = "select par_valor from parametro where par_nombre = 'PAR_PATH_PROCESO_ARCHIVO'";
		$rs_path = $conn->execute($sql);
		$ruta_archivo = trim($rs_path->fields("par_valor"));
		$sql = "select par_valor from parametro where par_nombre = 'PAR_RUTA_GZIP'";
		$rs_ruta_gzip = $conn->execute($sql);
		$ruta_gzip = trim($rs_ruta_gzip->fields("par_valor"));
		
		set_time_limit(0);
		
		$sql = "select ARP_ARCHIVO_CONSOLIDADO from archivo_proceso where arp_fecha_proceso = '$fecha_reenvio'";
		$rs = $conn->execute($sql);
		$contenido_consolidado = $conn->BlobDecode(reset($rs->fields));
		$sql = "select ARP_ARCHIVO_DETALLADO from archivo_proceso where arp_fecha_proceso = '$fecha_reenvio'";
		$rs = $conn->execute($sql);
		$contenido_detallado = $conn->BlobDecode(reset($rs->fields));	
		
		$contenido_consolidado = base64_decode($contenido_consolidado);
		$contenido_detallado = base64_decode($contenido_detallado);		
	
		$archivo_consolidado = $ruta_archivo . $fecha_nombre_archivo . "_CONSOLIDADO.csv";
		$archivo_detallado = $ruta_archivo . $fecha_nombre_archivo . "_DETALLADO.csv";
		
		$handle = fopen($archivo_consolidado, "wb");
		fwrite($handle, $contenido_consolidado);
		fclose($handle);		

		$handle = fopen($archivo_detallado, "wb");
		fwrite($handle, $contenido_detallado);
		fclose($handle);
		
		//Ahora valido que esten creados los dos archivos
		if (is_file($archivo_consolidado) && is_file($archivo_detallado))
		{
			@system($ruta_gzip . "gzip.exe -q9n -S .gz " . $archivo_detallado);
			$archivo_detallado_zip = $archivo_detallado . ".gz";
			if (is_file($archivo_detallado_zip))
			{
				//Si sigo acá es porque ya tengo los dos archivos creados y listo para mandarlos por mail
				@enviaArchivos($lista_distribucion,"Archivo consolidado regenerado el día " . date("d-m-Y"),$archivo_consolidado,"","Facturación AAERPA Archivo consolidado - Reenvío (" . date("d-m-Y") . ")");
				@enviaArchivos($lista_distribucion,"Archivo detallado regenerado el día " . date("d-m-Y"),$archivo_detallado_zip,"","Facturación AAERPA Archivo detallado - Reenvío (" . date("d-m-Y") . ")");
				@unlink($archivo_detallado_zip);
				@unlink($archivo_consolidado);
				$mensaje = '<span class=texto><b>Los archivos fueron reenviados con éxito.</b></span>';
			}else{
				//Salgo por error
				$mensaje = '<span class=textoerror>No se pudieron reenviar los archivos.</span>';
				@unlink($archivo_detallado);
				@unlink($archivo_consolidado);
			}
		}else{
			//Salgo por error
			$mensaje = '<span class=textoerror>No se pudieron reenviar los archivos.</span>';
		}
	}
?>
<script type="text/javascript" language="JavaScript" src="../includes/ajaxobjt.js"></script>
<script type="text/javascript">
	function validaReenvio()
	{
		if (document.frmReenviaArchivo.fechasArchivos.value == '')
		{
			alert('Debe seleccionar una fecha.');
			document.frmReenviaArchivo.fechasArchivos.focus();
			return false;
		}
		document.frmReenviaArchivo.reenviar.value = 'si';
		document.frmReenviaArchivo.btnReenviar.disabled = true;
		document.frmReenviaArchivo.submit();
	}
</script>

</head>
<body>
<? require_once('../includes/inc_topleft.php');
$pagina=basename($_SERVER['SCRIPT_FILENAME']);
require_once('../includes/inc_titulo.php'); ?>
<!-- Contenido -->
	<form name="frmReenviaArchivo" method="post" action="">	
	<input type="hidden" name="reenviar" value="">
	<?
		if ($mensaje != '')
		{
	?>
	<table align=center width="600" border="0" cellpadding="3" cellspacing="0">
		<tr><td>&nbsp;</td></tr>
		<tr align="center"><td><?=$mensaje?></td></tr>
		<tr><td>&nbsp;</td></tr>
		<tr align="center"><td><input style="cursor:pointer" class="botonover" type="submit" name="btnVolver" value="Volver"></td></tr>
	</table>	
	<?
		}else{
					
			$sql = "select ".$conn->SQLDate(FMT_DATETIME_DB, "arp_fecha_proceso")." as arp_fecha_proceso from archivo_proceso order by arp_fecha_proceso";
			$rs = $conn->execute($sql);
	?>		
	<table align=center width="600" border="0" cellpadding="3" cellspacing="0">
		<tr><td>&nbsp;</td></tr>
		<? if ($rs->EOF){ ?>
		<tr align="center"><td class="textoerror">No se encontraron archivos para enviar.</td></tr>
		<? }else{ ?>
		<tr><td align="center" class="texto">Seleccione la fecha de los archivos de facturación que desea reenviar.</td></tr>
		<tr><td>&nbsp;</td></tr>
		<tr>
			<td align=center>
				<select name="fechasArchivos" class="combo">
					<option selected="selected" value="">Seleccione una fecha</option>
					<? while (!$rs->EOF) { ?>
					<option value="<?=$rs->fields("arp_fecha_proceso")?>"><?=$rs->fields("arp_fecha_proceso")?></option>
					<? $rs->MoveNext();	} ?>
				</select>
			</td> 
		</tr>
		<tr><td>&nbsp;</td></tr>
		<tr align="center"><td><input style="cursor:pointer" class="botonover" type="button" name="btnReenviar" value="Ejecutar" onClick="validaReenvio();"></td></tr>
		<? } ?>
	</table>
	<? } ?>
	</form>
<!--Contenido-->
<? require_once("../includes/inc_bottom.php");?>
</BODY></HTML>
<?
}//Cierro if autorizacion
?>
