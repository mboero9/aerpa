<?php
function authSendEmail($message)
{
	$ok = false;
	
	global $conn;
	
	//Desde
	$namefrom = "Procesos Aerpa";
	
	//Para
	$querypara="SELECT par_valor from parametro where par_nombre='mail_alertas_para'";
	$rs=$conn->execute($querypara);
	$string_destinos = $rs->fields['par_valor'];
	$destinatarios = split(",",$string_destinos);
	$nameto = "Responsable";
	$to = $string_destinos; 
	//Headers
   	$headers  = "MIME-Version: 1.0" . $newLine;
    $headers .= "Content-type: text/html; charset=iso-8859-1" . $newLine;
   	$headers .= "To: ";
   	
	foreach($destinatarios as $destinatario)
	{
		$headers .= "$nameto <$destinatario>" . $newLine;
		 
	}
	 
	$headers .= "From: $namefrom <$from>" . $newLine;
	
	//Subjet
	$subjet = "Ha fallado un proceso!";
	
	//Headers
   	$headers  = "MIME-Version: 1.0" . $newLine;
    $headers .= "Content-type: text/html; charset=iso-8859-1" . $newLine;
   	$headers .= "To: $nameto <$to>" . $newLine;
   	$headers .= "From: $namefrom <$from>" . $newLine;
	
	//Misc
	$newLine = "\r\n";
	
	//mando mail
	$ok = mail($to,$subjet,$message,$headers);
	
	if(!ok)
	{
		echo("NO SE PUDO ENVIAR EL MAIL!");
	}

}
?> 