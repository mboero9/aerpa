<?
require_once("../includes/lib.php");
if (check_auth($usrid, $perid) && check_permission($permiso)) {
?>
<HTML><HEAD>
<? require_once("../includes/inc_header.php"); ?>
<script type="text/javascript">
function valido(sector) {
	var ok = true;
	var errores = "";
	var validos = /^[a-zA-Z0-9·¡È…ÌÕÛ”˙⁄¸‹Ò—][a-zA-Z0-9·¡È…ÌÕÛ”˙⁄¸‹Ò— \/\.\,\_\-]*$/;
	if (sector=='seccion') {
		if (!validos.test(document.formseccion.nombresecc.value)) {
			ok = false;
			errores += "- Nombre incorrecto o incompleto.\n";
		}
	}else{
		if (!validos.test(document.formitems.nombreitem.value)) {
			ok = false;
			errores += "- Nombre incorrecto o incompleto.\n";
		}	
		if (!validos.test(document.formitems.descitem.value)) {
			ok = false;
			errores += "- descripciÛn incorrecta o incompleta.\n";
		}	
		if (!validos.test(document.formitems.pagina.value)) {
			ok = false;
			errores += "- Pagina incorrecta o incompleta.\n";
		}			
	}
	if (!ok) {
		alert("Hay errores en los datos:\n" + errores);
		return(false);
	}else{
		return(true);
	}
} 
function cargarsecc() {
  if (document.getElementById('seccexistente').style.display=='') {
		document.getElementById('sectoritems').style.display='none';  
		document.getElementById('seccexistente').style.display='none';
		document.getElementById('seccnueva').style.display='';
		document.getElementById('titseccion').innerHTML='Ingresar Seccion Nueva';
		document.getElementById('confabmmenu').value='Seleccionar Seccion';  			
		document.getElementById('confaltaseccion').style.display='';								
		document.getElementById('modsecc').style.display='none';		
		document.getElementById('bajsecc').style.display='none';				
		document.formseccion.nombresecc.value="";
		document.formseccion.ordensecc.value="";		
		document.formseccion.defaultsecc.checked=false;				
		document.formseccion.asignablesecc.checked=false;	
		document.formseccion.nombresecc.focus();					

  } else {
		document.getElementById('seccexistente').style.display='';
		document.getElementById('seccnueva').style.display='none';  
		document.getElementById('titseccion').innerHTML='Seleccionar Seccion';		
		document.getElementById('confabmmenu').value='Nueva Seccion';  		
		document.getElementById('confaltaseccion').style.display='none';							
		document.getElementById('confmodseccion').style.display='none';											
		document.getElementById('bajsecc').style.display='none';													
		document.getElementById('modsecc').style.display='none';															
		document.formseccion.secciones.selectedIndex=0;
  }
}
function modificar_secc() {
//
// Funcion que busca los datos de la seccion para ser modificada
//
  url="abmmenu_ajax.php?tipo=secciondes&secciondes="+document.formseccion.secciones.value; 
  ajax(url);
	if (http.readyState == 4) { 
		results = http.responseText.split(";"); 
		document.formseccion.nombresecc.value=results[0];
		document.formseccion.ordensecc.value=results[1];		
		if (results[2]=='s') {
			document.formseccion.defaultsecc.checked=true;				
		}
		if (results[3]=='s') {
			document.formseccion.asignablesecc.checked=true;				
		}
		document.getElementById('sectoritems').style.display='none';		
		document.getElementById('seccexistente').style.display='none';
		document.getElementById('modsecc').style.display='none';
		document.getElementById('confaltaseccion').style.display='none';								
		document.getElementById('confmodseccion').style.display='';						
		document.getElementById('seccnueva').style.display='';
		document.getElementById('titseccion').innerHTML='Modificar Seccion';
		document.getElementById('confabmmenu').value='Seleccionar Seccion';  					
	}   
}
function borrar(sector) {
//
// Funcion que busca los datos de la seccion para ser modificados
//
  if (sector=='secciones') {
	  url="abmmenu_ajax.php?tipo=seccionitems&seccionitems="+document.formseccion.secciones.value; 
//alert(url);	  
	  ajax(url);
	  borrado = "Seccion: "+document.formseccion.secciones.options[document.formseccion.secciones.selectedIndex].text+"<br><br>";  
	  if (http.readyState == 4) {   
		  results = http.responseText.split("|"); 
		  if (results.length>0) {
			if (results.length>1) { borrado += "    con los siguientes "+results[0]+" items<br><br>";
			} else { borrado += "    con el siguiente item<br><br>"; }
			for (i=0;i<results.length-1;i++) {
				results2 = results[i].split(";"); 		
				borrado += "&nbsp;&nbsp;&nbsp;&nbsp;*&nbsp;"+results2[1]+"<br>";
			}		
		  }
		  document.getElementById('titborrar').innerHTML='Baja de Seccion';	  	  	  
		  document.getElementById('confbajaseccion').style.display='';	  	  	  	  
		  document.getElementById('botonesconf').style.display='';	  	  	  	  		  
		  document.getElementById('volverbajsecc').style.display='';	    	  		  
//		  document.getElementById('confmodseccion').style.display='none';  	  	  	  	  		  			  	  	  				  
	  }
  }else{
	  borrado = "Item: "+document.formitems.items.options[document.formitems.items.selectedIndex].text+"<br><br>";  
	  document.getElementById('titborrar').innerHTML='Baja de Item';	  	  	  	  
	  document.getElementById('confbajaitem').style.display='';	  	  	  	  
	  document.getElementById('confmoditem').style.display='none';	  	  	  	  	  	    
	  document.getElementById('volverbajitem').style.display='';	    	  		  
  } 
  document.getElementById('bajsecmensaje').innerHTML=borrado;	  
  document.getElementById('sectorseccion').style.display='none';	  	  
  document.getElementById('sectoritems').style.display='none';	  	  	  
  document.getElementById('borrar').style.display='';	  

}
function confirma(tipo) {
	switch(tipo) {
	case 'altasecc':
	case 'modsecc':
	      if (!valido('seccion')) { break; }
		  if (document.formseccion.ordensecc[0].checked) {
		     orden='primero';
		  }else{
			if (document.formseccion.ordensecc[1].checked) {		  	
			    orden='despuesde';
			}else{
				orden='';
			}
		  }	
		  parametros="tipo="+tipo+
					 "&id="+document.formseccion.secciones.value+
					 "&nomsecc="+document.formseccion.nombresecc.value+
					 "&orden="+orden+
					 "&despuesde="+document.formseccion.secciones2.value+					 					 
					 "&default="+document.formseccion.defaultsecc.checked+
					 "&asignable="+document.formseccion.asignablesecc.checked;
//abmmenu_ajax.php?tipo=modsecc&id=0&nomsecc=Tramites&orden=despuesde&despuesde=&default=false&asignable=false
		  url="abmmenu_ajax.php?"+parametros; 
//		alert(url);  
		  ajax(url);
		  if (http.readyState == 4) {  
			results = http.responseText.split(";"); 
			if (results[0]=='ok') {
			  document.getElementById('confmodseccion').style.display='none';	  	  					
			  document.getElementById('confaltaseccion').style.display='none';											  
			  document.getElementById('seccnueva').style.display='none';	  	  		
			  document.getElementById('sectorseccion').style.display='none';	  	  					  
			  document.getElementById('mensaje').style.display='';
			  if (tipo=='altasecc')	{
				  document.getElementById('grabado').innerHTML='Seccion Dada de Alta con Exito';				  
			  } else {
				  document.getElementById('grabado').innerHTML='Seccion Modificada con Exito';	
			  }
              setTimeout("location.href='abmmenu.php'",1500);			  
			}
		  }  
		  break;	
	case 'bajasecc':		  
		  parametros="tipo="+tipo+
					 "&id="+document.formseccion.secciones.value;		  
		  url="abmmenu_ajax.php?"+parametros; 
//alert(url);		  
		  ajax(url);
		  if (http.readyState == 4) {  
			results = http.responseText.split(";"); 
			if (results[0]=='ok') {
			  document.getElementById('borrar').style.display='none';
			  document.getElementById('mensaje').style.display='';
			  document.getElementById('grabado').innerHTML='La Seccion ha sido dada de Baja con Exito';	
              setTimeout("location.href='abmmenu.php'",1500);			  
			}
		  }  
		  break;	
	case 'altaitem':		  	  	
	case 'moditem':		
	      if (!valido('items')) { break; }	
		  if (document.formitems.ordenitem[0].checked) {
		     orden='primero';
		  }else{
			if (document.formitems.ordenitem[1].checked) {		  	
			    orden='despuesde';
			}else{
				orden='';
			}
		  }
		  parametros="tipo="+tipo+
					 "&id="+document.formitems.items.value+
					 "&idsecc="+document.formitems.secciones3.value+					 
					 "&nomitem="+escape(document.formitems.nombreitem.value)+
					 "&descitem="+escape(document.formitems.descitem.value)+
					 "&pagina="+document.formitems.pagina.value+
					 "&directorio="+document.formitems.directorio.value+
					 "&parametro="+document.formitems.parametro.value+
					 "&valparametro="+document.formitems.valparametro.value+
					 "&ordenitem="+orden+
					 "&despuesde="+document.formitems.items2.value+					 
					 "&targetitem="+document.formitems.targetitem.value+
					 "&tippermitem="+document.formitems.tippermitem.value+
					 "&separador="+(document.formitems.separador.checked ? "1" : "0")+					 					 					 					 					 
					 "&default="+(document.formitems.defaultitem.checked ? "1" : "0")+
					 "&asignable="+(document.formitems.asignableitem.checked ? "1" : "0");
//tipo=altaitem&id=0&idsecc=10&nomitem=prueba item1&descitem=prueba desc&pagina=pagina&directorio=direc&parametro=param&valparametro=valparam&ordenitem=&despuesde=&targetitem=&tippermitem=&separador=0&default=0&asignable=1
	    url="abmmenu_ajax.php?"+parametros; 
//		alert(url);  
		  ajax(url);
		  if (http.readyState == 4) {  
			results = http.responseText.split(";"); 
			if (results[0]=='ok') {
			  document.getElementById('botonesconf').style.display='none';	  	  					
			  document.getElementById('sectoritems').style.display='none';	  	  		
			  document.getElementById('mensaje').style.display='';
			  if (tipo=='altaitem')	{			  
				  document.getElementById('grabado').innerHTML='El Item ha sido dado de Alta con Exito';				  
			  }else{
				  document.getElementById('grabado').innerHTML='El Item ha sido Modificado con Exito';	
			  }
              setTimeout("location.href='abmmenu.php'",1500);
			}else{
			alert('Error:/n'+results);						
			}
		  }  
		  break;	
	case 'bajaitem':		  
		  parametros="tipo="+tipo+
					 "&id="+document.formitems.items.value;		  
		  url="abmmenu_ajax.php?"+parametros; 
		  ajax(url);
		  if (http.readyState == 4) {  
			results = http.responseText.split(";"); 
			if (results[0]=='ok') {
			  document.getElementById('borrar').style.display='none';	  	  		
			  document.getElementById('botonesconf').style.display='none';	  	  					  					  
			  document.getElementById('mensaje').style.display='';
			  document.getElementById('grabado').innerHTML='El Item ha sido dada de Baja con Exito';	
              setTimeout("location.href='abmmenu.php'",1500);			  
			}
		  }  
		  break;		  	  			  
	}//switch
}
function modificar_item() {
//
// Funcion que busca los datos del item para ser modificado
//
  url="abmmenu_ajax.php?tipo=itemid&itemid="+document.formitems.items.value; 
//alert(url);  
  ajax(url);
	if (http.readyState == 4) { 
		results = http.responseText.split(";"); 
//alert(results);		
		document.formitems.nombreitem.value=results[0];
		document.formitems.descitem.value=results[1];		
		document.formitems.pagina.value=results[2];		
		document.formitems.directorio.value=results[3];		
		document.formitems.parametro.value=results[4];		
		document.formitems.valparametro.value=results[5];		
		document.formitems.targetitem.value=results[6];
		if (results[7]!="") { 	
			for (i=0; i<document.formitems.tippermitem.options.length; i++) {
				if (document.formitems.tippermitem.options[i].value==results[7]) {
					document.formitems.tippermitem.selectedIndex=i;
					break;
				}
			}		
		}
		if (results[8]=='1')  { document.formitems.separador.checked=true;				
		}
		if (results[9]=='1')  {	document.formitems.defaultitem.checked=true;				
		}
		if (results[10]=='1') {	document.formitems.asignableitem.checked=true;				
		}		
		document.getElementById('itemexistente').style.display='none';
		document.getElementById('itemnuevo').style.display='';
		document.getElementById('tititems').innerHTML=document.getElementById('tititems').innerHTML+' - Modificar';
		document.getElementById('moditem').style.display='none';
		document.getElementById('confmoditem').style.display='';		
//	    swap_item();
	} 
}
function agregar_botones(sector) {
   if (sector=='secciones') {
	if (document.formseccion.secciones.value==0) {
		document.getElementById('bajsecc').style.display='none';  			
		document.getElementById('modsecc').style.display='none';  		
	} else {
		document.getElementById('bajsecc').style.display='';  				
		document.getElementById('modsecc').style.display='';  		
		document.formseccion.secciones2.selectedIndex=(document.formseccion.secciones.selectedIndex-1);				
		document.formitems.secciones3.selectedIndex=(document.formseccion.secciones.selectedIndex-1);						
	    cargarItems('seccion');
	}
   }else{
	if (document.formitems.items.value==0) {
		document.getElementById('bajitem').style.display='none';  			
		document.getElementById('moditem').style.display='none';  		
	} else {
		document.getElementById('sectorseccion').style.display='none';  					
//		document.getElementById('nvoitem').style.display='none';  							
		document.getElementById('nvoitem').value='Seleccionar Item';			
		document.getElementById('bajitem').style.display='';  				
		document.getElementById('moditem').style.display='';  		
		modificar_item();
	}
   }
}

function ajax(url) {
//alert(url);
	http.open("GET", url, false); 
	http.send(null);
}
function cargarItems(sector) {
	if (sector=='seccion') { id=document.formseccion.secciones.value;
	}else{ id=document.formitems.secciones3.value; }
	url="abmmenu_ajax.php?tipo=seccionitems&seccionitems="+id; 
//alert(url);		
	ajax(url);
	if (http.readyState == 4) { 
		results = http.responseText.split("|"); 
// vacio el combo previamente
	    if (sector=='seccion')  {
			for (i=document.formitems.items.options.length-1; i>=1; i--) {
				document.formitems.items.options[i] = null;
			}		
		}
		if (results.length>0) {
//lleno el combo si hay datos			
			for (i=1;i<results.length;i++) {
		 	  results2 = results[i-1].split(";"); 		
			    if (sector=='seccion')  {
					document.formitems.items.options[i]= new Option(results2[1], results2[0]);			
				}
				document.formitems.items2.options[i]= new Option(results2[1], results2[0]);							
			}		
		    document.getElementById('tititems').innerHTML="Items de la seccion: "+document.formseccion.secciones.options[document.formseccion.secciones.selectedIndex].text;					
			document.formitems.items.style.display='';														
		} else {
//No hay items de la seccion seleccionada, ingresar 1er.item.		
		    document.getElementById('tititems').innerHTML="No hay Items de la seccion: "+document.formseccion.secciones.options[document.formseccion.secciones.selectedIndex].text;					
			document.formitems.items.style.display='none';											
		}
	} 	
	document.getElementById('sectoritems').style.display='';	
}
function swap_item() {
  if (document.getElementById('itemexistente').style.display=='') {
	document.getElementById('sectorseccion').style.display='none';   
	document.getElementById('itemexistente').style.display='none'; 
	document.getElementById('itemnuevo').style.display='';
	document.getElementById('nvoitem').value='Seleccionar Item';	
	document.getElementById('confaltaitem').style.display='';		
  }else{
	document.getElementById('sectorseccion').style.display='';     
	document.getElementById('sectoritems').style.display='';     	
	document.getElementById('itemexistente').style.display=''; 
	document.getElementById('itemnuevo').style.display='none';
	document.getElementById('bajitem').style.display='none';	
	document.getElementById('confbajaitem').style.display='none';		
	document.getElementById('confmoditem').style.display='none';			
	document.getElementById('volverbajitem').style.display='none';					
	document.formitems.items.selectedIndex=0;	
	document.getElementById('nvoitem').value='Nuevo Item';	  
	document.getElementById('confaltaitem').style.display='none';			
	document.getElementById('borrar').style.display='none';	  	
  }
}
function volver(sector) {
	if (sector=='seccion') {
		document.getElementById('borrar').style.display='none'; 
		document.getElementById('sectorseccion').style.display=''; 
		document.getElementById('botonesconf').style.display='none';
	}else{
		swap_item();
	}
}
</script>
<script type="text/javascript" language="JavaScript" src="../includes/ajaxobjt.js"></script> 
</HEAD>

<body>
<? require_once('../includes/inc_topleft.php'); ?>
<!-- Contenido -->
<br>
<table align="center" width="100%">
<tr>
<td class="titulo1" align="center" >ABM Menu</td>
</tr>
<tr><td>
<form name="formseccion" action="abmmenu.php" method="post" onSubmit="return (validarCreacion());">
<div id=sectorseccion>
<table align="center" class="tablaconbordes" width="80%">
	<tr>
	<th class="celdatitulo" align="center"><div id=titseccion>Seleccionar Seccion</div></th>
	</tr>
	<tr><td height=70>
<!--Seleccionar seccion-->	
<div id=seccexistente>
	<table cellpadding="0" cellspacing="0">	
	<tr><td class="celdatexto" nowrap>Secciones:
	    <select name=secciones onChange="agregar_botones('secciones');">
	       <option value="0">-- Seleccionar Seccion --</option>
		<? 
			fill_combo("select ADM_SEC_ID, ADM_SEC_NOMBRE from SEGADMINSECCION order by ADM_SEC_ORDEN", $_POST["secciones"]);
		?>		
	    </select>
		
	</td>
	</tr>
	</table>		
</div>	
<!--Ingresar Seccion Nueva-->
<div id=seccnueva style="display:none">
	<table width="100%">	
	<tr>
	<td class="celdatexto" align="right" width="25%" nowrap>Nombre de Seccion Nueva: </td><td class="celdatexto" align="left" width="25%"><input type="text" name="nombresecc" size=30 maxlength="50"></td>
	<td class="celdatexto" align="right" width="25%">Default: </td><td class="celdatexto" align="left" width="25%"><input type="checkbox" name="defaultsecc" value="1"></td>	
	</tr>
	<tr>	
	<td class="celdatexto" align="right">Orden:              </td><td class="celdatexto" align="left"><input type="radio" name="ordensecc" value="primero">Al Comienzo de la SecciÛn</td>
	<td class="celdatexto" align="right">Asignable: </td><td class="celdatexto" align="left"><input type="checkbox" name="asignablesecc" value="1"></td>	
	</tr>
	<tr>	
	<td class="celdatexto" align="right">					 </td><td class="celdatexto" align="left" nowrap><input type="radio" name="ordensecc" value="despuesde">Despues de 
	<select name=secciones2>
<?	fill_combo("select ADM_SEC_ID, ADM_SEC_NOMBRE from SEGADMINSECCION order by ADM_SEC_ORDEN", $_POST["secciones"]); ?>
	</select></td>	
	</tr>
	<tr>			
	</table>		

</div>	
	</td></tr>
</table>
<table align="center" class="tablaconbordes" width="80%">
	<tr>
	<td align=center><input class="botonout" type="button" name="bajsecc" id=bajsecc     value="Borrar Seccion"    onClick="borrar('secciones');" style="display:none" onMouseOver="this.className = 'botonover';" onMouseOut="this.className = 'botonout';">
	                 <input class="botonout" type="button" name="modsecc" id=modsecc     value="Modificar Seccion" onClick="modificar_secc();"  style="display:none" onMouseOver="this.className = 'botonover';" onMouseOut="this.className = 'botonout';">
	                 <input class="botonout" type="button" name="seccnva" id=confabmmenu value="Nueva Seccion"     onClick="cargarsecc();"></td>	
	</tr>
</table>
</div>
</form>
</td></tr>
<tr><td valign=top>
<!--Sector de Items-->
<form name="formitems" action="abmmenu.php" method="post" onSubmit="return (validarCreacion());">
<div id=sectoritems style="display:none">
<table align="center" class="tablaconbordes" width="80%">
	<tr>
	<th class="celdatitulo" align="center"><div id=tititems></div></th>
	</tr>
	<tr><td>
<!--Items ya existentes para la seccion seleccionada-->	
<div id=itemexistente>	
	<table width="100%">
		<tr><td class="celdatexto" nowrap>Items:
<!--Este combo es completado por Ajax segun la seccion seleccionada-->		
		    <select name=items onChange="agregar_botones('items');">
		       <option value="0">-- Seleccionar Item --</option>
		    </select>
		</td>
		</tr>
	</table>
</div>
<!--Ingresar Item Nuevo-->	
<div id=itemnuevo style="display:none">	
	<table width="100%">
	<tr>	
	<td class="celdatexto" align="right">Seccion:</td><td class="celdatexto" align="left">
	<select name=secciones3 onChange="cargarItems('items');">
<?	fill_combo("select ADM_SEC_ID, ADM_SEC_NOMBRE from SEGADMINSECCION order by ADM_SEC_ORDEN", $_POST["secciones"]); ?>
    </select></td>	
	</tr>	
	<tr>
	<td class="celdatexto" align="right">Nombre del Item:    </td><td class="celdatexto" align="left"><input type="text" name="nombreitem"   size=30 maxlength="50"></td>
	<td class="celdatexto" align="right">Descripcion:        </td><td class="celdatexto" align="left"><input type="text" name="descitem"     size=30 maxlength="50"></td>	
	</tr>	
	<tr>	
	<td class="celdatexto" align="right">Pagina:             </td><td class="celdatexto" align="left"><input type="text" name="pagina"       size=30 maxlength="50"></td>		
	<td class="celdatexto" align="right">Directorio:         </td><td class="celdatexto" align="left"><input type="text" name="directorio"   size=30 maxlength="50"></td>			
	</tr>		
	<tr>	
	<td class="celdatexto" align="right">Parametro:          </td><td class="celdatexto" align="left"><input type="text" name="parametro"    size=30 maxlength="50"></td>				
	<td class="celdatexto" align="right">Valor del Parametro:</td><td class="celdatexto" align="left"><input type="text" name="valparametro" size=30 maxlength="50"></td>					
	</tr>	
	<tr>		
	<td class="celdatexto" align="right">Target:             </td><td class="celdatexto" align="left"><input type="text" name="targetitem"    size=5 maxlength="5"></td>
	<? $tperm = array("" => "- Nada -", PERM_VIEW => PERMLBL_VIEW, PERM_EDIT => PERMLBL_EDIT);	?>	
	<td class="celdatexto" align="right">AB Mesas:           </td><td class="celdatexto" align="left"><select name="tippermitem" >
																						 <?php fill_combo_arr($tperm); ?>	
																				 		 </select>																  
	</tr>
	<tr>	
	<td class="celdatexto" align="right">Orden:              </td><td class="celdatexto" align="left"><input type="radio" name="ordenitem" value="primero">Al Comienzo de la SecciÛn</td>
	<td class="celdatexto" align="right">Separador:          </td><td class="celdatexto" align="left"><input type="checkbox" name="separador" value="1"></td>			
	</tr>
	<tr>	
	<td class="celdatexto" align="right">					 </td><td class="celdatexto" align="left" nowrap><div id=ordenopc2><input type="radio" name="ordenitem" value="despuesde">Despues de <select name=items2></select></div></td>	
	<td class="celdatexto" align="right">Default:            </td><td class="celdatexto" align="left"><input type="checkbox" name="defaultitem" value="1"></td>		
	</tr>
	<tr>		
	<td></td>
	<td></td>	
	<td class="celdatexto" align="right">Asignable:          </td><td class="celdatexto" align="left"><input type="checkbox" name="asignableitem" value="1"></td>		
	</tr>	
	</table>
</div>	
	</td></tr>	
</table>
<div id=botonesitems>	
<table align="center" class="tablaconbordes" width="80%">	
	<tr><td align="center"><input class="botonout" type=button name="bajitem" id=bajitem value="Borrar Item"  style="display:none" onClick="borrar('item');" onMouseOver="this.className = 'botonover';" onMouseOut="this.className = 'botonout';">
						   <input class="botonout" type=button name="moditem"  id=moditem value="Modificar Item" style="display:none" onClick="modificar_item();" onMouseOver="this.className = 'botonover';" onMouseOut="this.className = 'botonout';">		                   
		                   <input class="botonout" type=button name="nvoitem"  id=nvoitem value="Nuevo Item" onClick="swap_item();" onMouseOver="this.className = 'botonover';" onMouseOut="this.className = 'botonout';">						   
    </td></tr>
</table>	
</div>
</div>
<div id=borrar style="display:none">
<table align="center" class="tablaconbordes" width="80%">
	<tr>
	<th class="celdatitulo" align="center"><div id=titborrar></div></th>
	</tr>
	<tr><td>
	<table align="center" width="50%">	
		<tr><td class=textoerror align=center>ATENCION !!!</td></tr>
		<tr><td class=textoerror><div id=bajsecmensaje></div></td></tr>
	</table>
	</td></tr>
</table>
</div>
<div id=botonesconf>
<table align="center" width="80%">
	<tr>
	<td align="center"><input class="botonout" type="button" name="volverbajsecc"   id="volverbajsecc"   value="Volver"         style="display:none" onClick="volver('seccion');" onMouseOver="this.className = 'botonover';" onMouseOut="this.className = 'botonout';">
					   <input class="botonout" type="button" name="volverbajitem"   id="volverbajitem"   value="Volver"         style="display:none" onClick="volver('item');" onMouseOver="this.className = 'botonover';" onMouseOut="this.className = 'botonout';">	
					   <input class="botonout" type="button" name="confmodseccion"  id="confmodseccion"  value="Confirmar"      style="display:none" onClick="confirma('modsecc');" onMouseOver="this.className = 'botonover';" onMouseOut="this.className = 'botonout';">
	                   <input class="botonout" type="button" name="confaltaseccion" id="confaltaseccion" value="Confirmar"      style="display:none" onClick="confirma('altasecc');" onMouseOver="this.className = 'botonover';" onMouseOut="this.className = 'botonout';">
	                   <input class="botonout" type="button" name="confbajaseccion" id="confbajaseccion" value="Confirmar Baja" style="display:none" onClick="confirma('bajasecc');" onMouseOver="this.className = 'botonover';" onMouseOut="this.className = 'botonout';">
					   <input class="botonout" type="button" name="confbajaitem"    id="confbajaitem"    value="Confirmar Baja" style="display:none" onClick="confirma('bajaitem');" onMouseOver="this.className = 'botonover';" onMouseOut="this.className = 'botonout';">					   
					   <input class="botonout" type="button" name="confmoditem"     id="confmoditem"     value="Confirmar ModificaciÛn" style="display:none" onClick="confirma('moditem');" onMouseOver="this.className = 'botonover';" onMouseOut="this.className = 'botonout';">
	                   <input class="botonout" type="button" name="confaltaitem"    id="confaltaitem"    value="Confirmar"      style="display:none" onClick="confirma('altaitem');" onMouseOver="this.className = 'botonover';" onMouseOut="this.className = 'botonout';"></td>					   
	</tr>
</table>
</div>
</form>
<div id=mensaje style="display:none">
<table cellspacing="0" width=578 cellpadding="0" align=center class=tablaconbordes>
	<tr><td align="center" valign="middle" height="80" class=grabando>
	<div id=grabado></div>
</td></tr>
</table>
</div>
</td></td>
</table>
						<!--Contenido-->


<? require_once("../includes/inc_bottom.php");?>


</BODY></HTML>
<?
}//Cierro if autorizacion
?>
