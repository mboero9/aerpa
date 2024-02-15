<?
require_once("../includes/lib.php");
if (check_auth($usrid, $perid) && check_permission($permiso)) {
?>
<HTML><HEAD>
<? require_once("../includes/inc_header.php"); ?>
<script type="text/javascript">
var newwindow=null;
function inicializar(instancia) { 
    if (instancia!=1) {
		document.form.CodRegOrig.focus();
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
/* verifico que sea un codigo numerico valido */	
	if (document.form.CodRegOrig.value!="") {
		if (!Valido(document.form.CodRegOrig.value,'n')) 	{ 
			ok = false;	
			errores += "- El C�digo del Registro Origen es Invalido\n";
		}
	}else{
	   if (document.form.RegOrig.value==0) {
			ok = false;	
			errores += "- Ingrese el Registro Origen o Seleccionelo.\n";
	   }
	}		
/*	var fecretiro = document.form.FecRetiro.value.substr(6,4)+document.form.FecRetiro.value.substr(3,2)+document.form.FecRetiro.value.substr(0,2);	
	if (document.form.FecRetiro.value=="") {
			ok = false;			
			errores += "- Debe Completar la Fecha de Retiro.\n";									
	}else{
		if (!parseDate(document.form.FecRetiro,'%d/%m/%Y',true)) {
			ok = false;			
			errores += "- El Formato de la Fecha de Retiro no es Valida.\n";									
		}else{
			if (fecretiro>document.form.fecHoy.value) {
								ok = false;			
								errores += "- La Fecha de Retiro es Superior a la Fecha Actual.\n";									
			}			
			if (fecretiro<'20060101') {	
								ok = false;			
								errores += "- La Fecha debe ser mayor al 01/01/2006.\n";									
			}
		}
	}*/
	if (document.form.FecDesde.value=="") {
			ok = false;			
			errores += "- Debe Completar la Fecha Desde.\n";									
	}else{
		if (!parseDate(document.form.FecDesde,'%d/%m/%Y',true)) {
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
		if (!parseDate(document.form.FecHasta,'%d/%m/%Y',true)) {
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
		if (dias><?=getParametro("desde_dias_consulta")?>) {
				ok = false;			
				errores += "- Fecha Desde: No se puede consultar Tr�mites con m�s de <?=getParametro("desde_dias_consulta")?> d�as.\n";										
		};		
		var dias = DifFechas(fechasta,fecdesde);
		if (dias><?=getParametro("interval_ dias_consulta")?>) {
				ok = false;			
				errores += "- La Diferencia entre Fecha Desde y Hasta no puede superar los <?=getParametro("interval_ dias_consulta")?> d�as.\n";										
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
function verCambio(campo) {

	switch(campo) {
	case 1:
/*		if (!Valido(document.form.CodRegOrig.value,'n')) 	{ 
			ok = false;	
			errores += "- El C�digo del Registro Origen es Invalido\n";
		}else{	*/
		   var ok=false;
		   for (i=1; i<=document.form.RegOrig.options.length; i++) {
		      if (document.form.CodRegOrig.value.RTrim()==document.form.RegOrig.options[i].value.RTrim()) {
			  		document.form.RegOrig.selectedIndex=i;
					ok=true;
					break;
			  }//if
	    	}//for
			if (ok) { document.form.FecDesde.focus(); }
	break;
	case 2:
		document.form.CodRegOrig.value=document.form.RegOrig.value.RTrim();
		document.form.FecDesde.focus(); 
	break;
	}
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
<!-- calendario desplegable -->
<script type="text/javascript" language="JavaScript" src="../includes/fecha.js"></script>
<style type="text/css">@import url(../calendar/calendar-win2k-1.css);</style>
<script type="text/javascript" src="../calendar/calendar.js"></script>
<script type="text/javascript" src="../calendar/lang/calendar-es.js"></script>
<script type="text/javascript" src="../calendar/calendar-setup.js"></script>
</HEAD>
<body onLoad="inicializar(<?=isset($_POST['CodRegOrig']);?>);" onFocus="if(newwindow){newwindow.focus();}">
<? require_once("../includes/inc_topleft.php"); 
/* Contenido */
$opcion=$_POST['opcion'];
$pagina="conTramites.php";
require_once('../includes/inc_titulo.php');
?>
<? if (!isset($_POST['CodRegOrig'])) {?>
<div id=divFormDatos>
<form name="form" method=post action="" onSubmit="return false">
<table align="center" width="70%" class=tablaconbordes>
<tr><td>
<table align="center" width="420">
	<tr><td class="celdatexto" width="30%" align="right">Registro Origen:</td>
	    <td width="70%"><input type=text class=textochico name=CodRegOrig size=5 maxlength="5" onChange="verCambio(1);"></td>
	</tr>
	<tr>
		<td class="celdatexto" nowrap align="right"></td>	
		<td colspan=4>
	    <select name=RegOrig id="RegOrig" class=textochico style="width:250px" onChange="verCambio(2);">
	       <option value="0">-- Seleccionar Registro Automotor --</option>
		<? 
			fill_combo("select REG_COD_INT, REG_DESCRIP from REG_AUTOM where REG_TIPO in ('O','A') order by REG_DESCRIP");
		?>		
	    </select>	
		<input type=hidden name=idregmod />	
		</td>	
	</tr>	
<!--	<tr><td class="celdatexto" align="right">Fecha de Retiro:</td>
		<td valign="middle"><input type="text" class=textochico size=8 maxlength="10" name="FecRetiro" id="FecRetiro" value="" style="text-align:right;" onBlur="parseDate(this,'<?php echo(FMT_DATE_CAL); ?>',true);">		
		<img src="../imagenes/calendario.png" name="selfecha1" id="selfecha1" title="Calendario" alt="" style="cursor:pointer;"></td>				
	</tr>	
--></table>
<table align="center" width="420">
	<tr><td class="celdatexto" width="30%" align="right" nowrap>Fecha de Retiro Desde:</td>
		<td valign="middle" width="70%" ><input type="text" class=textochico size=8 maxlength="10" name="FecDesde" id="FecDesde" value="" style="text-align:right;" onBlur="parseDate(this,'<?php echo(FMT_DATE_CAL); ?>',true);">		
		<img src="../imagenes/calendario.png" name="selfecha2" id="selfecha2" title="Calendario" alt="" style="cursor:pointer;"></td>				
	</tr>	
	<tr><td class="celdatexto" align="right" nowrap>Fecha de Retiro Hasta:</td>
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
	<input type=button class="botonout" name=botvolver     value="<?=VOLVER; ?>"  onMouseOver="this.className = 'botonover';" onMouseOut="this.className = 'botonout';" onClick="goMenu('eleTramites.php','opcion', <?=$_POST['opcion'];?>);">
	<input type=reset  class="botonout" name=botreset      value="<?=CANCELO; ?>" onMouseOver="this.className = 'botonover';" onMouseOut="this.className = 'botonout';">
	<input type=submit class="botonout" name=botconfirma   value="<?=CONFIRMO;?>" onMouseOver="this.className = 'botonover';" onMouseOut="this.className = 'botonout';" onClick="validaFormulario();">
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
					 ".$conn->SQLDate(FMT_DATE_DB, "a.TRA_FECHA_RETIRO")." as TRA_FECHA_RETIRO,
				     ".sqlstring($fecdesde->format(FMT_DATE_DB)) . " as FECDESDE,
					 ".sqlstring($fechasta->format(FMT_DATE_DB)) . " as FECHASTA
					 ,a.TRA_NRO_VOUCHER, CASE WHEN a.TRA_NRO_IMP=0 THEN ' ' ELSE 'S' END AS IMPRESO					  
	 			From TRAMITE a
		  inner join REG_AUTOM b on a.REG_CODIGO_ORI = b.REG_CODIGO and b.REG_COD_INT=".sqlstring($_POST['CodRegOrig'])."
			   where a.TRA_FECHA_RETIRO>=".sqldate($fecdesde->format(FMT_DATE_ISO))." 
			     and a.TRA_FECHA_RETIRO<=".sqldate($fechasta->format(FMT_DATE_ISO))." 
			order by a.TRA_FECHA_RETIRO";		  
//echo $Sql;					   
		$rs = $conn->execute($Sql);
		$des_origen=$rs->fields["DES_ORIGEN"];
if (!$rs->EOF) {		
?>
<table align="center" width="70%">
    <tr class=celdatexto><td  colspan=5 align=left>Registro Origen: <b><?=$_POST['CodRegOrig']." - ".$des_origen;?></b></td></tr>		
    <tr class=celdatexto><td  colspan=5 align=left>Fecha de Retiro Desde:<b><?=$_POST["FecDesde"];?></b>&nbsp;&nbsp;Hasta:<b><?=$_POST["FecHasta"];?></b></td></tr>		
</table>
<table align="center" width="70%" class=tablaconbordes>
	<tr class=celdatitulo><th>C�digo</th>
					 	  <th>Registro Automotor</th>
					 	  <th>Nro. de Tramite</th>						  
					 	  <th>Nro. de Voucher</th>
						  <th>Fecha de Retiro</th>
						  <th>Oficio</th>						  						  
	</tr>
<? $clase='fondotabla1';
   $totaltramites=0;
   while (!$rs->EOF) { ?>
	<tr  class=<?=$clase;?> onClick="seleccionado(<?=$i;?>);" onMouseOver="this.className = 'fondoconfirmacion';" onMouseOut="this.className = '<?=$clase;?>';">
		<td class="celdatexto" align=right><?=$rs->fields["REG_COD_INT"];?></td>
		<td class="celdatexto"><?=$rs->fields["DES_ORIGEN"];?></td>
		<td align=right class="celdatexto"><?=$rs->fields["TRA_DOMINIO"];?></td>
		<td align=right class="celdatexto"><?=$rs->fields["TRA_NRO_VOUCHER"];?></td>				
		<td align=center class="celdatexto"><?=$rs->fields["TRA_FECHA_RETIRO"];?></td>
		<td align=center class="celdatexto"><?=$rs->fields["IMPRESO"];?></td>						
	</tr>	
<?
	  $clase = ($clase == 'fondotabla1' ? 'fondotabla2' : 'fondotabla1');		
	  $totaltramites++;
	  $rs->movenext();						 
   }//while ?>	
	<tr class=celdatitulo><th colspan=6 align=left>&nbsp;&nbsp;Total de Tramites:&nbsp;&nbsp;<?=$totaltramites;?></th>
	</tr>   
	</table>
<table align="center" width="69%" class=noprint>
	<tr><td align=center>
	<form name="descarga" action="../export/csv.php" method="post">
	<input type=hidden name="titulo[]" value="<? echo($titulo1); ?>">
	<input type=hidden name="titulo[]" value="<? echo('Registro Origen:'.$_POST['CodRegOrig']." - ".$des_origen); ?>">
	<input type=hidden name="titulo[]" value="<? echo('Fecha de Retiro Desde:'.$_POST["FecDesde"].' Hasta:'.$_POST["FecHasta"]); ?>">		
	<input type=hidden name="titulosql" value="Codigo!0|Registro Automotor!1|Nro.de Tramite!2|Nro. de Voucher!6|Fecha de Retiro!3|Oficio!7">
	<input type=hidden name="sql" value="<? echo($Sql); ?>">
	<input type=hidden name="archivo" value="conTramites.txt">
	<input type=hidden name="archivo2" value="../tramites/conTramites.xml">	
	<input type=hidden name="propiedadesreport" value="<?php echo($propiedadesreport); ?>">
	<input type=button class="botonout" name=botvolver     value="<?=VOLVER; ?>"  onMouseOver="this.className = 'botonover';" onMouseOut="this.className = 'botonout';" onClick="goMenu('conTramites.php');">
	<input type=submit class="botonout" name=botconfirma   value="<?=EXPORTAR; ?>" onMouseOver="this.className = 'botonover';" onMouseOut="this.className = 'botonout';" onClick="validaFormulario();">
	<input type=button class="botonout" name=botconfirma   value="<?=IMPRIMIR;?>" onMouseOver="this.className = 'botonover';" onMouseOut="this.className = 'botonout';"  onClick="newwindow=window.open(href='../export/imprime_ps2.php', this.target, 'width=250,height=140,left=260,top=230,resizable=no');">
	</form>
	</td></tr>
</table>	
<?
}else{//no hay datos
?>	
<table align="center" width="70%" class=tablaconbordes>
<tr><td align=center>No se Encontr&oacute informaci�n</td></tr>
</table>
<table align="center" width="69%" class=noprint>
	<tr><td align=center>
	<input type=button class="botonout" name=botvolver     value="<?=VOLVER; ?>"  onMouseOver="this.className = 'botonover';" onMouseOut="this.className = 'botonout';" onClick="goMenu('conTramites.php');">
	</td></tr>
</table>	
<?
}//if (!$rs->EOF) {	
} 
?>
<!--Contenido-->
<? require_once("../includes/inc_bottom.php"); ?>
</BODY></HTML>
<?
} //Cierro if autorizacion 
?>
