<script type="text/javascript">
function showMenu(id) {
} // end showMenu
function goMenu(link, param, valor) {
	document.frmmenu.action = link;
	document.getElementById("mnu_param").name = param;
	document.getElementById("mnu_param").value = valor;
	document.frmmenu.submit();
} // end goMenu
function desplegar(iddiv) {
	var image = 'img'+iddiv;
	var secc  = 'mnu'+iddiv;
	var capas   = document.getElementsByTagName('div');	
	for (i=0;i<capas.length;i++){
		if (capas[i].id.substr(0,3)=='mnu') {		
				var flag  = 'flagsecc'+i;
				capas[i].style.display='none';
				imagesecc  = capas[i].id.replace('mnu','img');
			    document.getElementById(flag).value='';
			    document.getElementById(imagesecc).src="../imagenes/flec_derecha.gif";
		}
	}
		var flag  = 'flag'+iddiv;	
	    document.getElementById(flag).value='1';
	    document.getElementById(secc).style.display='';
	    document.getElementById(image).src="../imagenes/flec_abajo.gif";
}
function desplegar_todo() {
 var capas   = document.getElementsByTagName('div');
if (document.frmmenu.allsecc.value=='') {
// desplegar
    document.getElementById('flagallsecc').value='1';
	for (i=0;i<capas.length;i++){
		if (capas[i].id.substr(0,3)=='mnu') {
				var flag  = 'flagsecc'+i;
				capas[i].style.display='';
				imagesecc  = capas[i].id.replace('mnu','img');
			    document.getElementById(flag).value='1';
			    document.getElementById(imagesecc).src="../imagenes/flec_abajo.gif";
		}
	}
    document.getElementById('masmenos').src="../imagenes/icono_menos.gif";
} else {
//plegar
    document.getElementById('flagallsecc').value='';
	for (i=0;i<capas.length;i++){
		if (capas[i].id.substr(0,3)=='mnu') {
				var flag  = 'flagsecc'+i;
				capas[i].style.display='none';
				imagesecc  = capas[i].id.replace('mnu','img');
			    document.getElementById(flag).value='';
			    document.getElementById(imagesecc).src="../imagenes/flec_derecha.gif";
		}
	}
    document.getElementById('masmenos').src="../imagenes/icono_mas.gif";
}
}
</script>
<form name=frmmenu action="" method="post">
<input type=hidden id=mnu_param>
<table border=0 cellspacing=2 cellpadding=0 width=100%>
<?php
// Obtener la seccion activa para mostrar desglosada
$tmp = explode("/", dirname($_SERVER["PHP_SELF"]));
$mnu_path = $tmp[count($tmp) - 1];
$mnu_pag = basename($_SERVER["PHP_SELF"]);
$mnu_sql =
	"Select ADM_SEC_ID " .
	"From SEGADMINABM " .
	"Where ADM_ABM_LINK = " . sqlstring($mnu_pag) .
	"	And ADM_ABM_LINK_DIR = " . sqlstring($mnu_path);
$mnu_rs = $conn->Execute($mnu_sql);
if ($mnu_rs->EOF) {
	$mnu_sec = $mnu_rs->fields["ADM_SEC_ID"];
} // end if eof

// Obtener el menu completo para mostrar
$mnu_sql =
	"Select s.ADM_SEC_ID,s.ADM_SEC_NOMBRE as SECCION,a.ADM_ABM_ID,a.ADM_ABM_NOMBRE as ABM," .
	"	a.ADM_ABM_DESCRIPCION,a.ADM_ABM_LINK,a.ADM_ABM_LINK_DIR,a.ADM_ABM_LINK_PARAM," .
	"	a.ADM_ABM_LINK_PARAM_VALOR,a.ADM_ABM_SEPARADOR " .
	"From SEGADMINSECCION s " .
	"	Inner Join SEGADMINABM a On s.ADM_SEC_ID = a.ADM_SEC_ID " .
	"Where a.ADM_ABM_ID In ( " .
	"	Select ADM_ABM_ID From SEGADMINSEGURIDADABM " .
	"	Where PER_ID = " . sqlint($perid) . ") " .
	"Order by s.ADM_SEC_ORDEN, s.ADM_SEC_ID, a.ADM_ABM_ORDEN, a.ADM_ABM_ID";
$mnu_rs = $conn->Execute($mnu_sql);

// recorrer los elementos de menu, con corte de control por seccion
$mnu_lastsec = null;
$mnu_separador = false;
$seccnro=0;
while (!$mnu_rs->EOF) {
	// corte de control por seccion
	if ($mnu_lastsec != $mnu_rs->fields["ADM_SEC_ID"]) {
	 if (empty($mnu_lastsec)) {
?>
		<input type=hidden name="allsecc" id="flagallsecc" value="<?=$_POST["allsecc"];?>">
<?php
	    if ($_POST["allsecc"]=="") { $allimage='mas'; }else{ $allimage='menos'; }
		echo "<tr><td align=right class='menuback textochico'>
		          <a onclick='desplegar_todo();' style='cursor:pointer'>
				  <font color=#15539b>Men&uacute;</font>&nbsp;
				  <img id='masmenos' src='../imagenes/icono_$allimage.gif' width=10 height=10>
				  </a>
				  </td></tr>";
	 } else {
	 	echo "</table></div></td></tr>";
	 }
?>
	<tr><td class=menuseccion onclick="desplegar('secc<?=$seccnro;?>')" style="cursor:pointer">
	  <img id="<? echo('imgsecc'.$seccnro); ?>" src="../imagenes/flec_<? if ($_POST["secc".$seccnro]=="") { echo('derecha'); }else{ echo'abajo'; }?>.gif"><? echo(htmlentities($mnu_rs->fields["SECCION"])); ?>
	</td></tr>
	<tr><td>

	<div id="mnusecc<?=$seccnro;?>" <? if ($_POST["secc".$seccnro]=="") { echo('style="display:none"');}?>>
	<input type=hidden name="secc<?=$seccnro;?>" id="flagsecc<?=$seccnro;?>" value="<?=$_POST["secc".$seccnro];?>">

	 <table border=0 cellspacing=0 cellpadding=0 width=100% style="border-color: #15539b;border-style: solid;border-width: 8px 2px 2px;">
<?php
		$seccnro++;
		$mnu_lastsec = $mnu_rs->fields["ADM_SEC_ID"];
		$mnu_display = ($mnu_lastsec == $mnu_sec ? "" : "none");
	} elseif ($mnu_separador) {
?>
	<tr><td class=menusep height=1></td></tr>
<?php
	} // end if cambio seccion/separador

	// mostrar el item de menu
	$mnu_link = "../" . $mnu_rs->fields["ADM_ABM_LINK_DIR"] . "/" . $mnu_rs->fields["ADM_ABM_LINK"];
	$mnu_nombre = $mnu_rs->fields["ABM"];
	$mnu_title = $mnu_rs->fields["ADM_ABM_DESCRIPCION"];
	$mnu_param = $mnu_rs->fields["ADM_ABM_LINK_PARAM"];
	if (trim($mnu_param) == "") {
		$mnu_param = "_dummy_";
	} // end if param vacio
	$mnu_valor = $mnu_rs->fields["ADM_ABM_LINK_PARAM_VALOR"]
?>
	<tr><td class=menuitemOut onMouseOver="this.className = 'menuitemOvr';" onMouseOut="this.className = 'menuitemOut';"
	     onclick="goMenu('<?php echo($mnu_link); ?>', '<?php echo($mnu_param); ?>', '<?php echo($mnu_valor); ?>');" title="<?php echo(htmlentities($mnu_title)); ?>">
		&nbsp;&nbsp;&nbsp;&nbsp;<?php echo(htmlentities($mnu_nombre)); ?></td></tr>
<?php

	// si hay marca de separador, agregar una linea mas vacia
	$mnu_separador = $mnu_rs->fields["ADM_ABM_SEPARADOR"];

	// proximo item
	$mnu_rs->MoveNext();
} // end while !eof
	 if (!empty($mnu_lastsec)) { echo "</table></div></td></tr>"; }
?>

</table>
</form>