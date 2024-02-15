<?
require_once("../includes/lib.php");
if (check_auth($usrid, $perid) && check_permission($permiso, "eleTramites.php")) {
?>
<HTML><HEAD>
<? require_once("../includes/inc_header.php"); ?>
<script type="text/javascript" language="JavaScript" src="../includes/fecha.js"></script>
<script type="text/javascript">
function inicializar(id) {
	document.getElementById('divMensaje').style.display='none';
	document.form.botreset.style.display='none';
	document.form.modificado.value='';
	parametros="tipo=buscarxid"+
			   "&traCodigo="+id;
//tipo=altaitem&id=0&idsecc=10&nomitem=prueba item1&descitem=prueba desc&pagina=pagina&directorio=direc&parametro=param&valparametro=valparam&ordenitem=&despuesde=&targetitem=&tippermitem=&separador=true&default=valse&asignable=false
	  url="altaTramites_ajax.php?"+parametros;
//	  alert(url);
	  ajax(url);
	  if (http.readyState == 4) {
		results = http.responseText.split(";");
//alert(results);
		if (results[0]=='Inexistente')  {
			alert("Nro. de Tramite "+results[0]+" Inexistente. Ingreselo Nuevamente.\n");
		}else{
			if ((results[6]!="")||(results[7]!="")) {
			   alert("El Tramite esta Cerrado, no se puede Actualizar");
			   goMenu('eleTramites.php','opcion',<?=$_POST['opcion'];?>);
			}else{
				document.form.idregmod.value      =results[0];
				document.form.CodRegOrig.value    =results[1];
				document.getElementById('spanDesRegOrig').innerHTML=results[2];
				document.form.CodRegDest.value    =results[3];
				document.getElementById('spanDesRegDest').innerHTML=results[4];
				document.form.FecRetiro.value     =results[5];
	//			document.form.FecDevolucion.value =results[7];
	//			document.form.MotDevolucion.value =results[8];
				document.form.NroTramite.value    =results[9];
				document.form.NroVoucher.value    =results[10];				
				document.getElementById('subformMod').style.display='';
			    document.getElementById('divFormDatos').style.display='';
				document.form.CodRegOrig.disabled   = true;
				document.form.CodRegDest.disabled   = true;
				document.form.NroTramite.disabled   = true;
				document.form.NroVoucher.disabled   = true;				
				document.form.FecRetiro.disabled    = true;
				document.form.selfecha1.style.display='none';
				document.form.FecEntrega.disabled   = false;
				document.form.selfecha3.style.display='none';
				document.form.botdevolucion.style.display='';
				document.form.FecEntrega.focus();
			}
		}//if
	  }//if
}
function validaFormulario() {
	document.form.botconfirma.disabled=true;
	var ok = true;
	var errores = "";
/* verifico que sea un codigo numerico valido */
	if (!document.form.FecEntrega.disabled) {
		if ((document.form.FecEntrega.value) == "") {
				ok = false;
				errores += "- Ingrese la Fecha de Entrega Por Favor.\n";
		}
		if (!parseDate(document.form.FecEntrega,'%d/%m/%Y',true)) {
			ok = false;
			errores += "- El Formato de la Fecha de Entrega no es Valido.\n";
		} else {
			dtEntrega = checkDate(document.form.FecEntrega.value);
		}
		dtRetiro = checkDate(document.form.FecRetiro.value);
		var fecretiro  = document.form.FecRetiro.value.substr(6,4)+document.form.FecRetiro.value.substr(3,2)+document.form.FecRetiro.value.substr(0,2);
		var fecentrega = document.form.FecEntrega.value.substr(6,4)+document.form.FecEntrega.value.substr(3,2)+document.form.FecEntrega.value.substr(0,2);
		if (fecentrega<=fecretiro) {
			ok = false;
			errores += "- La Fecha de Entrega debe ser mayor a la Fecha de Retiro.\n";
		}
		if (fecentrega>document.form.fecHoy.value) {
			ok = false;
			errores += "- La Fecha de Entrega es posterior a la Fecha Actual.\n";
		}
<?php
$plazoentrega = getParametro(PAR_PLAZO_ENTREGA);
?>
		if (diffDateDay(dtRetiro, dtEntrega) >= <?php echo($plazoentrega); ?>) {
			ok = false;
			errores += "- No pueden pasar ms de <?php echo($plazoentrega); ?> d as entre el retiro y la entrega\n";
		}
	}else{
		if ((document.form.FecDevolucion.value) == "") {
				ok = false;
				errores += "- Ingrese la Fecha de Devolucin Por Favor.\n";
		}
		if (!parseDate(document.form.FecDevolucion,'%d/%m/%Y',true)) {
			ok = false;
			errores += "- El Formato de la Fecha de Devoluci n no es Valido.\n";
		}
		var fecretiro  = document.form.FecRetiro.value.substr(6,4)+document.form.FecRetiro.value.substr(3,2)+document.form.FecRetiro.value.substr(0,2);
		var fecdevolucion = document.form.FecDevolucion.value.substr(6,4)+document.form.FecDevolucion.value.substr(3,2)+document.form.FecDevolucion.value.substr(0,2);
		if (fecdevolucion<=fecretiro) {
			ok = false;
			errores += "- La Fecha de Devolucin debe ser mayor a la Fecha de Retiro.\n";
		}
		if (fecdevolucion>document.form.fecHoy.value) {
			ok = false;
			errores += "- La Fecha de Devolucin es Superior a la Fecha Actual.\n";
		}
		if (document.form.MotDevolucion.value==0) {
			ok = false;
			errores += "- Seleccione un Motivo de Devoluci n.\n";
		}
	}
	if (!ok) {
		alert("Hay errores en los datos:\n" + errores);
	}else{
		if (ConsAjax('grabar','')) {
			  if (document.form.FecEntrega.value=="") {
			  document.getElementById('grabado').innerHTML='Se ha Cargado la Fecha y Motivo de Devolución<br>'+
			  											   'al Tramite '+document.form.NroTramite.value.toUpperCase()+
			  											   ' con Fecha de Retiro '+document.form.FecRetiro.value;
	  		  }else{
			  document.getElementById('grabado').innerHTML='Se ha Cargado la Fecha de Entrega<br>'+
														   'al Tramite '+document.form.NroTramite.value.toUpperCase()+
			  											   ' con Fecha de Retiro '+document.form.FecRetiro.value;
			  }
			setTimeout("goMenu('eleTramites.php','opcion', <?=$_POST['opcion'];?>);",1500);
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
function devolucion() {
	if (document.form.botdevolucion.value=='Devolución') {
	document.form.FecEntrega.disabled    = true;
	document.form.FecEntrega.value       = '';
	document.form.FecDevolucion.disabled = false;
	document.form.MotDevolucion.disabled = false;
	document.form.botdevolucion.value='   Entrega   ';
    document.form.selfecha2.style.display='none';
    document.form.selfecha3.style.display='';
	document.form.FecDevolucion.focus();
	}else{
	document.form.FecEntrega.disabled    = false;
	document.form.FecDevolucion.value    = '';
	document.form.FecDevolucion.disabled = true;
	document.form.MotDevolucion.disabled = true;
	document.form.MotDevolucion.selectedIndex=0;
	document.form.botdevolucion.value='Devolución';
    document.form.selfecha2.style.display='';
    document.form.selfecha3.style.display='none';
	document.form.FecEntrega.focus();
	}
}
</script>
<script type="text/javascript" language="JavaScript" src="amTramites.js"></script>
<!-- Objeto Ajax -->
<script type="text/javascript" language="JavaScript" src="../includes/ajaxobjt.js"></script>
<!-- calendario desplegable -->
<style type="text/css">@import url(../calendar/calendar-win2k-1.css);</style>
<script type="text/javascript" src="../calendar/calendar.js"></script>
<script type="text/javascript" src="../calendar/lang/calendar-es.js"></script>
<script type="text/javascript" src="../calendar/calendar-setup.js"></script>
</HEAD>
<body onLoad="inicializar(<?=$_POST['idtramite'];?>);" >
<? require_once("../includes/inc_topleft.php");
/* Contenido */
$opcion=$_POST['opcion'];
$pagina="eleTramites.php";
require_once('../includes/inc_titulo.php');
include('amTramites_form.php');
?>
<script type="text/javascript">
	Calendar.setup( { inputField: "FecEntrega", ifFormat: "%d/%m/%Y", button: "selfecha2" } );
	Calendar.setup( { inputField: "FecDevolucion", ifFormat: "%d/%m/%Y", button: "selfecha3" } );
</script>
<!--Contenido-->
<? require_once("../includes/inc_bottom.php"); ?>
</BODY></HTML>
<?
} //Cierro if autorizacion
?>
