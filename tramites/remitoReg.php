<?php
require_once("../includes/lib.php");

if (check_auth($usrid, $perid) && check_permission($permiso)) {
// permiso ok
	// validacion AJAX - back end
	if (isset($_GET["ajax"])) {
		$idfam = 0;
		$desfam = '';
		$where = array();
		switch ($_GET["par_registro"]) {
			case 'origen':
				$where[] = "REM_ID_ORI IS NULL";
				$where[] = "tra_fecha_entrega is not NULL";
				$where[] = "mot_codigo is NULL";
				$tipo='ORI';
				break;
			case 'devolucion':
				$where[] = "REM_ID_ORI IS NULL";
				$where[] = "tra_fecha_entrega is not NULL";
				$where[] = "mot_codigo is not NULL";
				$tipo='ORI';
				break;
			case 'destino':
				//$where[] = "REG_CODIGO_DES in( select reg_codigo from REG_AUTOM where reg_familia = $idfam)";
				$where[] = "REM_ID_DES IS NULL";
				$tipo='DES';
				break;
		}
		if($_GET["rgi_id"]!='todas') $where[] = "RGI_CODIGO = " . sqlstring($_GET["rgi_id"]); 
		if (isset($_GET["genremito"])) {
//verifico que haya tramites para el remito						
			$sql = "select count(*) as canttramites ".				
				   " from TRAMITE ".
				   " inner join REG_AUTOM on REG_CODIGO = REG_CODIGO_".$tipo.
				   " where ".implode(" and ",$where);
			//echo("Cantidad de tramites: ".$sql."<br>");
			$rs=$conn->Execute($sql);	
			$canttramites=$rs->fields["canttramites"];
			if ($canttramites>0) {		
//busco los tramites de la region seleccionada para generar los remitos con corte por registro automotor				
				if( $tipo == 'ORI' ) {
					$sql = "select REG_CODIGO_".$tipo." from tramite ".
							" inner join REG_AUTOM on REG_CODIGO = REG_CODIGO_".$tipo.
							" where ".implode(" and ",$where).
							" group by REG_CODIGO_".$tipo.", REG_TIPO, REG_COD_INT  ".
							" order by REG_TIPO, REG_COD_INT  ";
				} else {
					$sql = "select distinct REG_FAMILIA from REG_AUTOM ".
							 "inner join TRAMITE on REG_CODIGO = REG_CODIGO_".$tipo.
							" where ".implode(" and ",$where).
							" order by REG_FAMILIA";
				}
			//echo("query de busqueda: ".$sql."<br>");
			//	echo("Tramites de la region: ".$sql."<br>");
				$rs=$conn->Execute($sql);
				$cant_remitos=0;
				$conn->StartTrans();
				$fams = array();		
				try {									
					while (!$rs->EOF) {   
		//aqui se genera el remito y se actualizan los tramites con el remito relacionado			
						$nro_remito=remito_nro($_GET["par_registro"]);
						if($cant_remitos==0){ $primer_remito=$nro_remito; }
						$id_remito =numerador('REMITO');
						$estado=PAR_REMITO_ESTADO_GEN;				
		//grabo el nvo remito
		//				if ($nro_remito>0) {		
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
						$cant_remitos++;
	//actualizo los tramites	
						if( $tipo == 'ORI' ) {	
							$sql = "Update TRAMITE Set REM_ID_".$tipo." = " . sqlint($id_remito).
								" Where REG_CODIGO_".$tipo."=".$rs->fields["REG_CODIGO_".$tipo].
								" and tra_fecha_entrega is not NULL".
								" and REM_ID_".$tipo." IS NULL and mot_codigo is ".($_GET["par_registro"]=='devolucion'?'not ':'')."NULL";
						}else{
							$fams[] = $rs->fields["REG_FAMILIA"];
							$sql= "Update TRAMITE ".
									"Set REM_ID_".$tipo." = " . sqlint($id_remito) .
									"where REG_CODIGO_".$tipo." in ( ".
										"select REG_CODIGO from REG_AUTOM where REG_FAMILIA = ".$rs->fields["REG_FAMILIA"].") ".
									"and REM_ID_".$tipo." IS NULL";
						}	
						$conn->Execute($sql);
						//echo("update de tramites: ".$sql."<br>");
						//echo("actualizacion de ramites: ".$sql."<br>");
	//						}//if	
					  $rs->movenext();	
					}//while
				} catch (exception $e) {
					dbhandleerror($e);
				}					  
				$conn->CompleteTrans();						  
				$ultimo_remito=$nro_remito; 
				$reg=($tipo == 'ORI' ? 'des' : 'ori');																				
				$sql_listado = "Select ".
					//"STUFF('0000000', 8-LEN(a.REM_NUMERO), LEN(a.REM_NUMERO), REM_NUMERO) as REM_NUMERO, ".
					"r.REG_COD_INT as REG_COD_INT_".strtoupper($reg).", ".
					"r.REG_DESCRIP as REG_DESCRIP_".strtoupper($reg).", ".
					"t.TRA_DOMINIO, t.TRA_NRO_VOUCHER, ".
					$conn->SQLDate(FMT_DATE_DB, "t.TRA_FECHA_RETIRO")." as TRA_FECHA_RETIRO, ".
					"s.REG_COD_INT as REG_COD_INT_".$tipo.", s.REG_DESCRIP as REG_DESCRIP_".$tipo.", ".
					"f.REG_COD_INT as REG_COD_INT_FAM, f.REG_DESCRIP as REG_DESCRIP_FAM ";							   
					if( $tipo == 'ORI' ) {	
						$sql_listado .= 
						"From TRAMITE t ".
							"Inner Join REMITO a On a.REM_ID = t.REM_ID_".$tipo." ".
								"and a.REM_NUMERO BETWEEN #_DESDE_# and #_HASTA_# ".
							"Inner Join REG_AUTOM r On t.REG_CODIGO_".strtoupper($reg)." = r.REG_CODIGO ".
							"Inner Join REG_AUTOM s On t.REG_CODIGO_".$tipo." = s.REG_CODIGO ".
							"Inner Join REG_AUTOM f On s.REG_FAMILIA = f.REG_COD_INT ".
							"Order by s.REG_COD_INT, t.TRA_DOMINIO";
					}else{
						$sql_listado .= 
						"From REG_AUTOM f ".
							"inner Join REG_AUTOM s ".
								"left join ".
									"TRAMITE t ".
									"Inner Join REMITO a On a.REM_ID = t.REM_ID_".$tipo." and ".
										"a.REM_NUMERO BETWEEN #_DESDE_# and #_HASTA_# ".
									"Inner Join REG_AUTOM r on r.reg_codigo = t.REG_CODIGO_".strtoupper($reg)." ".
								"on s.reg_codigo = t.REG_CODIGO_".$tipo." ".
							"on s.reg_familia = f.reg_cod_int ".
						"where f.reg_cod_int in(".
							"select distinct sr.reg_familia ".
							"from REG_AUTOM sr ".
								"inner join TRAMITE sa on sr.reg_codigo = sa.reg_codigo_".$tipo." ".
								"inner Join REMITO sb on sb.rem_id = sa.rem_id_".$tipo." ".
									"and sb.rem_numero BETWEEN #_DESDE_# and #_HASTA_# ".
									"and rem_tipo = '".$_GET["par_registro"]."' ".
						")".
						"Order by REG_COD_INT_FAM,REG_COD_INT_".$tipo." ,t.TRA_DOMINIO";
					}
					$out='ok|'.$cant_remitos.'|'.sprintf("%07s", $primer_remito).'|'.sprintf("%07s", $ultimo_remito).'|'.$sql_listado;	

			}else{
				$out='sintramites';						
			}
		}else{		
			$sql = "Select TRA_DOMINIO, TRA_NRO_VOUCHER	From TRAMITE ".
				" inner join REG_AUTOM on REG_CODIGO = REG_CODIGO_".$tipo.
				" where ".implode(" and ",$where);
			$rs = $conn->Execute($sql);
			echo $sql."<br />";
			$out = "tramites";
			if ($rs->EOF) {
				$out .= "\n";
			} else {
				while ($a = $rs->FetchRow()) {
					$out .= "\n" . implode("|", $a);
				} // end fetch
			} // end if eof
		}
//			header("content-type: text/plain; charset=iso-8859-1");
		echo($out);
		return;
	
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
		if (r.indexOf("invalid") == -1) {
			i = r.indexOf("\n");
			entidad = r.substr(0,i);
			if (i < r.length) {
				r = r.substr(i+1);
			} else {
				r = "";
			}
			document.frm.tram_lista.value = r;
		}
		document.getElementById("loading").style.display = "none";
		updating = false;
	}
} // end getHttp

function limpiar() {
	document.frm.region.selectedIndex = 0;
	markDirty();
} // end limpiar

function getTramites() {
	if (validar()) {
		// Busqueda AJAX de los tramites
		document.frm.tram_lista.value = "";
		updating = true;
		document.getElementById("loading").style.display = "";
		http.open("GET", "?ajax&rgi_id=" + escape(document.frm.region.value)+
			             "&par_registro=" + document.frm.par_registro.value, false);				
		http.send(null);
		getHttp();

		if (document.frm.tram_lista.value == "") {
			document.getElementById("bodytramites").innerHTML="";						
			alert("El remito no incluye ning�n tr�mite");
			return false;
		} else {
			s = "<table><tr><th class=celdatitulo>Dominio</th><th class=celdatitulo>Voucher</th></tr>";
			fondo = 1;
			regs = document.frm.tram_lista.value.split("\n");
			for (i = 0; i < regs.length; i++) {
				reg = regs[i].split("|");				
				s += "<tr class=fondotabla" + fondo + "><td class=celdatexto>" + reg[0] + "</td>" +
				     "<td class=celdatexto>" + reg[1] + "</td></tr>\n";
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
	var s = "";

	buf = new String(document.frm.region.value);
	if (buf.length == 0) {
		s += "- Debe seleccionar la regi�n\n";
	} // end valid registro

	// Si datos ok, agregar fila
	if (s != "") {
		alert("Se han encontrado errores:\n" + s);
		return false;
	} else {
		document.frm.ok.value = "1";
		return true;
	} // end if errores
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
			document.frm.region_desc.value =
				document.frm.region.options[document.frm.region.selectedIndex].text;
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
	    document.getElementById('tdREMITO').style.visibility='visible';
		/*alert("?ajax&genremito&rgi_id=" + document.frm.region.value +
		      "&par_registro=" + document.frm.par_registro.value);*/
		http.open("GET", "?ajax&genremito&rgi_id=" + document.frm.region.value +
		      "&par_registro=" + document.frm.par_registro.value, false);		
		http.send(null);
		  if (http.readyState == 4) {
			//alert(http.responseText);
			results = http.responseText.split("|");
//alert(results);			
			document.descarga.sql.value=results;
			if (results[0]=='ok') {
//			  document.frm.id_remito.value=results[3];
			  document.getElementById('imagenloading').style.display='none';
			  document.getElementById('spanREMITO').className='';						  
			  document.getElementById('spanREMITO').innerHTML='SE HAN GENERADO <b>'+results[1]+'<\/b>&nbsp;REMITOS<BR>'+'&nbsp;Desde el Remito Nro.:&nbsp;<b>'+results[2]+'<\/b>&nbsp;hasta el:&nbsp;<b>'+results[3]+'<\/b>';
			  document.descarga.sql.value=results[4];		
			  document.descarga.primer_remito.value = results[2];
			  document.descarga.ultimo_remito.value = results[3];
			  document.descarga.tipo.value = document.frm.par_registro.value;
			  document.descarga.propiedadesreport1.value ="ReportHeader|1|Regi�n:\n"+														  			  			  
			    "ReportHeader|2|"+document.frm.region.options[document.frm.region.selectedIndex].text+"\n"+
				"ReportHeader|4|"+results[2]+"\n"+
			    "ReportHeader|6|"+results[3]+"\n";
				//alert(document.descarga.coverprop.value);

				var titdev = 'DEVOLUCION DE VOUCHERS AL ORIGEN';
				switch ( document.frm.par_registro.value ) {
					case 'devolucion':
						titdev = 'DEVOLUCION DE LEGAJOS AL ORIGEN';
					case 'origen':
						document.descarga.archivo2.value = '../tramites/remito.xml';
						document.descarga.propiedadesreport.value = 
						"PageHeader|4|"+titdev+"\n"+			  			  
						"PageHeader|5|"+'ORIGINAL - Para Registro Origen'+"\n"+
						"PageHeader|7|#_REMITO_#\n";
						break;
					case 'destino':
						document.descarga.archivo2.value = '../tramites/remitodes.xml';
						document.descarga.propiedadesreport.value = 
						"PageHeader|4|"+'ENVIO A DESTINO'+"\n"+			  			  
						"PageHeader|5|"+'ORIGINAL - Para el Correo'+"\n"+
						"PageHeader|7|#_REMITO_#\n"+				
						"PageHeader|8|"+'Registro Cabecera:'+"\n"+
						"PageHeader|9|#_REG_COD_INT_FAM_#\n"+
						"PageHeader|10|#_REG_DESCRIP_FAM_#\n"+															
						"MainGroup|SubGroup|GroupHeader|0|Registro Destino:\n"+
						"MainGroup|SubGroup|GroupHeader|5|Registro Origen\n";
						break;
				}
			  newwindow=window.open(href='../export/imprime_ps5.php?desde='+results[2]+"&hasta="+results[3], this.target, 'width=250,height=140,left=260,top=230,resizable=no');											
//			  document.descarga.submit();
			  return true;
			}else{
			  document.getElementById('imagenloading').style.display='none';			
			  document.getElementById('spanREMITO').className='textoerror';						  
			  if (results[0]=='sintramites') {
				  document.getElementById('spanREMITO').innerHTML=(document.frm.par_registro.value=='origen' ? 'No Existen Tr�mites Pendientes para Devolver a Origen' : 'No Existen Tr�mites Pendientes');			
			  }else{
alert(results);			  
				  document.getElementById('spanREMITO').innerHTML='NO SE PUDO GENERAR EL REMITO';			
			  }
			  return false;			  
			}
		  }
}

</script>
</head>

<body onFocus="if(newwindow){newwindow.focus();}">
<?php require_once('../includes/inc_topleft.php'); 

$pagina=basename($_SERVER['SCRIPT_FILENAME']);
$opcion=$_POST['par_registro'];
$par_registro=$_POST['par_registro'];
require_once('../includes/inc_titulo.php');?>


<form action="remitoReg-do.php" method=post name=frm onSubmit="return checkSubmit();">

<table align="center">
<tr><td align="right" colspan="2" id="tdREMITO" style="visibility:hidden; height:80px"><span id=spanREMITO>Generando Remitos&nbsp;&nbsp;</span><img src="../imagenes/loading.gif" alt="loading" id="imagenloading">
</td></tr>
<tr><td class=celdatexto>Regi&oacute;n</td>
	<td class=celdatexto><select name=region <?php echo($dis); ?>>
		<option value="">Seleccione una opci&oacute;n</option>
		<option value="todas">Todas las Regiones</option>
<?php fill_combo("Select RGI_CODIGO,RGI_DESCRIP From REGION
	Where RGI_FECHA_BAJA Is Null Order by RGI_DESCRIP", null); ?>
		</select>
		<input type="hidden" name="region_desc">
		<input type=hidden name=accion value="<?php echo($accion); ?>">
		<input type=hidden name=ok value="0">
		<input type=hidden name=par_registro value="<?php echo($par_registro); ?>">
		
		</td></tr>
<tr><td colspan=2 class=celdatexto align=center>
		<input type=button class=botonout value="Deshacer" onClick="limpiar();">
		<input type=button class=botonout value="Buscar tr&aacute;mites" onClick="getTramites();">
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
<input type="hidden" name="primer_remito">
<input type="hidden" name="ultimo_remito">
<input type="hidden" name="sql">
<input type="hidden" name="archivo2">

<input type="hidden" name="propiedadesreport">

<input type="hidden" name="tipo">
<input type="hidden" name="propiedadesreport1">
</form>
<?php require_once("../includes/inc_bottom.php"); ?>

</body>

</html>
<?php
} // fin autorizacion
?>