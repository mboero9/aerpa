<?
require_once("../includes/lib.php");
if (check_auth($usrid, $perid) && check_permission($permiso)) {
?>
<HTML><HEAD>
<? require_once("../includes/inc_header.php"); ?>
<script type="text/javascript">
function inicializar(muestraVoucher,tipo) {
	document.getElementById('remito').style.display='';
	document.getElementById('okremito').style.display='none';
	document.getElementById('voucher').style.display='';
	document.getElementById('okvoucher').style.display='none';			
	document.form.reset();
	document.form.remDestino.focus();
	if (muestraVoucher == 2){
		habilitaVoucher(2);
	}else{
		habilitaVoucher(1);
	}
	if (tipo == 'dev')
	{
	document.form.NroVoucher.focus();
	}
	
}
function ajax(url) {
//alert(url);
	http.open("GET", url, false);
	http.send(null);
}
function buscar_tramite() {
		var ok=true;
		var errores="";
		var buscadox="";		
		document.form.idtramite.value="";		
		if ((document.form.NroTramite.value == false)&&(document.form.NroVoucher.value == false)) {
				ok = false;
				errores ="- Complete el Nro. de Voucher ó el Nro. de Trámite para realizar la busqueda.\n";
		}
/*		if ((document.form.NroTramite.value != false)&&(document.form.NroVoucher.value != false)) {
				ok = false;
				errores +="- Solo debe completar un solo criterio de búsqueda.\n";
		}*/
		if (document.form.NroTramite.value == true) {		
			buscadox='Tramite';		
			if (!valDominio(document.form.NroTramite.value)) {
				ok = false;
				errores +="- El Nro. de Trámite es Inválido.\n";
			}
		}
/* verifico el Nro. de Voucher */	
		if (document.form.NroVoucher.value != "") {
			buscadox='Voucher';						
				if (!Valido(document.form.NroVoucher.value,'n')) 	{		
					ok = false;
					errores += "- El Nro. de Voucher es Invalido.\n";
				}
		}		
	if (!ok) {
		//alert("Hay errores en los datos:\n" + errores);
		return false
	}
		  parametros="tipo=buscar"+
					 "&NroTramite="+document.form.NroTramite.value.toUpperCase()+
					 "&NroVoucher="+document.form.NroVoucher.value;					 
//tipo=altaitem&id=0&idsecc=10&nomitem=prueba item1&descitem=prueba desc&pagina=pagina&directorio=direc&parametro=param&valparametro=valparam&ordenitem=&despuesde=&targetitem=&tippermitem=&separador=true&default=valse&asignable=false
		  url="altaTramites_ajax.php?"+parametros;
//		alert(url);
		  ajax(url);
		  if (http.readyState == 4) {
			results = http.responseText.split("|");
//alert(results);
			if (results[0]=='Inexistente')  {
				//alert("Nro. de "+buscadox+document.form.NroTramite.value.toUpperCase()+" Inexistente. Ingréselo Nuevamente.\n");
				//inicializar();
				return false;
			}else{
			   if (results.length<=2) {
				results2 = results[0].split(";");
				if (results2[10]=="") {
					document.form.NroTramite.value=Trim(results2[8]);
					document.form.NroVoucher.value=Trim(results2[9]);
				}else{
					//alert("El Tramite tiene generado el Remito de Origen.\n"+
						//  "No se puede ingresar una devolución");
						//document.form.NroTramite.value="";
						//document.form.NroVoucher.value="";						  
						return false;
				}
				ir(results2[0]);
			   }else{
			    var sinidorigen=0;
				var tmp_NroTramite="";
				var tmp_NroVoucher="";				
			    for (i=0;i<(results.length-1);i++) {
					results2 = results[i].split(";");				
				   if (results2[10]=="") { sinidorigen++;
						tmp_NroTramite=Trim(results2[8]);
						tmp_NroVoucher=Trim(results2[9]);				   
				   }
				}			   
				if (sinidorigen>1) {
				s = "";			   
				fondo = 1;
				results2 = "";				
			    for (i=0;i<(results.length-1);i++) {
						results2 = results[i].split(";");
						
// falta ocultar los que tienen ID						
					s += "<tr class=fondotabla" + fondo + " onClick=\"ir("+results2[0]+"); document.form.NroVoucher.value='"+Trim(results2[9])+"';\" onMouseOver=\"this.className = 'fondoconfirmacion';\" onMouseOut=\"this.className ='fondotabla" + fondo + "';\" style=\"cursor:pointer; "+(results2[10]!="" ? "display:none" : "")+"\">"+
						 "	<td class=celdatexto>" + results2[2] + "</td>"+
						 "	<td class=celdatexto>" + results2[4] + "</td>"+
						 "	<td class=celdatexto align=center>" + results2[8] + "</td>"+
						 "	<td class=celdatexto align=center>" + results2[9] + "</td>"+		
						 "	<td class=celdatexto align=center>" + results2[5] + "</td>"+
						 "   </tr>\n";
						
						fondo = (fondo == 1 ? 2 : 1);
						
				}//FOR
				document.getElementById("tramites").tBodies[0].innerHTML = s;				
				document.getElementById('divFormDatos').style.display='none';
				document.getElementById('divEleccion').style.display='';
				}else{//if (sinidorigen>1)
					if (sinidorigen==1){
						document.form.NroTramite.value=tmp_NroTramite;
						document.form.NroVoucher.value=tmp_NroVoucher;					
					}else{
					//alert("El Tramite tiene generado el Remito de Origen.\n"+
						//  "No se puede ingresar una devolución");
						//document.form.NroTramite.value="";
						//document.form.NroVoucher.value="";
						return false;
					}
				}//ifelse (sinidorigen>1)
			   }//ifelse (results.length<=2)
			}//ifelse (results[0]=='Inexistente')
		  }
		  return false;
}


function buscar_voucher( origen ) {
	var ok=true;
	var errores="";
	var buscadox="";
	switch ( origen.name ) {
		case 'numeroTramite':
			if ( origen.value != "" && !valDominio(origen.value)) {
				alert("Inexistente");
				return false;
			}
			var destino = document.form.numeroVoucher;
			var entodev = "_2";
			break;
		case 'numeroVoucher':
			var destino = document.form.numeroTramite;
			var entodev = "_2";
			break;
		case 'NroTramite':
			if ( origen.value != "" && !valDominio(origen.value)) {
				alert("Inexistente");
				return false;
			}
			var destino = document.form.NroVoucher;
			var entodev = "";
			break;
		case 'NroVoucher':
			var destino = document.form.NroTramite;
			var entodev = "";
			break;
	}
	if ((origen.value == false)&&(destino.value == false)) {
		ok = false;
		errores ="- Complete el Nro. de Voucher ó el Nro. de Trámite para realizar la busqueda.\n";
	}
	if ( origen.value != "") {
		if(origen.name.indexOf('Tramite')!=-1) {
			buscadox='Voucher';
			if (!valDominio(origen.value)) {
				errores +="- El Nro. de Trámite es Inválido.\n";
				ok = false;
			}
		} else {
			buscadox='Tramite';
			if (!Valido(origen.value,'n')) 	{		
				ok = false;
				errores += "- El Nro. de Voucher es Invalido.\n";
			}
		}
	}
	if (destino.value != "") {
		if(destino.name.indexOf('Tramite')!=-1) {
			buscadox='Voucher';
			if (!Valido(destino.value,'n')) 	{		
				ok = false;
				errores += "- El Nro. de Voucher es Invalido.\n";
			}
		} else {
			buscadox='Tramite';
			if (!valDominio(destino.value)) {
				ok = false;
				errores +="- El Nro. de Trámite es Inválido.\n";
			}
		}
	}		
	if (!ok) {
		//alert("Hay errores en los datos:\n" + errores);
		//document.form.numeroVoucher.focus();
		return false
	}

	parametros="tipo=buscar_2"+
		"&"+origen.name+"="+origen.value.toUpperCase()+
		"&"+destino.name+"="+destino.value.toUpperCase();
	url="altaTramites_ajax.php?"+parametros;
	
	ajax(url);
	
	if (http.readyState == 4) {
		results = http.responseText.split("|");
		if (results[0]=='Inexistente'){
			alert(results[0]);
			 return false;
		}else {
			if (results[0]=='sinremitodestino') return false;
			else {
				if (results[0]=='tramitecargado') return false;						
				else {
					if (results.length<=2) {
						results2 = results[0].split(";");
						if(destino.name.indexOf('Tramite')!=-1) {
							destino.value =	Trim(results2[8]);
							origen.value = Trim(results2[9]);
						} else {
							destino.value =	Trim(results2[9]);
							origen.value = Trim(results2[8]);
						}
						eval('ir'+entodev+'('+results2[0]+');');
				   }else{
						s = "";
						fondo = 1;
						results2 = "";				
						for (i=0;i<(results.length-1);i++) {
							results2 = results[i].split(";");
							s += "<tr class=fondotabla" + fondo + " onClick=\"ir"+entodev;
							if(destino.name.indexOf('Tramite')!=-1) {
								s += "("+results2[0]+"); document.form."+origen.name+".value='"+Trim(results2[9]);
								s += "'; document.form."+destino.name+".value='"+Trim(results2[8])+"';\" ";
							} else {
								s += "("+results2[0]+"); document.form."+destino.name+".value='"+Trim(results2[9]);
								s += "'; document.form."+origen.name+".value='"+Trim(results2[8])+"';\" ";
							}
							
							s += "onMouseOver=\"this.className = 'fondoconfirmacion';\" onMouseOut=\"this.className ='fondotabla";
							s += fondo + "';\" style=\"cursor:pointer;\">"+
							"	<td class=celdatexto>" + results2[2] + "</td>"+
							"	<td class=celdatexto>" + results2[4] + "</td>"+
							"	<td class=celdatexto align=center>" + results2[8] + "</td>"+
							"	<td class=celdatexto align=center>" + results2[9] + "</td>"+		
							"	<td class=celdatexto align=center>" + results2[5] + "</td>"+
							"   </tr>\n";
							
							fondo = (fondo == 1 ? 2 : 1);
						}//FOR
						document.getElementById("tramites").tBodies[0].innerHTML = s;				
						document.getElementById('divFormDatos').style.display='none';
						document.getElementById('divEleccion').style.display='';
					}
				}
			}				
		}
	}
	return true;
}

function buscar_tramite_2() {
		var ok=true;
		var errores="";
		var buscadox="";		
		document.form.idtramite_2.value="";		
		if ((document.form.numeroTramite.value == false)&&(document.form.numeroVoucher.value == false)) {
				ok = false;
				errores ="- Complete el Nro. de Voucher ó el Nro. de Trámite para realizar la busqueda.\n";
		}

		if (document.form.numeroTramite.value != "") {		
			buscadox='Tramite';		
			if (!valDominio(document.form.numeroTramite.value)) {
				ok = false;
				errores +="- El Nro. de Trámite es Inválido.\n";
			}
		}
		/* verifico el Nro. de Voucher */	
		if (document.form.numeroVoucher.value != "") {
			buscadox='Voucher';						
				if (!Valido(document.form.numeroVoucher.value,'n')) 	{		
					ok = false;
					errores += "- El Nro. de Voucher es Invalido.\n";
				}
		}		
	if (!ok) {
		//alert("Hay errores en los datos:\n" + errores);
		//document.form.numeroVoucher.focus();
		return false
	}
		  parametros="tipo=buscar_2"+
					 "&NroTramite="+document.form.numeroTramite.value.toUpperCase()+
					 "&NroVoucher="+document.form.numeroVoucher.value;					 
		  url="altaTramites_ajax.php?"+parametros;
		  
		  ajax(url);
		  
		  if (http.readyState == 4) {
			results = http.responseText.split("|");
			
			if (results[0]=='Inexistente')  {
				
				if (buscadox == 'Voucher'){
					//alert("Nro. de "+ buscadox + " " + document.form.numeroVoucher.value.toUpperCase()+" inexistente. Ingréselo Nuevamente.\n");
				}else{
					//alert("Nro. de "+ buscadox + " " + document.form.numeroTramite.value.toUpperCase()+" inexistente. Ingréselo Nuevamente.\n");
				}
				//inicializar();
				//habilitaVoucher(2);
				return false;

			}else{
				
				if (results[0]=='sinremitodestino')
				{
					if (buscadox == 'Voucher'){
						//alert("El "+ buscadox + " " + document.form.numeroVoucher.value.toUpperCase()+" ingresado no tiene un remito de destino cargado.\n");
					}else{
						//alert("El "+ buscadox + " " + document.form.numeroTramite.value.toUpperCase()+" ingresado no tiene un remito de destino cargado.\n");
					}
					//inicializar();
					//habilitaVoucher(2);
					return false;
				
				}else{
					if (results[0]=='tramitecargado') 
					{
						if (buscadox == 'Voucher'){
							//alert("Nro. de "+ buscadox + " " + document.form.numeroVoucher.value.toUpperCase()+" ya tiene fecha de entrega.\n");
						}else{
							//alert("Nro. de "+ buscadox + " " + document.form.numeroTramite.value.toUpperCase()+" ya tiene fecha de entrega.\n");
						}
						//inicializar();
						//habilitaVoucher(2);
						return false;						
					
					}else{
					
						if (results.length<=2) {
							results2 = results[0].split(";");
							document.form.numeroTramite.value=Trim(results2[8]);
							document.form.numeroVoucher.value=Trim(results2[9]);
		
							ir_2(results2[0]);
					   }else{
	
							s = "";			   
							fondo = 1;
							results2 = "";				
							for (i=0;i<(results.length-1);i++) {
									results2 = results[i].split(";");
									
								s += "<tr class=fondotabla" + fondo + " onClick=\"ir_2("+results2[0]+"); document.form.numeroVoucher.value='"+Trim(results2[9])+"'; document.form.numeroTramite.value='"+Trim(results2[8])+"';\" onMouseOver=\"this.className = 'fondoconfirmacion';\" onMouseOut=\"this.className ='fondotabla" + fondo + "';\" style=\"cursor:pointer;\">"+
									 "	<td class=celdatexto>" + results2[2] + "</td>"+
									 "	<td class=celdatexto>" + results2[4] + "</td>"+
									 "	<td class=celdatexto align=center>" + results2[8] + "</td>"+
									 "	<td class=celdatexto align=center>" + results2[9] + "</td>"+		
									 "	<td class=celdatexto align=center>" + results2[5] + "</td>"+
									 "   </tr>\n";
									
									fondo = (fondo == 1 ? 2 : 1);
									
							}//FOR
							document.getElementById("tramites").tBodies[0].innerHTML = s;				
							document.getElementById('divFormDatos').style.display='none';
							document.getElementById('divEleccion').style.display='';
						   }
						}
					}				
				}
		  }
		  return false;
}


function ir(i) {
	document.form.idtramite.value=i;
	document.getElementById('divFormDatos').style.display='';
	document.getElementById('divEleccion').style.display='none';
}
function ir_2(i) {
	document.form.idtramite_2.value=i;
	document.getElementById('divFormDatos').style.display='';
	document.getElementById('divEleccion').style.display='none';
}

function grabar_entrega() {
	
	if (document.form.tipoGrabarEntrega.value == 1)
	{
		var ok = true;
		var errores = "";
		if (document.form.remDestino.value == false) {
			ok = false;
			errores += "- Ingrese un Nro. de Remito Destino\n";
		}else{	
			if (!Valido(document.form.remDestino.value,'n')) {
				ok = false;
				errores += "- El Nro. de Remito es Invalido\n";
			}else{
				if (!ConsAjax('remito','')) {
					ok = false;
					errores += "- Remito Inexistente\n";		
					document.form.idremito.value="";		
					document.form.remDestino.focus();		
				}else{
					if (!Valido(document.form.idremito.value,'n')) {	
						ok = false;
						errores += "- El Remito se encuentra "+document.form.idremito.value+"\n";		
						document.form.idremito.value="";		
						document.form.remDestino.focus();						  
					}
				}			
			}
		}
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
		//	dtRetiro = checkDate(document.form.FecRetiro.value);
		//	var fecretiro  = document.form.FecRetiro.value.substr(6,4)+document.form.FecRetiro.value.substr(3,2)+document.form.FecRetiro.value.substr(0,2);
		var fecentrega = document.form.FecEntrega.value.substr(6,4)+document.form.FecEntrega.value.substr(3,2)+document.form.FecEntrega.value.substr(0,2);
		//	if (fecentrega<=fecretiro) {
		//		ok = false;
		//		errores += "- La Fecha de Entrega debe ser mayor a la Fecha de Retiro.\n";
		//	}
		if (fecentrega>document.form.fecHoy.value) {
			ok = false;
			errores += "- La Fecha de Entrega es posterior a la Fecha Actual.\n";
		}
		if ((document.form.fechagen_rem_des.value) != "") {
			 if (fecentrega<document.form.fechagen_rem_des.value) {
				ok = false;
				errores += "- La Fecha de Entrega es inferior a la Fecha de Generación del Remito.\n";
			 }
		}	
		<?php
		//$plazoentrega = getParametro(PAR_PLAZO_ENTREGA);
		?>
		/*	if (diffDateDay(dtRetiro, dtEntrega) >= <?php echo($plazoentrega); ?>) {
			ok = false;
			errores += "- No pueden pasar más de <?php echo($plazoentrega); ?> días entre el retiro y la entrega\n";
		}	*/
		if(document.form.FecEntrega.value !="")
		{
			dia = dia_habil(document.form.FecEntrega.value);
			if(dia!="1")
			{
				ok = false;
				errores += "Fecha invalida: "+dia+"\n"; 
			}
		}
		if (!ok) {
			alert("Hay errores en los datos:\n" + errores);
			return false
		}else{
			if (ConsAjax('fecentrega','')) {	 
				document.getElementById('remito').style.display='none';
				document.getElementById('okremito').style.display='';			
				setTimeout("inicializar();",1500);
			}else{
				alert("Ocurrio un Error y no se ha podido grabar la Fecha");
			}
		}
	
	}else{
		
		var ok=true;
		var errores="";
		var buscadox="";
		//document.form.idtramite_2.value="";		

		//var ok = true;
		//var errores = "";
		/* verifico el Nro. de Voucher */	
		if (document.form.numeroVoucher.value != "") {
				buscadox='Voucher';	
				if (!Valido(document.form.numeroVoucher.value,'n')) 	{		
					ok = false;
					errores += "- El Nro. de Voucher es Invalido.\n";
					alert("Hay errores en los datos:\n" + errores);
					document.form.numeroVoucher.focus();
					return false;						
				}
		}
		
		if (document.form.numeroTramite.value != "") {		
			buscadox='Tramite';	
			if (!valDominio(document.form.numeroTramite.value)) {
				ok = false;
				errores +="- El Nro. de Trámite es Inválido.\n";
				alert("Hay errores en los datos:\n" + errores);
				document.form.numeroTramite.focus();
				return false;				
			}
		}

		if (document.form.numeroVoucher.value == false) {
			ok = false;
			errores += "- Por Favor, ingrese el Nro. de Voucher.\n";
			alert("Hay errores en los datos:\n" + errores);
			document.form.numeroVoucher.focus();
			return false;				
			
		}
		
		if (document.form.numeroTramite.value == false) {
			ok = false;
			errores += "- Por Favor, ingrese el Nro. de Tramite.\n";
			alert("Hay errores en los datos:\n" + errores);
			document.form.numeroTramite.focus();
			return false;				
			
		}
		
		if ((document.form.FecEntrega.value) == "") {
				ok = false;
				errores += "- Ingrese la Fecha de Entrega Por Favor.\n";
				alert("Hay errores en los datos:\n" + errores);
				document.form.FecEntrega.focus();
				return false;						
		}
		if (!parseDate(document.form.FecEntrega,'%d/%m/%Y',true)) {
			ok = false;
			errores += "- El Formato de la Fecha de Entrega no es Valido.\n";
			alert("Hay errores en los datos:\n" + errores);
			document.form.FecEntrega.focus();
			return false;					
		} else {
			dtEntrega = checkDate(document.form.FecEntrega.value);
		}
		//	dtRetiro = checkDate(document.form.FecRetiro.value);
		//	var fecretiro  = document.form.FecRetiro.value.substr(6,4)+document.form.FecRetiro.value.substr(3,2)+document.form.FecRetiro.value.substr(0,2);
		var fecentrega = document.form.FecEntrega.value.substr(6,4)+document.form.FecEntrega.value.substr(3,2)+document.form.FecEntrega.value.substr(0,2);
		//	if (fecentrega<=fecretiro) {
		//		ok = false;
		//		errores += "- La Fecha de Entrega debe ser mayor a la Fecha de Retiro.\n";
		//	}
		if (fecentrega>document.form.fecHoy.value) {
			ok = false;
			errores += "- La Fecha de Entrega es posterior a la Fecha Actual.\n";
			alert("Hay errores en los datos:\n" + errores);
			document.form.FecEntrega.focus();
			return false;					
		}
		
		
		  parametros="tipo=buscar_2"+
					 "&NroTramite="+document.form.numeroTramite.value.toUpperCase()+
					 "&NroVoucher="+document.form.numeroVoucher.value;					 
		  url="altaTramites_ajax.php?"+parametros;
		  
		  ajax(url);
		  
		  if (http.readyState == 4) {
			results = http.responseText.split("|");

			if (results[0]=='Inexistente')  {
				
				alert("Nro. de Voucher / Trámite inexistente. Ingréselos nuevamente.\n");
				
				/*if (buscadox == 'Voucher'){
					alert("Nro. de "+ buscadox + " " + document.form.numeroVoucher.value.toUpperCase()+" inexistente. Ingréselo Nuevamente.\n");
				}else{
					alert("Nro. de "+ buscadox + " " + document.form.numeroTramite.value.toUpperCase()+" inexistente. Ingréselo Nuevamente.\n");
				}*/
				inicializar();
				habilitaVoucher(2);
				return false;

			}else{
				
				if (results[0]=='sinremitodestino')
				{
					alert("El Voucher / Trámite ingresado no tiene un remito de destino cargado.\n");
					
					/*if (buscadox == 'Voucher'){
						alert("El "+ buscadox + " " + document.form.numeroVoucher.value.toUpperCase()+" ingresado no tiene un remito de destino cargado.\n");
					}else{
						alert("El "+ buscadox + " " + document.form.numeroTramite.value.toUpperCase()+" ingresado no tiene un remito de destino cargado.\n");
					}*/
					inicializar();
					habilitaVoucher(2);
					return false;
				
				}else{
					if (results[0]=='tramitecargado') 
					{
						alert("El Voucher / Trámite ingresado ya tiene fecha de entrega.\n");
						/*if (buscadox == 'Voucher'){
							alert("Nro. de "+ buscadox + " " + document.form.numeroVoucher.value.toUpperCase()+" ya tiene fecha de entrega.\n");
						}else{
							alert("Nro. de "+ buscadox + " " + document.form.numeroTramite.value.toUpperCase()+" ya tiene fecha de entrega.\n");
						}*/
						inicializar();
						habilitaVoucher(2);
						return false;						
					
					}else{}
					}				
				}
		  }
		
	  	if (document.form.idtramite_2.value == false) {
			ok = false;
			errores += "- Por Favor, seleccione un Tramite.\n";
			alert("Hay errores en los datos:\n" + errores);
			document.form.numeroVoucher.focus();
			return false;				
		}
		
		if (!ok) {
			alert("Hay errores en los datos:\n" + errores);
			return false;
		}
		
		ConsAjax('remito_2','');
		
		if ((document.form.fechagen_rem_des.value) != "") {
			 if (fecentrega<document.form.fechagen_rem_des.value) {
				ok = false;
				errores += "- La Fecha de Entrega es inferior a la Fecha de Generación del Remito.\n";
			 }
		}	
		<?php
		//$plazoentrega = getParametro(PAR_PLAZO_ENTREGA);
		?>
		/*	if (diffDateDay(dtRetiro, dtEntrega) >= <?php echo($plazoentrega); ?>) {
			ok = false;
			errores += "- No pueden pasar más de <?php echo($plazoentrega); ?> días entre el retiro y la entrega\n";
		}	*/
		if(document.form.FecEntrega.value !="")
		{
			dia = dia_habil(document.form.FecEntrega.value);
			if(dia!="1")
			{
				ok = false;
				errores += "Fecha invalida: "+dia+"\n"; 
			}
		}
		
		if (!ok) {
			alert("Hay errores en los datos:\n" + errores);
			document.form.FecEntrega.focus();
			return false;
		}else{
			si=ConsAjax('fecentrega_2','');
		
			if (si==true) {	 
				document.getElementById('remito').style.display='none';
				document.getElementById('okremito').style.display='';			
				setTimeout("inicializar(2);",1500);
			}else{
				alert("Ocurrio un Error y no se ha podido grabar la fecha de entrega");
			}	
		}	
	}
	
}
function grabar_devolucion() {
	
	var ok=true;
	var errores="";
	var buscadox="";
	
	if (document.form.NroVoucher.value != "") {
			buscadox='Voucher';	
			if (!Valido(document.form.NroVoucher.value,'n')) 	{		
				ok = false;
				errores += "- El Nro. de Voucher es Invalido.\n";
				alert("Hay errores en los datos:\n" + errores);
				document.form.NroVoucher.focus();
				return false;						
			}
	}
	
	if (document.form.NroTramite.value != "") {		
		buscadox='Tramite';	
		if (!valDominio(document.form.NroTramite.value)) {
			ok = false;
			errores +="- El Nro. de Trámite es Inválido.\n";
			alert("Hay errores en los datos:\n" + errores);
			document.form.NroTramite.focus();
			return false;				
		}
	}

	if (document.form.NroVoucher.value == false) {
		ok = false;
		errores += "- Por Favor, ingrese el Nro. de Voucher.\n";
		alert("Hay errores en los datos:\n" + errores);
		document.form.NroVoucher.focus();
		return false;				
		
	}
	
	if (document.form.NroTramite.value == false) {
		ok = false;
		errores += "- Por Favor, ingrese el Nro. de Tramite.\n";
		alert("Hay errores en los datos:\n" + errores);
		document.form.NroTramite.focus();
		return false;				
		
	}
	
	/* TENGO QUE VALIDAR LA FECHA EN EL CASO DE QUE ESTA HAYA SIDO INGRESADA */
	if ((document.form.fechaEntrega.value) != "") {

		if (!parseDate(document.form.fechaEntrega,'%d/%m/%Y',true)) {
			ok = false;
			errores += "- El Formato de la Fecha de Entrega no es Valido.\n";
			alert("Hay errores en los datos:\n" + errores);
			document.form.fechaEntrega.focus();
			return false;			
		} else {
			dtEntrega = checkDate(document.form.fechaEntrega.value);
		}
		var fecentrega = document.form.fechaEntrega.value.substr(6,4)+document.form.fechaEntrega.value.substr(3,2)+document.form.fechaEntrega.value.substr(0,2);
		if (fecentrega>document.form.fecHoy.value) {
			ok = false;
			errores += "- La Fecha de Entrega es posterior a la Fecha Actual.\n";
			alert("Hay errores en los datos:\n" + errores);
			document.form.fechaEntrega.focus();
			return false;			
		}
		
		ConsAjax('remito_dev','');
		
		if ((document.form.fechagen_rem_des_dev.value) != "") {
			 if (fecentrega<document.form.fechagen_rem_des_dev.value) {
				ok = false;
				errores += "- La Fecha de Entrega es inferior a la Fecha de Generación del Remito.\n";
				alert("Hay errores en los datos:\n" + errores);
				document.form.fechaEntrega.focus();
				return false;				
			 }
		}	
		<?php
		//$plazoentrega = getParametro(PAR_PLAZO_ENTREGA);
		?>
		if(document.form.fechaEntrega.value !="")
		{
			dia = dia_habil(document.form.fechaEntrega.value);
			if(dia!="1")
			{
				ok = false;
				errores += "Fecha invalida: "+dia+"\n"; 
				alert("Hay errores en los datos:\n" + errores);
				document.form.fechaEntrega.focus();
				return false;				
			}
		}
	
	}	
	/* TENGO QUE VALIDAR LA FECHA EN EL CASO DE QUE ESTA HAYA SIDO INGRESADA */	
	
	if (document.form.MotDevolucion.value==0) {
		ok = false;
		errores += "- Selecccione un Motivo de Devolución\n";
		alert("Hay errores en los datos:\n" + errores);
		document.form.MotDevolucion.focus();
		return false;		
	}		
	
	
	/**********************************************************************************************************/
	  parametros="tipo=buscar"+
				 "&NroTramite="+document.form.NroTramite.value.toUpperCase()+
				 "&NroVoucher="+document.form.NroVoucher.value;					 
	  url="altaTramites_ajax.php?"+parametros;
	  ajax(url);
	  if (http.readyState == 4) {
		results = http.responseText.split("|");
		if (results[0]=='Inexistente')  {
			alert("Nro. de voucher / trámite Inexistente. Ingréselo Nuevamente.\n");
			inicializar(1,'dev');
			return false;
		}else{
		   if (results.length<=2) {
			results2 = results[0].split(";");
			if (results2[10]=="") {

			}else{
				alert("El Tramite tiene generado el Remito de Origen.\n"+
					  "No se puede ingresar una devolución");
					document.form.NroTramite.value="";
					document.form.NroVoucher.value="";						  
					inicializar(1,'dev');
					return false;
			}
			ir(results2[0]);
		   }else{
			var sinidorigen=0;
			for (i=0;i<(results.length-1);i++) {
				results2 = results[i].split(";");				
			   if (results2[10]=="") { 
					sinidorigen++;
			   }
			}			   
			if (sinidorigen>1) {}else{
				if (sinidorigen==1){
				}else{
				alert("El Tramite tiene generado el Remito de Origen.\n"+
					  "No se puede ingresar una devolución");
					document.form.NroTramite.value="";
					document.form.NroVoucher.value="";
					inicializar(1,'dev');
					return false;
				}
			}
		   }
		}
	  }	
	
	/**********************************************************************************************************/	

	if (document.form.idtramite.value == false) {
		ok = false;
		errores += "- Por Favor, seleccione un Tramite.\n";
		alert("Hay errores en los datos:\n" + errores);
		document.form.NroVoucher.focus();
		return false;				
	}
	
	if (!ok) {
		alert("Hay errores en los datos:\n" + errores);
		return false
	}else{
		si=ConsAjax('devolucion','');
	
		if (si==true) {	 
			document.getElementById('voucher').style.display='none';
			document.getElementById('okvoucher').style.display='';			
			setTimeout("inicializar(1,'dev');",1500);		   
		}else{
			alert(si);
		}	
	}	
}
function limpiar_devolucion() {
	document.form.NroVoucher.value="";
	document.form.NroTramite.value="";
	document.form.idregmod.value="";
	document.form.idtramite.value="";
	document.form.fechaEntrega.value = '';
	document.form.MotDevolucion.selectedIndex=0;
	
}
function limpiar_entrega() {
	document.form.remDestino.value="";
	document.form.FecEntrega.value="";
	document.form.conformidad.value="";
}

function habilitaVoucher(accion)
{
	if (accion == 1)
	{
		document.form.numeroVoucher.value = '';
		document.form.numeroTramite.value = '';
		document.form.numeroVoucher.disabled = true;
		document.form.numeroTramite.disabled = true;
		document.form.remDestino.disabled = false;
		document.form.conformidad.disabled = false;
		document.form.tipoGrabarEntrega.value = 1;
		document.form.reset();
		document.form.remDestino.focus();

	}
	else if (accion == 2)
	{
		document.form.remDestino.value = '';
		document.form.conformidad.value = '';
		document.form.remDestino.disabled = true;
		document.form.conformidad.disabled = true;
		document.form.numeroVoucher.disabled = false;
		document.form.numeroTramite.disabled = false;		
		document.form.tipoGrabarEntrega.value = 2;
		document.form.reset();
		document.form.optEntregaVoucher.checked = 'checked';
		document.form.numeroVoucher.focus(); 
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
<script type="text/javascript" language="JavaScript" src="../includes/fecha.js"></script>
</HEAD>
<body onLoad="inicializar();" >
<? require_once("../includes/inc_topleft.php");
/* Contenido */
$pagina="entdevTramite.php";
require_once('../includes/inc_titulo.php');
?>
<div id=divFormDatos>
<form name="form" action="" onSubmit="return buscar();">
<input type="hidden" name="tipoGrabarEntrega" value="">
<table align="center" width="50%" class=tablaconbordes style="height: 150px">
<tr><td class=celdatitulo align=center>Entrega</td></tr>
<tr><td>
	<table id="remito">
	<tr>
		<td class="celdatexto" align="right">Seleccione tipo de entrega:</td>
		<td class="celdatexto"><input type="radio" id="optEntregaRemito" name="optEntrega" value="remito" checked="checked" onClick="habilitaVoucher(1)">Remito&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" id="optEntregaVoucher" name="optEntrega" value="voucher" onClick="habilitaVoucher(2)">N° de Voucher / Tr&aacute;mite</td>
	</tr>	
	<tr><td class="celdatexto" align="right">Remito DESTINO:</td>
		<td><input type=text class=textochico name=remDestino size=10 maxlength="15" style="text-transform:uppercase" onFocus="limpiar_devolucion();"></td>
	</tr>
	<tr>
		<td class="celdatexto" align="right">N&uacute;mero de Voucher:</td>
		<td><input type=text class=textochico name="numeroVoucher" size=10 maxlength="15" style="text-transform:uppercase" onBlur="if(!buscar_voucher(this)) this.focus();" onFocus="limpiar_devolucion();"></td>
	</tr>
	<tr>
		<td class="celdatexto" align="right">N&uacute;mero de Tr&aacute;mite:</td>
		<td><input type=text class=textochico name="numeroTramite" size=10 maxlength="15" style="text-transform:uppercase" onBlur="if(!buscar_voucher(this)) this.focus();" onFocus="limpiar_devolucion();"></td>
	</tr>
	<!--tr>
		<td class="celdatexto" align="right">N&uacute;mero de Voucher:</td>
		<td><input type=text class=textochico name="numeroVoucher" size=10 maxlength="15" style="text-transform:uppercase" onBlur="buscar_tramite_2();" onFocus="limpiar_devolucion();"></td>
	</tr>
	<tr>
		<td class="celdatexto" align="right">N&uacute;mero de Tr&aacute;mite:</td>
		<td><input type=text class=textochico name="numeroTramite" size=10 maxlength="15" style="text-transform:uppercase" onBlur="buscar_tramite_2();" onFocus="limpiar_devolucion();"></td>
	</tr-->	
			
		<tr><td class="celdatexto" width="120" align="right">Fecha de Entrega:</td>
			<td width="90"><input type="text" class=textochico size=8 maxlength="10" name="FecEntrega" id="FecEntrega" value="" style="text-align:right;" onBlur="parseDate(this,'<?php echo(FMT_DATE_CAL); ?>',true);">
			<img src="../imagenes/calendario.png" name="selfecha2" id="selfecha2" title="Calendario" alt="" style="cursor:pointer;"></td>				
		</tr>		
		<tr><td class="celdatexto" width="120" align="right">Conformidad:</td>
			<td width="90"><input type="text" class=textochico size=50 maxlength="100" name="conformidad" id="conformidad" value="" ></td>				
			<input type=hidden name=idremito />			
			<input type=hidden name=fecHoy value="<?=date('Ymd');?>"/>		
			<input type=hidden name=fechagen_rem_des />	
			<input type=hidden name=fechagen_rem_des_dev />				
		</tr>
	</table>
</td>
</tr>
<tr><td width="100%">
	<table id="okremito" style="display:none" width="100%">
	<tr><td class=grabando style="height: 110px" align=center width="100%">LA FECHA DE ENTREGA FUE GRABADA</td></tr>
	</table>
</td></tr>	
</table>
<table align="center" width="50%" style="height: 30px">
<tr><td align=center><input type=button class="botonout" name=botconfentrega value="Confirmar" onMouseOver="this.className='botonover';" onMouseOut="this.className='botonout';" onClick="grabar_entrega();"></td></tr>				
</table>
<table align="center" width="50%" class=tablaconbordes style="height: 150px" >
<tr><td class=celdatitulo align=center>Devoluci&oacute;n</td></tr>
<tr><td>
	<table id="voucher">
<tr><td class="celdatexto" align="right">N&uacute;mero de Voucher:</td>
    <td><input type=text class=textochico name=NroVoucher size=10 maxlength="8" style="text-transform:uppercase" onBlur="if(!buscar_voucher(this)) this.focus();" onFocus="limpiar_entrega();"></td>
</tr>	
<tr><td class="celdatexto" align="right">N&uacute;mero de Tr&aacute;mite:</td>
    <td><input type=text class=textochico name=NroTramite size=10 maxlength="8" style="text-transform:uppercase" onBlur="if(!buscar_voucher(this)) this.focus();" onFocus="limpiar_entrega();"></td></tr>
		
	<tr><td class="celdatexto" width="120" align="right">Fecha de Entrega:</td>
		<td width="90"><input type="text" class=textochico size=8 maxlength="10" name="fechaEntrega" id="fechaEntrega" value="" style="text-align:right;" onBlur="parseDate(this,'<?php echo(FMT_DATE_CAL); ?>',true);">
		<img src="../imagenes/calendario.png" name="selfecha3" id="selfecha3" title="Calendario" alt="" style="cursor:pointer;"></td>				
	</tr>		
	
	<tr>
		<td class="celdatexto" nowrap align="right">Motivo de Devoluci&oacute;n:</td>	
		<td colspan=4>
	    <select name=MotDevolucion id="MotDevolucion" class=textochico style="width:250px">
	       <option value="0">-- Seleccionar Motivo de Devolución --</option>
		<? 
			fill_combo("select MOT_CODIGO, MOT_DESCRIP from MOTIVO_DEV where mot_fecha_baja is null order by MOT_DESCRIP", $_POST["MotDevolucion"]);
		?>		
	    </select>	
		<input type=hidden name=idregmod />	
		<input type=hidden name=idtramite>
		<input type="hidden" name="idtramite_2">			
		</td>	
	</tr>	
</table>
</td>
</tr>
<tr><td width="100%">
<table id="okvoucher" style="display:none" width="100%">
<tr><td class=grabando style="height: 110px" align=center width="100%">LA DEVOLUCION FUE GRABADA</td></tr>
</table>
</td></tr>	
</table>
<table align="center" width="50%" style="height: 30px">
<tr><td align=center><input type=button class="botonout" name=botcancdev value="Cancelar" onMouseOver="this.className='botonover';" onMouseOut="this.className='botonout';" onClick="limpiar_devolucion(); 	document.form.NroVoucher.focus();"><input type=button class="botonout" name=botconfdev value="Confirmar" onMouseOver="this.className='botonover';" onMouseOut="this.className='botonout';" onClick="grabar_devolucion();"></td></tr>				
</table>
</form>
</div>
<div id=divEleccion style="display:none">
<form action="" name=formeleccion>
<table class=tablaconbordes align=center cellpadding="0" cellspacing="0" border=0 width="80%">
<tr><td class=celdatitulo align=center colspan=4>Seleccione el Tr&aacute;mite a Modificar</td></tr>
	<tr><td>
	<table cellpadding="1" cellspacing="1" width="100%" id="tramites">
		<thead>
	<tr><td class=celdatitulo align="center" width="35%">Registro Origen</td>
		<td class=celdatitulo align="center" width="35%">Registro Destino</td>
		<td class=celdatitulo align="center" width="10%">Nro.de Tr&aacute;mite</td>
		<td class=celdatitulo align="center" width="10%">Nro.de Voucher</td>		
		<td class=celdatitulo align="center" width="20%">Fecha de Retiro</td></tr>
		</thead>
		<tbody>
		</tbody>	
	</table>
	</td></tr>
</table>
<table width="50%" align=center>
<tr><td align="center"><input type=button class="botonout" name=botcvolver   value="<?=VOLVER;?>" onMouseOver="this.className = 'botonover';" onMouseOut="this.className = 'botonout';" onClick="document.getElementById('divEleccion').style.display='none'; document.getElementById('divFormDatos').style.display='';" /></td></tr>
</table>
</form>
</div>
<script type="text/javascript">
	Calendar.setup( { inputField: "FecEntrega", ifFormat: "%d/%m/%Y", button: "selfecha2" } );
	Calendar.setup( { inputField: "fechaEntrega", ifFormat: "%d/%m/%Y", button: "selfecha3" } );
	habilitaVoucher(1);
</script>
<!--Contenido-->
<? require_once("../includes/inc_bottom.php"); ?>
</BODY></HTML>
<?
} //Cierro if autorizacion
?>
