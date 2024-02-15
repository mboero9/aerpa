<?php
require_once("../includes/lib.php");

if (check_auth($usrid, $perid) && check_permission($permiso)) {
// permiso ok
	// validacion AJAX - back end
	if (isset($_GET["ajax"])) {
		try {
			if (isset($_GET["genremito"])) {
				$idfam = 0;
				$desfam = '';
				$where = array();
				switch ($_GET["par_registro"]) {
					case 'origen':
						$where[] = "REG_CODIGO_ORI=".sqlint($_GET["reg_id"]);
						$where[] = "REM_ID_ORI IS NULL";
						$where[] = "tra_fecha_entrega is not NULL";
						$where[] = "mot_codigo is NULL";
						$id_update = 'REM_ID_ORI';
						break;
					case 'devolucion':
						$where[] = "REG_CODIGO_ORI=".sqlint($_GET["reg_id"]);
						$where[] = "REM_ID_ORI IS NULL";
						$where[] = "tra_fecha_entrega is not NULL";
						$where[] = "mot_codigo is not NULL";
						$id_update = 'REM_ID_ORI';
						break;
					case 'destino':
						$sql = "select reg_familia from REG_AUTOM Where reg_codigo = ".$_GET["reg_id"];	
						$rs=$conn->Execute($sql);	
						$idfam=$rs->fields["reg_familia"];
						$sql = "select reg_descrip from REG_AUTOM Where reg_cod_int = $idfam";	
						$rs=$conn->Execute($sql);	
						$desfam=$rs->fields["reg_descrip"];
						$where[] = "REG_CODIGO_DES in( select reg_codigo from REG_AUTOM where reg_familia = $idfam)";
						$where[] = "REM_ID_DES IS NULL";
						$id_update = 'REM_ID_DES';
						break;
				}
//verifico que haya tramites para el remito						
				$sql = "select count(*) as canttramites 
						  from TRAMITE
					Where ".implode(" and ",$where);
				$rs=$conn->Execute($sql);	
//			    $canttramites=$sql;				
			    $canttramites=$rs->fields["canttramites"];
				if ($canttramites>0) {							
//aqui se genera el remito y se actualizan los tramites con el remito relacionado			
					$nro_remito=remito_nro($_GET["par_registro"]);
					$id_remito = numerador('REMITO');
					$estado=PAR_REMITO_ESTADO_GEN;				
//grabo el nvo remito		
					if ($nro_remito>0) {		
						$sql="Insert into REMITO  (REM_ID,
												   REM_NUMERO,
												   REM_TIPO, 
												   REM_FECHA_GENERACION,
												   REM_ESTADO,
												   USR_ID,
												   REM_FECHA_ACT
												  )
										   VALUES (".sqlint($id_remito).",
												   ".sqlint($nro_remito).",
												   ".sqlstring($_GET["par_registro"]).",												   
												   ".sqldate(dbtime()).",										   						   
												   ".sqlstring($estado).",										   						   										   
												   ".sqlint($usrid).",
												   ".sqldate(dbtime())."										   
												  )";	
						$conn->Execute($sql);
//actualizo los tramites									
						$sql = "Update TRAMITE Set ".$id_update." = " . sqlint($id_remito) . " Where ".implode(" and ",$where);
						$conn->Execute($sql);
						switch ($_GET["par_registro"]) {
							case 'origen':
							case 'devolucion':
								$reg = 'des';
								$sql_listado = "Select ".
									"t.TRA_DOMINIO, t.TRA_NRO_VOUCHER, ".
									$conn->SQLDate(FMT_DATE_DB, "t.TRA_FECHA_RETIRO")." as TRA_FECHA_RETIRO, ".		
								   "d.REG_COD_INT as REG_COD_INT_DES, ".
								   "d.REG_DESCRIP as REG_DESCRIP_DES, ".
								   "o.REG_COD_INT as REG_COD_INT_ORI, ".
								   "o.REG_DESCRIP as REG_DESCRIP_ORI ".
								   "from TRAMITE t ".
										"inner join REG_AUTOM o on o.reg_codigo = t.reg_codigo_ori ".
										"inner join REG_AUTOM d on d.reg_codigo = t.reg_codigo_des ".
									"Where ".$id_update." = " . sqlint($id_remito) .
									" Order by t.TRA_DOMINIO";
								break;
							case 'destino':
								$reg = 'ori';
								$sql_listado = "select 
												t.TRA_DOMINIO, 
												t.TRA_NRO_VOUCHER, ".
												 $conn->SQLDate(FMT_DATE_DB, "t.TRA_FECHA_RETIRO").
												" as TRA_FECHA_RETIRO, 
												f.REG_COD_INT as REG_COD_INT_FAM, 
												d.REG_COD_INT as REG_COD_INT_DES, 
												d.REG_DESCRIP as REG_DESCRIP_DES,
												o.REG_COD_INT as REG_COD_INT_ORI, 
												o.REG_DESCRIP as REG_DESCRIP_ORI
												from REG_AUTOM f
													inner Join REG_AUTOM d
														left join
															TRAMITE t
															Inner Join REMITO b on b.rem_id = t.rem_id_des and b.rem_id = ".$id_remito."
															Inner Join REG_AUTOM o on o.reg_codigo = t.reg_codigo_ori
														on d.reg_codigo = t.reg_codigo_des
													on d.reg_familia = f.reg_cod_int 
												where
													f.reg_cod_int = $idfam
												order by d.REG_DESCRIP,t.TRA_DOMINIO";
								break;
						}
						$out='ok|'.sprintf("%07s", $nro_remito).'|'.$canttramites.'|'.$sql_listado.'|'.$idfam.'|'.$desfam;	
					}//if		
				}else{
					$out='sintramites';						
				}
			}else{
				if (isset($_GET["registro"])) {
					$sql = "Select REG_CODIGO,REG_COD_INT,REG_DESCRIP
						From REG_AUTOM
						Where 1 = 1" .
						(($_GET["reg_cod"] == "") && ($_GET["reg_desc"] == "") ? " And 1 = 0" : "") .
						($_GET["reg_cod"] != "" ?
							" And REG_COD_INT = " . sqlstring($_GET["reg_cod"]) : "") .
						($_GET["reg_desc"] != "" ?
							" And Lower(REG_DESCRIP) Like " . sqlstring(strtolower($_GET["reg_desc"] . "%")) : "");
					$rs = $conn->Execute($sql);
					$out = "registro";
					if ($rs->EOF) {
						$out .= "\n";
					} else {
						while ($a = $rs->FetchRow()) {
							$out .= "\n" . implode("|", $a);
						} // end fetch
					} // end if eof
				} else {
// se toman todos los tramites que no tienen un remito vinculado
					switch ($_GET["par_registro"]) {
						case 'origen':
							$where[] = "REG_CODIGO_ORI=".sqlint($_GET["reg_id"]);
							$where[] = "REM_ID_ORI IS NULL";
							$where[] = "tra_fecha_entrega is not NULL";
							$where[] = "mot_codigo is NULL";
							$id_update = 'REM_ID_ORI';
							break;
						case 'devolucion':
							$where[] = "REG_CODIGO_ORI=".sqlint($_GET["reg_id"]);
							$where[] = "REM_ID_ORI IS NULL";
							$where[] = "tra_fecha_entrega is not NULL";
							$where[] = "mot_codigo is not NULL";
							$id_update = 'REM_ID_ORI';
							break;
						case 'destino':
							$sql = "select reg_familia from REG_AUTOM Where reg_codigo = ".$_GET["reg_id"];	
							$rs=$conn->Execute($sql);	
							$idfam=$rs->fields["reg_familia"];
							$sql = "select reg_descrip from REG_AUTOM Where reg_cod_int = $idfam";	
							$rs=$conn->Execute($sql);	
							$desfam=$rs->fields["reg_descrip"];
							$where[] = "REG_CODIGO_DES in( select reg_codigo from REG_AUTOM where reg_familia = $idfam)";
							$where[] = "REM_ID_DES IS NULL";
							$id_update = 'REM_ID_DES';
							break;
					}
					$sql  = "Select TRA_DOMINIO, TRA_NRO_VOUCHER ";
					$sql .= "From TRAMITE ";
					$sql .= "Where ".implode(" and ",$where)." ";
					$sql .= "Order by 1";					
					$rs = $conn->Execute($sql);
					$out = "tramites";
					if ($rs->EOF) {
						$out .= "\n";
					} else {
						while ($a = $rs->FetchRow()) {
							$out .= "\n" . implode("|", $a);
						} // end fetch
					} // end if eof
				} // end if registro
			}
//			header("content-type: text/plain; charset=iso-8859-1");
			echo($out);
			return;
		} catch (exception $e) {
			dbhandleerror($e);
		}
	} // end if AJAX

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>

<head>
<?php require_once("../includes/inc_header.php"); ?>
<!-- funcion parseDate -->
<script type="text/javascript" language="JavaScript" src="../includes/fecha.js"></script>

<!-- calendario desplegable -->
<style type="text/css">@import url(../calendar/calendar-win2k-1.css);</style>
<script type="text/javascript" src="../calendar/calendar.js"></script>
<script type="text/javascript" src="../calendar/lang/calendar-es.js"></script>
<script type="text/javascript" src="../calendar/calendar-setup.js"></script>

<script type="text/javascript">
<?php require_once("../includes/ajaxobjt.js"); ?>

var updating = false;
var newwindow=null;

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
			if (entidad == "registro") {
				document.frm.reg_lista.value = r;
			} else {
				document.frm.tram_lista.value = r;
			}
		}
		document.getElementById("loading").style.display = "none";
		document.body.style.cursor = '';
		updating = false;
	}
} // end getHttp

function getRegistro(criterio) {
	var ie  = (document.all)? true:false;
	// Busqueda AJAX del registro
	if ((document.frm.reg_cod.value == "") && (document.frm.reg_desc.value == "")) {
		return;
	}

	document.frm.reg_destino.value = document.frm.reg_lista.value = "";
	if (criterio == "cod") {
		document.frm.reg_desc.value = "";
	} else {
		document.frm.reg_cod.value = "";
	}
	updating = true;
	document.getElementById("loading").style.display = "";
	http.open("GET", "?ajax&registro&reg_cod=" + escape(document.frm.reg_cod.value) + "&reg_desc=" + escape(document.frm.reg_desc.value), false);
	http.send(null);
	getHttp();

	rl = new String(document.frm.reg_lista.value);
	if (rl.length > 0) {
		regs = rl.split("\n");
	} else {
		alert("No se encuentra ningún registro con esas condiciones");
		regs = new Array();
	}
	if (regs.length == 1) {
		reg = regs[0].split("|");
		document.frm.reg_destino.value = reg[0];
		document.frm.reg_cod.value = reg[1];
		document.frm.reg_desc.value = reg[2];
		document.getElementById("reg_elegir").style.display = "none";
		
		document.frm.buscar.focus();
		
		if (ie) { document.all['reg_elegir'].innerHtml= "";		
		}else{document.getElementById("reg_elegir").innerHTML = ""; }
	} else {
		s = "";
		fondo = 1;
		for (i = 0; i < regs.length; i++) {
			reg = regs[i].split("|");
			s += "<tr class=fondotabla" + fondo + " style=\"cursor: pointer;\" " +
				"onMouseOver=\"this.className = 'fondoconfirmacion';\" " +
				"onMouseOut=\"this.className = 'fondotabla" + fondo + "';\" " +
				"onclick=\"selRegistro(" + reg[0] + ",'" +
				reg[1].replace(/\"/g, '&quot;').replace(/\'/g, "\\'") + "','" +
				reg[2].replace(/\"/g, '&quot;').replace(/\'/g, "\\'") +
				"');\"><td class=celdatexto>" + reg[1] +
				"<\/td><td class=celdatexto>" + reg[2] + "<\/td><\/tr>\n";
			fondo = (fondo == 1 ? 2 : 1);
		} // end for
		if (ie) { document.all['reg_elegir'].innerHtml= s;		
		}else{document.getElementById("reg_elegir").innerHTML = s; }		
		document.getElementById("reg_elegir").style.display = "";
	} // end if
} // end getRegistro

function selRegistro(id, numero, descrip) {
	document.frm.reg_destino.value = id;
	document.frm.reg_cod.value = numero;
	document.frm.reg_desc.value = descrip;
	document.getElementById("reg_elegir").style.display = "none";
	document.getElementById("reg_elegir").innerHTML = "";
} // end selRegistro

function limpiar() {
	document.getElementById("reg_elegir").style.display = "none";
	document.getElementById("reg_elegir").innerHTML = "";
	document.getElementById('spanREMITO').innerHTML	="";
    document.getElementById('spanREMITO').className='';						  	
	document.frm.reg_destino.value =
	document.frm.reg_cod.value =
	document.frm.reg_desc.value = "";
	markDirty();
} // end limpiar

function getTramites() {

	if (updating){
		 return;
	}

	document.getElementById('spanREMITO').innerHTML	="";
    document.getElementById('spanREMITO').className='';						  	
	if (validar()) {
		// Busqueda AJAX de los tramites
		document.frm.tram_lista.value = "";
		updating = true;
		document.getElementById("loading").style.display = "";
		http.open("GET", "?ajax&reg_id=" + document.frm.reg_destino.value+
			             "&par_registro=" + document.frm.par_registro.value, false);				
		http.send(null);
		getHttp();

		if (document.frm.tram_lista.value == "") {
			document.getElementById("bodytramites").innerHTML="";						
			alert("El remito no incluye ningún trámite");
			return false;
		} else {			
			s = "<table><tr><th class=celdatitulo>Dominio</th><th class=celdatitulo>Voucher</th></tr>";
			fondo = 1;
			regs = document.frm.tram_lista.value.split("\n");
			for (i = 0; i < regs.length; i++) {
				reg = regs[i].split("|");			
				s += "<tr class=fondotabla" + fondo + "><td class=celdatexto>" + reg[0] + "<\/td>" +
				     "<td class=celdatexto>" + reg[1] + "<\/td><\/tr>\n";
				fondo = (fondo == 1 ? 2 : 1);
			} // end for
			s +="</table>";
			document.getElementById("bodytramites").innerHTML=s;			
			return true;
		} // end if
	} else {
		return false;
	} // end if validar
} // end getTramites

function validar() {
	if (document.frm.reg_cod.value=="") {
	   return false;
	}else{
	var s = "";
		buf = new String(document.frm.reg_destino.value);
		if (buf.length == 0) {
			s += "- Debe seleccionar el registro\n";
		} // end valid registro
	
		// Si datos ok, agregar fila
		if (s != "") {
			alert("Se han encontrado errores:\n" + s);
			return false;
		} else {
			document.frm.ok.value = "1";
			return true;
		} // end if errores
	}
} // end validar

function checkSubmit() {
	// evitar nuevo submit si haciendo submit
	if (document.frm.enviar.disabled) {
		return false;
	} // end if !reentry

	if ((document.frm.ok.value == "1") && (document.frm.tram_lista.value != "")) {
		return true;
	} else {
		if (getTramites()) {
			return true;
		} else {
			document.frm.enviar.disabled = false;
			return false;
		}
	}
} // end if checkSubmit

function markDirty() {
	document.frm.ok.value = 0;
	document.frm.tram_lista.value = "";
	document.getElementById("bodytramites").innerHTML="";				
} // end markDirty
function imprimir_remito() {
	if (document.frm.reg_destino.value=="") {
		  document.getElementById('spanREMITO').className='textoerror';						  		
		  document.getElementById('spanREMITO').innerHTML='NO ENCONTRARON TRAMITES';			
	}else{
		http.open("GET", "?ajax&genremito&reg_id=" + document.frm.reg_destino.value +
			  "&par_registro=" + document.frm.par_registro.value, false);
		http.send(null);
		if (http.readyState == 4) {
			results = http.responseText.split("|");
			//alert(results);
			document.getElementById('tdREMITO').style.visibility='visible';
			if (results[0]=='ok') {
		//				document.frm.id_remito.value=results[3];
				document.getElementById('spanREMITO').className='';						  
				document.getElementById('spanREMITO').innerHTML='REMITO GENERADO NRO.:&nbsp;<b>'+results[1]+'<\/b><BR>'+'&nbsp;con&nbsp;'+results[2]+'&nbsp;Trámites';
				document.descarga.sql.value=results[3];
				var titdev = 'DEVOLUCION DE VOUCHERS AL ORIGEN';
				switch ( document.frm.par_registro.value ) {
					case 'devolucion':
						titdev = 'DEVOLUCION DE LEGAJOS AL ORIGEN';
					case 'origen':
						document.descarga.archivo2.value = '../tramites/remito.xml';
						document.descarga.propiedadesreport.value = 
						"PageHeader|4|"+titdev+"\n"+			  			  
						"PageHeader|5|"+'ORIGINAL - Para Registro Origen'+"\n"+
						"PageHeader|7|"+results[1]+"\n";
						break;
					case 'destino':
						document.descarga.archivo2.value = '../tramites/remitodes.xml';
						document.descarga.propiedadesreport.value = 
						"PageHeader|4|"+'ENVIO A DESTINO'+"\n"+			  			  
						"PageHeader|5|"+'ORIGINAL - Para el Correo'+"\n"+
						"PageHeader|7|"+results[1]+"\n"+				
						"PageHeader|9|"+results[4]+"\n"+
						"PageHeader|10|"+results[5]+"\n"+															
						"MainGroup|SubGroup|GroupHeader|0|Registro Destino:\n"+
						"MainGroup|SubGroup|GroupHeader|5|Registro Origen\n";
						break;
				}
				newwindow=window.open(href='../export/imprime_ps4.php', this.target, 'width=250,height=140,left=260,top=230,resizable=no');											
		//				document.descarga.submit();
				return true;
			}else{
			  document.getElementById('spanREMITO').className='textoerror';						  
			  if (results[0]=='sintramites') {
				  document.getElementById('spanREMITO').innerHTML='NO ENCONTRARON TRAMITES';			
			  }else{
				  document.getElementById('spanREMITO').innerHTML='NO SE PUDO GENERAR EL REMITO';			
			  }
			  return false;			  
			}
		}
	}//if (document.frm.reg_destino.value=="")
}
function hacer_focus() {
	document.frm.reg_cod.focus();
}
</script>
</head>

<body onLoad="hacer_focus();" onFocus="if(newwindow){newwindow.focus();}">
<?php require_once('../includes/inc_topleft.php'); 

$pagina=basename($_SERVER['SCRIPT_FILENAME']);
$opcion=$_POST['par_registro'];
$par_registro=$_POST['par_registro'];
require_once('../includes/inc_titulo.php');

?>
<form action="" method=post name=frm onSubmit="return checkSubmit();">

<table align=center>
<tr><td align="right" colspan="2" id="tdREMITO" style="visibility:hidden; height:80px"><span id=spanREMITO></span></td></tr>
<tr><td class=celdatexto>Registro</td>
	<td class=celdatexto><input type=text name=reg_cod maxlength=5 size=5 title="N&uacute;mero"
		onchange="markDirty(this);" onBlur="getRegistro('cod');">
		<input type=text name=reg_desc maxlength=60 size=30 title="Nombre o parte del mismo"
		onchange="markDirty(this);" onBlur="getRegistro('desc');">
		<input type=hidden name=reg_destino>
		<input type=hidden name=reg_lista>
		<input type=hidden name=accion value="<?php echo($accion); ?>">
		<input type=hidden name=ok value="0">
		<input type=hidden name=par_registro value="<?php echo($par_registro); ?>">		
		<table id="reg_elegir" style="display: none;"></table></td></tr>

<tr><td colspan=2 class=celdatexto align=center>
		<input type=button class=botonout value="Deshacer" onClick="limpiar();">
		<input type=button class=botonout value="Buscar tr&aacute;mites" name="buscar" onClick="getTramites();">
		<input type=button name=enviar class=botonout value="Imprimir" onClick="imprimir_remito();">
		<input type=button class=botonout value="Volver" onClick="window.location = '../home/';">
		<img src="../imagenes/loading.gif" id=loading style="display: none;">
	</td></tr>

<tr><td class=celdatexto>Tr&aacute;mites a incluir</td>
	<td><input type=hidden name=tram_lista>
		<div id="bodytramites"></div>	
	</td></tr>
</table>

</form>
		
<form action="" method="post" name="descarga">
<input type="hidden" name="sql" />
<input type="hidden" name="archivo2" />
<input type="hidden" name="propiedadesreport" />
</form>
<?php require_once("../includes/inc_bottom.php"); ?>
</body>

</html>
<?php
} // fin autorizacion
?>