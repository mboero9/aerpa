<?php 
require_once("../includes/lib.php");
if (check_auth($usrid, $perid) && check_permission($permiso)) {
/* Anulacion de Remito por Origen/Destino */

$datos = $_POST;

/* Cargo el comboBox de operadores */
$sql_operadores = "SELECT usr_id, usr_apellido + ' ' + usr_nombre as nombre, usr_username FROM usuario ORDER BY nombre";
$rs_operadores = $conn->Execute( $sql_operadores );
$combo_operadores = "";
$usuarios = array();

while(!$rs_operadores->EOF){
    
    $combo_operadores.= "<option value='".$rs_operadores->fields['usr_id']."'>".$rs_operadores->fields['nombre']."</option>";
    $usuarios[$rs_operadores->fields['usr_id']]= $rs_operadores->fields['usr_username'];
    $rs_operadores->movenext();
}

?>
<html>
<head>
    <?php require_once("../includes/inc_header.php"); ?>

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
			document.form.submit();

	}
	//document.form.botconfirma.disabled=false;
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
<!-- calendario desplegable -->
<script type="text/javascript" language="JavaScript" src="../includes/fecha.js"></script>
<style type="text/css">@import url(../calendar/calendar-win2k-1.css);</style>
<script type="text/javascript" src="../calendar/calendar.js"></script>
<script type="text/javascript" src="../calendar/lang/calendar-es.js"></script>
<script type="text/javascript" src="../calendar/calendar-setup.js"></script>

</head>
<body>
<?php require_once("../includes/inc_topleft.php"); 
/* Contenido */
$opcion=$_POST['opcion'];
$pagina="bajatramites.php";

?>
<table width="100%" cellspacing="0" cellpadding="0" border="0" align="center">
    <tbody><tr><td valign="middle" height="35" align="center" class="titulo1" colspan="2">Baja de Tr&aacute;mites</td></tr>
</tbody></table>


<!-- Form para ingresar numero de remito -->
<form name="form" id="form" action="bajatramites.php" method="post" onSubmit="return false">
    
    <input type="hidden" name="secc4" value="1" />
    
<table align="center" width="30%" class="tablaconbordes" >
    <tr>
        <th align="center" class="celdatitulo">Ingrese datos para la b&uacute;squeda</th>
    </tr>
    
    <tr>
        <td align="center" class="celdatexto">
            Fecha Desde: <input type="text" class="textochico" size="8" maxlength="10" name="FecDesde" id="FecDesde"  onBlur="parseDate(this,'<?php echo(FMT_DATE_CAL); ?>',true);"/>
            <img src="../imagenes/calendario.png" name="selfecha2" id="selfecha2" title="Calendario" alt="" style="cursor:pointer;" />
        </td>
    </tr>
    
    <tr>
        <td align="center" class="celdatexto">
            Fecha Hasta: <input type="text" class="textochico" size="8" maxlength="10" name="FecHasta" id="FecHasta" />
            <img src="../imagenes/calendario.png" name="selfecha3" id="selfecha3" title="Calendario" alt="" style="cursor:pointer;" />
        </td>
    </tr>
    
    <tr>
        <td align="center" class="celdatexto">
            Operador: 
            <select name="operador">
                <option value="0">Todos</option>
                <?=$combo_operadores?>
            </select>
        </td>
    </tr>
	<tr>
		<td class="celdatexto" align="right" nowrap>Formato de salida:
			<input type="radio" name="radFormatoSalida" value="PANTALLA" checked="checked">Por pantalla
			<input type="radio" name="radFormatoSalida" value="ARCHIVO">Generar Archivo
		</td>
	</tr>	
    <tr>
        <td colspan="4" align="center">
            <input type="submit" class="botonout" name="buscar" value="BUSCAR" onMouseOver="this.className = 'botonover';" onMouseOut="this.className = 'botonout';"  onClick="validaFormulario();"/>
        </td>
    </tr>

</table>
<input type=hidden name=fecHoy value="<?=date('Ymd');?>"/>
</form>
<script type="text/javascript">
	Calendar.setup( { inputField: "FecDesde", ifFormat: "%d/%m/%Y", button: "selfecha2" } );
	Calendar.setup( { inputField: "FecHasta", ifFormat: "%d/%m/%Y", button: "selfecha3" } );
</script>
<!--Fin Form -->



<?php

if ( isset($datos['radFormatoSalida']) &&  $datos['radFormatoSalida'] == 'PANTALLA'){
    
    //print_r ($usuarios);
    $oper = "";
    if ( $datos['operador'] != '0' ){$oper =" AND baja_usuario_id = ".$datos['operador']." ";}
    
	$fecdesde = $datos["FecDesde"];
	$fechasta = $datos["FecHasta"];
    
    $sql_bajas = "SELECT bt.*, ro.reg_descrip as reg_ori, rd.reg_descrip as reg_des, remd.rem_numero as remito_destino, remo.rem_numero as remito_origen, convert(varchar,baja_fecha, 105)+ ' '+convert(varchar,baja_fecha, 108 ) as baja_fecha2 
                    FROM baja_tramite as bt
                    left join remito as remo on bt.rem_id_ori = remo.rem_id
                    left join remito as remd on bt.rem_id_des = remd.rem_id
                    left join reg_autom as ro on bt.reg_codigo_ori = ro.reg_codigo 
                    left join reg_autom as rd on bt.reg_codigo_des = rd.reg_codigo 
                    WHERE baja_fecha between convert(datetime,".sqlstring($fecdesde.' 00:00:00').",105) 
                    	and convert(datetime,".sqlstring($fechasta.' 23:59:59').",105)
                     ".$oper."
                    ORDER BY baja_fecha DESC";
    
    $rs_bajas = $conn->Execute( $sql_bajas );
    
        if ( $rs_bajas->EOF ){ ?>
        <center><font color="red"><b>No se registran datos para la b&uacute;squeda.</b></font></center>
<?php   }else{
    
?>
<center>
<table align="center" width="70%">
    <tr class=celdatexto><td align="center">Fecha de Carga Desde:<b><?=$_POST["FecDesde"];?></b>&nbsp;&nbsp;Hasta:<b><?=$_POST["FecHasta"];?></b></td></tr>
</table>
<table class="tablaconbordes" >
    <tr>
        <th class="celdatitulo">Dominio</th>
        <th class="celdatitulo">Voucher</th>
        <th class="celdatitulo">Usr carga</th>
        <th class="celdatitulo">Reg Origen</th>
        <th class="celdatitulo">Reg Destino</th>
        <th class="celdatitulo">Remito Ori</th>
        <th class="celdatitulo">Remito Des</th>
        <th class="celdatitulo">Usuario Baja</th>
        <th class="celdatitulo">Fecha Baja</th>
    </tr>
<?php

    $clase = "1";
    while ( !$rs_bajas->EOF ){
    ?>
        <tr class="fondotabla<?=$clase?>" onmouseout="this.className = 'fondotabla<?=$clase?>';" onmouseover="this.className = 'fondoconfirmacion';">
            <td class="celdatexto" align="center"><?=$rs_bajas->fields['tra_dominio']?></td>
            <td class="celdatexto" align="center"><?=$rs_bajas->fields['tra_nro_voucher']?></td>
            <td class="celdatexto" align="center"><?=$usuarios[$rs_bajas->fields['usr_id_carga']]?></td>
            <td class="celdatexto" align="center"><?=$rs_bajas->fields['reg_ori']?></td>
            <td class="celdatexto" align="center"><?=$rs_bajas->fields['reg_des']?></td>
            <td class="celdatexto" align="center"><?=$rs_bajas->fields['remito_origen']?></td>
            <td class="celdatexto" align="center"><?=$rs_bajas->fields['remito_destino']?></td>
            <td class="celdatexto" align="center"><?=$usuarios[$rs_bajas->fields['baja_usuario_id']]?></td>
            <td class="celdatexto" align="center"><?=$rs_bajas->fields['baja_fecha2']?> Hs</td>
        </tr>    
    <?php
        $rs_bajas->MoveNext();
        if ( $clase == "1" ){$clase = "2";}else{$clase = "1";}
    }//end While iteracion de registros
} //end IF datos vacios
?>

</table>
</center>  
<?php    
}


if ( isset($datos['radFormatoSalida']) &&  $datos['radFormatoSalida'] == 'ARCHIVO'){
    
    
    $oper = "";
    if ( $datos['operador'] != '0' ){$oper =" AND baja_usuario_id = ".$datos['operador']." ";}
    
	$fecdesde = $datos["FecDesde"];
	$fechasta = $datos["FecHasta"];
    
    $sql_bajas = "SELECT tra_dominio,tra_nro_voucher, uc.usr_username, ro.reg_descrip as reg_ori, rd.reg_descrip as reg_des, rem_id_ori, rem_id_des, u.usr_username
                        , convert(varchar,baja_fecha, 105)+ ' '+convert(varchar,baja_fecha, 108 ) as baja_fecha2  
                        FROM baja_tramite , reg_autom as ro  , reg_autom as rd , usuario as u, usuario as uc 
                        WHERE baja_fecha between convert(datetime,".sqlstring($fecdesde.' 00:00:00').",105) and convert(datetime,".sqlstring($fechasta.' 23:59:59').",105)   
                        AND baja_tramite.reg_codigo_ori = ro.reg_codigo
                        AND baja_tramite.reg_codigo_des = rd.reg_codigo
                        AND baja_tramite.baja_usuario_id = u.usr_id
                        AND baja_tramite.baja_usuario_id = uc.usr_id"
                        .$oper." 
                        ORDER BY baja_fecha DESC";   
    
    
?>
	<form name="descarga" action="../export/csv.php" method="post">
	<input type=hidden name="titulo[]" value="<?=($titulo1); ?>" />
	<input type=hidden name="titulosql" value="Dominio!0|Voucher!1|Usuario Carga!2|Registro Origen!3|Registro Destino!4|Remito Origen!5|Remito Destino!6|Usuario Baja!7|Fecha Baja!8" />
	<input type=hidden name="sql" value="<?=($sql_bajas); ?>" />
	<input type=hidden name="archivo" value="tramitesBajas.csv" />
	<input type=hidden name="archivo2" value="../reportes/TramitesBajas.xml" />
	<input type=hidden name="propiedadesreport" value="<?=($propiedadesreport); ?>" />
	</form>
	<script language="javascript">
		function descargaArchivo(){
			document.descarga.submit();
		}
		setTimeout('descargaArchivo()',500)
	</script> 
<?php
}






}?>