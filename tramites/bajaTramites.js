function dia_habil(fecha)
{
	var respuesta;
	url="altaTramites_ajax.php?dia="+escape(fecha);
//		alert(url);
		  ajax(url);
		  if (http.readyState == 4) 
		  {
		  	respuesta = http.responseText;
		  	return respuesta;
		  }
		
}
function Valido(valor,tipo) {
	//Validador de campos
//alert("valor:"+valor+"tipo:"+tipo);
	switch(tipo) {
	  case 'n':
		var validos = /^[0-9]*$/;
		break;
	  case 't':
		var validos = /^[a-zA-Z��������������][a-zA-Z�������������� \/\.\,\_\-]*$/;
		break;
	  case 'x':
		var validos = /[DTCdtc]/;
		break;
	}
	if(!validos.test(valor)) {
		return false;
	}else{
		return true;
	}
}//Cierro ValidNum
function valDominio(dominio) {
	var tramiteok=true;
	var tramite=dominio;
	switch(tramite.length) {
		case 6:
				var a=tramite.substr(0,3);
				var b=tramite.substr(3,3);
				if (((Valido(a,'n'))&&(!Valido(b,'t')))||
					((Valido(a,'t'))&&(!Valido(b,'n')))||
					((!Valido(a,'t'))&&(!Valido(b,'t')))||
					((!Valido(a,'n'))&&(!Valido(b,'n')))) 	{
						tramiteok = false;
//						alert("- (Error 1001): Nro. de Tramite Invalido.\n");
				}
		break;
		case 7:
				var a=tramite.substr(0,3);
				var b=tramite.substr(4,3);
				if (((Valido(a,'n'))&&(!Valido(b,'t')))||
					((Valido(a,'t'))&&(!Valido(b,'n')))||
					((!Valido(a,'t'))&&(!Valido(b,'t')))||
					((!Valido(a,'n'))&&(!Valido(b,'n')))) 	{
						tramiteok = false;
//						alert("- (Error 1002): Nro. de Tramite Invalido.\n");
				}else{
					if (!Valido(tramite.substr(3,1),'x')) {
						tramiteok = false;
//						alert("- (Error 1003): Nro. de Tramite Invalido.\n");
					}
				}
		break;
		case 8:
				var a=tramite.substr(0,1);
				var b=tramite.substr(1,7);
				if (((Valido(a,'t'))&&(!Valido(b,'n')))||
					((!Valido(a,'t'))&&(!Valido(b,'t')))||
					((!Valido(a,'n'))&&(!Valido(b,'n')))) 	{
						tramiteok = false;
//						alert("- (Error 1004): Nro. de Tramite Invalido.\n");
				}
		break;
		default:
						tramiteok = false;
//						alert("- (Error 1005): Nro. de Tramite Invalido.\n");
	}
	return tramiteok;
}
function ConsAjax(tipo,campo) {
	if ((tipo=='grabar')&&(document.getElementById('subformMod').style.display=='')) {
		if (document.form.modificado.value!=1) { return false; }
		tipo='modificar';
	}
/* funcion que interactua con la base */
	switch(tipo) {
	case 'desregistro':
		  var id=((campo=='O') ? document.form.CodRegOrig.value : document.form.CodRegDest.value);
  	      if (!Valido(id,'n')) {
		  						 if (campo=='O') {
								 	document.form.CodRegOrig.value="";
								 	document.form.CodRegOrig.focus();
								 }else{
									document.form.CodRegDest.value="";
								    document.form.CodRegDest.focus();
								 }
								 alert("El C�digo Ingresado es Invalido");
								 return false;
							   }
		  parametros="tipo="+tipo+
					 "&reg_tipo="+campo+
					 "&numero="+id;
		  url="altaTramites_ajax.php?"+parametros;
		  ajax(url);
		  if (http.readyState == 4) {
			results = http.responseText.split("|");
			if (campo=='O') {
				if (results[1]=='D') {
					document.getElementById('spanDesRegOrig').innerHTML="El Registro posee solo funci�n Destino";
					document.form.CodRegOrig.value="";					
					document.form.CodRegOrig.focus();
				}else{
					document.getElementById('spanDesRegOrig').innerHTML=results[0]+" / "+results[1]+"- Ambos";
				}
			}else{
				document.getElementById('spanDesRegDest').innerHTML=results[0]+" / "+results[1]+"- Destino";
			};
		  }
		  break;
	case 'borrar':
		  parametros="tipo="+tipo+
					 "&regOrigen="+document.form.CodRegOrig.value+
					 "&regDestino="+document.form.CodRegDest.value+
					 "&NroTramite="+document.form.NroTramite.value.toUpperCase()+
					 "&NroVoucher="+document.form.NroVoucher.value+					 
					 "&FecRetiro="+document.form.FecRetiro.value+
					 "&usuario="+document.form.usuario.value;
//tipo=grabar&regOrigen=&regDestino=&NroTramite=&FecEntrega=&usuario=2
		  url="altaTramites_ajax.php?"+parametros;
//		alert(url);
		  ajax(url);
		  if (http.readyState == 4) {
			results = http.responseText.split("|");
//alert(results);
			if (results[0]=='ok') {
			  document.getElementById('divFormDatos').style.display='none';
			  document.getElementById('divMensaje').style.display='';
			  document.getElementById('grabado').innerHTML='El Tramite ha sido dado de Alta con Exito';
			  return true;
			}else{
			  alert("ERROR: NO SE PUDO ACTUALIZAR LOS DATOS");
			}
		  }
		  break;
	case 'grabar':
		  parametros="tipo="+tipo+
					 "&regOrigen="+document.form.CodRegOrig.value+
					 "&regDestino="+document.form.CodRegDest.value+
					 "&NroTramite="+document.form.NroTramite.value.toUpperCase()+
					 "&NroVoucher="+document.form.NroVoucher.value+					 
					 "&FecRetiro="+document.form.FecRetiro.value+
					 "&usuario="+document.form.usuario.value;
//tipo=grabar&regOrigen=&regDestino=&NroTramite=&FecEntrega=&usuario=2
		  url="altaTramites_ajax.php?"+parametros;
//		alert(url);
		  ajax(url);
		  if (http.readyState == 4) {
			results = http.responseText.split("|");
//alert(results);
			if (results[0]=='ok') {
			  document.getElementById('divFormDatos').style.display='none';
			  document.getElementById('divMensaje').style.display='';
			  document.getElementById('grabado').innerHTML='El Tramite ha sido dado de Alta con Exito';
			  return true;
			}else{
			  alert("ERROR: NO SE PUDO ACTUALIZAR LOS DATOS");
			}
		  }
		  break;
	case 'modificar':
		  parametros="tipo="+tipo+
					 "&idregmod="+document.form.idregmod.value+
					 "&regOrigen="+document.form.CodRegOrig.value+
					 "&regDestino="+document.form.CodRegDest.value+
					 "&NroTramite="+document.form.NroTramite.value.toUpperCase()+
					 "&NroVoucher="+document.form.NroVoucher.value+					 					 
					 "&FecRetiro="+document.form.FecRetiro.value+
					 "&FecEntrega="+document.form.FecEntrega.value+
					 "&MotDevolucion="+document.form.MotDevolucion.value+
					 "&usuario="+document.form.usuario.value;
//tipo=modificar&idregmod=1&regOrigen=1&regDestino=2&NroTramite=DHZ446&FecRetiro=04/02/2006&FecEntrega=&FecDevolucion=&MotDevolucion=0&usuario=2
		  url="altaTramites_ajax.php?"+parametros;
//		alert(url);
		  ajax(url);
		  if (http.readyState == 4) {
			results = http.responseText.split("|");
//alert(results);
			if (results[0]=='ok') {
			  document.getElementById('divFormDatos').style.display='none';
			  document.getElementById('divMensaje').style.display='';
			  return true;
			}else{
			  alert("ERROR: NO SE PUDO MODIFICAR LOS DATOS");
			}
		  }
		  break;
	case 'verificar':
		  parametros="tipo="+tipo+
					 "&regOrigen="+document.form.CodRegOrig.value+
					 "&regDestino="+document.form.CodRegDest.value+
					 "&NroTramite="+document.form.NroTramite.value.toUpperCase()+
					 "&FecRetiro="+document.form.FecRetiro.value+
					 "&NroVoucher="+document.form.NroVoucher.value;
		  url="altaTramites_ajax.php?"+parametros;
//		  alert(url);
		  ajax(url);
		  if (http.readyState == 4) {
			results = http.responseText.split("|");
//alert(results);			
			if ((results[0]=='existe')||(results[1]=='existe')) {
				(results[0]=='existe' ? document.form.NroTramite.value="" : "");
				(results[1]=='existe' ? document.form.NroVoucher.value="" : "");				
			  return true;
			}else{
			  return false;
			}
		  }
		  break;
	case 'verificar2':
		  var ok=true;
		  parametros="tipo="+tipo+
					 "&idtramite="+document.form.idtramite.value+		  
					 "&NroVoucher="+document.form.NroVoucher.value+
					 "&NroTramite="+document.form.NroTramite.value.toUpperCase()+
					 "&FecRetiro="+document.form.FecRetiro.value;
		  url="altaTramites_ajax.php?"+parametros;
//		  alert(url);
		  ajax(url);
		  if (http.readyState == 4) {
			results = http.responseText.split("|");
//alert(results);			
			if (results[0]=='voucher') {
				ok=false;
				document.form.NroVoucher.value="";				
			}
			if (results[1]=='tramite') {
				ok=false;
				document.form.NroTramite.value="";				
			}
			if (ok) { return true; }else{ return false; }
		  }
		  break;		  
	case 'remito':
		  parametros="tipo="+tipo+
					 "&remDestino="+document.form.remDestino.value;
		  url="altaTramites_ajax.php?"+parametros;
//		  alert(url);
		  ajax(url);
		  if (http.readyState == 4) {
			results = http.responseText.split("|");
//alert(results);			
			if (results[0]!='inexistente') {
			  if (results[1]=='GENERADO') {
				  document.form.idremito.value=results[0];
			  }else{
				  document.form.idremito.value=results[1];				  
			  }
			  document.form.fechagen_rem_des.value=results[2];				  			  
			  return true;
			}else{
			  return false;
			}
		  }
		  break;		  
	case 'fecentrega':
		  parametros="tipo="+tipo+
					 "&idremito="+document.form.idremito.value+
					 "&FecEntrega="+document.form.FecEntrega.value+
					 "&conformidad="+escape(document.form.conformidad.value);		 
		  url="altaTramites_ajax.php?"+parametros;
//		  alert(url);
		  ajax(url);
		  if (http.readyState == 4) {
			results = http.responseText.split("|");
//alert(results);			
			if (results[0]=='ok') {
			  return true;
			}else{
			  return false;
			}
		  }
		  break;		  
	case 'devolucion':
		  parametros="tipo="+tipo+
					 "&idtramite="+document.form.idtramite.value+
					 "&MotDevolucion="+document.form.MotDevolucion.value;		 
		  url="altaTramites_ajax.php?"+parametros;
//		  alert(url);
		  ajax(url);
		  if (http.readyState == 4) {
			results = http.responseText.split("|");
//alert(results);			
			if (results[0]=='ok') {
			  return true;
			}else{
			  
			  return results[0];
			}
		  }
		  break;	
	
	case 'validacion_baja':	  	
			  parametros="tipo="+tipo+
					 "&NroVoucher="+document.form.NroVoucher.value+
					 "&NroTramite="+document.form.NroTramite.value.toUpperCase();	 
		  url="altaTramites_ajax.php?"+parametros;
		  ajax(url);
		  if (http.readyState == 4) {
			results = http.responseText.split("|");
			if (results[0]=='ok') {
			  return true;
			}else{			  
			  return results;
			}
		  }
		  break;		  
	
	case 'anula_remito':	  
			  parametros="tipo="+tipo+
					 "&NroRemitoDes="+document.form.idremito_des.value+
                     "&usuario="+document.form.usuario.value;	 
		  url="altaTramites_ajax.php?"+parametros;
		  ajax(url);
		  if (http.readyState == 4) {
			results = http.responseText.split("|");
			return results;
		  }
		  break;			  
	
	case 'nuevo_remito':	  
			  parametros="tipo="+tipo+"&usuario="+document.form.usuario.value;	 
		  url="altaTramites_ajax.php?"+parametros;
		  ajax(url);
		  if (http.readyState == 4) {
			results = http.responseText.split("|");
			  
			  return results;
			
		  }
		  break;		
	
	case 'actualizo_tramite':	  
		     var id_remito = campo;		
			  parametros="tipo="+tipo+"&id_nuevo_remito="+id_remito+
					 "&NroRemitoDes="+document.form.idremito_des.value;	 
		  url="altaTramites_ajax.php?"+parametros;
		  
		  ajax(url);
		  if (http.readyState == 4) {
			results = http.responseText.split("|");
			  
			if (results[0]=='ok') {
			  return true;
			}else{			  
			  return results;
			}		
		  }
		break;	
		  	
	case 'tramitesxrem':	  	
			 parametros="tipo="+tipo+"&NroRemitoDes="+document.form.idremito_des.value;	
		  url="altaTramites_ajax.php?"+parametros;
		  
		  ajax(url);
		  if (http.readyState == 4) {
			results = http.responseText.split("|");
			  return results;
		  }
	 break;
  	  
	}//switch
}
