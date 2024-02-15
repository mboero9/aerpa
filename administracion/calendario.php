<? 
function sumarmeses ($fechaini, $meses)
{
	$dia=substr($fechaini,8,2);
	$mes=substr($fechaini,5,2);
	$anio=substr($fechaini,0,4);
//Sumamos los meses requeridos
	$tmpanio=floor($meses/12);
	$tmpmes=$meses%12;
	$anionew=$anio+$tmpanio;
	$mesnew=$mes+$tmpmes;
	if ($mesnew<1)	{
		  $mesnew=$mesnew+12;
		  if ($mesnew<10) {
		     $mesnew="0".$mesnew;
		     $anionew=$anionew+1;
		  }
    }
//Ponemos la fecha en formato americano y la devolvemos
	$fecha=date( "Y-m-d", mktime(0,0,0,$mesnew,'01',$anionew) );
	return $fecha;
}//fin funcion
function restarmeses ($fechaini, $meses)
{
	$dia=substr($fechaini,8,2);
	$mes=substr($fechaini,5,2);
	$anio=substr($fechaini,0,4);
//Sumamos los meses requeridos
	$tmpanio=floor($meses/12);
	$tmpmes=$meses%12;
	$anionew=$anio-$tmpanio;
	$mesnew=$mes-$tmpmes;
	if ($mesnew<1)	{
		  $mesnew=$mesnew-12;
		  if ($mesnew<10) {
		     $mesnew="0".$mesnew;
		     $anionew=$anionew-1;
		  }
    }
//Ponemos la fecha en formato americano y la devolvemos
	$fecha=date( "Y-m-d", mktime(0,0,0,$mesnew,'01',$anionew) );
	return $fecha;
}//fin funcion
function calcula_numero_dia_semana($dia,$mes,$ano){
	$numerodiasemana = date('w', mktime(0,0,0,$mes,$dia,$ano));
/*	if ($numerodiasemana == 0) 
		$numerodiasemana = 6;
	else
		$numerodiasemana--;*/
	return $numerodiasemana;
}

//funcion que devuelve el ltimo d a de un mes y ao dados
function ultimoDia($mes,$ano){ 
    $ultimo_dia=28; 
    while (checkdate($mes,$ultimo_dia + 1,$ano)){ 
       $ultimo_dia++; 
    } 
    return $ultimo_dia; 
} 

function dame_nombre_mes($mes){
	 switch ($mes){
	 	case 1:	 $nombre_mes="Enero";      break;
	 	case 2:	 $nombre_mes="Febrero";    break;
	 	case 3:	 $nombre_mes="Marzo";      break;
	 	case 4:	 $nombre_mes="Abril";      break;
	 	case 5:	 $nombre_mes="Mayo";	   break;
	 	case 6:	 $nombre_mes="Junio";      break;
	 	case 7:	 $nombre_mes="Julio";	   break;
	 	case 8:	 $nombre_mes="Agosto";	   break;
	 	case 9:	 $nombre_mes="Septiembre"; break;
	 	case 10: $nombre_mes="Octubre";    break;
	 	case 11: $nombre_mes="Noviembre";  break;
	 	case 12: $nombre_mes="Diciembre";  break;
	}
	return $nombre_mes;
}
 
function mostrar_calendario($par_fecha,$feriado){

	$dia=substr($par_fecha,8,2);
	$mes=substr($par_fecha,5,2);
	$ano=substr($par_fecha,0,4);


$mes_hoy=date("m");
$ano_hoy=date("Y");

if (($mes_hoy <> $mes) || ($ano_hoy <> $ano)){
	$hoy=0;
}else{
	$hoy=date("d");
}
	//tomo el nombre del mes que hay que imprimir
	$nombre_mes = dame_nombre_mes($mes);
	
	//construyo la cabecera de la tabla
	echo "<table width=200 cellspacing=3 cellpadding=2 border=0><tr><td colspan=7 align=center class=celdatitulo>";
	echo "<table width=100% cellspacing=2 cellpadding=2 border=0><tr>";
	   echo "<td align=center class=celdatituloColumna>$nombre_mes $ano</td>";
	   echo "<td align=right style=font-size:10pt;font-weight:bold;color:white>";
	echo "</tr></table></td></tr>";
	echo '	<tr>
			    <td width=14% align=center class=altn title="Domingo">Do</td>
			    <td width=14% align=center class=altn title="Lunes">Lu</td>
			    <td width=14% align=center class=altn title="Martes">Ma</td>
			    <td width=14% align=center class=altn title="Mi&eacute;rcoles">Mi</td>
			    <td width=14% align=center class=altn title="Jueves">Ju</td>
			    <td width=14% align=center class=altn title="Viernes">Vi</td>
			    <td width=14% align=center class=altn title="S&aacute;bado">Sa</td>
			</tr>';
	
	//Variable para llevar la cuenta del dia actual
	$dia_actual = 1;
	
	//calculo el numero del dia de la semana del primer dia
	$numero_dia = calcula_numero_dia_semana(1,$mes,$ano);
	//echo "Numero del dia de demana del primer: $numero_dia <br>";
	
	//calculo el  ltimo dia del mes
	$ultimo_dia = ultimoDia($mes,$ano);
	
	echo "<tr style='height: 25'>";
		 
	//recorro todos los dems d as hasta el final del mes
	$i=0;
	while ($dia_actual <= $ultimo_dia){
		if ($i < $numero_dia){
			//si el dia de la semana i es menor que el numero del primer dia de la semana no pongo nada en la celda
			echo "<td></td>";
		} else {
		
		//si estamos a principio de la semana escribo el <TR>
		if ($numero_dia == 0)
			echo "<tr style='height: 25'>";
		    $clase = ((($numero_dia == 0) || ($numero_dia == 6)) ? 'celdatitulo' : 'fondotabla2');		
		    $clase = ($dia_actual == $hoy ? '' : $clase);	
			if (is_array($feriado)) {
			    $clase = (array_key_exists($dia_actual, $feriado) != "" ? 'fondoconfirmacion' : $clase);							
			}
			$fec_actual=($dia_actual < 10 ? "0" : "").$dia_actual."/".$mes."/".$ano;
			echo "<td align=center class='$clase' onClick='seleccionofecha(\"".$fec_actual."\",\"".$clase."\",\"".$feriado[$dia_actual]."\")' onMouseOver='this.className = \"fondoconfirmacion\";' onMouseOut='this.className = \"$clase\"' title=\"$feriado[$dia_actual]\" style=\"cursor: pointer\" >$dia_actual</td>";
			$dia_actual++;
			$numero_dia++;
		//si es el utimo de la semana, me pongo al principio de la semana y escribo el </tr>			
			if ($numero_dia == 7){
				$numero_dia = 0;
				echo "</tr>";
			}
		}
		$i++;

	}	
	//compruebo que celdas me faltan por escribir vacias de la  ltima semana del mes
	for ($i=$numero_dia;$i<7;$i++){
		echo "<td></td>";
	}
	
	echo "</tr>";
	echo "</table>";
}	
?>