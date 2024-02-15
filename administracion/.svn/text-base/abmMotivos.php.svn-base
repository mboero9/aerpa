<?
require_once("../includes/lib.php");
if (check_auth($usrid, $perid) && check_permission($permiso)) {
?>
<HTML><HEAD>
<? require_once("../includes/inc_header.php"); ?><script type="text/javascript">
function validarCreacion() {
	var ok = true;
	var errores = "";
	var f = document.form;
	var rx = /^[a-zA-Z0-9·¡È…ÌÕÛ”˙⁄¸‹Ò—][a-zA-Z0-9·¡È…ÌÕÛ”˙⁄¸‹Ò— \/\.\,\_\-]*$/;
	var rx2 = /^[0-9][0-9 \/\.\,\_\-]*$/;
	s = new String(f.desmotivo.value);
	if (!rx.test(s)) {
		ok = false;
		errores += "- Motivo de DevoluciÛn Invalido.\n";
	}
	if (rx2.test(s)) {
		ok = false;
		errores += "- Motivo de DevoluciÛn Invalido.\n";
	}
	if (!ok) {
		alert("Hay errores en los datos:\n" + errores);
		return(false);
	}else{
		return(true);
	}
} // end validarCreacion
function selecciono() {
	if (document.form.motivo.value==0) {
		document.getElementById('divNvoMotivo').style.visibility='hidden';
		document.form.botconfirmar.style.display='none';		
		document.form.botbajmotivo.style.display='none';		
		document.form.botvolver.style.display   ='none';		
		document.form.botnvomotivo.style.display='';				
	}else{
		document.form.botbajmotivo.style.display   ='';
		document.form.botconfirmar.style.display='';		
		document.form.botvolver.style.display   ='';				
		document.form.botnvomotivo.style.display='none';				
	    document.form.desmotivo.value=document.form.motivo.options[document.form.motivo.selectedIndex].text;
		document.getElementById('divNvoMotivo').style.visibility='visible';	
	    document.getElementById('subtitulo').innerHTML='Modificar Motivo';	
	    document.form.desmotivo.focus();
	}
}
function nvoMotivo() {
	document.form.motivo.value=0;
	document.form.botvolver.style.display='';					
	document.form.botconfirmar.style.display='';	
	document.form.botnvomotivo.style.display='none';			
	document.getElementById('divSelect').style.visibility='hidden';
	document.getElementById('divNvoMotivo').style.visibility='visible';
    document.getElementById('subtitulo').innerHTML='Ingrese el Nuevo Motivo';	
    document.form.desmotivo.value='';
    document.form.desmotivo.focus();
}
function volver(par) {
	if (par!="") {
		setTimeout("goMenu('abmMotivos.php')", 500);		
	}
}
function confirmarBaja() {
   document.form.desmotivo.disabled=true;
   document.getElementById('divSelect').style.visibility='hidden';   
   document.getElementById('divBotones').style.visibility='hidden';
   document.getElementById('divConfBaja').style.visibility='visible';      
}
</script>

</HEAD>
<body onLoad="volver('<?=$_POST['motivo'];?>');">
<? require_once('../includes/inc_topleft.php'); ?>
<!-- Contenido -->
<?
$pagina='abmMotivos.php';
require_once('../includes/inc_titulo.php');
if (!isset($_POST['motivo'])) {
?>
<form name="form" action="" method="post" onSubmit="return (validarCreacion());">
<div id=divSelect>
<table width=50% align=center cellpadding="0" cellspacing="1" height="100">
	<td class=celdatexto align="right">Motivo de Devoluci&oacute;n:</td>
	<td align="left">
		<SELECT name="motivo" class=textochico onChange="selecciono();">
	       <option value="0">-- Seleccione un Motivo de Devoluci&oacute;n--</option>		
<?	fill_combo("select MOT_CODIGO, MOT_DESCRIP from MOTIVO_DEV where mot_fecha_baja is null order by MOT_DESCRIP", $_POST["motivo"]); ?>		   
		</SELECT>
	</td>	
	</tr>
</table>
</div>
<input type=hidden name=baja value=0>
<input type=hidden name=permisocomo value=<?=$percomo;?>>
<div id=divNvoMotivo style="visibility:hidden">
<table width=70% align="center" class="tablaconbordes">
	<tr><td id=subtitulo class=celdatitulo align=center></td></tr>
	<tr>
	<td class="celdatexto" align="center">Motivo de Devoluci&oacute;n: <input type="text" name="desmotivo" size=50 maxlength="100"></td>
	</tr>
	<tr>
</table>
</div>
<div id=divConfBaja style="visibility:hidden">
<table width=70% align="center" class="tablaconbordes" height="100">
	<tr>
	<td class="textoerror" align="center">Esta Seguro que desea Eliminar el Motivo</td>
	</tr>
	<tr>
	<tr>
	<td align="center" class="celdatexto">
	<input class="botonover" type="button" name="botbajano" value="NO" onClick="location.href='abmMotivos.php'" >	
	<input class="botonover" type="button" name="botbajasi" value="SI" onClick="document.form.baja.value=1; document.form.submit();">
	</td>
	</tr>	
</table>
</div>
<div id=divBotones>
<table align="center">
	<tr>
	<td align="center" class="celdatexto">
	<input class="botonover" type="button" name="botvolver"    value="Volver"   onClick="location.href='abmMotivos.php'" style="display:none">
	<input class="botonover" type="button" name="botbajmotivo" value="Eliminar" onClick="confirmarBaja();" style="display:none">	
	<input class="botonover" type="button" name="botnvomotivo" value="Nuevo"    onClick="nvoMotivo();">
	<input class="botonover" type="submit" name="botconfirmar" value="Confirmar" style="display:none">
	</td>
	</tr>
</table>
</div>
</form>
<? 
}else{
	//actualiza la base
	$ok=false;
	if (($_POST['baja']==1)&&($_POST['motivo']!=0)) {
			$query = "UPDATE MOTIVO_DEV set mot_fecha_baja=".sqldate(dbtime()).",  
										    usr_id=".sqlint($usrid).", 
										    mot_fecha_act=".sqldate(dbtime())."
									where mot_codigo=".sqlstring($_POST['motivo']);
			if ($conn->execute($query))	{ $ok=true;  
				   $mensaje="SE HA DADO DE BAJA EL MOTIVO";
			}else{ $mensaje="NO SE HA PODIDO DAR DE BAJA EL MOTIVO"; }				   
	}else{
		if ($_POST['motivo']==0) {
			$query = "SELECT MOT_CODIGO FROM MOTIVO_DEV WHERE MOT_DESCRIP=".sqlstring($_POST['desmotivo']).
					" AND MOT_FECHA_BAJA IS NULL";
			$rs=$conn->execute($query);
			if(!$rs->EOF) {
				   $mensaje="MOTIVO EXISTENTE";						
			}else{
			$query = "INSERT INTO MOTIVO_DEV (mot_codigo, 
										  	  mot_descrip, 
										  	  mot_tipo, 											  
											  usr_id, 
											  mot_fecha_act) 
									VALUES (".sqlstring(numerador('MOTIVO_DEV')).", 
											".sqlstring($_POST['desmotivo']).",
											'D',
											".sqlint($usrid).", 
											".sqldate(dbtime()).")";
				if ($conn->execute($query))	{ $ok=true; 
					   $mensaje="SE HA DADO DE ALTA EL NUEVO MOTIVO";			
				}else{ $mensaje="NO SE HA PODIDO DAR DE ALTA EL MOTIVO"; }
			}//IF (!$rs->EOF)
		}else{
			$query = "SELECT MOT_CODIGO FROM MOTIVO_DEV WHERE MOT_DESCRIP=".sqlstring($_POST['desmotivo']).
					" AND MOT_FECHA_BAJA IS NULL AND MOT_CODIGO!=".sqlstring($_POST['motivo']);
			$rs=$conn->execute($query);
			if(!$rs->EOF) {
				   $mensaje="MOTIVO EXISTENTE";						
			}else{			
				$query = "UPDATE MOTIVO_DEV set mot_descrip=".sqlstring($_POST['desmotivo']).", 
												usr_id=".sqlint($usrid).", 
												mot_fecha_act=".sqldate(dbtime())."
										where mot_codigo=".sqlstring($_POST['motivo']);
				if ($conn->execute($query))	{ $ok=true; 
					   $mensaje="SE HA ACTUALIZADO EL MOTIVO";						
				}else{ $mensaje="NO SE HA PODIDO MODIFICAR EL MOTIVO"; }	
			}
		}//if else ($_POST['motivo']==0)
	}//if else(($_POST['baja']==1)&&($_POST['motivo']!=0))
	if ($ok) {
			require_once('../includes/inc_grabado.php');
	}//if ok
	?>
	<table align="center">
		<tr height="200">
		<td class=textoerror><?=$mensaje;?></td>
		</tr>
	</table>
	<?

}//if (!isset())
?>
<!--Contenido-->
<? require_once("../includes/inc_bottom.php");?>
</BODY></HTML>
<?
}//Cierro if autorizacion
?>
