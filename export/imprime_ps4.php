<?
$debug = false;
if($debug) ini_set('display_errors', 1);
if($debug) error_reporting(E_ALL);
if($debug) print_r($_REQUEST);
require_once("../includes/lib.php");
require_once("../report/report.php");
?>
<HTML><HEAD>
<? require_once("../includes/inc_header.php"); ?>
<script type="text/javascript">
/*var TieneFoco = true;
function DaFoco() {
	if (!self.focus)
		setTimeout("self.focus()",500)
}
window.onblur = DaFoco;*/
function confirma() {
    document.formimpresion.botconfimp.disabled=true;
	var ok=true;
	if (!Valido(document.formimpresion.copias.value)) 	{
		ok = false;
		errores += "- La Cantidad de Copias no es Valida\n";
	}
	if (!ok) {
		alert("Hay errores en los datos:\n" + errores);
	    document.formimpresion.botconfimp.disabled=true;
		return false;
	}else{
		if (document.formimpresion.prn.value!="") {
			document.formimpresion.submit();
		}
	}
}
function inicializar(par) {
  if (par==1) {
	<?php if(!$debug) print "window.close();\n"; ?>
  }else{
	document.formimpresion.sql.value        =opener.document.descarga.sql.value;
	<?php if($debug) print "alert(document.formimpresion.sql.value);\n"; ?>
	document.formimpresion.archivo2.value   =opener.document.descarga.archivo2.value;
	document.formimpresion.propiedadesreport.value =
		opener.document.descarga.propiedadesreport.value;
  	buscarPapel();
  }
}
function buscarPapel() {
	  parametros="tipo=papel"+
				 "&impresora="+document.formimpresion.prn.value;
	  url="imprime_ps_ajax.php?"+parametros;
	  ajax(url);
	  if (http.readyState == 4) {
		results = http.responseText.split("|");
			var asignopapel=false;
			for (i=0; i<document.formimpresion.papel.options.length; i++) {
				if (document.formimpresion.papel.options[i].value==results[0]) {
					document.formimpresion.papel.selectedIndex=i;
					asignopapel=true;
					break;
				}
			}
		    if (!asignopapel) { //si no asigno no existe en el combo, lo agrego
				document.formimpresion.papel.options[document.formimpresion.papel.options.length+1]= new Option(results[0], results[0]);
				document.formimpresion.papel.selectedIndex=document.formimpresion.papel.options.length+1;
			}
	  }
}
function Valido(valor) {
	var validos = /^[0-9]*$/;
	if(!validos.test(valor)) {
		return false;
	}else{
		return true;
	}
}//Cierro ValidNum
function ajax(url) {
//alert(url);
	http.open("GET", url, false);
	http.send(null);
}
</script>
<!-- Objeto Ajax -->
<script type="text/javascript" language="JavaScript" src="../includes/ajaxobjt.js"></script>
</HEAD>
<body onLoad="inicializar(<?=isset($_POST["prn"]);?>)">
<?
if (isset($_POST["prn"])) { ?>
<br><br>
		<table align="center" width="30%" class=tablaconbordes>
			<tr><th colspan=2 align=center class=celdatitulo nowrap>Seleccionar Impresora</th></tr>
			<tr valign="middle"><td align=center nowrap>Generando Impresi&oacute;n<img src="../imagenes/loading.gif" alt="loading"></td></tr>
		</table>
<?
	for ($n=1; $n<=2; $n++) { //original y duplicado
		$sql = stripslashes($_POST["sql"]);
		$prn = $_POST["prn"];
		$rs = $conn->Execute($sql);
		$rpt =& new Report();
		$rpt->Recordset = $rs;
		$rpt->LoadFromXML($_POST["archivo2"]);
		$rpt->PageSize = $_POST["papel"];
		$propiedadesreporttemp=$propiedadesreport;
		$propiedadesreport=($n<2 ? $_POST["propiedadesreport"] : str_replace("ORIGINAL - Para Registro Origen", "DUPLICADO - Para el Correo", $_POST["propiedadesreport"]));
		if ($propiedadesreporttemp==$propiedadesreport) {
			$propiedadesreport=($n<2 ? $_POST["propiedadesreport"] : str_replace("ORIGINAL - Para el Correo", "DUPLICADO - Para el Correo", $_POST["propiedadesreport"]));	
		}
		if ($propiedadesreport != "") {
			$props = explode("\n", stripslashes($propiedadesreport));
			foreach($props as $prop) {
				$prp = explode("|", $prop);
				// el trabalenguas que sigue termina interpretando algo como:
				// $rpt->ReportHeader->objects[3]->text = "pirulo"
				// Sirve para fijar valores en secciones del report
				// donde todavia no se hizo un fetch
				$j = &$rpt;
				for ($i = 0; $i < count($prp) - 2; $i++) {
					$j = &$j->$prp[$i];
				}
				$j->objects[$prp[$i]]->Text = $prp[$i+1];
			} // end foreach prop
		} // end if propiedadesreport
		$rpt->Generate();
		$psfile = $rpt->SendToFile();
		for ($j=1; $j<=$_POST["copias"]; $j++) {
			print_text($prn, $psfile);
		}
		unlink($psfile);
	}
}else{
?>
<form name=formimpresion action="" method="post">
<table align="center" width="30%" class=tablaconbordes>
<tr><th colspan=2 align=center class=celdatitulo>Seleccionar Impresora</th></tr>
<tr valign="middle">
	<td class="celdatexto">Impresora:</td>
	<td><select class=textochico name=prn id="prn" onChange="buscarPapel()">
		<? fill_combo_vec(printers()); ?>
		</select>
	</td>
</tr>
<tr><td class="celdatexto">Papel</td>
	<td>
		<select class=textochico name="papel">
		<option value="RPT_A4">A4</option>
		<option value="RPT_LETTER">Carta</option>
		<option value="RPT_LEGAL">Oficio</option>
		</select>
	</td>
</tr>
<tr><td class="celdatexto">Copias :</td>
	<td><input type=text name=copias size=3 maxlength="2" value="1" class=textochico ></td></tr>
</table>
<table align="center" width="30%">
<tr><td align="center" colspan="2" nowrap>
	<input type="button" value="<?=CANCELO;?>" onClick="window.close();" class="botonout">
	<input type="button" name=botconfimp value="<?=CONFIRMO;?>" onClick="confirma();" class="botonout">
	<input type=hidden name="archivo2">
	<input type=hidden name="sql">
	<input type=hidden name="propiedadesreport">
	</td>
</tr>
</table>
</form>
<?
}//ifelse if (isset($_POST["confirmo"]))
?>
</body>
</HTML>