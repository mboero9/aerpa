<?
require_once("../includes/lib.php");
if (check_auth($usrid, $perid) && check_permission($permiso, "eleTramites.php")) {
?>
<HTML><HEAD>
<? require_once("../includes/inc_header.php"); ?>
<script type="text/javascript">
function devolucion() {
	if (document.form.botdevolucion.value=='Devolución') {
//		document.form.FecEntrega.disabled    = true;
//		document.form.FecEntrega.value       = '';
		document.form.MotDevolucion.disabled = false;
		document.form.botdevolucion.value='   Entrega   ';
		document.getElementById('fecentdev').innerHTML = 'Fecha de Devolución:';						
//	    document.form.selfecha2.style.display='none';
	}else{
//		document.form.FecEntrega.disabled    = false;
		document.form.MotDevolucion.disabled = true;
		document.form.MotDevolucion.selectedIndex=0;
		document.form.botdevolucion.value='Devolución';
		document.getElementById('fecentdev').innerHTML = 'Fecha de Entrega:';				
//	    document.form.selfecha2.style.display='';
		document.form.FecEntrega.focus();
	}
}
function inicializar(id) {
	document.getElementById('divFormDatos').style.display='';
	document.getElementById('divMensaje').style.display='none';
	document.form.botreset.style.display='none';
	document.form.modificado.value='';
	document.getElementById('subformMod').style.display='';
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
			alert("Tramite "+results[0]+" Inexistente. Ingreselo Nuevamente.\n");
			goMenu('eleTramites.php','opcion', <?=$_POST['opcion'];?>);
		}else{
			document.form.idregmod.value      =results[0];
			document.form.CodRegOrig.value    =results[1];
			document.getElementById('spanDesRegOrig').innerHTML=results[2];
			document.form.CodRegDest.value    =results[3];
			document.getElementById('spanDesRegDest').innerHTML=results[4];
			document.form.FecRetiro.value     =results[5];
			document.form.FecEntrega.value    =results[6];
			document.form.MotDevolucion.value =results[7];
			document.form.NroTramite.value    =results[8];
			document.form.NroVoucher.value    =results[9];			
			document.form.idremito_ori.value  =results[10];						
			document.form.idremito_des.value  =results[11];									
			document.form.FecCierre.value     =results[12];									
		}//if	
		if ((results[11]!="")||(results[6]!="")) { 
		   document.form.CodRegOrig.disabled=true;
		   document.form.CodRegDest.disabled=true;
		   document.form.NroTramite.disabled=true;
		   document.form.NroVoucher.disabled=true;		   		   
		   document.form.FecRetiro.disabled=true;
		   document.form.selfecha1.style.display='none';		   
			if (results[10]!="") { 		   
			   document.form.selfecha2.style.display='none';		   
		    }else{
			   document.form.selfecha2.style.display='';
			   document.form.botdevolucion.style.display='';
			   document.form.FecEntrega.disabled=false;		   		   
			   document.form.FecEntrega.focus();		   		   
		    }
		}else{
			   document.form.selfecha2.style.display='none';		   		
		}
	  }//if
}
function validaFormulario() {
document.form.botconfirma.disabled=true;
var ok = true;
var errores = "";
/* verifico que sea un codigo numerico valido */
if (!document.form.CodRegOrig.disabled) {
	if (document.form.CodRegOrig.value=="")	{	
			ok = false;
			errores += "- Ingrese un Registro Origen\n";
	}else{	
		if (!Valido(document.form.CodRegOrig.value,'n')) 	{
			ok = false;
			errores += "- El Registro Origen es Invalido\n";
		}else{
			if (document.getElementById('spanDesRegOrig').innerHTML=='Inexistente' ||
				document.getElementById('spanDesRegOrig').innerHTML=='') {
					ok = false;
					errores += "- El Registro Origen es Inexistente\n";
			}
		}
	}
/* verifico que sea un codigo numerico valido */
	if (!Valido(document.form.CodRegDest.value,'n')) {
		ok = false;
		errores += "- El Registro Destino es Invalido\n";
	}else{
		if (document.form.CodRegOrig.value==document.form.CodRegDest.value) {
				ok = false;
				errores += "- El Registro Origen no debe ser Igual al de Destino.\n";
		}
		if (document.getElementById('spanDesRegDest').innerHTML=='Inexistente' ||
			document.getElementById('spanDesRegDest').innerHTML=='') {
	 			ok = false;
				errores += "- El Registro Destino es Inexistente\n";
		}
	}
/* verifico el Nro. de Tramite */
	if (!valDominio(document.form.NroTramite.value)) {
		ok = false;
	    errores += "- El Nro. de Tramite es Invalido.\n";
	}
	if (document.form.NroVoucher.value=="") {
		ok = false;
	    errores += "- Debe ingresar el Nro. de Voucher.\n";
	}else{
			if (!Valido(document.form.NroVoucher.value,'n')) 	{		
				ok = false;
			    errores += "- El Nro. de Voucher es Invalido.\n";
			}
	}
	
/* verifico la fecha de retiro */	
	if (document.form.FecRetiro.value == false) {
						ok = false;
						errores += "- Debe ingresar la Fecha de Retiro.\n";
	}else{
		if (!parseDate(document.form.FecRetiro,'%d/%m/%Y',true)) {
			ok = false;
			errores += "- El Formato de la Fecha de Retiro no es Valido.\n";
		}else{
	
			var fecretiro = document.form.FecRetiro.value.substr(6,4)+document.form.FecRetiro.value.substr(3,2)+document.form.FecRetiro.value.substr(0,2);
			if (fecretiro>document.form.fecHoy.value) {
								ok = false;
								errores += "- La Fecha de Retiro es Superior a la Fecha Actual.\n";
			}//if
			if (fecretiro<'20060101') {
								ok = false;
								errores += "- La Fecha debe ser mayor al 01/01/2006.\n";
			}//if
		}//IF formato
	}//if else
	}
//	else{
		var fecretiro  = document.form.FecRetiro.value.substr(6,4)+document.form.FecRetiro.value.substr(3,2)+document.form.FecRetiro.value.substr(0,2);
		if ((document.form.idremito_des.value)!= "") {
			if (document.form.FecEntrega.value=="") {
						ok = false;
						errores += "- Debe ingresar la Fecha de "+(document.form.MotDevolucion.disabled ? "Entrega" : "Devolución")+".\n";
			}else{		
				if (!parseDate(document.form.FecEntrega,'%d/%m/%Y',true)) {
					ok = false;
					errores += "- El Formato de la Fecha de "+(document.form.MotDevolucion.disabled ? "Entrega" : "Devolución")+" no es Valido.\n";
				}else{
					var fecentrega = document.form.FecEntrega.value.substr(6,4)+document.form.FecEntrega.value.substr(3,2)+document.form.FecEntrega.value.substr(0,2);
					if (fecentrega<=fecretiro) {
						ok = false;
						errores += "- La Fecha de "+(document.form.MotDevolucion.disabled ? "Entrega" : "Devolución")+" debe ser mayor a la Fecha de Retiro.\n";
					}
					if (fecentrega>document.form.fecHoy.value) {
						ok = false;
						errores += "- La Fecha de "+(document.form.MotDevolucion.disabled ? "Entrega" : "Devolución")+" es Superior a la Fecha Actual.\n";
					}
				}
			}//if else (document.form.FecRetiro.value=="")
		}

	if (!ConsAjax('verificar2','')) {
			ok = false;
			if (document.form.NroTramite.value == false) { errores += "- El Tramite ya existe.\n"; }									
			if (document.form.NroVoucher.value == false) { errores += "- El Nro. de Voucher ya existe.\n"; }			
	};

	var dia ="";
	if(document.form.FecRetiro.value != "")
	{
		dia = dia_habil(document.form.FecRetiro.value);
		if(dia!="1")
		{
			ok = false;
			errores += "Fecha invalida: "+dia+"\n"; 
		}
	}
	if(document.form.FecEntrega.value != "")
	{
		dia = dia_habil(document.form.FecEntrega.value);
		if(dia!="1")
		{
			ok = false;
			errores += "Fecha invalida: "+dia+"\n"; 
		}
	}
	if(document.form.FecCierre.value !="")
	{
		dia = dia_habil(document.form.FecCierre.value);
		if(dia!="1")
		{
			ok = false;
			errores += "Fecha invalida: "+dia+"\n"; 
		}
	}
	if (!ok) {
		alert("Hay errores en los datos:\n" + errores);
	}else{
		if (ConsAjax('grabar','')) {
			  document.getElementById('grabado').innerHTML='El Tramite '+document.form.NroTramite.value.toUpperCase()+
			  											   ' con Fecha de Retiro '+document.form.FecRetiro.value+'<BR>Ha sido Modificado Correctamente';

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
<body onLoad="inicializar(<?=$_POST['idtramite'];?>);" >
<? require_once("../includes/inc_topleft.php");
/* Contenido */
$opcion=$_POST['opcion'];
$pagina="eleTramites.php";
require_once('../includes/inc_titulo.php');
include('amTramites_form.php');
?>
<script type="text/javascript">
	Calendar.setup( { inputField: "FecRetiro", ifFormat: "%d/%m/%Y", button: "selfecha1" } );
	Calendar.setup( { inputField: "FecEntrega", ifFormat: "%d/%m/%Y", button: "selfecha2" } );
</script>
<!--Contenido-->
<? require_once("../includes/inc_bottom.php"); ?>
</BODY></HTML>
<?
} //Cierro if autorizacion
?>
