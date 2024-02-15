<?php
require_once("inc_constants.php");
require_once("inc_sqlwrap.php");
require_once("db.php");
/////////////////////////////////////////////////////
// Compara fecha en php
////////////////////////////////////////////////////
function compara_fechas($fecha1,$fecha2)
{
	/*
		Return values:
	1 - first is greater
	2 - second is greater
	3 - same date
	false - parameter error
	*/
	global $ret_val;
	$fec1 = split("/",$fecha1);
	$fec2 = split("/",$fecha2);
	if(count($fec1)==3 && count($fec2)==3 && (checkdate($fec1[1],$fec1[0],$fec1[2]) && checkdate($fec2[1],$fec2[0],$fec2[2])))
	{
		//echo($fec1[0]."/".$fec1[1]."/".$fec1[2])
		//Tiempo en segundos desde Enero 1, de 1970;
	
		$fec_sec1 = mktime(0,0,0,$fec1[1],$fec1[0],$fec1[2]);
		$fec_sec2 = mktime(0,0,0,$fec2[1],$fec2[0],$fec2[2]);
	
		if($fec_sec1 == $fec_sec2)
		{
			$ret_val = 3;
		}
		elseif($fec_sec1>$fec_sec2)
		{
			$ret_val = 1; 
		}
		else
		{
			$ret_val = 2;
		}
	}
	else
	{
		$ret_val = false;
	}
		return $ret_val;
	
}
/////////////////////////////////////////////////////
// Mostrar checked si true
// (para radio buttons y checkboxes)
function is_checked($b) {
	return($b ? " checked=true" : "");
} // end show_checked
/////////////////////////////////////////////////////

/////////////////////////////////////////////////////
// Mostrar label de accion de ABM
function abm_label($accion) {
	switch ($accion) {
		case ABM_VIEW: $lbl = ABMLBL_VIEW; break;
		case ABM_NEW: $lbl = ABMLBL_NEW; break;
		case ABM_EDIT: $lbl = ABMLBL_EDIT; break;
		case ABM_DEL: $lbl = ABMLBL_DEL; break;
		case ABM_SETDEFAULT: $lbl = ABMLBL_SETDEFAULT; break;
		case ABM_REHABILITAR: $lbl = ABMLBL_REHABILITAR; break;		
	} // end switch

	return $lbl;
} // end abm_label
/////////////////////////////////////////////////////

/////////////////////////////////////////////////////
// Llenar un combo desde sql
function fill_combo($sql, $presel = null) {
	global $conn;

	$fm = $conn->fetchMode;
	$conn->SetFetchMode(ADODB_FETCH_NUM);
	$rs = $conn->Execute($sql);
	while (!$rs->EOF) {
?>
	<option value="<?php echo($rs->fields[0]); ?>" <?php echo($rs->fields[0] == $presel ? " selected" : ""); ?>><?php echo(htmlentities($rs->fields[1])); ?></option>
<?php
		$rs->MoveNext();
	} // end while !eof
	$conn->SetFetchMode($fm);
} // end fill_combo
/////////////////////////////////////////////////////

/////////////////////////////////////////////////////
// Llenar un combo desde array
function fill_combo_arr($arr, $presel = null) {
	foreach ($arr as $key => $val) {
?>
	<option value="<?php echo($key); ?>" <?php echo($key == $presel ? " selected" : ""); ?>><?php echo(htmlentities($val)); ?></option>
<?php
	} // end foreach
} // end fill_combo_arr
/////////////////////////////////////////////////////

/////////////////////////////////////////////////////
// Llenar un combo desde vector
function fill_combo_vec($vec, $presel = null) {
	foreach ($vec as $val) {
?>
	<option value="<?php echo($val); ?>" <?php echo($val == $presel ? " selected" : ""); ?>><?php echo(htmlentities($val)); ?></option>
<?php
	} // end foreach
} // end fill_combo_arr
/////////////////////////////////////////////////////

/////////////////////////////////////////////////////
// Devolver un string texto delimitado de un recordset
function txt_rs($sql) {
	global $conn;

	$out = "";
	$fm = $conn->fetchMode;
	$conn->SetFetchMode(ADODB_FETCH_NUM);
	$rs = $conn->Execute($sql);
	if (!$rs->EOF) {
		while (!$rs->EOF) {
			$val0 = str_replace("|", ":", $rs->fields[0]);
			$val1 = str_replace("|", ":", $rs->fields[1]);
			$out .= "$val0|$val1\n";
			$rs->MoveNext();
		} // end while !eof
	} // end if !eof
	$conn->SetFetchMode($fm);
	return $out;
} // end txt_rs
/////////////////////////////////////////////////////

/////////////////////////////////////////////////////
// Funcion para convertir la letra de columna de Excel (ej.: AB)
// en un index utilizable con arrays
function xlscol($excelcol) {
	$excelcol = strtoupper($excelcol);

	$asc_a = ord("A");
	$asc_z = ord("Z");
	$ltr = $asc_z - $asc_a + 1;

	$len = strlen($excelcol);

	$col = 0;
	for ($i = 0; $i < $len; $i++) {
		$col += (ord($excelcol{$i}) - $asc_a + 1) * pow($ltr, $len - 1 - $i);
	} // end for $i

	return ($col - 1);
} // end function xlscol
/////////////////////////////////////////////////////

/////////////////////////////////////////////////////
// Funcion para sugerir al proxy que la pagina
// no es cacheable -- soluciona muchos problemas
function anticache() {
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
	header("Cache-Control: no-store, no-cache, must-revalidate");
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Pragma: no-cache");
} // end anticache
/////////////////////////////////////////////////////

/////////////////////////////////////////////////////
function getParametro($nombre){
	global $conn;

	$sql = "Select PAR_VALOR From PARAMETRO Where Lower(PAR_NOMBRE) = " . sqlstring(strtolower($nombre));
	$rspar = $conn->Execute($sql);

	return $rspar->fields["PAR_VALOR"];
} // end getParametro()
/////////////////////////////////////////////////////

/////////////////////////////////////////////////////
function numerador($tabla) {
	global $conn;
	$sql = "Select NUM_ULTIMO From NUMERADOR Where Lower(NUM_TABLA) = "
		. sqlstring(strtolower($tabla));
	$rs = $conn->Execute($sql);
	if ($rs->EOF) {
		$sql = "Insert Into NUMERADOR (NUM_TABLA, NUM_ULTIMO) "
			."Values (" . sqlstring(strtolower($tabla)) . ", " . sqlint(1) . ")";
		$conn->Execute($sql);
		return 1;
	} else {
		$id = $rs->fields["NUM_ULTIMO"] + 1;
		$sql = "Update NUMERADOR "
			. "Set NUM_ULTIMO = " . sqlint($id)
			. " Where Lower(NUM_TABLA) = " . sqlstring(strtolower($tabla));
		$conn->Execute($sql);
		return $id;
	} // end if eof
} // end numerador
/////////////////////////////////////////////////////
/////////////////////////////////////////////////////
function remito_nro($tipo_remito) {
	global $conn;
	switch (strtoupper($tipo_remito)) {
		case 'ORIGEN':
			$par_nombre = NRO_ORIGEN;
			break;
		case 'DEVOLUCION':
			$par_nombre = NRO_DEVOLUCION;
			break;
		case 'DESTINO':
			$par_nombre = NRO_DESTINO;
			break;
	}
	$sql = "Select par_valor From PARAMETRO where par_nombre = '".$par_nombre."'";
	//echo($sql);
	$rs = $conn->Execute($sql);
	if ($rs->EOF) {
		$sql = "Insert Into PARAMETRO (par_nombre, par_valor) Values ('" . $par_nombre . "', " . sqlint(1) . ")";		
		$conn->Execute($sql);
		return 1;
	} else {
		$id = $rs->fields["par_valor"] + 1;
		$sql = "Update PARAMETRO Set par_valor = " . sqlint($id) . " where par_nombre = '".$par_nombre."'";
		$conn->Execute($sql);
		return $id;
	} // end if eof
} // end remito_nro
/////////////////////////////////////////////////////

/////////////////////////////////////////////////////
// Devuelve el directorio raiz del sitio
function raizsitio() {
	$tmp = explode("/", dirname($_SERVER["PHP_SELF"]));
	array_pop($tmp);
	if (count($tmp) > 0) {
		$raiz = implode("/", $tmp) . "/";
	} else {
		$raiz = "/";
	}
	return $raiz;
} // end raizsitio
/////////////////////////////////////////////////////

/////////////////////////////////////////////////////
// Fecha + hora de base de datos
function dbtime() {
	global $conn;
	$sql = "Select Count(*) as a," . $conn->sysTimeStamp . " as b From numerador";
	$rs = $conn->Execute($sql);
	$ret = strtotime($rs->fields["b"]);
	return $ret;
} // end dbtime
/////////////////////////////////////////////////////

/////////////////////////////////////////////////////
// Fecha de base de datos
function dbdate() {
	global $conn;
	$sql = "Select Count(*) as a," . $conn->sysDate . " as b From numerador";
	$rs = $conn->Execute($sql);
	$ret = strtotime($rs->fields["b"]);
	return $ret;
} // end dbdate
/////////////////////////////////////////////////////

/////////////////////////////////////////////////////
// Error handler para base de datos
function dbhandleerror($e) {
	global $err, $desarrollo;
//	$err[] = $e;
	if ($desarrollo) {
		?><pre><?php print_r($e); ?></pre><?php
	} else {
		?><span class=textoerror>Se ha producido un error en base de datos en el archivo
			<?php echo($_SERVER["PHP_SELF"]); ?></span><?php
	}
} // end dbhandleerror()
/////////////////////////////////////////////////////

/////////////////////////////////////////////////////
// Devuelve true/false si hubo errores
function dberror() {
	global $err;
	return (count($err) > 0);
} // end if dberror
/////////////////////////////////////////////////////

/////////////////////////////////////////////////////
// Barra de navegacion para paginado de ABMs
function navigationbar($page, $totalrows) {
	$lastpage = ceil($totalrows / RECS_PER_PAGE);
?>
<table width="100%"><tr>
<td style="text-align: left;"><img height="12" width="15"
	<?php if ($page > 1) { ?>
	src="../imagenes/nav-left2.png" title="Primera pgina"
	style="cursor: pointer;" onclick="go_page(1);"
	<?php } else { ?>
	src="../imagenes/1x1.png"
	<?php } // end if page 1 ?> />

	<img height="12" width="12"
	<?php if ($page > 1) { ?>title="P gina anterior"
	src="../imagenes/nav-left.png"
	style="cursor: pointer;" onclick="go_page(<?php echo($page - 1); ?>);"
	<?php } else { ?>
	src="../imagenes/1x1.png"
	<?php } // end if page 1 ?> /></td>

<td style="text-align: center;" class="pagelink"><?php
$pages = (PAGE_LINKS <= $lastpage ? PAGE_LINKS : $lastpage);
$first = $page - floor($pages / 2);
$last = $page + ceil($pages / 2) - 1;
if ($first < 1) { $last += 1 - $first; $first = 1; }
if ($last >= $lastpage) { $first -= $last - $lastpage; $last = $lastpage; }
if ($first < 1) { $first = 1; }

for ($i = $first; $i <= $last; $i++) {
	if ($i == $page) {
		echo(" <b title=\"Pgina actual\">[$page]</b> ");
	} else {
?> <a href="#" title="P gina <?php echo($i); ?>"
	onclick="go_page(<?php echo($i); ?>);"><?php echo($i); ?></a> <?php
	} // end if
} // end for
	?></td>

<td style="text-align: right;"><img height="12" width="12"
	<?php if ($page < $lastpage) { ?>
	src="../imagenes/nav-right.png" title="Pgina siguiente"
	style="cursor: pointer;" onclick="go_page(<?php echo($page + 1); ?>);"
	<?php } else { ?>
	src="../imagenes/1x1.png"
	<?php } // end if page 1 ?> />

	<img height="12" width="15"
	<?php if ($page < $lastpage) { ?>
	src="../imagenes/nav-right2.png" title=" ltima pgina""
	style="cursor: pointer;" onclick="go_page(<?php echo($lastpage); ?>);"
	<?php } else { ?>
	src="../imagenes/1x1.png"
	<?php } // end if totalpages ?> /></td>

</tr></table>
<?php
} // end navigationbar
/////////////////////////////////////////////////////
