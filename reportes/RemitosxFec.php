<?
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
		document.form.Fecha.focus();
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
	document.form.botconfirma.disabled=false;
	var ok = true;
	var errores = "";
	if (document.form.Fecha.value=="") {
			ok = false;
			errores += "- Debe Completar la Fecha de Generación.\n";
	}else{
		if (!parseDate(document.form.Fecha,'%d/%m/%Y',true)) {
			ok = false;
			errores += "- El Formato de la Fecha de Generación no es Valida.\n";
		}else{
			var fecha = document.form.Fecha.value.substr(6,4)+document.form.Fecha.value.substr(3,2)+document.form.Fecha.value.substr(0,2);
			if (fecha>document.form.fecHoy.value) {
					ok = false;
					errores += "- La Fecha de Generación es Superior a la Fecha Actual.\n";
			}
		}
	}
	if (!ok) {
		alert("Hay errores en los datos:\n" + errores);
	}else{
			document.form.submit();
	}
	return false;
}
function ajax(url) {
	http.open("GET", url, false);
	http.send(null);
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
<body onLoad="inicializar(<?=isset($_POST['Fecha']);?>);" onFocus="if(newwindow){newwindow.focus();}">
<? require_once("../includes/inc_topleft.php");
/* Contenido */
?>
<? if (!isset($_POST['Fecha'])) {
$opcion=$_POST['opcion'];
$pagina=basename($_SERVER['SCRIPT_FILENAME']);
require_once('../includes/inc_titulo.php');
?>
<form name="form" method=post action="" onSubmit="return false">
<table align="center" width="40%" class=tablaconbordes>
<tr><td>
<table align="center" width="100%">
	<tr>
		<td class="celdatexto" align="center">
			Origen:
			<input type="radio" name="tipo_remito" id="tipo_remito1" value="origen">
		</td>
		<td class="celdatexto">
			Destino:
			<input type="radio" name="tipo_remito" id="tipo_remito2" value="destino" checked="checked">	
		</td>
		<td class="celdatexto">
			Devolución:
			<input type="radio" name="tipo_remito" id="tipo_remito3" value="devolucion" />
		</td>
	</tr>
	<tr>
		<td class="celdatexto" width="30%" align="right" nowrap>Fecha de Generaci&oacute;n:</td>
		<td valign="middle" width="70%" colspan="2" >
			<input type="text" class=textochico size=8 maxlength="10" name="Fecha" id="Fecha" value="" style="text-align:right;" onBlur="parseDate(this,'<?php echo(FMT_DATE_CAL); ?>',true);">
		<img src="../imagenes/calendario.png" name="selfecha2" id="selfecha2" title="Calendario" alt="" style="cursor:pointer;"></td>
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
	Calendar.setup( { inputField: "Fecha", ifFormat: "%d/%m/%Y", button: "selfecha2" } );
</script>
<? }else{
		$fecha     = new Date($_POST["Fecha"]);
		$fecha_hasta     = new Date($_POST["Fecha"].' 23:59:59');		
		$tipo=($_POST["tipo_remito"]=='destino' ? 'des' : 'ori');
			if (!is_numeric($_POST["rows"])) {
		$Sql="Select Count(*) as CANTIDAD
			from remito b
			where b.rem_tipo=".sqlstring($_POST["tipo_remito"])."
			and b.rem_fecha_generacion >= ".sqldate($fecha->format(FMT_DATE_ISO))."  
			and b.rem_fecha_generacion <= ".sqldate($fecha_hasta->format(FMT_DATE_ISO));									 
				$rs = $conn->Execute($Sql);
				$totalrows = $rs->fields["CANTIDAD"];
			} else {
				$totalrows = $_POST["rows"];
			} // end if rows		
/* esta query no muestra los anulados */		
/*		$Sql="select a.rem_id_".$tipo.", b.rem_numero, c.reg_cod_int, c.reg_descrip, count(*) as tramites, b.rem_estado
			from tramite a 
			inner join remito b on a.rem_id_".$tipo."=b.rem_id and b.rem_tipo=".sqlstring($_POST["tipo_remito"])."
			and b.rem_fecha_generacion >= ".sqldate($fecha->format(FMT_DATE_ISO))."  
			and b.rem_fecha_generacion <= ".sqldate($fecha_hasta->format(FMT_DATE_ISO))."  			
			inner join reg_autom c on c.reg_codigo = reg_codigo_".$tipo."
			group by a.rem_id_".$tipo.", b.rem_numero, c.reg_cod_int, reg_descrip, rem_estado
			order by b.rem_numero";			*/
/* esta query incluye los anulados */			
		$Sql="select a.rem_id_".$tipo.", b.rem_numero, c.reg_cod_int, c.reg_descrip, count(*) as tramites, b.rem_estado
			from remito b
			left join tramite a on a.rem_id_".$tipo."=b.rem_id
			left join reg_autom c on c.reg_codigo = a.reg_codigo_".$tipo." 
			where b.rem_tipo=".sqlstring($_POST["tipo_remito"])."
			and b.rem_fecha_generacion >= ".sqldate($fecha->format(FMT_DATE_ISO))."  
			and b.rem_fecha_generacion <= ".sqldate($fecha_hasta->format(FMT_DATE_ISO))."  			
			group by a.rem_id_".$tipo.", b.rem_numero, c.reg_cod_int, reg_descrip, rem_estado
			order by b.rem_numero";			
//echo $Sql;
$rs = $conn->PageExecute($Sql,20,$page);
		$propiedadesreport =
			"PageHeader|4|Reporte de Remitos ".ucfirst($_POST["tipo_remito"])." Despachados\n".
			"PageHeader|6|".$_POST["Fecha"]."\n";			

if (!$rs->EOF) {
	$tramites=0;	
	$total=0;
?>
<form name="form" method=post action="" onSubmit="return false">
	<input type="hidden" name="page" value="">
	<input type="hidden" name="tipo_remito" value="<?php echo($_POST['tipo_remito']); ?>">	
	<input type="hidden" name="Fecha" value="<?php echo($_POST['Fecha']); ?>">	
	<input type="hidden" name="rows" value="<?php echo($totalrows); ?>">
</form>
<table border=0 cellspacing=0 width=100% cellpadding=0 align="center">
<tr><td align="center" colspan=2 class="titulo1" height="35" valign=middle>Reporte de Remitos&nbsp;<?=ucfirst($_POST['tipo_remito']); ?> Despachados</td></tr>
</table>	
<?php if ($totalrows) { ?>
<table align="center" width="30%">
<tr><td colspan="4"><?php navigationbar($page,$totalrows); ?></td></tr>
</table>
<?php } // end if totalrows ?>
<table align="center" width="60%">
    <tr class=celdatexto><td  colspan=4 align=left>Remitos Tipo:&nbsp;<b><?=ucfirst($_POST["tipo_remito"]);?></b>&nbsp;&nbsp;&nbsp;Fecha de Generaci&oacute;n:<b><?=$_POST["Fecha"];?></b></td></tr>
</table>
<table align="center" width="60%" class=tablaconbordes>
	<tr class=celdatitulo><th width="20%">Remito Nro.</th>
					 	  <th colspan=2 width="65%">Registro</th>
					 	  <th width="5%">Cant. de Tramites</th>
					 	  <th width="10%">Estado</th>						  
	</tr>
<? $clase='fondotabla1';
   while (!$rs->EOF) {
	   $tramites+=$rs->fields['tramites'];
?>
	<tr class=<?=$clase;?> onClick="seleccionado(<?=$i;?>);" onMouseOver="this.className = 'fondoconfirmacion';" onMouseOut="this.className = '<?=$clase;?>';">		
		<td align=right class=celdatexto><?=$rs->fields["rem_numero"];?></td>
		<td class=celdatexto align=center><?=$rs->fields["reg_cod_int"];?></td>
		<td class=celdatexto nowrap><?=$rs->fields["reg_descrip"];?></td>
		<td class=celdatexto align=center><?=($rs->fields['rem_estado']==ANULADO ? '-' : $rs->fields['tramites']);?></td>
		<td class=celdatexto><?=$rs->fields['rem_estado'];?></td>		
	</tr>
<?
	  $clase = ($clase == 'fondotabla1' ? 'fondotabla2' : 'fondotabla1');			  
	  $rs->movenext();
   }//while ?>
	<tr><td colspan=5 width="100%">
		<table cellpadding="3" cellspacing="1" width="100%">
			<tr class=celdatitulo>
			<td align=right width="30%">Total de Remitos:</td>
	 	    <td align=left  width="20%"><?php echo($totalrows); ?></td>
			<td align=right width="30%">Total de Tramites:</td>
		 	<td align=left  width="20%"><?=$tramites;?></td>
			</tr>
		</table>
	</td></tr>
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
	<input type=hidden name="titulosql" value="Remito Nro.!1|Registro Automotor!2|.!3|Cant.de Trámites!4|Estado!5">
	<input type=hidden name="sql" value="<? echo($Sql); ?>">
	<input type=hidden name="archivo" value="RemitosxFec.txt">
	<input type=hidden name="archivo2" value="../reportes/RemitosxFec.xml">
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
<tr><td align=center>No se Encontr&oacute remitos generados</td></tr>
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
