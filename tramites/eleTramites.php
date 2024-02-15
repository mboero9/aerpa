<?
require_once("../includes/lib.php");
if (check_auth($usrid, $perid) && check_permission($permiso)) {
//nueva version
?>
<HTML><HEAD>
<? require_once("../includes/inc_header.php"); ?>
<script type="text/javascript">
function inicializar() {
	document.form.NroVoucher.value='';
	document.form.NroTramite.value='';	
	document.form.NroVoucher.focus();
}
function ajax(url) {
//alert(url);
	http.open("GET", url, false);
	http.send(null);
}
function buscar() {
		var ok=true;
		var errores="";
		var buscadox="";
		if ((document.form.NroTramite.value == false)&&(document.form.NroVoucher.value == false)) {
				ok = false;
				errores ="- Complete el Nro. de Voucher � el Nro. de Tr�mite para realizar la busqueda.\n";
		}
		if ((document.form.NroTramite.value != false)&&(document.form.NroVoucher.value != false)) {
				ok = false;
				errores +="- Solo debe completar un solo criterio de b�squeda.\n";
		}
		if (document.form.NroTramite.value == true) {	
			buscadox='Tramite';
			if (!valDominio(document.form.NroTramite.value)) {
				ok = false;
				errores +="- El Nro. de Tr�mite es Inv�lido.\n";
			}
		}
/* verifico el Nro. de Voucher */	
		if (document.form.NroVoucher.value != "") {
/*			if (document.form.NroVoucher.value.length < 8) {
				ok = false;
				errores += "- El Nro. de Tramite es Voucher debe contener 8 digitos.\n";				
			}else{*/
			buscadox='Voucher';			
				if (!Valido(document.form.NroVoucher.value,'n')) 	{		
					ok = false;
					errores += "- El Nro. de Voucher es Invalido.\n";
				}
/*			}*/
		}		
	if (!ok) {
		alert("Hay errores en los datos:\n" + errores);
		return false
	}
		  parametros="tipo=buscar"+
					 "&NroTramite="+document.form.NroTramite.value.toUpperCase()+
					 "&NroVoucher="+document.form.NroVoucher.value;					 						 
		  url="altaTramites_ajax.php?"+parametros;
//		alert(url);
		  ajax(url);
		  if (http.readyState == 4) {
			results = http.responseText.split("|");
//alert(results);
			if (results[0]=='Inexistente')  {
				alert("Nro. de tramite "+buscadox+document.form.NroTramite.value.toUpperCase()+" Inexistente.\n");
				inicializar();

			}
			else if (results[0]=='Sin fecha')  {
				alert("Nro. de tramite "+buscadox+document.form.NroTramite.value.toUpperCase()+". Aun no posee fecha de entrega cargada.\n");
				inicializar();

			}
			else{
			   if (results.length<=2) {
				results2 = results[0].split(";");
				ir(results2[0]);
			   }else{
			    for (i=0;i<(results.length-1);i++) {
						results2 = results[i].split(";");
						var a='tramOrig'+i;
						var b='tramDest'+i;
						var c='tramNro'+i;
						var d='tramFec'+i;
						var e='tramId'+i;
						var f='linea'+i;
						document.getElementById(a).innerHTML=results2[2];
						document.getElementById(b).innerHTML=results2[4];
						document.getElementById(c).innerHTML=document.form.NroTramite.value.toUpperCase();
						document.getElementById(d).innerHTML=results2[5];
						document.getElementById(e).value=results2[0];
						document.getElementById(f).style.display='';
				}
				document.getElementById('divFormDatos').style.display='none';
				document.getElementById('divEleccion').style.display='';
			   }
			}
		  }
		  return false;
}
function cambioNroTramite() {
//	if (document.getElementById('subformMod').style.display=='') {
				document.form.botbuscar.disabled=false;
//	}
}
function seleccionado(i) {
  var id='tramId'+i;
  ir(document.getElementById(id).value);
}
function ir(i) {
	if (document.formsubmit.opcion.value==0) { //modificacion
	    document.formsubmit.action='modTramites.php';
	}
	document.formsubmit.idtramite.value=i;
	document.formsubmit.submit();
}
</script>
<script type="text/javascript" language="JavaScript" src="amTramites.js"></script>
<!-- Objeto Ajax -->
<script type="text/javascript" language="JavaScript" src="../includes/ajaxobjt.js"></script>
</HEAD>
<body onLoad="inicializar();" >
<? require_once("../includes/inc_topleft.php");
/* Contenido */
$opcion=$_POST['opcion'];
$pagina="eleTramites.php";
require_once('../includes/inc_titulo.php');
?>
<div id=divFormDatos>
<form name="form" action="" onSubmit="return buscar();">
<table align="center" width="50%" class=tablaconbordes style="height: 200px">
<tr><td class="celdatexto" align="right">N&uacute;mero de Voucher:</td>
    <td><input type=text class=textochico name=NroVoucher size=10 maxlength="8" style="text-transform:uppercase" onChange="cambioNroTramite();"></td>
 	<td rowspan="2"><input type=submit class="botonout" name=botbuscar value="Buscar" onMouseOver="this.className='botonover';" onMouseOut="this.className='botonout';"></td></tr>	
<tr><td class="celdatexto" align="right">N&uacute;mero de Tr&aacute;mite:</td>
    <td><input type=text class=textochico name=NroTramite size=10 maxlength="8" style="text-transform:uppercase" onChange="cambioNroTramite();"></td></tr>
</table>
</form>
</div>
<div id=divEleccion style="display:none">
<form action="" name=formeleccion>
<table class=tablaconbordes align=center cellpadding="0" cellspacing="0" border=0 width="80%">
<tr><td class=celdatitulo align=center colspan=4>Seleccione el Tr&aacute;mite a Modificar</td></tr>
<?
$clase='fondotabla1';
for ($i=0;$i<=40;$i++) { ?>
	<tr><td>
<div id=linea<?=$i?> style="display:none">
	<table cellpadding="1" cellspacing="1" width="100%">
<? if ($i==0) { ?>
	<tr><td class=celdatitulo align="center" width="35%">Registro Origen</td>
		<td class=celdatitulo align="center" width="35%">Registro Destino</td>
		<td class=celdatitulo align="center" width="10%">Nro.de Tr&aacute;mite</td>
		<td class=celdatitulo align="center" width="20%">Fecha de Retiro</td></tr>
<? } ?>
	<tr class=<?=$clase;?> onClick="seleccionado(<?=$i;?>);" onMouseOver="this.className = 'fondoconfirmacion';" onMouseOut="this.className = '<?=$clase;?>';" style="cursor:pointer">
	    <td class="celdatexto" width="35%"><span id=tramOrig<?=$i;?>></span></td>
		<td class="celdatexto" width="35%"><span id=tramDest<?=$i;?>></span></td>
		<td class="celdatexto" width="10%" align="center"><span id=tramNro<?=$i;?>></span></td>
		<td class="celdatexto" width="20%" align="center"><span id=tramFec<?=$i;?>></span><input type=hidden id=tramId<?=$i;?> /></td>		
	</tr>
	</table>
</div>
	</td></tr>
<? 	 	if ($clase=='fondotabla1') { $clase='fondotabla2';
    	} else { $clase='fondotabla1'; }
} //for ?>
</table>
<table width="50%" align=center>
<tr><td align="center"><input type=button class="botonout" name=botcvolver   value="<?=VOLVER;?>" onMouseOver="this.className = 'botonover';" onMouseOut="this.className = 'botonout';" onClick="document.getElementById('divEleccion').style.display='none'; document.getElementById('divFormDatos').style.display='';" /></td></tr>
</table>
</form>
</div>
<form name=formsubmit action="" method="post">
	<input type=hidden name=idtramite>
	<input type=hidden name=opcion value="<?=$_POST['opcion'];?>" />
</form>
<!--Contenido-->
<? require_once("../includes/inc_bottom.php"); ?>
</BODY></HTML>
<?
} //Cierro if autorizacion
?>
