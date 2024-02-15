<?php
require_once("../includes/lib.php");
if (check_auth($usrid, $perid) && check_permission($permiso)) {
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<?php require_once("../includes/inc_header.php"); ?>
<script type="text/javascript">
<?php require_once("../includes/ajaxobjt.js"); ?>

var updating = false;
var newwindow=null;
var debug = false;
// Callback para el GET
// validacion AJAX - front end
function getHttp() {
	if (http.readyState == 4) {
		// obtener respuesta como texto
		r = new String(http.responseText);
//alert(r);		
		if (r.indexOf("invalid") == -1) {
			i = r.indexOf("\n");
			entidad = r.substr(0,i);
			if (i < r.length) {
				r = r.substr(i+1);
			} else {
				r = "";
			}
			document.form1.tram_lista.value = r;
		}
		document.getElementById("loading").style.display = "none";
		updating = false;
	}
} // end getHttp

function ValidarRem() {
	ok=true;
    var tipo_err;
	var desde=document.form1.desde.value;
	var hasta=document.form1.hasta.value;
	var tipo = get_tipo();
	var anulados=0;
	var cant_remitos=0;
	
	if(desde=="") {
		alert("-- No ha ingresado el número de remito desde! --");
		ok=false;
		return ok;
	}
	if(hasta=="") {
		
		document.form1.hasta.value=document.form1.desde.value;
		cant_remitos=1;
	}
	if(document.form1.desde.value==document.form1.hasta.value)
	{
		cant_remitos=1;
	}
	nro_valido = new RegExp("^[0-9]*$","i");
	if(!nro_valido.test(desde)) {
		alert("-- El Nro. de Remito desde contiene caracteres invalidos! -- ");
		ok=false;
		return ok;
	}
	if(!nro_valido.test(hasta)) {
		alert("-- El Nro. de Remito hasta contiene caracteres invalidos! -- ");
		ok=false;
		return ok;
	}		
	var url	= "reimprimirRemito_ajax.php?ajax=remito&desde=" + document.form1.desde.value+"&hasta="+
		document.form1.hasta.value+"&par_registro="+tipo;
	//if(debug) alert(url);
	http.open("GET", url , false);
	/*alert("reimprimirRemito_ajax.php?ajax=remito&desde=" + document.form1.desde.value+"&hasta="+document.form1.hasta.value+
					      "&par_registro="+tipo);*/
					
	http.send(null);
	if (http.readyState == 4) {	
		r = new String(http.responseText);
		//alert(r);
		if (r.indexOf("invalid") == -1) {
			i = r.indexOf("\n");
			entidad = r.substr(0,i);
			if (i < r.length) {
				r = r.substr(i+1);
			} else {
				r = "";
			}
			regs = r.split("\n");					
		}
			
		for (i = 0; i < regs.length; i++) {

				reg = regs[i].split("|");
				
				if (reg[0]=='CERRADO') {
					tipo_err='Hay remitos con fecha de cierre en el rango.';
					ok=false;
					break;
					}
				if (reg[0]=='ENTREGADO') {
					tipo_err='Hay remitos con fecha de entrega en el rango.';
					ok=false;
					break;
					}
				if(reg[0]=='ANULADO'){
				
					if(cant_remitos==1)
					{
						tipo_err=("El remito se encuentra anulado.");
						ok=false;
						break;
					}
					else
					{
					anulados+=1;
					tipo_err="Se encontraron: ";
					ok=true;
					}
					
				}		
							
				if (reg[0]=='') {

					tipo_err='No se encontraron remitos en ese rango de numeros.';
					ok=false;
					break;
				}

		}//for
	}//if	
	
	if(!ok) {		
		alert(tipo_err);
		return false;
	}else{
		if(anulados>0)
		{
			alert(tipo_err+anulados+' remitos anulados en el rango');
		}
		return true;
	}
}//Cierro validar remito
function imprimir_remito() {
	if (ValidarRem()) {
		var tipo = get_tipo();
		var condicion = ' where b.REM_FECHA_CIERRE is null ';
		switch ( tipo ) {
			case 'devolucion':
				var campo1= 'ori';
				var campo2= 'des';
				var condicion = ' where b.REM_FECHA_CIERRE is null and b.REM_TIPO="devolucion" and a.MOT_CODIGO is not null ';
				var condicionhis= ' where b.REM_FECHA_CIERRE is null and b.REM_TIPO="devolucion" and a.MOT_CODIGO is not null ';
				break;
			case 'origen':
				var campo1= 'ori';
				var campo2= 'des';
				var condicion = ' where b.REM_FECHA_CIERRE is null and b.REM_TIPO="origen" and a.MOT_CODIGO is null ';
				var condicionhis= ' where b.REM_FECHA_CIERRE is null and b.REM_TIPO="origen" and a.MOT_CODIGO is null ';
				break;
			case 'destino':
				var campo1= 'des';
				var campo2= 'ori';
				var condicion = ' where a.TRA_FECHA_ENTREGA is null';
				var condicionhis = ' where a.HIS_FECHA_ENTREGA is null';
		}
	 
		// Modificación para tomar en cuenta el histórico
		document.descarga.sql.value=
			""+
				"Select a.TRA_DOMINIO, "+
					//"STUFF('0000000', 8-LEN(b.REM_NUMERO), LEN(b.REM_NUMERO), b.REM_NUMERO) as REM_NUMERO, "+
					"a.TRA_NRO_VOUCHER, s.REG_COD_INT as REG_COD_INT_"+campo2.toUpperCase()+", s.REG_DESCRIP as REG_DESCRIP_"+
					campo2.toUpperCase()+", r.REG_DESCRIP as REG_DESCRIP_"+campo1.toUpperCase()+", "+
					"r.REG_COD_INT  as REG_COD_INT_"+campo1.toUpperCase()+", "+
					"f.REG_DESCRIP as REG_DESCRIP_FAM, "+
					"f.REG_COD_INT  as REG_COD_INT_FAM, "+
					document.form1.fecharetiro.value+" as TRA_FECHA_RETIRO "+
				"From REG_AUTOM f "+
				"inner Join REG_AUTOM r "+
					"left join "+
						"TRAMITE a "+
						"Inner Join REMITO b on b.rem_id = a.rem_id_"+campo1+" and b.rem_numero BETWEEN #_DESDE_# and #_HASTA_# and rem_tipo = '"+tipo+"' "+
						"Inner Join REG_AUTOM s on s.reg_codigo = a.reg_codigo_"+campo2+" "+
					"on r.reg_codigo = a.reg_codigo_"+campo1+" "+
				"on r.reg_familia = f.reg_cod_int "+
				condicion+
				" and f.reg_cod_int in("+
					"select distinct sr.reg_familia "+
						"from REG_AUTOM sr "+
						"inner join TRAMITE sa on sr.reg_codigo = sa.reg_codigo_"+campo1+" "+
						"inner Join REMITO sb on sb.rem_id = sa.rem_id_"+campo1+" and sb.rem_numero BETWEEN #_DESDE_# and #_HASTA_# and rem_tipo = '"+tipo+"' "+
				")"+
			" union "+
				"Select a.HIS_DOMINIO as TRA_DOMINIO, "+
				//"STUFF('0000000', 8-LEN(b.REM_NUMERO), LEN(b.REM_NUMERO), b.REM_NUMERO) as REM_NUMERO, "+
				"a.HIS_NRO_VOUCHER as TRA_NRO_VOUCHER, s.REG_COD_INT as REG_COD_INT_"+campo2.toUpperCase()+", "+
				"s.REG_DESCRIP as REG_DESCRIP_"+campo2.toUpperCase()+", r.REG_DESCRIP as REG_DESCRIP_"+campo1.toUpperCase()+", "+
				"r.REG_COD_INT  as REG_COD_INT_"+campo1.toUpperCase()+", "+
				"f.REG_DESCRIP as REG_DESCRIP_FAM, "+
				"f.REG_COD_INT  as REG_COD_INT_FAM, "+
				document.form1.fecharetirohis.value+" as TRA_FECHA_RETIRO "+
				"From REG_AUTOM f "+
				"inner Join REG_AUTOM r "+
					"left join "+
						"TRAMITE_HIS a "+
						"Inner Join REMITO b on b.rem_id = a.rem_id_"+campo1+" and b.rem_numero BETWEEN #_DESDE_# and #_HASTA_# and rem_tipo = '"+tipo+"' "+
						"Inner Join REG_AUTOM s on s.reg_codigo = a.reg_codigo_"+campo2+" "+
					"on r.reg_codigo = a.reg_codigo_"+campo1+" "+
				"on r.reg_familia = f.reg_cod_int "+
				condicionhis+
				" and f.reg_cod_int in("+
					"select distinct sr.reg_familia "+
						"from REG_AUTOM sr "+
						"inner join TRAMITE_HIS sa on sr.reg_codigo = sa.reg_codigo_"+campo1+" "+
						"inner Join REMITO sb on sb.rem_id = sa.rem_id_"+campo1+" and sb.rem_numero BETWEEN #_DESDE_# and #_HASTA_# and rem_tipo = '"+tipo+"' "+
				")"+
			" Order by REG_COD_INT_"+campo1.toUpperCase()+", TRA_DOMINIO";
		if( debug ) alert( document.descarga.sql.value );

		//////////////////////////////////////////////////////////////////////
		//agrego los ceros a numero de remito
		var des = new String(document.form1.desde.value);
		var has = new String(document.form1.hasta.value);
		var ceros = new String("0000000");
		var cant_ceros = ceros.substr(des.length);
		document.form1.desde.value = cant_ceros+des;
		var cant_ceros = ceros.substr(has.length);
		document.form1.hasta.value = cant_ceros+has;
		//fin agrego ceros

		var titdev = 'DEVOLUCION DE VOUCHERS AL ORIGEN';
		switch ( tipo ) {
			case 'devolucion':
				titdev = 'DEVOLUCION DE LEGAJOS AL ORIGEN';
			case 'origen':
				document.descarga.archivo2.value = '../tramites/remito.xml';
				document.descarga.propiedadesreport.value = 
				"PageHeader|4|"+titdev+"\n"+			  			  
				"PageHeader|5|"+(document.form1.copia_duplicado.checked?'DUPLICADO - Para el Correo':'ORIGINAL - Para Registro Origen')+"\n"+
				"PageHeader|7|#_REMITO_#\n";
				break;
			case 'destino':
				document.descarga.archivo2.value = '../tramites/remitodes.xml';
				document.descarga.propiedadesreport.value = 
				"PageHeader|4|"+'ENVIO A DESTINO'+"\n"+			  			  
				"PageHeader|5|"+(document.form1.copia_duplicado.checked?'DUPLICADO - Para el Correo':'ORIGINAL - Para el Correo')+"\n"+
				"PageHeader|7|#_REMITO_#\n"+				
				"PageHeader|8|"+'Registro Cabecera:'+"\n"+
				"PageHeader|9|#_REG_COD_INT_FAM_#\n"+
				"PageHeader|10|#_REG_DESCRIP_FAM_#\n"+															
				"MainGroup|SubGroup|GroupHeader|0|Registro Destino:\n"+
				"MainGroup|SubGroup|GroupHeader|5|Registro Origen\n";
				break;
		}

		//alert( document.descarga.archivo2.value );
		//alert( document.descarga.propiedadesreport.value );
		document.descarga.propiedadesreport1.value = 
			"ReportHeader|4|"+document.form1.desde.value+"\n"+
			"ReportHeader|6|"+document.form1.hasta.value;
		document.descarga.primer_remito.value = document.form1.desde.value;
		document.descarga.ultimo_remito.value = document.form1.hasta.value;
		document.descarga.tipo.value = tipo;															
	    newwindow=window.open(href='../export/imprime_'+((document.form1.copia_ambos.checked?'ps5':'ps2')+'.php?desde='+document.form1.desde.value+'&hasta='+document.form1.hasta.value), this.target, 'width=250,height=140,left=260,top=230,resizable=yes');									
		document.form1.desde.value='';
		document.form1.hasta.value='';
												
	
//			  document.descarga.submit();
		  return true;		
	}
}
function getTramites() {
	if (ValidarRem()) {
		// Busqueda AJAX de los tramites
		document.form1.tram_lista.value = "";
		updating = true;
		document.getElementById("loading").style.display = "";
		var tipo = (document.getElementById("tipo_remito1").checked ? 'origen' : 'destino');
		http.open("GET", "reimprimirRemito_ajax.php?ajax=detalle&nro_rem=" + document.form1.nro_rem.value+
						      "&par_registro="+tipo, false);				
		http.send(null);
		getHttp();

		if (document.form1.tram_lista.value == "") {
			document.getElementById("tramites").tBodies[0].innerHTML = "";
			alert("El remito no incluye ningún trámite");
			return false;
		} else {
			s = "";
			fondo = 1;
			regs = document.form1.tram_lista.value.split("\n");
//alert(regs);			
			for (i = 0; i < regs.length; i++) {
				reg = regs[i].split("|");			
				s += "<tr class=fondotabla" + fondo + "><td class=celdatexto>" + reg[0] + "</td>" +
				     "<td class=celdatexto>" + reg[1] + "</td></tr>\n";
				fondo = (fondo == 1 ? 2 : 1);
			} // end for
			document.getElementById("tramites").tBodies[0].innerHTML = s;
			return true;
		} // end if
	} else {
		return false;
	} // end if validar
} // end getTramites

function get_tipo() {
	for( i=0 ; i < document.form1.tipo_remito.length ; i++) {
		if( document.form1.tipo_remito[i].checked ) return document.form1.tipo_remito[i].value;
	}
}
</script>
</head>
<body onLoad="document.form1.desde.focus();" onFocus="if(newwindow){newwindow.focus();}">
<!--NUEVO-->
<?php require_once("../includes/inc_topleft.php"); 
/* Contenido */
$pagina="reimprimirRemito.php";
require_once('../includes/inc_titulo.php');
?>
<!-- Form para ingresar numero de remito -->
<form name="form1" id="form1" action="" method="post" onSubmit="return ValidarRem(document.getElementById('nro_rem').value);">
<table align="center" width="40%" class="tablaconbordes">
<tr>
<th colspan="4" align="center" class="celdatitulo">Ingrese número y tipo de Remito</th>
</tr>
<tr>
	<td class="celdatexto" align="center">
		Origen:<input type="radio" name="tipo_remito" id="tipo_remito1" value="origen" />
	</td>
	<td align="center" class="celdatexto">
		Destino:<input type="radio" name="tipo_remito" id="tipo_remito2" value="destino" checked="checked" />
	</td>
	<td class="celdatexto" align="center">
		Devolución:<input type="radio" name="tipo_remito" id="tipo_remito3" value="devolucion" />
	</td>
</tr>
<tr>
<td colspan=4 align="center" class="celdatexto">N° Remito desde: <input type="text" name="desde" id="desde" maxlength="15"><img src="../imagenes/loading.gif" id=loading style="display: none;"></td>
</tr>
<tr>
<td colspan=4 align="center" class="celdatexto">N° Remito hasta: <input type="text" name="hasta" id="hasta" maxlength="15"><img src="../imagenes/loading.gif" id=loading style="display: none;"></td>
</tr>
<tr id="copias">
<td align="center" class="celdatexto" colspan="4">Original:<input type="radio" id="copia_original" name="copia" value="copia_original"> 
Duplicado:<input type="radio" id="copia_duplicado" name="copia" value="copia_duplicado"> Ambos:<input type="radio" id="copia_ambos" value="copia_ambos" name="copia" checked="checked"></td>
</tr>
<tr>
<td colspan="4" align="center">

<!--<input type=button class=botonout value="Buscar tr&aacute;mites" onClick="getTramites();">-->

<input type=hidden name=remitoconceros>
<input type=hidden name=codigo>
<input type=hidden name=descripcion>
<input type=hidden name=fecharetiro value="<?=$conn->SQLDate(FMT_DATE_DB, 'a.TRA_FECHA_RETIRO');?>">
<input type=hidden name=fecharetirohis value="<?=$conn->SQLDate(FMT_DATE_DB, 'a.HIS_FECHA_RETIRO');?>">
<input type="button" class="botonout" name=botimprimir     value="Imprimir"  onMouseOver="this.className = 'botonover';" onMouseOut="this.className = 'botonout';" onClick="imprimir_remito();">
</td>
</tr>
<!--<tr><td class=celdatexto>Tr&aacute;mites incluidos</td>
	<td><input type=hidden name=tram_lista>
		<table id="tramites">
		<thead>
			<tr><th class=celdatitulo>Dominio</th>
			    <th class=celdatitulo>Voucher</th></tr>-->
		</thead>
		
</table>
</form>
<form action="" method="post" name="descarga">
<input type="hidden" name="sql">
<input type="hidden" name="archivo2" />
<input type="hidden" name="propiedadesreport">
<input type="hidden" name="propiedadesreport1">
<input type="hidden" name="primer_remito">
<input type="hidden" name="ultimo_remito">
<input type="hidden" name="tipo">
<input type=hidden name=fecharetiro value="<?=$conn->SQLDate(FMT_DATE_DB, 'a.TRA_FECHA_RETIRO');?>">
</form>

<!--Fin Form -->
<?php require_once("../includes/inc_bottom.php"); ?>
</body>
</html>
<?php
}//Cierro if de seguridad
?>