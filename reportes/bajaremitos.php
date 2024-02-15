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

function mostrar_detalle (rem, tipo){
    
    document.getElementById('rem').value = rem;
    document.getElementById('tipo').value = tipo;
    
    document.getElementById('detalle').submit();

    
    return false;
    
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
$pagina="bajaremitos.php";
require_once('../includes/inc_titulo.php');
?>



<!-- Form para ingresar numero de remito -->
<form name="form" id="form" action="bajaremitos.php" method="post" onSubmit="return false">
    
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
    
    $sql_bajas = "SELECT b.rem_numero ,upper (b.rem_tipo) as  rem_tipo , upper (u.usr_username) as usr_username, convert(varchar,baja_fecha, 105)+ ' '+convert(varchar,baja_fecha, 108 ) as baja_fecha2 
                    FROM baja_remito as b, usuario as u
                    WHERE b.baja_usuario_id = u.usr_id
                    AND b.baja_fecha between convert(datetime,".sqlstring($fecdesde.' 00:00:00').",105) and convert(datetime,".sqlstring($fechasta.' 23:59:59').",105)"
                    .$oper." 
                    ORDER BY b.baja_fecha DESC";
    
        
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
        <th class="celdatitulo">Remito</th>
        <th class="celdatitulo">Tipo</th>
        <th class="celdatitulo">Usuario Baja</th>
        <th class="celdatitulo">Fecha Baja</th>
    </tr>
<?php

    $clase = "1";
    while ( !$rs_bajas->EOF ){
    ?>
        <tr class="fondotabla<?=$clase?>" onmouseout="this.className = 'fondotabla<?=$clase?>';" onmouseover="this.className = 'fondoconfirmacion';">
            <td class="celdatexto" align="center">
                <a href="#" onclick="return mostrar_detalle('<?=$rs_bajas->fields['rem_numero']?>','<?=$rs_bajas->fields['rem_tipo']?>');">
                    <?=$rs_bajas->fields['rem_numero']?>
                </a>
            </td>
            <td class="celdatexto" align="center"><?=$rs_bajas->fields['rem_tipo']?></td>
            <td class="celdatexto" align="center"><?=$rs_bajas->fields['usr_username']?></td>
            <td class="celdatexto" align="center"><?=$rs_bajas->fields['baja_fecha2']?> Hs</td>
        </tr>    
    <?php
        $rs_bajas->MoveNext();
        if ( $clase == "1" ){$clase = "2";}else{$clase = "1";}
    }//end While iteracion de registros
} //end IF datos vacios
?>

</table>
<form name="detalle" id="detalle" action="detalleTramites.php" method="POST" style="display: none;">
    <input type="hidden" name="rem"  id="rem" value="" />
    <input type="hidden" name="tipo" id="tipo" value="" />
    <input type="hidden" name="secc4" value="1" />
</form>
</center>  
<?php    
}


if ( isset($datos['radFormatoSalida']) &&  $datos['radFormatoSalida'] == 'ARCHIVO'){
    
    
    $oper = "";
    if ( $datos['operador'] != '0' ){$oper =" AND baja_usuario_id = ".$datos['operador']." ";}
    
	$fecdesde = $datos["FecDesde"];
	$fechasta = $datos["FecHasta"];
    
    $sql_bajas = "SELECT b.rem_numero ,upper (b.rem_tipo) as  rem_tipo, upper (u.usr_username) as usr_username, convert(varchar,baja_fecha, 105)+ ' '+convert(varchar,baja_fecha, 108 ) as baja_fecha2 
                    FROM baja_remito as b, usuario as u
                    WHERE b.baja_usuario_id = u.usr_id
                    AND b.baja_fecha between convert(datetime,".sqlstring($fecdesde.' 00:00:00').",105) and convert(datetime,".sqlstring($fechasta.' 23:59:59').",105)"
                    .$oper." 
                    ORDER BY b.baja_fecha DESC";
  
    
    
?>
	<form name="descarga" action="../export/csv.php" method="post">
	<input type=hidden name="titulo[]" value="<?=($titulo1); ?>" />
	<input type=hidden name="titulosql" value="Remito!0|Tipo Remito!1|Usuario Baja!2|Fecha Baja!3" />
	<input type=hidden name="sql" value="<?=($sql_bajas); ?>" />
	<input type=hidden name="archivo" value="remitosAnulados.csv" />
	<input type=hidden name="archivo2" value="../reportes/RemitosAnulados.xml" />
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