<?
require_once("../includes/lib.php");
require_once("../report/report.php");
if (check_auth($usrid, $perid) && check_permission($permiso)) {
?>
<HTML><HEAD>
<? require_once("../includes/inc_header.php"); ?>
</HEAD>
<script type="text/javascript">
function confirma() {
	if (document.formimpresion.prn.value!="") {
		document.formimpresion.confirmo.value=1;
		document.formimpresion.submit();
	}
}
function inicializar(par) {
  if (par==1) {
//    goMenu(document.formimpresion.dondevamos.value);
  }
}
</script>
<body onLoad="inicializar(<?=$_POST["confirmo"];?>)">
<? require_once("../includes/inc_topleft.php"); ?>
<br><br><br><br><br><br>
<?
if (isset($_POST["confirmo"])) {
	$sql = stripslashes($_POST["sql"]);
	$prn = $_POST["prn"];
	$rs = $conn->Execute($sql);
	$rpt =& new Report();
	$rpt->Recordset = $rs;
	$rpt->LoadFromXML("conTramites.xml");
	$rpt->PageSize = RPT_A4;
	$rpt->Generate();
	$psfile = $rpt->SendToFile();
	for (j=1; j<=$_POST["copias"]; j++) {
		print_text($prn, $psfile);
	}
	unlink($psfile);
}else{ 
?>
<form name=formimpresion action="" method="post">
<table align="center" width="30%" class=tablaconbordes>
<tr><th colspan=2 align=center class=celdatitulo>Seleccionar Impresora</th></tr>
<tr valign="middle"><td>Impresora:</td>
	<td><select name=prn id="prn" class="txtbox">
		<? fill_combo_vec(printers()); ?>
		</select>
	</td>
</tr>
<tr><td>Copias : </td>
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
<!--	<input type=hidden name="titulosql" value="<? echo($_POST["titulosql"]); ?>">
	<input type=hidden name="anchos" value="<? echo($_POST["anchos"]); ?>">		
-->	<input type=hidden name="sql" value="<? echo(stripslashes($_POST["sql"])); ?>">
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