<?
set_time_limit(0);

require_once("../includes/lib.php");
if (check_auth($usrid, $perid) && check_permission($permiso)) {
// Set page
if (is_numeric($_POST["page"])) {
	$page = $_POST["page"];
} else {
	$page = 1;
} // end page
?>
<HTML><HEAD>
<? require_once("../includes/inc_header.php"); ?>
<script type="text/javascript">
var newwindow=null;
function inicializar(instancia) {
    if (instancia!=1) {
		document.form.CodRegDest.focus();
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
/* verifico que sea un codigo numerico valido */
	if (document.form.CodRegDest.value!="") {
		if (!Valido(document.form.CodRegDest.value,'n')) 	{
			ok = false;
			errores += "- El Código del Registro Destino es Invalido\n";
		}
	}else{
	   if (document.form.RegDest.value==0) {
			ok = false;
			errores += "- Ingrese el Registro Destino o Seleccionelo.\n";
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
		   var ok=false;
		   for (i=1; i<=document.form.RegDest.options.length; i++) {
		      if (document.form.CodRegDest.value.RTrim()==document.form.RegDest.options[i].value.RTrim()) {
			  		document.form.RegDest.selectedIndex=i;
					ok=true;
					break;
			  }//if
	    	}//for
			if (ok) { document.form.FecDesde.focus(); }
	break;
	case 2:
		document.form.CodRegDest.value=document.form.RegDest.value.RTrim();
		document.form.FecDesde.focus();
	break;
	}
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
function go_page(page) {
	document.form.page.value = page;
	document.form.submit();
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
<body onLoad="inicializar(<?=isset($_POST['CodRegDest']);?>);" onFocus="if(newwindow){newwindow.focus();}">
<? require_once("../includes/inc_topleft.php");
/* Contenido */
$opcion=$_POST['opcion'];
$pagina=basename($_SERVER['SCRIPT_FILENAME']);
require_once('../includes/inc_titulo.php');
?>
<? if (!isset($_POST['CodRegDest'])) {?>
<div id=divFormDatos>
<form name="form" method=post action="" onSubmit="return false">
<!--<form name="form" onsubmit="return (validaFormulario());">-->
<table align="center" width="70%" class=tablaconbordes>
<tr><td>
<table align="center" width="420">
	<tr><td class="celdatexto" width="40%" align="right">Registro Destino:</td>
	    <td width="60%"><input type=text class=textochico name=CodRegDest size=5 maxlength="5" onChange="verCambio(1);"></td>
	</tr>
	<tr>
		<td class="celdatexto" nowrap align="right"></td>
		<td colspan=4>
	    <select name=RegDest id="RegDest" class=textochico style="width:250px" onChange="verCambio(2);">
	       <option value="0">-- Seleccionar Registro Automotor --</option>
		<?
			fill_combo("select REG_COD_INT, REG_DESCRIP from REG_AUTOM where REG_TIPO in ('D','A') order by REG_DESCRIP");
		?>
	    </select>
		<input type=hidden name=idregmod />
		</td>
	</tr>
	<tr><td class="celdatexto" align="right" nowrap>Fecha de Retiro Desde:</td>
		<td valign="middle" width="60%" ><input type="text" class=textochico size=8 maxlength="10" name="FecDesde" id="FecDesde" value="" style="text-align:right;" onBlur="parseDate(this,'<?php echo(FMT_DATE_CAL); ?>',true);">
		<img src="../imagenes/calendario.png" name="selfecha2" id="selfecha2" title="Calendario" alt="" style="cursor:pointer;"></td>
	</tr>
	<tr><td class="celdatexto" align="right" nowrap>Fecha de Retiro Hasta:</td>
		<td valign="middle"><input type="text" class=textochico size=8 maxlength="10" name="FecHasta" id="FecHasta" value="" style="text-align:right;" onBlur="parseDate(this,'<?php echo(FMT_DATE_CAL); ?>',true);">
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
	<input type=button class="botonout" name=botvolver     value="<?=VOLVER; ?>"  onMouseOver="this.className = 'botonover';" onMouseOut="this.className = 'botonout';" onClick="goMenu('<?=basename($_SERVER['SCRIPT_FILENAME']);?>','opcion', <?=$_POST['opcion'];?>);">
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
		$conmaxretiro="";
		if ($_POST["maxretiro"]==1) {//selecciono filtro con n horas habiles
			$contitmaxretiro="\n"."MainGroup|GroupHeader|7|Solo Tr&aacute;mites con m&aacute;s de ".(MAX_ENTREGA*36)." hs. h&aacute;biles.";
			if (!is_numeric($_POST["rows"])) {
			$Sql="	Set Nocount On 
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
		      Select Count(*) as CANTIDAD 
			  	From TRAMITE a 
			  inner join REG_AUTOM b on a.REG_CODIGO_DES = b.REG_CODIGO and b.REG_COD_INT=".sqlstring($_POST['CodRegDest'])."
				   where a.TRA_FECHA_RETIRO >= ".sqldate($fecdesde->format(FMT_DATE_ISO))."
					 and a.TRA_FECHA_RETIRO <= ".sqldate($fechasta->format(FMT_DATE_ISO))."  
					 and a.TRA_FECHA_RETIRO < @ultfecha 
					 and (dbo.DifDias(a.TRA_FECHA_RETIRO,a.TRA_FECHA_ENTREGA) > " . sqlint(MAX_ENTREGA) . " OR a.TRA_FECHA_ENTREGA is null)
					 Set Nocount off";								 
				$rs = $conn->Execute($Sql);
				//echo $Sql;
				$totalrows = $rs->fields["CANTIDAD"];
			} else {
				$totalrows = $_POST["rows"];
			} // end if rows	
			$Sql="	Set Nocount On 
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
				  Select b.REG_COD_INT,
						 b.REG_DESCRIP as DES_DESTINO,
						 a.TRA_DOMINIO,
						 ".$conn->SQLDate(FMT_DATE_DB, "a.TRA_FECHA_RETIRO")." as TRA_FECHA_RETIRO,
						 ".$conn->SQLDate(FMT_DATE_DB, "a.TRA_FECHA_ENTREGA")." as TRA_FECHA_ENTREGA,
						 c.MOT_DESCRIP,
						 Case When a.TRA_FECHA_ENTREGA is null 
							  Then dbo.DifDias(a.TRA_FECHA_RETIRO,". $conn->sysDate .")+1  
							  Else dbo.DifDias(a.TRA_FECHA_RETIRO,a.TRA_FECHA_ENTREGA)+1 end as dif_dias 
						,a.TRA_NRO_VOUCHER, CASE WHEN a.TRA_NRO_IMP=0 THEN ' ' ELSE 'S' END AS IMPRESO  						  
					From TRAMITE a
			  inner join REG_AUTOM b on a.REG_CODIGO_DES = b.REG_CODIGO and b.REG_COD_INT=".sqlstring($_POST['CodRegDest'])."
			  left  join MOTIVO_DEV c on a.MOT_CODIGO = c.MOT_CODIGO
				   where a.TRA_FECHA_RETIRO >= ".sqldate($fecdesde->format(FMT_DATE_ISO))."
					 and a.TRA_FECHA_RETIRO <= ".sqldate($fechasta->format(FMT_DATE_ISO))."  
					 and a.TRA_FECHA_RETIRO < @ultfecha 
					 and (dbo.DifDias(a.TRA_FECHA_RETIRO,a.TRA_FECHA_ENTREGA) > " . sqlint(MAX_ENTREGA) . " OR a.TRA_FECHA_ENTREGA is null) 
				order by a.TRA_FECHA_RETIRO DESC, a.TRA_DOMINIO, a.TRA_NRO_VOUCHER
				Set Nocount off";
				//echo($Sql);			
		}else{//NO selecciono filtro con n horas habiles
			if (!is_numeric($_POST["rows"])) {
			$Sql="Select Count(*) as CANTIDAD 
					From TRAMITE a
			  inner join REG_AUTOM b on a.REG_CODIGO_DES = b.REG_CODIGO and b.REG_COD_INT=".sqlstring($_POST['CodRegDest'])."
			  left  join MOTIVO_DEV c on a.MOT_CODIGO = c.MOT_CODIGO
				   where a.TRA_FECHA_RETIRO>=".sqldate($fecdesde->format(FMT_DATE_ISO))."
					 and a.TRA_FECHA_RETIRO<=".sqldate($fechasta->format(FMT_DATE_ISO));
				$rs = $conn->Execute($Sql);
				$totalrows = $rs->fields["CANTIDAD"];
			} else {
				$totalrows = $_POST["rows"];
			} // end if rows	
			$Sql="Select b.REG_COD_INT,
						 b.REG_DESCRIP as DES_DESTINO,
						 a.TRA_DOMINIO,
						 ".$conn->SQLDate(FMT_DATE_DB, "a.TRA_FECHA_RETIRO")." as TRA_FECHA_RETIRO,
						 ".$conn->SQLDate(FMT_DATE_DB, "a.TRA_FECHA_ENTREGA")." as TRA_FECHA_ENTREGA,
						 c.MOT_DESCRIP 
						 $dif_dias 
						 ,a.TRA_NRO_VOUCHER, CASE WHEN a.TRA_NRO_IMP=0 THEN ' ' ELSE 'S' END AS IMPRESO  
					From TRAMITE a
			  inner join REG_AUTOM b on a.REG_CODIGO_DES = b.REG_CODIGO and b.REG_COD_INT=".sqlstring($_POST['CodRegDest'])."
			  left  join MOTIVO_DEV c on a.MOT_CODIGO = c.MOT_CODIGO
				   where a.TRA_FECHA_RETIRO>=".sqldate($fecdesde->format(FMT_DATE_ISO))."
					 and a.TRA_FECHA_RETIRO<=".sqldate($fechasta->format(FMT_DATE_ISO)). 
					 $conmaxretiro . "
				order by a.TRA_FECHA_RETIRO DESC, a.TRA_DOMINIO, a.TRA_NRO_VOUCHER";			
		}//if selecciono filtro con n horas habiles
//echo $Sql;
$rs = $conn->PageExecute($Sql,20,$page);
//		$rs = $conn->execute($Sql);
		$des_destino=$rs->fields["DES_DESTINO"];
		$propiedadesreport =
			"MainGroup|GroupHeader|4|".$_POST["FecDesde"]."\n" .
			"MainGroup|GroupHeader|6|".$_POST["FecHasta"].$contitmaxretiro;
if (!$rs->EOF) {
?>
<?php if ($totalrows) { ?>
<table align="center" width="30%">
<tr><td colspan="4"><?php navigationbar($page,$totalrows); ?></td></tr>
</table>
<?php } // end if totalrows ?>
<table align="center" width="70%">
    <tr class=celdatexto><td align=left>Registro Destino: <b><?=$_POST['CodRegDest']." - ".$des_destino;?></b>
    <tr class=celdatexto><td align=left>Fecha de Retiro Desde:<b><?=$_POST["FecDesde"];?></b>&nbsp;&nbsp;Hasta:<b><?=$_POST["FecHasta"];?></b></td>
<?		if ($_POST["maxretiro"]==1) {	 ?>
         <td align=right><b>Solo Tr&aacute;mites con m&aacute;s de <?=(MAX_ENTREGA*36);?>hs. h&aacute;biles:</b></td>
<? 		} ?>
	</tr>
</table>
<table align="center" width="70%" class=tablaconbordes>
	<tr class=celdatitulo><th width="20%">Nro. de Tr&aacute;mite</th>
						  <th width="20%">Nro. de Voucher</th>
					 	  <th width="15%">Fecha de Retiro</th>
						  <th width="15%">Fecha de Entregado o Devoluci&oacute;n</th>
					 	  <th width="50%">Motivo</th>
						  <th width="50%">Oficio</th>
		<? if ($_POST["maxretiro"]==1) {		?>
					 	  <th>D&iacute;as</th>
		<? } ?>
						  
	</tr>
<form name="form" method=post action="" onSubmit="return false">
	<input type="hidden" name="page" value="">
	<input type="hidden" name="CodRegDest" value="<?php echo($_POST['CodRegDest']); ?>">	
	<input type="hidden" name="FecDesde" value="<?php echo($_POST['FecDesde']); ?>">	
	<input type="hidden" name="FecHasta" value="<?php echo($_POST['FecHasta']); ?>">
	<input type="hidden" name="maxretiro" value="<?php echo($_POST["maxretiro"]); ?>">				
	<input type="hidden" name="rows" value="<?php echo($totalrows); ?>">
</form>		
<? $clase='fondotabla1';
   while (!$rs->EOF) { ?>
	<tr  class=<?=$clase;?> onClick="seleccionado(<?=$i;?>);" onMouseOver="this.className = 'fondoconfirmacion';" onMouseOut="this.className = '<?=$clase;?>';">
		<td align=right class="celdatexto"><?=$rs->fields["TRA_DOMINIO"];?></td>
		<td align=right class="celdatexto"><?=$rs->fields["TRA_NRO_VOUCHER"];?></td>
		<td align=center class="celdatexto"><?=$rs->fields["TRA_FECHA_RETIRO"];?></td>
		<td align=center class="celdatexto"><?=$rs->fields["TRA_FECHA_ENTREGA"];?></td>
		<td class="celdatexto" align="center"><?=$rs->fields["MOT_DESCRIP"];?></td>
		<td class="celdatexto" align="center"><?=$rs->fields["IMPRESO"];?></td>
		<? if ($_POST["maxretiro"]==1) {		?>
		<td><?=$rs->fields["dif_dias"];?></td>		
		<? } ?>
	</tr>
<?
	  $clase = ($clase == 'fondotabla1' ? 'fondotabla2' : 'fondotabla1');		
	  $rs->movenext();
   }//while 
	  $cols = ($_POST["maxretiro"]==1 ? '7' : '6');		   
   ?>
	<tr class=celdatitulo><th colspan=<?=$cols;?> align=left>&nbsp;&nbsp;Total de Tramites:&nbsp;&nbsp;<?=$totalrows;?></th>
	</tr>      
</table>
<?php if ($totalrows) { ?>
<table align="center" width="30%">
<tr><td colspan="8"><?php navigationbar($page,$totalrows); ?></td></tr>
</table>
<?php } ?>
<table align="center" width="69%" class=noprint>
	<tr><td align=center>
	<form name="descarga" action="../export/csv.php" method="post">
	<input type=hidden name="titulo[]" value="<? echo($titulo1); ?>">
	<input type=hidden name="titulo[]" value="<? echo('Registro Destino:'.$_POST['CodRegDest']." - ".$des_destino); ?>">
	<input type=hidden name="titulo[]" value="<? echo('Fecha de Retiro Desde:'.$_POST["FecDesde"].' Hasta:'.$_POST["FecHasta"]); ?>">
	<input type=hidden name="titulosql" value="Nro.de Tramite!2|Nro. de Voucher!<?=($_POST["maxretiro"]==1)?'7':'6'; ?>|Fecha de Retiro!3|Fecha de Entrega!4|Motivo!5|<?=($_POST["maxretiro"]==1)?'Dias!6|':'';?>Oficio!<?=($_POST["maxretiro"]==1)?'8':'7'; ?>">
	<input type=hidden name="sql" value="<? echo($Sql); ?>">
	<input type=hidden name="archivo" value="TramitesRecVsEsp.txt">
	<input type=hidden name="archivo2" value="<?=($_POST["maxretiro"]==1)?'../reportes/TramitesRecVsEsp72.xml':'../reportes/TramitesRecVsEsp.xml'; ?>">
	<input type=hidden name="propiedadesreport" value="<?php echo($propiedadesreport); ?>">
	<input type=button class="botonout" name=botvolver     value="<?=VOLVER; ?>"  onMouseOver="this.className = 'botonover';" onMouseOut="this.className = 'botonout';" onClick="goMenu('<?=basename($_SERVER['SCRIPT_FILENAME']);?>');">
	<input type=submit class="botonout" name=botconfirma   value="<?=EXPORTAR; ?>" onMouseOver="this.className = 'botonover';" onMouseOut="this.className = 'botonout';">
	<input type=button class="botonout" name=botconfirma   value="<?=IMPRIMIR;?>" onMouseOver="this.className = 'botonover';" onMouseOut="this.className = 'botonout';"  onClick="newwindow=window.open(href='../export/imprime_ps2.php', this.target, 'width=250,height=140,left=260,top=230,resizable=no');">
<!--	<input type=button class="botonout" name=botconfirma   value="<?=IMPRIMIR;?>" onMouseOver="this.className = 'botonover';" onMouseOut="this.className = 'botonout';"  onClick="window.showModalDialog('../export/imprime_ps2.php','','dialogWidth:250px;dialogHeight:250px')">-->
	</form>
	</td></tr>
</table>
<?
}else{//no hay datos
?>
<table align="center" width="70%" class=tablaconbordes>
<tr><td align=center>No se Encontr&oacute informaci&oacute;n</td></tr>
</table>
<table align="center" width="69%" class=noprint>
	<tr><td align=center>
	<input type=button class="botonout" name=botvolver     value="<?=VOLVER; ?>"  onMouseOver="this.className = 'botonover';" onMouseOut="this.className = 'botonout';" onClick="goMenu('<?=basename($_SERVER['SCRIPT_FILENAME']);?>');">
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
