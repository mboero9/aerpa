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
		var validos = /^[a-zA-ZáÁéÉíÍóÓúÚüÜñÑ][a-zA-ZáÁéÉíÍóÓúÚüÜñÑ \/\.\,\_\-]*$/;
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

/* MIL 13/04/2015 CAMBIO DE FORMATO DE PANTENTES */
function valDominio(dominio){
	var tramiteok=true;
	var tramite=dominio;
    
    var ralfa = new RegExp("[^0-9 A-Z a-z. _-]");
	var rvalor =  tramite.match(ralfa);  
    
    if ( rvalor ) tramiteok = false;
    
    if ( tramite.length > 8) tramiteok = false;
    if ( tramite.length < 1) tramiteok = false;
    
    return tramiteok;
}

function valDominioViejo(dominio){
	var tramiteok=true;
	var tramite=dominio;
	switch(tramite.length) {
		case 5:
				var a=tramite.substr(0,3);
				var b=tramite.substr(3,2);
				if (!((Valido(a,'t'))&&(Valido(b,'n')))){
					tramiteok = false;
				}
		break;		
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
								 alert("El Código Ingresado es Invalido");
								 return false;
							   }
		  parametros="tipo="+tipo+
					 "&reg_tipo="+campo+
					 "&numero="+id;
		  url="altaTramites_ajax.php?"+parametros;
		  ajax(url);
		  if (http.readyState == 4) {
			results = http.responseText.split("|");
			var tipo_reg;
			var colores;
			var cp;
			switch(results[1])
			{
				case "A":
					tipo_reg = "/Abonado";
					color = "<font color=blue>";
					cp = results[2];
					break;
				case "D":
					tipo_reg = "/No Abonado";
					color = "<font color=red>";
					cp = results[2];
					break;
				default:
					tipo_reg = "";
					color = "<font color=black>";
					cp="";
			}//Fin Switch
			if (campo=='O') {
				if (results[1]=='D') {
					document.getElementById('spanDesRegOrig').innerHTML="El Registro posee solo función Destino";
					document.form.CodRegOrig.value="";					
					document.form.CodRegOrig.focus();
				}else{
					document.getElementById('spanDesRegOrig').innerHTML='<b>'+results[0]+'('+cp+')'+color+' '+tipo_reg+'</b></font>';
				}
			}else{
				document.getElementById('spanDesRegDest').innerHTML='<b>'+results[0]+'('+cp+')'+color+' '+tipo_reg+'</b></font>';
			};
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
//alert(url);
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
	
	case 'remito_2':
		  parametros="tipo="+tipo+
					 "&numeroTramite="+ document.form.numeroTramite.value + "&numeroVoucher=" + document.form.numeroVoucher.value;
		  url="altaTramites_ajax.php?"+parametros;
//alert(url);
		  ajax(url);
		  if (http.readyState == 4) {
			results = http.responseText.split("|");
//alert(results);			
			if (results[0]!='inexistente') {
			  	document.form.fechagen_rem_des.value=results[0];
			  return true;
			}else{
			  return false;
			}
		  }
		  break;
	
	case 'remito_dev':
		  parametros="tipo=remito_2"+
					 "&numeroTramite="+ document.form.NroTramite.value + "&numeroVoucher=" + document.form.NroVoucher.value;
		  url="altaTramites_ajax.php?"+parametros;
//alert(url);
		  ajax(url);
		  if (http.readyState == 4) {
			results = http.responseText.split("|");
//alert(results);			
			if (results[0]!='inexistente') {
			  	document.form.fechagen_rem_des_dev.value=results[0];
			  return true;
			}else{
			  return false;
			}
		  }
		  break;			  
	
	case 'fecentrega_2':
		  parametros="tipo="+tipo+
					 "&numeroVoucher="+document.form.numeroVoucher.value+
					 "&numeroTramite="+document.form.numeroTramite.value+
					 "&FecEntrega="+document.form.FecEntrega.value;	 
		  url="altaTramites_ajax.php?"+parametros;
		  //alert(url);
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
					 "&MotDevolucion="+document.form.MotDevolucion.value+
					 "&fechaEntrega="+document.form.fechaEntrega.value;	
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
	}//switch
}

function Trim(TRIM_VALUE)
{
	if(TRIM_VALUE.length < 1)
	{
		return"";
	}

	TRIM_VALUE = RTrim(TRIM_VALUE);
	TRIM_VALUE = LTrim(TRIM_VALUE);
	
	if(TRIM_VALUE=="")
	{
		return "";
	}else{
		return TRIM_VALUE;
	}
}

function RTrim(VALUE)
{
	var w_space = String.fromCharCode(32);
	var v_length = VALUE.length;
	var strTemp = "";
	if(v_length < 0)
	{
		return"";
	}
	var iTemp = v_length -1;
	
	while(iTemp > -1)
	{
		if(VALUE.charAt(iTemp) == w_space){
		}else{
			strTemp = VALUE.substring(0,iTemp +1);
			break;
		}
	
		iTemp = iTemp-1;
	}
	
	return strTemp;	
}

function LTrim(VALUE)
{
	var w_space = String.fromCharCode(32);
	if(v_length < 1)
	{
		return"";
	}
	
	var v_length = VALUE.length;
	var strTemp = "";
	
	var iTemp = 0;
	
	while(iTemp < v_length)
	{
		if(VALUE.charAt(iTemp) == w_space){
		}else{
			strTemp = VALUE.substring(iTemp,v_length);
			break;
		}
	
		iTemp = iTemp + 1;
	}
	return strTemp;
}