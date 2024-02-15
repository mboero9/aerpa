<?
require_once("../includes/lib.php");
if (check_auth($usrid, $perid) && check_permission($permiso)) {
?>
<html><head>
<? require_once("../includes/inc_header.php"); ?>
<script type="text/javascript">
function inicializar() {
	  parametros="tipo=conhabiles";
	  url="abcHabiles_ajax.php?"+parametros;
		  ajax(url);
		  if (http.readyState == 4) {
			results = http.responseText.split("|");	
		    for(i=0;i<(results.length-1);i++) {
			   var var_dia = "dia_"+parseInt(results[i].substr(4,1),10);
			   document.getElementById(var_dia).checked=false;
			}//for			
		  }//if	  
}
function recargar(fecha) {
//alert(fecha);
   document.form.fecha.value=fecha;   
   document.form.submit();
}   
function confirmo(tipo)  {
	ok=false;
	switch(tipo) {
	case 'modhabiles':
	if (document.form.modhabiles.value==1) { //hubo modificacion en los dias habiles, actualizar
		  parametros="tipo="+tipo;
		  for(i=0;i<7;i++) {
		       var var_dia="dia_"+i;
		       parametros+="&"+var_dia+"="+(document.getElementById(var_dia).checked ? '1' : '0')
		  }
		  url="abcHabiles_ajax.php?"+parametros;
//alert(url)		  
		  ajax(url);
		  if (http.readyState == 4) {
			results = http.responseText.split("|");	
//alert(results);
			if (results[0]=="") {
			   document.getElementById('tabladiaslaborables').style.display='none';
			   document.getElementById('confdiaslaborables').style.display='';			   
			   setTimeout("document.getElementById('tabladiaslaborables').style.display=''", 500);	
			   setTimeout("document.getElementById('confdiaslaborables').style.display='none'", 500);				   
			}//if
		  }//if			
	}//if
	break;
	case 'altaferiado':	
	if (document.form.fec_seleccionada.value!="") { //hubo modificacion en los dias habiles, actualizar
		  parametros="tipo="+tipo+
		             "&fecha="+document.form.fec_seleccionada.value+
					 "&descripcion="+escape(document.form.des_feriado.value);
		  url="abcHabiles_ajax.php?"+parametros;
//alert(url)		  
		  ajax(url);
		  if (http.readyState == 4) {
			results = http.responseText.split("|");	
//alert(results);
			if (results[0]=="ok") {
			   document.getElementById('tablaseleccionfecha').style.display='none';
			   document.getElementById('modferiado').style.display='';			   
			   document.getElementById('texto_modferiado').innerHTML='SE HA DADO DE ALTA EL FERIADO';			   
			   setTimeout("document.form.submit();", 500);				   
			}//if*/
		  }//if			
	}//if	
	break;
	case 'borraferiado':	
	if (document.form.fec_seleccionada.value!="") { //hubo modificacion en los dias habiles, actualizar
		  parametros="tipo="+tipo+
		             "&fecha="+document.form.fec_seleccionada.value;
		  url="abcHabiles_ajax.php?"+parametros;
//alert(url)		  
		  ajax(url);
		  if (http.readyState == 4) {
			results = http.responseText.split("|");	
//alert(results);
			if (results[0]=="ok") {
			   document.getElementById('tablaseleccionfecha').style.display='none';
			   document.getElementById('modferiado').style.display='';			   
			   document.getElementById('texto_modferiado').innerHTML='SE BORRADO EL FERIADO';			   
			   setTimeout("document.form.submit();", 500);				   
			}//if*/
		  }//if			
	}//if	
	break;
	}//switch
}
function ajax(url) {
//alert(url);
	http.open("GET", url, false);
	http.send(null);
}
function seleccionofecha(fecha,clase,desferiado) {
//con la clase ya se si es feriado o no
//  alert(clase);
  document.form.fec_seleccionada.value=fecha;
  document.form.des_feriado.value=desferiado;
  if (clase=="fondoconfirmacion") {
  	document.getElementById('bot_bferiado').style.display='';	
  	document.getElementById('bot_aferiado').style.display='none';		
    document.form.des_feriado.disabled=true;	
  }else{
    document.form.des_feriado.disabled=false;
  	document.getElementById('bot_aferiado').style.display='';  
  	document.getElementById('bot_bferiado').style.display='none';		
    document.form.des_feriado.focus();
  }//ifelse
  document.getElementById('tablaseleccionfecha').style.visibility='visible';
}
</script>
<!-- Objeto Ajax -->
<script type="text/javascript" language="JavaScript" src="../includes/ajaxobjt.js"></script>

</head>
<?
require ("calendario.php");

if (!isset($_POST['modhabiles'])){
	$tiempo_actual = time();
	$mes = date("m", $tiempo_actual);
	$ano = date("Y", $tiempo_actual);
	$dia=date("d");
	$fecha=$ano . "-" . $mes . "-" . $dia;
}else {
	$fecha=$_POST[fecha];
	$mes = substr($fecha,5,2);
	$ano = substr($fecha,0,4);
	$dia = substr($fecha,8,2);	
}
//calculo mes anterior
	$fecmes_anterior=restarmeses($fecha, 1);		
	$fecmes_anterior=substr($fecmes_anterior,0,8)."01";		
//calculo trimestre anterior
	$fecmes_trianterior=restarmeses($fecha, 3);			
//calculo mes siguiente
	$fecmes_siguiente=sumarmeses($fecha, 1);	
	$ultimodia=ultimoDia(substr($fecmes_siguiente,5,2),substr($fecmes_siguiente,0,4));
	$fecmes_siguiente=substr($fecmes_siguiente,0,8).$ultimodia;
//calculo trimestre siguiente
	$fecmes_trisiguiente=sumarmeses($fecha, 3);		
	$fecmes_trisiguiente=substr($fecmes_trisiguiente,0,8)."01";	
/*echo "<br>mes anterior".$fecmes_anterior;		
echo "<br>fecha".$fecha;	
echo "<br>mes siguiente".$fecmes_siguiente;		*/
?>	
<body onLoad="inicializar();">
<? require_once('../includes/inc_topleft.php');
$pagina=basename($_SERVER['SCRIPT_FILENAME']);
require_once('../includes/inc_titulo.php'); ?>
<!-- Contenido -->
<?
// saco los feridados del mes
	$fechadesde     = new Date($fecmes_anterior);		
	$fechahasta     = new Date($fecmes_siguiente);			
	$Sql="Select ".$conn->SQLDate(FMT_DATE_DB, "FER_FECHA")." as FER_FECHA ,
				 FER_DESCRIPCION 
 			From FERIADO
		   where FER_FECHA >= ".sqldate($fechadesde->format(FMT_DATE_ISO))."
			 and FER_FECHA <= ".sqldate($fechahasta->format(FMT_DATE_ISO));
//echo "<br>".$Sql;			 
		$rs = $conn->Execute($Sql);
	 $mes_temp=($mes-1);
	 while (!$rs->EOF) { 
//	    echo "<br>Feriado:".$rs->fields["FER_FECHA"]."-".$mes;
        switch(substr($rs->fields["FER_FECHA"],3,2)) {
		  case ($mes-1): 
		  	   $feriado1[intval(substr($rs->fields["FER_FECHA"],0,2),10)]=$rs->fields["FER_DESCRIPCION"];			   
			   break;
		  case ($mes):	 
		  	   $feriado2[intval(substr($rs->fields["FER_FECHA"],0,2),10)]=$rs->fields["FER_DESCRIPCION"];			   
			   break;
		  case ($mes+1): 
		  	   $feriado3[intval(substr($rs->fields["FER_FECHA"],0,2),10)]=$rs->fields["FER_DESCRIPCION"];
			   break;		  
		}
	  $rs->movenext();		
	 }
/*echo "<br>";	 
var_dump($feriado1);	 
echo "<br>";	 
var_dump($feriado2);	 
echo "<br>";	 
var_dump($feriado3);	 */
?>
<form name=form method="post" action="">	
<table align=center width="600">
<tr class=celdatitulo>
<td align=left width="5%" title="Trimestre Anterior" style="cursor:pointer" onClick="recargar('<?=$fecmes_trianterior;?>')" >&nbsp;&lt;&lt;&nbsp;</td>
<td align=left width="5%" title="Mes Anterior"       style="cursor:pointer" onClick="recargar('<?=$fecmes_anterior;?>')" >&nbsp;&lt;&nbsp;</td> 
<td align=center width="80%">FERIADOS</td>
<td align=right width="5%" title="Mes Siguiente" style="cursor:pointer" onClick="recargar('<?=$fecmes_siguiente;?>')" >&nbsp;&gt;&nbsp;</td> 
<td align=right width="5%" title="Trimestre Siguiente" style="cursor:pointer" onClick="recargar('<?=$fecmes_trisiguiente;?>')" >&nbsp;&gt;&gt;&nbsp;</td> 
</tr>
</table>
<table align="center">
<tr style="vertical-align:top"><td>
<? mostrar_calendario($fecmes_anterior,$feriado1); ?>
</td>
<td>
<? mostrar_calendario($fecha,$feriado2); ?>
</td>
<td>
<? mostrar_calendario($fecmes_siguiente,$feriado3); ?>
</td></tr>
</table>
<table align="center" id="tablaseleccionfecha" style="visibility: hidden; height: 60">
<tr><td class=textochico>Fecha Seleccionada:</td>
	<td><input type=text name=fec_seleccionada size=10 disabled class=textochico></td>
	<td class=textochico>Descripci&oacute;n:</td>	
	<td><input type=text name=des_feriado size=30 class=textochico></td>	
	<td><input type=button class=botonout value="Agregar Feriado" id=bot_aferiado onClick="confirmo('altaferiado');" style="display:none;">
		<input type=button class=botonout value=" Borrar  Feriado "  id=bot_bferiado onClick="confirmo('borraferiado');" style="display:none;">		
	</td>
</tr>
</table>
<table align="center" id="modferiado" style="display:none; height: 60">
<tr><td class=textoerror><span id=texto_modferiado></span></td></tr>
</table>
<table align="center" id="tabladiaslaborables" style="height: 90">
<tr>
<th class=celdatitulo align=center colspan=7>D&iacute;as Laborables</th>
</tr>
<tr>
<th class=celdatitulo>Domingo</th>
<th class=celdatitulo>Lunes</th>
<th class=celdatitulo>Martes</th>
<th class=celdatitulo>Miercoles</th>
<th class=celdatitulo>Jueves</th>
<th class=celdatitulo>Viernes</th>
<th class=celdatitulo>Sabado</th>
</tr>
<tr>
<?
	for ($i=0;$i<7;$i++){
	  echo '<td align=center><input type=checkbox name=dia_'.$i.' id="dia_'.$i.'" checked onClick="document.form.modhabiles.value=1;"></td>';
	}
?>
</tr>
</table>
<table align="center" id="confdiaslaborables" style="display:none; height: 90">
<tr><td class=textoerror>SE HA ACTUALIZADO LOS DIAS LABORABLES</td></tr>
</table>
<table align=center>
<tr><td><input type=button class=botonout value=Confirmar onClick="confirmo('modhabiles');" onMouseOver="this.className = 'botonover';" onMouseOut="this.className = 'botonout';"></td></tr>
</table>
   <input type=hidden name=modhabiles value="<?=$_POST['modhabiles'];?>">
   <input type=hidden name=fecha value="<?=$fecha;?>">
</form>
						<!--Contenido-->


<? require_once("../includes/inc_bottom.php");?>


</BODY></HTML>
<?
}//Cierro if autorizacion
?>
