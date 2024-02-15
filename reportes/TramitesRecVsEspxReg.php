<?
require_once("../includes/lib.php");
if (check_auth($usrid, $perid) && check_permission($permiso)) {
?>
<HTML><HEAD>
<? require_once("../includes/inc_header.php"); ?>
<script type="text/javascript">
var newwindow=null;
function Valido(valor,tipo) {
	//Validador de campos
//alert("valor:"+valor+"tipo:"+tipo);	
	switch(tipo) {
	  case 'n':
		var validos = /^[0-9]*$/;
		break;
	  case 't':
		var validos = /^[a-zA-ZáÁéÉíÍóÓúÚüÜñÑ][a-zA-ZáÁéÉíÍóÓúÚüÜñÑ \/\.\,\_\-]*$/;	
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
    if (document.form.region.value==0) {
			ok = false;	
			errores += "- Seleccione la Región.\n";
	}
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
				errores += "- Fecha Desde: No se puede consultar Trámites con más de <?=getParametro("desde_dias_consulta")?> días.\n";										
		};		
		var dias = DifFechas(fechasta,fecdesde);
		if (dias><?=getParametro("interval_ dias_consulta")?>) {
				ok = false;			
				errores += "- La Diferencia entre Fecha Desde y Hasta no puede superar los <?=getParametro("interval_ dias_consulta")?> días.\n";										
				errores += "  Diferencia actual: "+dias+" días.\n";													
		};
	}
	if (!ok) {
		alert("Hay errores en los datos:\n" + errores);
	}else{             
		document.form.desregion.value=document.form.region.options[document.form.region.selectedIndex].text;
		document.form.submit();
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
<body onFocus="if(newwindow){newwindow.focus();}">
<? require_once("../includes/inc_topleft.php");
/* Contenido */
$opcion=$_POST['opcion'];
$pagina=basename($_SERVER['SCRIPT_FILENAME']);
require_once('../includes/inc_titulo.php');
?>
<? if (!isset($_POST['region'])) {?>
<form name="form" method=post action="" onSubmit="return false">
<table align="center" width="70%" class=tablaconbordes>
<tr><td>
<table align="center" width="420">
	<tr><td class="celdatexto" width="30%" align="right" nowrap>Región:</td>
		<td colspan=4>
	    <select name=region id="region" class=textochico style="width:250px">
	       <option value="0">-- Seleccionar la Regi&oacute;n --</option>
		<? 
			fill_combo("select RGI_CODIGO, RGI_DESCRIP from REGION order by RGI_DESCRIP", $_POST["region"]);
		?>		
	    </select>	
		<input type=hidden name=desregion />	
		</td>	
	</tr>	
</table>
<table align="center" width="420">
	<tr><td class="celdatexto" width="30%" align="right" nowrap>Fecha de Retiro Desde:</td>
		<td valign="middle" width="70%" ><input type="text" class=textochico size=8 maxlength="10" name="FecDesde" id="FecDesde" value="<?=$_POST["FecDesde"];?>" style="text-align:right;" onBlur="parseDate(this,'<?php echo(FMT_DATE_CAL); ?>',true);">		
		<img src="../imagenes/calendario.png" name="selfecha2" id="selfecha2" title="Calendario" alt="" style="cursor:pointer;"></td>				
	</tr>	
	<tr><td class="celdatexto" align="right" nowrap>Fecha de Retiro Hasta:</td>
		<td valign="middle"><input type="text" class=textochico size=8 maxlength="10" name="FecHasta" id="FecHasta" value="<?=$_POST["FecHasta"];?>" style="text-align:right;" onBlur="parseDate(this,'<?php echo(FMT_DATE_CAL); ?>',true);">		
		<img src="../imagenes/calendario.png" name="selfecha3" id="selfecha3" title="Calendario" alt="" style="cursor:pointer;"></td>				
	</tr>
	<tr><td class="celdatexto" align="right" nowrap>Solo Tr&aacute;mites con m&aacute;s de <?=(MAX_ENTREGA*36);?>hs. h&aacute;biles:</td>
		<td align="left" ><input type="checkbox" name="maxretiro" id="maxretiro" value="1" /></td>
	</tr>	
</table>
<input type=hidden name=fecHoy value="<?=date('Ymd');?>"/>
<input type=hidden name=usuario value="<?=$usrid;?>"/>
</td></tr>
</table>
<table align="center" width="69%">
	<tr><td align=center>
	<input type=button class="botonout" name=botvolver     value="<?=VOLVER; ?>"  onMouseOver="this.className = 'botonover';" onMouseOut="this.className = 'botonout';" onClick="goMenu('<?=basename($_SERVER['SCRIPT_FILENAME']);?>');">
	<input type=reset  class="botonout" name=botreset      value="<?=CANCELO; ?>" onMouseOver="this.className = 'botonover';" onMouseOut="this.className = 'botonout';">
	<input type=submit class="botonout" name=botconfirma   value="<?=CONFIRMO;?>" onMouseOver="this.className = 'botonover';" onMouseOut="this.className = 'botonout';" onClick="validaFormulario();">	
	</td></tr>
</table>
</form>
<script type="text/javascript">
	Calendar.setup( { inputField: "FecDesde", ifFormat: "%d/%m/%Y", button: "selfecha2" } );
	Calendar.setup( { inputField: "FecHasta", ifFormat: "%d/%m/%Y", button: "selfecha3" } );
</script>
<? }else{
	$fecdesde     = new Date($_POST["FecDesde"]);
	$fechasta     = new Date($_POST["FecHasta"]);
	if ($_POST["maxretiro"]==1) 
	{//selecciono filtro con n horas habiles

			$contitmaxretiro="\n"."PageHeader|12|Solo Tr&aacute;mites con m&aacute;s de ".(MAX_ENTREGA*36)." hs. h&aacute;biles";
					
			$Sql="Set Nocount On 
					Declare 
					@ultfecha datetime, 
					@hoy datetime 
					Select @hoy = ". $conn->sysDate ." 
					Select @ultfecha = @hoy 
					While dbo.DifDias(@ultfecha,@hoy) < ".sqlint(MAX_ENTREGA)." 
					Begin 
					Select @ultfecha = DateAdd(day, -1, @ultfecha) 
					End 
					/* A esta altura, @ultfecha tiene la fecha desde la cual hay 3 dias habiles hasta hoy */ 
					Select a.TRA_DOMINIO,
						 a.TRA_NRO_VOUCHER,
						 ".$conn->SQLDate(FMT_DATE_DB, "a.TRA_FECHA_RETIRO")." as TRA_FECHA_RETIRO, 
						 ".$conn->SQLDate(FMT_DATE_DB, "a.TRA_FECHA_ENTREGA")." as TRA_FECHA_ENTREGA, 					 	 
						 d.MOT_DESCRIP,
						 Case When a.TRA_FECHA_ENTREGA is null 
							  Then dbo.DifDias(a.TRA_FECHA_RETIRO,". $conn->sysDate .")+1  
							  Else dbo.DifDias(a.TRA_FECHA_RETIRO,a.TRA_FECHA_ENTREGA)+1 end as dif_dias,
						  CASE WHEN a.TRA_NRO_IMP=0 THEN ' ' ELSE 'S' END AS IMPRESO,   
						  b.REG_COD_INT, 
						  b.REG_DESCRIP as DES_ORIGEN
					From TRAMITE a
			  inner join REG_AUTOM b on a.REG_CODIGO_DES = b.REG_CODIGO and b.RGI_CODIGO=".sqlstring($_POST['region'])."
			  left  join REGION    c on c.RGI_CODIGO = ".sqlstring($_POST['region'])."
			  left  join MOTIVO_DEV d on a.MOT_CODIGO = d.MOT_CODIGO		  
				   where a.TRA_FECHA_RETIRO>=".sqldate($fecdesde->format(FMT_DATE_ISO))." 
					 and a.TRA_FECHA_RETIRO<=".sqldate($fechasta->format(FMT_DATE_ISO))."
					 and a.TRA_FECHA_RETIRO < @ultfecha 
					 and (dbo.DifDias(a.TRA_FECHA_RETIRO,a.TRA_FECHA_ENTREGA) > " . sqlint(MAX_ENTREGA) . " OR a.TRA_FECHA_ENTREGA is null)  
				order by a.TRA_FECHA_RETIRO DESC, b.REG_COD_INT, a.TRA_NRO_VOUCHER 
				Set Nocount off";
		//echo($sql);
		}
		else
		{
			$Sql="Select a.TRA_DOMINIO, 		 
						 a.TRA_NRO_VOUCHER, 
						 ".$conn->SQLDate(FMT_DATE_DB, "a.TRA_FECHA_RETIRO")." as TRA_FECHA_RETIRO, 
						 ".$conn->SQLDate(FMT_DATE_DB, "a.TRA_FECHA_ENTREGA")." as TRA_FECHA_ENTREGA, 					 	 
						 d.MOT_DESCRIP, 
						 CASE WHEN a.TRA_NRO_IMP=0 THEN ' ' ELSE 'S' END AS IMPRESO,
						 b.REG_COD_INT, 
						 b.REG_DESCRIP as DES_ORIGEN   
					From TRAMITE a
			  inner join REG_AUTOM b on a.REG_CODIGO_DES = b.REG_CODIGO and b.RGI_CODIGO=".sqlstring($_POST['region'])."
			  left  join REGION    c on c.RGI_CODIGO = ".sqlstring($_POST['region'])."
			  left  join MOTIVO_DEV d on a.MOT_CODIGO = d.MOT_CODIGO		  
				   where a.TRA_FECHA_RETIRO>=".sqldate($fecdesde->format(FMT_DATE_ISO))." 
					 and a.TRA_FECHA_RETIRO<=".sqldate($fechasta->format(FMT_DATE_ISO))." 
				order by a.TRA_FECHA_RETIRO DESC, b.REG_COD_INT, a.TRA_NRO_VOUCHER";
		}//Fin else 72hs
			$rs = $conn->execute($Sql);		
	//echo $Sql;							
			$propiedadesreport =
				"PageHeader|6|".$_POST["region"]."\n" .
				"PageHeader|7|".$_POST["desregion"]."\n" .
				"PageHeader|9|".$_POST["FecDesde"]."\n" .
				"PageHeader|11|".$_POST["FecHasta"].$contitmaxretiro;
	if (!$rs->EOF) {
	?>
	<table align="center" width="70%">
		<tr class=celdatexto>
		<td  colspan=<?=($_POST["maxretiro"]==1)?'2':'4'; ?> align=left>Regi&oacute;n: <b><?=$_POST['region']." - ".$_POST["desregion"];?></b></td>
		<?=($_POST["maxretiro"]==1)?'<td class="celdatexto" align="right" nowrap><b>Solo Tr&aacute;mites con m&aacute;s de '.(MAX_ENTREGA*36).' hs. h&aacute;biles:</b></td>':'';?>
		</tr>
		<tr class=celdatexto><td  colspan=4 align=left>Fecha de Retiro Desde:<b><?=$_POST["FecDesde"];?></b>&nbsp;&nbsp;Hasta:<b><?=$_POST["FecHasta"];?></b></td></tr>
	</table>
	<table align="center" width="70%" class=tablaconbordes>
		<tr class=celdatitulo><th width="15%">Nro. de Tr&aacute;mite</th>
							  <th width="15%">Nro. de Voucher</th>
							  <th width="15%">Fecha de Retiro</th>
							  <th width="15%">Fecha de Entregado o Devoluci&oacute;n</th>
							  <th width="40%">Motivo</th>
							  <th width="20%">Oficio</th>
							  <? if ($_POST["maxretiro"]==1) {		?>
					 	  	  <th width="20%">D&iacute;as</th>
		<? } ?>
							  
		</tr>
	<? $clase='fondotabla1';
	   while (!$rs->EOF) { ?>
		<tr  class=<?=$clase;?> onClick="seleccionado(<?=$i;?>);" onMouseOver="this.className = 'fondoconfirmacion';" onMouseOut="this.className = '<?=$clase;?>';">
			<td class=celdatexto align=right><?=$rs->fields["TRA_DOMINIO"];?></td>
			<td class=celdatexto align=center><?=$rs->fields["TRA_NRO_VOUCHER"];?></td>
			<td class=celdatexto align=center><?=$rs->fields["TRA_FECHA_RETIRO"];?></td>
			<td class=celdatexto align=center><?=$rs->fields["TRA_FECHA_ENTREGA"];?></td>
			<td class=celdatexto align="center" ><?=$rs->fields["MOT_DESCRIP"];?></td>
			<td class=celdatexto align="center" ><?=$rs->fields["IMPRESO"];?></td>
			<? if ($_POST["maxretiro"]==1) {		?>
			<td class=celdatexto align="center"><?=$rs->fields["dif_dias"];?></td>		
		<? } ?>
		</tr>
	<?
		  $clase = ($clase == 'fondotabla1' ? 'fondotabla2' : 'fondotabla1');		
		  $rs->movenext();
	   }//while ?>
	</table>
	<table align="center" width="69%" class=noprint>
		<tr><td align=center>
		<form name="descarga" action="../export/csv.php" method="post">
		<input type=hidden name="titulo[]" value="<? echo($titulo1); ?>">
		<input type=hidden name="titulo[]" value="<? echo('Región:'.$_POST['region']." - ".$_POST["desregion"]); ?>">
		<input type=hidden name="titulo[]" value="<? echo('Fecha de Retiro Desde:'.$_POST["FecDesde"].' Hasta:'.$_POST["FecHasta"]); ?>">
		<input type=hidden name="titulosql" value="Nro.de Tramite!0|Nro. de Voucher!1|Fecha de Retiro!2|Fecha de Entrega!3|Motivo!4|<?=($_POST["maxretiro"]==1)?'Dias!5|Oficio!6':'Oficio!5'; ?>">
		<input type=hidden name="sql" value="<? echo($Sql); ?>">
		<input type=hidden name="archivo" value="TramitesRecVsEspxReg.txt">
		<input type=hidden name="archivo2" value="<?=($_POST["maxretiro"]==1)?'../reportes/TramitesRecVsEspxReg72.xml':'../reportes/TramitesRecVsEspxReg.xml'; ?>">
		<input type=hidden name="propiedadesreport" value="<?php echo($propiedadesreport); ?>">
		<input type=button class="botonout" name=botvolver     value="<?=VOLVER; ?>"  onMouseOver="this.className = 'botonover';" onMouseOut="this.className = 'botonout';" onClick="goMenu('<?=basename($_SERVER['SCRIPT_FILENAME']);?>');">
		<input type=submit class="botonout" name=botconfirma   value="<?=EXPORTAR; ?>" onMouseOver="this.className = 'botonover';" onMouseOut="this.className = 'botonout';">
		<input type=button class="botonout" name=botconfirma   value="<?=IMPRIMIR;?>" onMouseOver="this.className = 'botonover';" onMouseOut="this.className = 'botonout';"  onClick="newwindow=window.open(href='../export/imprime_ps2.php', this.target, 'width=250,height=140,left=260,top=230,resizable=yes');">
		</form>
		</td></tr>
	</table>
	<?
	}else{//no hay datos
	?>
	<table align="center" width="70%" class=tablaconbordes>
	<tr><td align=center>No se Encontr&oacute información</td></tr>
	</table>
	<table align="center" width="69%" class=noprint>
		<tr><td align=center>
		<input type=button class="botonout" name=botvolver     value="<?=VOLVER; ?>"  onMouseOver="this.className = 'botonover';" onMouseOut="this.className = 'botonout';" onClick="goMenu('<?=basename($_SERVER['SCRIPT_FILENAME']);?>');">
		</td></tr>
	</table>
<?
}//fin else region
}
?>
<!--Contenido-->
<? require_once("../includes/inc_bottom.php"); ?>
</BODY></HTML>
<?
} //Cierro if autorizacion
?>
