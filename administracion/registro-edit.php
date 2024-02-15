<?php
require_once("../includes/lib.php");

if (check_auth($usrid, $perid) && check_permission($permiso, "registros.php")) {
	// permiso ok
	// validacion AJAX - back end
	if (isset($_GET["ajax"])) {
		try {
			/*		if (isset($_GET["nombre"])) {
			 $sql =
				"Select REG_CODIGO From REG_AUTOM
				Where REG_CODIGO != " . sqlint($_GET["id"]) . "
				And REG_FECHA_BAJA Is Null
				And Lower(REG_DESCRIP) = " . sqlstring(strtolower($_GET["nombre"])) ;
				$rs = $conn->Execute($sql);
				$out = "nombre\n" . (!$rs->EOF ? "1" : "0");
				} elseif (isset($_GET["numero"])) {*/
			$sql =
				"Select REG_CODIGO From REG_AUTOM
				Where REG_CODIGO != " . sqlint($_GET["id"]) . "
				And Lower(REG_COD_INT) = " . sqlstring(strtolower($_GET["numero"]));
			//echo $sql;
			$rs = $conn->Execute($sql);
			$out = "numero\n" . (!$rs->EOF ? "1" : "0");
			//		}

			header("content-type: text/plain; charset=iso-8859-1");
			echo($out);
			return;
		} catch (exception $e) {
			dbhandleerror();
		}
	} // end if AJAX

	$accion = $_POST["accion"];

	try {
		$provincias = "";
		$sql = "Select PRO_CODIGO From PROVINCIA Order by PRO_CODIGO";
		$rs = $conn->Execute($sql);
		while (!$rs->EOF) {
			$provincias .= $rs->fields["PRO_CODIGO"];
			$rs->MoveNext();
		} // end while !eof
	} catch (exception $e) {
		dbhandleerror($e);
	}

	$v_id = 0;

	if ($accion != ABM_NEW) {
		try {
			$sql = "Select REG_CODIGO,REG_DESCRIP,REG_CALLE,REG_NUMERO,REG_PISO," .
		"	REG_CPA,REG_LOCALIDAD,PRO_CODIGO,RGI_CODIGO," .
		"	REG_COD_INT,REG_TIPO,REG_FAMILIA, " .
			$conn->SQLDate(FMT_DATE_DB, "REG_FECHA_BAJA")." as REG_FECHA_BAJA	".
		"From REG_AUTOM " .
		"Where REG_CODIGO = " . sqlint($_POST["id"]);
			$rs = $conn->Execute($sql);
			if (!$rs->EOF) {
				$v_id = $rs->fields["REG_CODIGO"];
				$v_nombre    = trim($rs->fields["REG_DESCRIP"]);
				$v_calle     = trim($rs->fields["REG_CALLE"]);
				$v_altura    = trim($rs->fields["REG_NUMERO"]);
				$v_piso      = trim($rs->fields["REG_PISO"]);
				$v_cpa       = trim($rs->fields["REG_CPA"]);
				$v_localidad = trim($rs->fields["REG_LOCALIDAD"]);
				$v_provincia = trim($rs->fields["PRO_CODIGO"]);
				$v_region    = trim($rs->fields["RGI_CODIGO"]);
				$v_numero    = trim($rs->fields["REG_COD_INT"]);
				$v_tipo      = trim($rs->fields["REG_TIPO"]);
				$v_fecbaja   = trim($rs->fields["REG_FECHA_BAJA"]);
				$v_familia    = trim($rs->fields["REG_FAMILIA"]);
				if($v_fecbaja!="") $accion=ABM_REHABILITAR;
			} // end if eof
		} catch (exception $e) {
			dbhandleerror($e);
		}
	} // end if !ABM_NEW
	$view   = ($accion  == ABM_VIEW);
	$new    = ($accion  == ABM_NEW);
	$edit   = (($accion == ABM_NEW) || ($accion == ABM_EDIT));
	$del    = ($accion  == ABM_DEL);
	$rehabilitar = ($accion  == ABM_REHABILITAR);
	$ro     = (!$edit ? " readonly" : "");
	$dis    = (!$edit ? " disabled" : "");
	?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>

<head>
	<?php require_once("../includes/inc_header.php"); ?>
<script type="text/javascript" language="JavaScript"
	src="../includes/ajaxobjt.js"></script>
<script type="text/javascript" language="JavaScript"
	src="../includes/comp_cpa.js"></script>
<script type="text/javascript"><!--
<?php if ($edit) { ?>

var updating = false;

var oldvalue;

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
			r = r.substr(i+1);
			if (entidad == "nombre") {
				document.frm.nombre_existe.value = r;
			} else if (entidad == "numero") {
				document.frm.numero_existe.value = r;
			}
		}
		document.getElementById("loading").style.display = "none";
		updating = false;
	}
} // end getHttp
<?php } // end if edit ?>


/**
 * Carga familia desde numero si esta vacia.
 */	
function CargaFamilia (){	

		bufFamilia = new String(document.frm.familia.value);
		bufNumero = new String(document.frm.numero.value);

		var numero=document.frm.numero.value;
		var familia=document.frm.familia.value;
	
		if ((bufNumero.length != 0)&&(bufFamilia.length == 0)){
					document.frm.familia.value = document.frm.numero.value;	
					document.getElementById('spanFam').innerHTML='';				
					document.frm.familia.focus();
		}else if (numero != familia ){
					document.frm.familia.value = document.frm.numero.value;					
					document.getElementById('spanFam').innerHTML='';
					document.frm.familia.focus();
		}			
}


function onFocus(obj){

oldvalue = obj.value;

}

function ajax(url) {
	http.open("GET", url, false);
	http.send(null);
}

/**
 * Recupero el codigo postal de la familia por ajax.
 */	
function ValidaFamiliaAjax(){	
		var parametros;
		var url;
		var bufFamilia = new String(document.frm.familia.value);
		var numero = document.frm.numero.value;
		var familia_value = document.frm.familia.value;
			
			if ((numero != familia_value)&&(bufFamilia.lenght != 0)){
				 
				  parametros="familia="+familia_value;
				  url="registro-edit_ajax.php?"+parametros;
					
				  ajax(url);
					results = http.responseText.split("|");
				
				var cp;
				if (results[1]=="A" || results[1]=="D"){
					cp = results[2];
				}else{
					cp="";
				}
			
				 var resultado=results[0];
				
					/** 
					 * validacion para cp inexistente
					 */				 

					if (results[0]=='Inexistente') {
							document.getElementById('spanFam').innerHTML='<br/>'+'<b>Inexistente o <br/>Registro No es cabecera</b></font>';
							document.frm.familia.value=oldvalue;
							
					}else if (cp == ""){
							document.getElementById('spanFam').innerHTML='';
							var obj = document.getElementById('familia');																			
							obj.value="";
							setTimeout('frm.familia.focus();', 50);
									
					}else{
							document.getElementById('spanFam').innerHTML='<br/>'+'<b>'+results[0]+'('+cp+')'+'</b></font>';
					}	
					
					// valido si los codigos postales son iguales
					// alert( cp + " " + document.frm.cpa.value );
					if (cp != ""){		
							if (!comp_cpa(cp,document.frm.cpa.value)){
									conf = confirm("Los codigos postales no son iguales, lo acepta?");
									if (!conf){
											var obj = document.getElementById('familia');										
											document.getElementById('spanFam').innerHTML='';										
											obj.value="";
											setTimeout('frm.familia.focus();', 50);
									}
							}else{
									document.getElementById('spanFam').innerHTML='';
							}
					}					
			}	
			
			if ((bufFamilia.lenght != 0)&&(numero == familia_value)){
				document.getElementById('spanFam').innerHTML='';				  				
				document.frm.familia.focus();
			}
			return true;		
}


function validar() {
<?php if ($edit) { ?>
	var s = "";

	// evitar nuevo submit si haciendo submit
	if (document.frm.enviar.disabled) {
		return false;
	} // end if !reentry

	buf = new String(document.frm.nombre.value).replace(/^\s*(\S*(\s+\S+)*)\s*$/, "$1");
	if (buf.length == 0) {
		s += "- Debe ingresar el nombre\n";
	} else {
		// Validacion AJAX sincronica para detectar duplicado
/*		document.frm.nombre_existe.value = "";
		updating = true;
		document.getElementById("loading").style.display = "";
		http.open("GET", "?ajax&id=<?php echo($_POST["id"]); ?>&nombre=" + escape(document.frm.nombre.value+"&numero=" + escape(document.frm.numero.value), false);
		http.send(null);
		getHttp();
		s += (document.frm.nombre_existe.value == "1" ?
			"- El nombre de registro ya existe\n" : "");*/
	} // end valid nombre

	buf = new String(document.frm.calle.value).replace(/^\s*(\S*(\s+\S+)*)\s*$/, "$1");
	if (buf.length == 0) {
		s += "- Debe ingresar la calle\n";
	} // end valid calle

	buf = new String(document.frm.altura.value).replace(/^\s*(\S*(\s+\S+)*)\s*$/, "$1");
	if (buf.length == 0) {
		s += "- Debe ingresar la altura\n";
	} // end valid altura

	buf = new String(document.frm.cpa.value).toUpperCase().replace(/^\s*(\S*(\s+\S+)*)\s*$/, "$1");
	document.frm.cpa.value = buf;
	if (buf.length == 0) {
		s += "- Debe ingresar el codigo postal\n";
	} else {
		rx = /^(([1-9]{1}[0-9]{3})|([<?php echo($provincias); ?>]{1}[1-9]{1}[0-9]{3}[A-Z]{3}))$/;
		s += (!rx.test(buf) ? "- El codigo postal es incorrecto\n" : "");
	} // end valid cpa

	buf = new String(document.frm.localidad.value).replace(/^\s*(\S*(\s+\S+)*)\s*$/, "$1");
	if (buf.length == 0) {
		s += "- Debe ingresar la localidad\n";
	} // end valid localidad

	buf = new String(document.frm.provincia.value);
	if (buf.length == 0) {
		s += "- Debe seleccionar la provincia\n";
	} // end valid provincia

	buf = new String(document.frm.region.value);
	if (buf.length == 0) {
		s += "- Debe seleccionar la region\n";
	} // end valid region

	buf = new String(document.frm.numero.value).replace(/^\s*(\S*(\s+\S+)*)\s*$/, "$1");
	rx = /^[0-9]{5}$/;
	if (buf.length == 0) {
		s += "- Debe ingresar el numero\n";
	} else if (!rx.test(buf)) {
		s += "- El número es incorrecto\n";
	} else {
		// Validacion AJAX sincronica para detectar duplicado
		document.frm.numero_existe.value = "";
		updating = true;
		document.getElementById("loading").style.display = "";
		http.open("GET", "?ajax&id=<?php echo($_POST["id"]); ?>&numero=" + escape(document.frm.numero.value), false);
		http.send(null);
		getHttp();
		s += (document.frm.numero_existe.value == "1" ?
			"- El numero de registro ya existe\n" : "");
	} // end valid numero

	buf = new String(document.frm.tipo.value);
	if (buf.length == 0) {
		s += "- Debe seleccionar el tipo\n";
	} // end valid tipo


	buf = new String(document.frm.familia.value).replace(/^\s*(\S*(\s+\S+)*)\s*$/, "$1");
	rx = /^[0-9]{5}$/;
	if (buf.length == 0) {
		s += "- Debe ingresar la familia\n";
	} else if (!rx.test(buf)) {
		s += "- La familia es incorrecta\n";
	} 

	
	buf = new String(document.frm.tipo.value);
	if (buf.length == 0) {
		s += "- Debe seleccionar el tipo\n";
	} // end valid tipo


	// Si datos ok, agregar fila
	if (s != "") {
		alert("Se han encontrado errores:\n" + s);
		return false;
	} else {
		document.frm.enviar.disabled = true;
		return true;
	} // end if errores
<?php } // end if edit ?>

<?php if ($del) { ?>
	return confirm("Esta seguro que desea eliminar este registro?");
<?php } // end if delete ?>
<? if ($rehabilitar) { ?>
    return;
<?php } // end if rehabilitar ?>
} // end validar

--></script>
</head>
<body>
<?php require_once('../includes/inc_topleft.php'); ?>

<p class=titulo1>Registro Automotor</p>

<form action="registro-edit-do.php" method=post name=frm
	onSubmit="return validar();"><input type=hidden name=accion
	value="<?=($accion); ?>"> <input type=hidden name=reentrante value="1">
<input type=hidden name=id value="<?=($v_id); ?>">

<p align=center>

<table class=tablanormal>

	<tr>
		<td class=celdatexto>Nombre:</td>
		<td class=celdatexto><input type=text name=nombre maxlength=60
			value="<?php echo(htmlentities($v_nombre)); ?>" <?php echo($ro); ?>>
		<input type=hidden name=nombre_existe></td>
		<input type=hidden name=familia_existe>
		</td>
	</tr>

	<tr>
		<td class=celdatexto>Calle:</td>
		<td class=celdatexto><input type=text name=calle maxlength=60
			value="<?php echo(htmlentities($v_calle)); ?>" <?php echo($ro); ?>></td>
	</tr>

	<tr>
		<td class=celdatexto>Altura:</td>
		<td class=celdatexto><input type=text name=altura maxlength=5
			value="<?php echo(htmlentities($v_altura)); ?>" <?php echo($ro); ?>></td>
	</tr>

	<tr>
		<td class=celdatexto>Piso:</td>
		<td class=celdatexto><input type=text name=piso maxlength=3
			value="<?php echo(htmlentities($v_piso)); ?>" <?php echo($ro); ?>></td>
	</tr>

	<tr>
		<td class=celdatexto>C&oacute;digo postal:</td>
		<td class=celdatexto><input type=text name=cpa maxlength=8
			style="text-transform: uppercase;"
			value="<?php echo(htmlentities($v_cpa)); ?>" <?php echo($ro); ?> onChange="this.value = this.value.toUpperCase();"></td>
	</tr>

	<tr>
		<td class=celdatexto>Localidad:</td>
		<td class=celdatexto><input type=text name=localidad maxlength=40
			value="<?php echo(htmlentities($v_localidad)); ?>"
			<?php echo($ro); ?>></td>
	</tr>

	<tr>
		<td class=celdatexto>Provincia:</td>
		<td class=celdatexto><select name=provincia <?php echo($dis); ?>>
			<option value="">Seleccione una opci&oacute;n</option>
			<?php fill_combo("Select PRO_CODIGO,PRO_DESCRIP From PROVINCIA Order by PRO_DESCRIP", $v_provincia); ?>
		</select></td>
	</tr>

	<tr>
		<td class=celdatexto>Regi&oacute;n:</td>
		<td class=celdatexto><select name=region <?php echo($dis); ?>>
			<option value="">Seleccione una opci&oacute;n</option>
			<?php fill_combo("Select RGI_CODIGO,RGI_DESCRIP From REGION
	Where RGI_FECHA_BAJA Is Null Order by RGI_DESCRIP", $v_region); ?>
		</select></td>
	</tr>

	<tr>
		<td class=celdatexto>N&uacute;mero:</td>
		<td class=celdatexto><input type=text name="numero" id="numero" maxlength="5"
			value="<?php echo(htmlentities($v_numero)); ?>" <?php echo($ro); ?>
			onchange="CargaFamilia();"> <input type=hidden name=numero_existe></td>
	</tr>

	<tr>
		<td class=celdatexto>Familia:</td>
		<td class="celdatexto">
			<input type="text"  name="familia"
			id="familia" maxlength="5"
			value="<?php echo(htmlentities($v_familia)); ?>"
			onchange="ValidaFamiliaAjax()"  
			onfocus="onFocus(this)" /> 
			<span id="spanFam" class="celdatexto"></span>
		</td>
	</tr>

	<tr>
		<td class=celdatexto>Tipo:</td>
		<td class=celdatexto><select name=tipo <?php echo($dis); ?>>
			<option value="">&nbsp;</option>
			<?php fill_combo_arr(array(TREG_DESTINO => TREGLBL_DESTINO, TREG_AMBOS => TREGLBL_AMBOS), $v_tipo); ?>
		</select></td>
	</tr>

	<tr>
		<td class=celdatexto>Fec. Baja:</td>
		<td class=celdatexto><input type=text name=fecbaja
			value="<?=(htmlentities($v_fecbaja)); ?>" readOnly></td>
	</tr>
	<tr>
		<td colspan=2 class=celdatexto align=center><?php  if (!$view) { ?> <input
			type=submit name=enviar class=botonout
			value="<?php echo(abm_label($accion)); ?>"> <?php } // end if view ?>
		<input type=button class=botonout value="Volver"
			onClick="window.location = 'registros.php';"> <img
			src="../imagenes/loading.gif" id=loading style="display: none;"></td>
	</tr>

</table>
</p>
</form>
			<?php require_once("../includes/inc_bottom.php"); ?>
</body>

</html>
			<?php
} // fin autorizacion
?>