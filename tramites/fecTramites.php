<?
require_once("../includes/lib.php");
if (check_auth($usrid, $perid) && check_permission($permiso)) {
?>
<HTML><HEAD>
<? require_once("../includes/inc_header.php"); ?>
<script type="text/javascript">
function inicializar(instancia) { 
    if (instancia!=1) {
		document.form.FecDesde.focus();
	}
}	
function Valido(valor,tipo) {
	//Validador de campos
//alert("valor:"+valor+"tipo:"+tipo);	
	switch(tipo) {
	  case 'n':
		var validos = /^[0-9]*$/;
		break;
	  case 't':
		var validos = /^[a-zA-Z��������������][a-zA-Z�������������� \/\.\,\_\-]*$/;	
		break;
	  case 'x':
		var validos = /[DTCdtc]/;
		break;
	}
	if(!validos.test(valor)) {
		return false;
	}else{
		return true;
	}
}//Cierro ValidNum
function validaFormulario() {
//	document.form.botconfirma.disabled=true;
	var ok = true;
	var errores = "";
	if (document.form.FecDesde.value=="") {
			ok = false;			
			errores += "- Debe Completar la Fecha Desde.\n";									
	}else{
		if (!parseDate(document.form.FecDesde,'%d-%m-%Y',true)) {
			ok = false;			
			errores += "- El Formato de la Fecha Desde no es Valida.\n";									
		}else{
			var fecdesde = document.form.FecDesde.value.substr(6,4)+document.form.FecDesde.value.substr(3,2)+document.form.FecDesde.value.substr(0,2);	
			if (fecdesde>document.form.fecHoy.value) {
					ok = false;			
					errores += "- La Fecha Desde es Superior a la Fecha Actual.\n";									
			}else{			
/*				if (fecdesde<fecretiro) {
					ok = false;			
					errores += "- La Fecha Desde no debe ser menor a la Fecha de Retiro.\n";									
				}			*/
			}
		}	
	}
	if (document.form.FecHasta.value=="") {
			ok = false;			
			errores += "- Debe Completar la Fecha Hasta.\n";									
	}else{	
		if (!parseDate(document.form.FecHasta,'%d-%m-%Y',true)) {
			ok = false;			
			errores += "- El Formato de la Fecha Desde no es Valida.\n";									
		}else{
			var fechasta = document.form.FecHasta.value.substr(6,4)+document.form.FecHasta.value.substr(3,2)+document.form.FecHasta.value.substr(0,2);	
			if (fechasta>document.form.fecHoy.value) {
					ok = false;			
					errores += "- La Fecha Hasta es Superior a la Fecha Actual.\n";									
			}else{			
				if (fechasta<fecdesde) {
					ok = false;			
					errores += "- La Fecha Hasta no debe ser menor a la Fecha de Desde.\n";									
				}			
			}
		}		
	}	
	if (ok) {
		var dias = DifFechas(document.form.fecHoy.value,fecdesde);
		if (dias>90) {
				ok = false;			
				errores += "- Fecha Desde: No se puede consultar Tr�mites con m�s de 90 d�as.\n";										
		};		
		var dias = DifFechas(fechasta,fecdesde);
		if (dias>31) {
				ok = false;			
				errores += "- La Diferencia entre Fecha Desde y Hasta no puede superar los 31 d�as.\n";										
				errores += "  Diferencia actual: "+dias+" d�as.\n";													
		};
	}
	if (!ok) {
		alert("Hay errores en los datos:\n" + errores);
	}else{             
//		if (ConsAjax('grabar','')) { 
//			setTimeout("goMenu('eleTramites.php','opcion', <?=$_POST['opcion'];?>);",1500);
			document.form.submit();
//		};
	}
	document.form.botconfirma.disabled=false;
	return false;	
} 
String.prototype.RTrim = function(){
	return this.replace(/\s+$/,""); 
}
function ajax(url) {
//alert(url);
	http.open("GET", url, false); 
	http.send(null);
}
function DifFechas(fec1,fec2) {

   var miFecha1 = new Date( fec1.substr(0,4), fec1.substr(4,2), fec1.substr(6,2));
   var miFecha2 = new Date( fec2.substr(0,4), fec2.substr(4,2), fec2.substr(6,2));

   //Resta fechas y redondea
   var diferencia = miFecha1.getTime() - miFecha2.getTime();
   var dias = Math.floor(diferencia / (1000 * 60 * 60 * 24));
   return(dias);
}

</script>
<!-- Objeto Ajax -->
<script type="text/javascript" language="JavaScript" src="../includes/ajaxobjt.js"></script> 
<!-- calendario desplegable -->
<script type="text/javascript" language="JavaScript" src="../includes/fecha.js"></script>
<style type="text/css">@import url(../calendar/calendar-win2k-1.css);</style>
<script type="text/javascript" src="../calendar/calendar.js"></script>
<script type="text/javascript" src="../calendar/lang/calendar-es.js"></script>
<script type="text/javascript" src="../calendar/calendar-setup.js"></script>
</HEAD>
<body onLoad="inicializar(<?=isset($_POST['FecDesde']);?>);" >
<? require_once("../includes/inc_topleft.php"); 
/* Contenido */
$opcion=$_POST['opcion'];
$pagina="fecTramites.php";
require_once('../includes/inc_titulo.php');
?>
<? if (!isset($_POST['FecDesde'])) {?>
<div id=divFormDatos>
<form name="form" method=post action="">
<!--<form name="form" onsubmit="return (validaFormulario());">-->
<table align="center" width="70%" class=tablaconbordes>
<tr><td>
<table align="center" width="420">
	<tr><td class="celdatexto" width="30%" align="right" nowrap>Fecha de R�tiro Desde:</td>
		<td valign="middle" width="70%" ><input type="text" class=textochico size=8 maxlength="10" name="FecDesde" id="FecDesde" value="" style="text-align:right;" onBlur="parseDate(this,'<?php echo(FMT_DATE_CAL); ?>',true);">		
		<img src="../imagenes/calendario.png" name="selfecha2" id="selfecha2" title="Calendario" alt="" style="cursor:pointer;"></td>				
	</tr>	
	<tr><td class="celdatexto" align="right" nowrap>Fecha de R�tiro Hasta:</td>
		<td valign="middle"><input type="text" class=textochico size=8 maxlength="10" name="FecHasta" id="FecHasta" value="" style="text-align:right;" onBlur="parseDate(this,'<?php echo(FMT_DATE_CAL); ?>',true);">		
		<img src="../imagenes/calendario.png" name="selfecha3" id="selfecha3" title="Calendario" alt="" style="cursor:pointer;"></td>				
	</tr>	
</table>
<input type=hidden name=fecHoy value="<?=date('Ymd');?>"/>
<input type=hidden name=usuario value="<?=$usrid;?>"/>
</td></tr>
</table>
<table align="center" width="69%">
	<tr><td align=center>
	<input type=button class="botonout" name=botvolver     value="<?=VOLVER; ?>"  onMouseOver="cambiarClase(this,'botonover');" onMouseOut="cambiarClase(this,'botonout');" onClick="goMenu('fecdTramites.php');">
	<input type=reset  class="botonout" name=botreset      value="<?=CANCELO; ?>" onMouseOver="cambiarClase(this,'botonover');" onMouseOut="cambiarClase(this,'botonout');">
	<input type=submit class="botonout" name=botconfirma   value="<?=CONFIRMO;?>" onMouseOver="cambiarClase(this,'botonover');" onMouseOut="cambiarClase(this,'botonout');" onClick="validaFormulario();">
	</td></tr>
</table>
</form>
</div>
<script type="text/javascript">
	Calendar.setup( { inputField: "FecDesde", ifFormat: "%d/%m/%Y", button: "selfecha2" } );
	Calendar.setup( { inputField: "FecHasta", ifFormat: "%d/%m/%Y", button: "selfecha3" } );		
</script>
<? }else{ 
		$fecdesde     = new Date($_POST["FecDesde"]);
		$fechasta     = new Date($_POST["FecHasta"]);		
		$Sql="Select b.REG_COD_INT, 
					 b.REG_DESCRIP as DES_ORIGEN,
					 a.TRA_DOMINIO, 
					 ".$conn->SQLDate(FMT_DATE_DB, "a.TRA_FECHA_RETIRO")." as TRA_FECHA_RETIRO					 
	 			From TRAMITE a
		  inner join REG_AUTOM b on a.REG_CODIGO_ORI = b.REG_CODIGO 
			   where a.TRA_FECHA_RETIRO>=".sqldate($fecdesde->format(FMT_DATE_ISO))." 
			     and a.TRA_FECHA_RETIRO<=".sqldate($fechasta->format(FMT_DATE_ISO))." 
			order by b.REG_COD_INT, a.TRA_FECHA_RETIRO";		  
//echo $Sql;					   
		$rs = $conn->execute($Sql);
if (!$rs->EOF) {
	$corte1_ant=$rs->fields["REG_COD_INT"].substr($rs->fields["TRA_FECHA_RETIRO"],0,2);	
	$contador=0;	
	$total=0;
?>
<table align="center" width="70%" class=tablaconbordes>
	<tr class=celdatitulo><th>C�digo</th>
					 	  <th>Registro Automotor</th>
					 	  <th>Nro. de Tramite</th>						  
					 	  <th>Fecha de Retiro</th>						  						  
	</tr>
<? $clase='fondotabla1';
   while (!$rs->EOF) { 
	   $corte1_act=$rs->fields["REG_COD_INT"].substr($rs->fields["TRA_FECHA_RETIRO"],0,2); 
	   if ($corte1_ant!=$corte1_act) {
?>
	<tr class=celdatitulo><td colspan=2 align=center>Total por Fecha de Retiro:</th>
					 	  <td colspan=2 align=left><?=$contador;?></td>
	</tr>
<?
	       $corte1_ant=$corte1_act;
		   $total+=$contador;
		   $contador=0;
	   }
       $contador++;
?>	  
	<tr class=<?=$clase;?> onClick="seleccionado(<?=$i;?>);" onMouseOver="cambiarClase(this,'fondoconfirmacion');" onMouseOut="cambiarClase(this,'<?=$clase;?>');">
		<td align=right class=celdatexto><?=$rs->fields["REG_COD_INT"];?></td>
		<td class=celdatexto><?=$rs->fields["DES_ORIGEN"];?></td>
		<td align=right class=celdatexto><?=$rs->fields["TRA_DOMINIO"];?></td>				
		<td align=center class=celdatexto><?=$rs->fields["TRA_FECHA_RETIRO"];?></td>						
<!--        <td><?=$corte1_ant.'--'.$contador;?></td>		-->
	</tr>	
<?
 	 	if ($clase=='fondotabla1') { $clase='fondotabla2';
    	} else { $clase='fondotabla1'; }
	  $rs->movenext();						 
   }//while ?>	
	<tr class=celdatitulo><td colspan=2 align=center>Total por Fecha de Retiro:</th>
					 	  <td colspan=2 align=left><?=$contador;?></td>
	</tr>   
<?   $total+=$contador;	?>
	<tr class=celdatitulo><td colspan=2 align=center>Total General:</td>
					 	  <td colspan=2 align=left><?=$total;?></td>
	</tr>   
	</table>
<?
}else{
?>	
<table align="center" width="70%" class=tablaconbordes>
<tr><td>No ha informaci�n</td></tr>
</table>
<?
}
?>	

<table align="center" width="69%" class=noprint>
	<tr><td align=center>
	<form name="descarga" action="../export/csv.php" method="post">
	<input type=hidden name="titulo[]" value="Reporte de Registro Automotor">
	<input type=hidden name="titulosql" value="Codigo.!0|Registro Automotor!1|Nro.de Tramites!2|Fecha de Retiro!3">
	<input type=hidden name="sql[]" value="<? echo($Sql); ?>">
	<input type=hidden name="archivo" value="conTramites.txt">
	<input type=button class="botonout" name=botvolver     value="<?=VOLVER; ?>"  onMouseOver="cambiarClase(this,'botonover');" onMouseOut="cambiarClase(this,'botonout');" onClick="goMenu('conTramites.php');">
	<input type=submit class="botonout" name=botconfirma   value="<?=EXPORTAR; ?>" onMouseOver="cambiarClase(this,'botonover');" onMouseOut="cambiarClase(this,'botonout');" onClick="validaFormulario();">
<!--	<input type=button class="botonout" name=botconfirma   value="<?=IMPRIMIR;?>" onMouseOver="cambiarClase(this,'botonover');" onMouseOut="cambiarClase(this,'botonout');" onClick="Javascript:window.print();">-->
	<input type=button class="botonout" name=botconfirma   value="<?=IMPRIMIR;?>" onMouseOver="cambiarClase(this,'botonover');" onMouseOut="cambiarClase(this,'botonout');" onClick="document.impresion.submit()">		
	</form>
<?	$tmpfname = tempnam("", "tramite");	?>
	<form name="impresion" action="imprime.php" method=post>
	<input type=hidden name="titulo[]" value="Reporte de Registro Automotor">
	<input type=hidden name="titulosql" value="Codigo.!0|Registro Automotor!1|Nro.de Tramites!2|Fecha de Retiro!3">
	<input type=hidden name="sql[]" value="<? echo($Sql); ?>">
	<input type=hidden name="archivo" value="<? echo($tmpfname); ?>">		
	</form>
	</td></tr>
</table>
<? } ?>
<div id=divMensaje style="display:none">
<table cellspacing="0" width=578 cellpadding="0" align=center class=tablaconbordes>
	<tr><td align="center" valign="middle" height="80" class=grabando>
	<div id=grabado></div>
</td></tr>
</table>
</div>
<!--Contenido-->
<? require_once("../includes/inc_bottom.php"); ?>
</BODY></HTML>
<?
} //Cierro if autorizacion 
?>