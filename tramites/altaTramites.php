<?
require_once("../includes/lib.php");
if (check_auth($usrid, $perid) && check_permission($permiso)) {
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<HTML><HEAD>
<? require_once("../includes/inc_header.php"); ?>
<script type="text/javascript">
function inicializar() {
	document.getElementById('divFormDatos').style.display='';
	document.getElementById('divMensaje').style.display='none';
	document.form.botvolver.style.display='none';
	//Comentado para que deje el numero de registro origen
	document.form.CodRegOrig.value="";
	document.form.CodRegDest.value="";
	document.form.NroTramite.value="";
	document.form.NroVoucher.value="";	
	//DesComentado la linea de abajo para que NO deje la descripcion de registro origen si se comenta aparece la descripcion
	document.getElementById('spanDesRegOrig').innerHTML="";
	document.getElementById('spanDesRegDest').innerHTML="";
//	document.form.FecRetiro.value=document.form.FecRetiro.value.substr(;
	document.form.FecRetiro.value = '<?=date('d/m/Y');?>';
	document.form.CodRegOrig.focus();
}
function validaFormulario() {
	document.form.botconfirma.disabled=true;
	var ok = true;
	var errores = "";
	var foco = false;
/* verifico que sea un codigo numerico valido */
	if (document.form.CodRegOrig.value=="") {
		ok = false;
		errores += "- Debe ingresar el Código del Registro Origen\n";
		document.form.CodRegOrig.focus();
	}else{
		if (!Valido(document.form.CodRegOrig.value,'n')) 	{
			ok = false;
			errores += "- El Código del Registro Origen es Invalido\n";
			document.form.CodRegOrig.focus();
		}else{
			if (document.getElementById('spanDesRegOrig').innerHTML=='Inexistente' ||
				document.getElementById('spanDesRegOrig').innerHTML=='') {
					ok = false;
					errores += "- El Código del Registro Origen es Inexistente\n";
					document.form.CodRegOrig.focus();
			}else{
				if (document.getElementById('spanDesRegOrig').innerHTML=="El Registro posee solo función Destino") {
					ok = false;
					errores += "- El Código del Registro Origen posee solo función Destino\n";
					document.form.CodRegOrig.focus();
				}
			}
		}
	}
	if (errores!="") { document.form.CodRegOrig.focus(); foco=true;}
/* verifico que sea un codigo numerico valido */
	if (document.form.CodRegDest.value=="") {
		ok = false;
		errores += "- Debe ingresar el Código del Registro Destino\n";
		document.form.CodRegDest.focus();
	}else{
		if (!Valido(document.form.CodRegDest.value,'n')) {
			ok = false;
			errores += "- El Código del Registro Destino es Invalido\n";
			document.form.CodRegDest.focus();
		}else{
			if (document.form.CodRegOrig.value==document.form.CodRegDest.value) {
					ok = false;
					errores += "- El Código del Origen no debe ser Igual al de Destino.\n";
					document.form.CodRegDest.focus();
			}
			if (document.getElementById('spanDesRegDest').innerHTML=='Inexistente' ||
				document.getElementById('spanDesRegDest').innerHTML=='') {
					ok = false;
					errores += "- El Código del Registro Destino es Inexistente\n";
					document.form.CodRegDest.focus();
			}
		}
	}
	if ((errores!="")&&(!foco)) { document.form.CodRegDest.focus(); foco=true;}
/* verifico el Nro. de Tramite */
	if (document.form.NroTramite.value=="") {
		ok = false;
	    errores += "- Debe ingresar el Nro. de Tramite.\n";
	}else{
		if (!valDominio(document.form.NroTramite.value)) {
			ok = false;
		    errores += "- El Nro. de Tramite es Invalido.\n";
		}
	}
	if ((errores!="")&&(!foco)) { document.form.NroTramite.focus(); foco=true; }
/* verifico el Nro. de Voucher */	
	if (document.form.NroVoucher.value=="") {
		ok = false;
	    errores += "- Debe ingresar el Nro. de Voucher.\n";
	}else{
/*		if (document.form.NroVoucher.value.length < 8) {
			ok = false;
		    errores += "- El Nro. de Tramite es Voucher debe contener 8 digitos.\n";
		}else{*/
			if (!Valido(document.form.NroVoucher.value,'n')) 	{		
				ok = false;
			    errores += "- El Nro. de Voucher es Invalido.\n";
			}
/*		}*/
	}
	if ((errores!="")&&(!foco)) { document.form.NroVoucher.focus(); foco=true; }
/* verifico el la Fecha de Retiro */
	if (document.form.FecRetiro.value=="") {
			ok = false;
			errores += "- Debe Completar la Fecha de Retiro.\n";
	}else{
		if (!parseDate(document.form.FecRetiro,'%d/%m/%Y',true)) {
			ok = false;
			errores += "- El Formato de la Fecha de Retiro no es Valida.\n";
		}else{
			var fecretiro = document.form.FecRetiro.value.substr(6,4)+document.form.FecRetiro.value.substr(3,2)+document.form.FecRetiro.value.substr(0,2);
			if (fecretiro>document.form.fecHoy.value) {
								ok = false;
								errores += "- La Fecha de Retiro es Superior a la Fecha Actual.\n";
			}
			if (fecretiro<'20060101') {
								ok = false;
								errores += "- La Fecha debe ser mayor al 01/01/2006.\n";
			}
		}
	}
	dia = dia_habil(document.form.FecRetiro.value);
	
	if(dia!="1")
	{
		ok = false;
		errores += "Fecha invalida: "+dia+"\n"; 
	}
	if ((errores!="")&&(!foco)) { document.form.FecRetiro.focus(); foco=true; }
	if (ConsAjax('verificar','')) {
			ok = false;
			if (document.form.NroTramite.value == false) { errores += "- El Trámite ya existe.\n"; }
			if (document.form.NroVoucher.value == false) { errores += "- El Nro. de Voucher ya existe.\n"; }			
	};
	if (!ok) {
		alert("Hay errores en los datos:\n" + errores);
	}else{
		if (ConsAjax('grabar','')) {
			setTimeout("inicializar();",1500);
		};
	}
	document.form.botconfirma.disabled=false;
	return false;
}
function ajax(url) {
//alert(url);
	http.open("GET", url, false);
	http.send(null);
}
</script>
<script type="text/javascript" language="JavaScript" src="amTramites.js"></script>
<!-- Objeto Ajax -->
<script type="text/javascript" language="JavaScript" src="../includes/ajaxobjt.js"></script>
<!-- calendario desplegable -->
<script type="text/javascript" language="JavaScript" src="../includes/fecha.js"></script>
<style type="text/css">@import url(../calendar/calendar-win2k-1.css);</style>
<script type="text/javascript" src="../calendar/calendar.js"></script>
<script type="text/javascript" src="../calendar/lang/calendar-es.js"></script>
<script type="text/javascript" src="../calendar/calendar-setup.js"></script>
</HEAD>

<body onLoad="inicializar();" >
<? require_once('../includes/inc_topleft.php');
/* Contenido */
$pagina=basename($_SERVER['SCRIPT_FILENAME']);
require_once('../includes/inc_titulo.php');
include('amTramites_form.php'); ?>
<script type="text/javascript">
	Calendar.setup( { inputField: "FecRetiro", ifFormat: "%d/%m/%Y", button: "selfecha1" } );
</script>
<!--Contenido-->
<? require_once("../includes/inc_bottom.php");?>
</BODY></HTML>
<? }//Cierro if autorizacion
?>
