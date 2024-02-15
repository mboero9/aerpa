<?
require_once("../includes/lib.php");
require_once("../report/report.php");
if (check_auth($usrid, $perid) && check_permission($permiso)) {
?>
<HTML><HEAD>
<? require_once("../includes/inc_header.php"); ?>
<script type="text/javascript">
function confirma() {
	var ok=true;
	if (!Valido(document.formimpresion.copias.value)) 	{ 
		ok = false;	
		errores += "- La Cantidad de Copias no es Valida\n";
	}
	if (!ok) {
		alert("Hay errores en los datos:\n" + errores);
	}else{  
		if (document.formimpresion.prn.value!="") {
			document.formimpresion.confirmo.value=1;
			document.formimpresion.submit();
		}
	}		
}
function inicializar(par) {
  if (par==1) {
    goMenu(document.formimpresion.dondevamos.value);
  }else{
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
<body onLoad="inicializar(<?=$_POST["confirmo"];?>)">
<? require_once("../includes/inc_topleft.php"); ?>
<br><br><br><br><br><br>
<?
if (isset($_POST["confirmo"])) { ?>
		<table align="center" width="30%" class=tablaconbordes>
			<tr><th colspan=2 align=center class=celdatitulo>Seleccionar Impresora</th></tr>
			<tr valign="middle"><td align=center>Generando Impresi&oacute;n<img src="../imagenes/loading.gif" alt="loading"></td></tr>
		</table>
		<form name=formimpresion action="" method="post">		
			<input type=hidden name="dondevamos" value="<? echo($_POST["dondevamos"]); ?>">				
		</form>
<?	$sql = stripslashes($_POST["sql"]);
	$prn = $_POST["prn"];
	$rs = $conn->Execute($sql);
	$rpt =& new Report();
	$rpt->Recordset = $rs;
	$rpt->LoadFromXML($_POST["archivo2"]);
	$rpt->PageSize = $_POST["papel"];
	$rpt->Generate();
	$psfile = $rpt->SendToFile();
	for ($j=1; $j<=$_POST["copias"]; $j++) {
		print_text($prn, $psfile);
	}
	unlink($psfile);
}else{ 
?>
<form name=formimpresion action="" method="post">
<table align="center" width="30%" class=tablaconbordes>
<tr><th colspan=2 align=center class=celdatitulo>Seleccionar Impresora</th></tr>
<tr valign="middle">
	<td class="celdatexto">Impresora:</td>
	<td><select name=prn id="prn" onChange="buscarPapel()">
		<? fill_combo_vec(printers()); ?>
		</select>
	</td>
</tr>
<tr><td class="celdatexto">Papel</td>
	<td>
		<select name="papel">
		<option value="RPT_A4">A4</option>
		<option value="RPT_LETTER">Carta</option>
		<option value="RPT_LEGAL">Oficio</option>				
		</select>
	</td>
</tr>
<tr><td class="celdatexto">Copias :</td>
	<td><input type=text name=copias size=3 maxlength="2" value="1"></td></tr>
</table>
<table align="center" width="30%">
<tr><td align="center" colspan="2">
	<input type="button" value="<?=CANCELO;?>" onClick="inicializar(1);" class="botonout">
	<input type="button" value="<?=CONFIRMO;?>" onClick="confirma();" class="botonout">
<?  if (is_array($_POST["titulo"])) {
		foreach($_POST["titulo"] as $titulo) {
	//		echo($titulo . "\n");
			echo '<input type=hidden name="titulo[]" value="'.$titulo.'">';
		} // end foreach titulo
		echo("\n");
	} else {
		echo '<input type=hidden name="titulo" value="'.array($_POST["titulo"]).'">';
	} // end if array titulo
?>
	<input type=hidden name="archivo2" value="<? echo($_POST["archivo2"]); ?>">
	<input type=hidden name="sql" value="<? echo(stripslashes($_POST["sql"])); ?>">
	<input type=hidden name="confirmo">	
	<input type=hidden name="dondevamos" value="<? echo($_POST["dondevamos"]); ?>">		
	</td>
</tr>	
</table>
</form>
<? 

}//ifelse if (isset($_POST["confirmo"]))
require_once("../includes/inc_bottom.php"); ?>
</body>
</HTML>
<?
}
?>