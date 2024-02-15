<?php
header('Content-Type: text/plain');
require_once("../includes/lib.php");
/*-------------------------------------------------------------------------------------------------------------*/
//Actualizar parametros
if(!empty($_GET['actualizar']))
{
	$parametros = explode("|",$_GET['actualizar']);
	
	if((count($parametros))!=5)
	{
		echo("Error|Numero de parametros invalido");
	}//Cierro if count
	else
	{
		$conn->StartTrans();
		try
		{
			$sql="UPDATE proceso set proc_loguear=".sqlint($parametros[1])." ,proc_cron=".sqlint($parametros[3]).
			",proc_periodo=".sqlint($parametros[2]).",proc_cron_cfg='* * * * *' WHERE proc_id=".sqlint($parametros[0]);
			$conn->Execute($sql);			
			echo($parametros[1]."|".$parametros[2]."|".$parametros[3]);
		}//Cierro try
		catch(exception $e)
		{
			echo("Error|No se pudo actualizar");
		}//Cierro catch
		$conn->CompleteTrans();
	}//Cierro else
}//Cierro if !empty
/*----------------------------------------------------------------------------------------------------------*/
if(!empty($_GET['script']))
{
	//Si se envia un script a ejecutar
	
	$script = trim($_GET['script']);
	system("c:\php\php -f ".RUTA_PROCESOS.$script,$ok);
	//echo("c:\php\php -f ".RUTA_PROCESOS.$script);
	echo($ok);
	
}
/*--------------------------------------------------------------------------------------------------------*/
if(!empty($_GET['crear']))
{	//Crear proceso
	$conn->StartTrans();
	try
	{
		$parametros = explode("|",$_GET['crear']);
		$sql="INSERT into proceso VALUES(".numerador("proceso").", ".sqlstring($parametros[0]).", ".
		sqlstring($parametros[1]).", ".sqlint($parametros[4]).", null, null, null, null, null, ";
		$sql .= (strtolower ($parametros[2])=="s")?sqlint('1'):sqlint('0');
		$sql.=", ";
		$sql .= (strtolower ($parametros[5])=="s")?sqlint('1'):sqlint('0');
		$sql .=", ";
		$sql .= sqlstring('* * * * *').")";
		$conn->Execute($sql);			
		echo("1");
	}//Cierro try
	catch(exception $e)
	{
		echo("0");
	}//Cierro catch
	$conn->CompleteTrans();
}
/*-------------------------------------------------------------------------------------------------------------------*/
if(!empty($_GET['eliminar']))
{
	$conn->StartTrans();
	try
	{
		$sql="DELETE FROM PROCESO WHERE PROC_ID=".sqlint($_GET['eliminar']);
		$conn->Execute($sql);
		echo("1");
	
	}//Cierro try
	catch(exception $e)
	{
		echo("0");
	}//Cierro catch
	$conn->CompleteTrans();
}
?>