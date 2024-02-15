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
				errores += "- Fecha Desde: No se puede consultar Trámites con mas de <?=getParametro("desde_dias_consulta")?> días.\n";
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
<body onLoad="inicializar(<?=isset($_POST['FecDesde']);?>);" onFocus="if(newwindow){newwindow.focus();}">
<? require_once("../includes/inc_topleft.php");
/* Contenido */
$opcion=$_POST['opcion'];
$pagina=basename($_SERVER['SCRIPT_FILENAME']);
require_once('../includes/inc_titulo.php');
?>
<? if (!isset($_POST['FecDesde']) || (isset($_POST['FecDesde']) && $_POST["radFormatoSalida"] == "ARCHIVO")) {
	
	//me fijo si tengo que generar el archivo
	if ($_POST["radFormatoSalida"] == "ARCHIVO")
	{
		$fecdesde     = $_POST["FecDesde"];
		$fechasta     = $_POST["FecHasta"];
		$Sql="SELECT convert(varchar,TRA_FECHA_CARGA,103) as TRA_FECHA_CARGA,
		b.REG_COD_INT AS REG_O, c.REG_COD_INT AS REG_D, CASE WHEN c.REG_TIPO = 'A' THEN 'Abonado' ELSE 'No Abonado' END AS REG_TIPO,
		d .USR_APELLIDO, b.REG_CPA AS CP_ORIGEN, c.REG_CPA AS CP_DESTINO, e.RGI_DESCRIP
		FROM TRAMITE a INNER JOIN
		REG_AUTOM b ON a.REG_CODIGO_ORI = B.REG_CODIGO INNER JOIN
		REG_AUTOM c ON a.reg_codigo_des = c.reg_codigo INNER JOIN
		USUARIO d ON a.USR_ID_CARGA = D .USR_ID INNER JOIN
		REGION e ON c.RGI_CODIGO = e.RGI_CODIGO
				    WHERE  a.TRA_FECHA_CARGA between convert(datetime,".sqlstring($fecdesde.' 00:00:00').",105)
			     and convert(datetime,".sqlstring($fechasta.' 23:59:59').",105)  
			order by a.TRA_FECHA_CARGA";
		//echo $Sql."<BR>";

		$rs = $conn->execute($Sql);
		$propiedadesreport =
			"PageHeader|6|".$_POST["FecDesde"]."\n" .
			"PageHeader|8|".$_POST["FecHasta"];
?>
	<form name="descarga" action="../export/csv.php" method="post">
	<input type=hidden name="titulo[]" value="<? echo($titulo1); ?>">
	<input type=hidden name="titulosql" value="Fecha de Carga!0|Registro Origen!1|Registro Destino!2|Tipo!3|Usuario!4|C.P. Origen!5|C.P. Destino!6|Region Destino!7">
	<input type=hidden name="sql" value="<? echo($Sql); ?>">
	<input type=hidden name="archivo" value="tramitesGeneral.txt">
	<input type=hidden name="archivo2" value="../reportes/TramitesGeneral.xml">
	<input type=hidden name="propiedadesreport" value="<?php echo($propiedadesreport); ?>">
	</form>
<?	
	}
?>
<div id=divFormDatos>
<form name="form" method=post action="" onSubmit="return false">
<!--<form name="form" onsubmit="return (validaFormulario());">-->
<table align="center" width="70%" class=tablaconbordes>
<tr><td>
<table align="center" width="420">
	<tr><td class="celdatexto" width="30%" align="right" nowrap>Fecha de Carga Desde:</td>
		<td valign="middle" width="70%" ><input type="text" class=textochico size=8 maxlength="10" name="FecDesde" id="FecDesde" value="" style="text-align:right;" onBlur="parseDate(this,'<?php echo(FMT_DATE_CAL); ?>',true);">
		<img src="../imagenes/calendario.png" name="selfecha2" id="selfecha2" title="Calendario" alt="" style="cursor:pointer;"></td>
	</tr>
	<tr><td class="celdatexto" align="right" nowrap>Fecha de Carga Hasta:</td>
		<td valign="middle"><input type="text" class=textochico size=8 maxlength="10" name="FecHasta" id="FecHasta" value="" style="text-align:right;" onBlur="parseDate(this,'<?php echo(FMT_DATE_CAL); ?>',true);">
		<img src="../imagenes/calendario.png" name="selfecha3" id="selfecha3" title="Calendario" alt="" style="cursor:pointer;"></td>
	</tr>
	<tr>
		<td class="celdatexto" align="right" nowrap>Formato de salida:</td>
		<td align="left" class="celdatexto" valign="middle">
			<input type="radio" name="radFormatoSalida" value="PANTALLA" checked="checked">Por pantalla
			<input type="radio" name="radFormatoSalida" value="ARCHIVO">Generar Archivo
		</td>
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
</div>
<script type="text/javascript">
	Calendar.setup( { inputField: "FecDesde", ifFormat: "%d/%m/%Y", button: "selfecha2" } );
	Calendar.setup( { inputField: "FecHasta", ifFormat: "%d/%m/%Y", button: "selfecha3" } );
</script>
<? }else{
		$fecdesde     = $_POST["FecDesde"];
		$fechasta     = $_POST["FecHasta"];
		$Sql="SELECT convert(varchar,TRA_FECHA_CARGA,103) as TRA_FECHA_CARGA,
		b.REG_COD_INT AS REG_O, c.REG_COD_INT AS REG_D, CASE WHEN c.REG_TIPO = 'A' THEN 'Abonado' ELSE 'No Abonado' END AS REG_TIPO,
		d .USR_APELLIDO, b.REG_CPA AS CP_ORIGEN, c.REG_CPA AS CP_DESTINO, e.RGI_DESCRIP
		FROM TRAMITE a INNER JOIN
		REG_AUTOM b ON a.REG_CODIGO_ORI = B.REG_CODIGO INNER JOIN
		REG_AUTOM c ON a.reg_codigo_des = c.reg_codigo INNER JOIN
		USUARIO d ON a.USR_ID_CARGA = D .USR_ID INNER JOIN
		REGION e ON c.RGI_CODIGO = e.RGI_CODIGO
				    WHERE  a.TRA_FECHA_CARGA between convert(datetime,".sqlstring($fecdesde.' 00:00:00').",105)
			     and convert(datetime,".sqlstring($fechasta.' 23:59:59').",105)  
			order by a.TRA_FECHA_CARGA";
//echo $Sql."<BR>";

		$rs = $conn->execute($Sql);
		$propiedadesreport =
			"PageHeader|6|".$_POST["FecDesde"]."\n" .
			"PageHeader|8|".$_POST["FecHasta"];

if (!$rs->EOF) {
	$corte1_ant=$rs->fields["TRA_FECHA_CARGA"];//.substr($rs->fields["NRO_ORIGEN"],0,2);
	$contador=0;
	$total=0;
?>
<table align="center" width="70%">
    <tr class=celdatexto><td  colspan=4 align=left>Fecha de Carga Desde:<b><?=$_POST["FecDesde"];?></b>&nbsp;&nbsp;Hasta:<b><?=$_POST["FecHasta"];?></b></td></tr>
</table>
<table align="center" width="70%" class=tablaconbordes>
	<tr class=celdatitulo><th>Fecha de Carga</th>
					 	  <th>Registro Origen</th>
					 	  <th>Registro Destino</th>
					 	  <th>Tipo</th>
						  <th>Usuario</th>
						  <th>C.P. Origen</th>
						  <th>C.P. Destino</th>
						  <th>Region Destino</th>
	</tr>
<? $clase='fondotabla1';
   while (!$rs->EOF) {
	   $corte1_act=$rs->fields["TRA_FECHA_CARGA"];//.substr($rs->fields["NRO_ORIGEN"],0,2);
	   if ($corte1_ant!=$corte1_act) {
?>
	<tr class=celdatitulo><td colspan=4 align=center>Total por Fecha de Carga:</th>
					 	  <td colspan=6 align=left><?=$contador;?></td>
	</tr>
<?
	       $corte1_ant=$corte1_act;
		   $total+=$contador;
		   $contador=0;
	   }
       $contador++;
?>
	<tr class=<?=$clase;?> onClick="seleccionado(<?=$i;?>);" onMouseOver="this.className = 'fondoconfirmacion';" onMouseOut="this.className = '<?=$clase;?>';">
		
		<td align=center class=celdatexto><?=$rs->fields["TRA_FECHA_CARGA"];?></td>
		<td class=celdatexto align="center"><?=$rs->fields["REG_O"];?></td>
		<td align=center class=celdatexto><?=$rs->fields["REG_D"];?></td>
		<td align="center" class="celdatexto"><?=$rs->fields['REG_TIPO'];?></td>
		<td align=center class=celdatexto><?=$rs->fields["USR_APELLIDO"];?></td>
		<td align=center class=celdatexto><?=$rs->fields["CP_ORIGEN"];?></td>
		<td align=center class=celdatexto><?=$rs->fields["CP_DESTINO"];?></td>
		<td align=center class=celdatexto><?=$rs->fields["RGI_DESCRIP"];?></td>
<!--        <td><?=$corte1_ant.'--'.$contador;?></td>		-->
	</tr>
<?
	  $clase = ($clase == 'fondotabla1' ? 'fondotabla2' : 'fondotabla1');			  
	  $rs->movenext();
   }//while ?>
	<tr class=celdatitulo><td colspan=4 align=center>Total por Fecha de Carga:</th>
					 	  <td colspan=6 align=left><?=$contador;?></td>
	</tr>
<?   $total+=$contador;	?>
	<tr class=celdatitulo><td colspan=4 align=center>Total General:</th>
					 	  <td colspan=6 align=left><?=$total;?></td>
	</tr>
	</table>

<table align="center" width="69%" class=noprint>
	<tr><td align=center>
	<form name="descarga" action="../export/csv.php" method="post">
	<input type=hidden name="titulo[]" value="<? echo($titulo1); ?>">
	<input type=hidden name="titulosql" value="Fecha de Carga!0|Registro Origen!1|Registro Destino!2|Tipo!3|Usuario!4|C.P. Origen!5|C.P. Destino!6|Region Destino!7">
	<input type=hidden name="sql" value="<? echo($Sql); ?>">
	<input type=hidden name="archivo" value="tramitesGeneral.txt">
	<input type=hidden name="archivo2" value="../reportes/TramitesGeneral.xml">
	<input type=hidden name="propiedadesreport" value="<?php echo($propiedadesreport); ?>">
	<input type=button class="botonout" name=botvolver     value="<?=VOLVER; ?>"  onMouseOver="this.className = 'botonover';" onMouseOut="this.className = 'botonout';" onClick="goMenu('<?=basename($_SERVER['SCRIPT_FILENAME']);?>');">
	<input type=submit class="botonout" name=botconfirma   value="<?=EXPORTAR; ?>" onMouseOver="this.className = 'botonover';" onMouseOut="this.className = 'botonout';" onClick="validaFormulario();">
	<input type=button class="botonout" name=botconfirma   value="<?=IMPRIMIR;?>" onMouseOver="this.className = 'botonover';" onMouseOut="this.className = 'botonout';"  onClick="newwindow=window.open(href='../export/imprime_ps2.php', this.target, 'width=250,height=140,left=260,top=230,resizable=no');">
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
}//if (!$rs->EOF) {
}

if ($_POST["radFormatoSalida"] == "ARCHIVO"){
	if (!$rs->EOF) {
?>
	<script language="javascript">
		function descargaArchivo(){
			document.descarga.submit();
		}
		setTimeout('descargaArchivo()',500)
	</script> 
<?
	}else{
?>
	<script language="javascript">
		function descargaArchivo(){
			alert('No se encontraron datos para generar el archivo.');
		}
		setTimeout('descargaArchivo()',500)
	</script> 	
<?
	}
}
?>

<!--Contenido-->
<? require_once("../includes/inc_bottom.php"); ?>
</BODY></HTML>
<?
} //Cierro if autorizacion
?>
