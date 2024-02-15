<?
require_once("../includes/lib.php");
if (!empty($_GET['tipo'])) {
	switch($_GET['tipo']) {
	case 'modhabiles':
		$dia_semana[]="Domingo";
		$dia_semana[]="Lunes";
		$dia_semana[]="Martes";
		$dia_semana[]="Miercoles";
		$dia_semana[]="Jueves";
		$dia_semana[]="Viernes";
		$dia_semana[]="Sabado";
		for ($i=0;$i<7;$i++){	
				$Sql="Select count(*) as cant 
					    From PARAMETRO
					   where PAR_NOMBRE=".sqlstring('dia_'.$i);		  
				$rs = $conn->execute($Sql);
//echo "\n".$Sql;								
//echo "\nCantidad:".$rs->fields["cant"]."-".$_GET['dia_'.$i];
				if (($rs->fields["cant"]>0)&&($_GET['dia_'.$i]==1)) {
					$Sql="delete from PARAMETRO where PAR_NOMBRE=".sqlstring('dia_'.$i);
//echo "\n".$Sql;													
					$conn->execute($Sql);					
				}						
				if (($rs->fields["cant"]==0)&&($_GET['dia_'.$i]==0)) {
//echo "\nDAR DE ALTA";																	
				$Sql="insert into PARAMETRO (PAR_NOMBRE,
										     PAR_VALOR)
						  values (".sqlstring('dia_'.$i).",'".$dia_semana[$i]." d&iacute;a No Laborable')";
//echo "\n".$Sql;				
					$conn->execute($Sql);					
				}	
		 }//for					
		break;
	case 'conhabiles':		
				$Sql="Select PAR_NOMBRE 
					    From PARAMETRO
					   where PAR_NOMBRE like ".sqlstring('dia_%')."
					     and PAR_VALOR like '%No Laborable'";
				$rs = $conn->execute($Sql);	
				while (!$rs->EOF) {
				  echo $rs->fields["PAR_NOMBRE"]."|";
				  $rs->movenext();						  
				}
		break;
	case 'altaferiado':	
			$conn->StartTrans();
			try {		
				$fecha     = new Date($_GET["fecha"]);		
				$Sql="Insert into FERIADO (FER_FECHA, 
										   FER_DESCRIPCION
										  )
								   VALUES (".sqldate($fecha->format(FMT_DATE_ISO)).",
										   ".sqlstring($_GET['descripcion'])."						   								   								   
										  )";
//echo $Sql;										  
				$rs = $conn->Execute($Sql);
				echo 'ok';
			} catch (exception $e) {
				dbhandleerror($e);
			}				
			$conn->CompleteTrans();
				
		break;
	case 'borraferiado':	
			$conn->StartTrans();
			try {		
				$fecha     = new Date($_GET["fecha"]);		
				$Sql="delete from FERIADO 
				       where FER_FECHA=".sqldate($fecha->format(FMT_DATE_ISO));
//echo $Sql;										  
				$rs = $conn->Execute($Sql);
				echo 'ok';
			} catch (exception $e) {
				dbhandleerror($e);
			}				
			$conn->CompleteTrans();
				
		break;		
	}//switch		
}//if (!empty($tipo))	

?>
