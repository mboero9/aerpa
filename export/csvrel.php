<?php
set_time_limit(0);

require_once("../includes/lib.php");

$fm = $conn->fetchMode;
$conn->SetFetchMode(ADODB_FETCH_NUM);

// funciones de exportacion
function csvtitles($rs, $tit, $index) {
	global $fldtit;
	global $fldshow;
	global $fldtot;
	global $fldtotsuma;
	global $fldtotcuenta;
	$i = $index;

	$fldtit[$i] = array();
	$fldshow[$i] = array();
	$fldtot[$i] = array();
	$fldtotsuma[$i] = array();
	$fldtotcuenta[$i] = array();
	if ($tit == "") {
		// no se especifican titulos de columna, mostrar todo con los nombres de campo del query,
		// sin totalizadores
		for ($j = 0; $j < $rs->FieldCount(); $j++) {
			$fld = $rs->FetchField($j);
			$fldtit[$i][] = '"' . str_replace('"', '""', $fld->name) . '"';
			$fldshow[$i][] = $j;
		} // end for j
	} else {
		// titulos especificados, extraerlos del array
		$fldtit[$i] = explode("|", $tit);
		for ($j = 0; $j < count($fldtit[$i]); $j++) {
			// separar los elementos nombrecol[!posicion[!totalizado]]
			$tmp = explode("!", $fldtit[$i][$j]);
			// guardar el titulo limpio de codigos
			$fldtit[$i][$j] = $tmp[0];
			// guardar el nro de columna a mostrar
			if (count($tmp) > 1) {
				$fldshow[$i][] = (int) $tmp[1];
			} else {
				$fldshow[$i][] = $j;
			} // end if count > 0
			// guardar si se totaliza, y de que forma
			if (count($tmp) > 2) {
				$fldtot[$i][$j] = $tmp[2];
				$fldtotsuma[$i][$j] = $fldtotcuenta[$i][$j] = 0;
			} // end if count > 0
		} // end for j
	} // end if titulos

	// titulos de columna
	$linea = "";
	for($j = 0; $j < count($fldtit[$i]); $j++) {
		$linea .= ($j == 0 ? '' : ',') . $fldtit[$i][$j];
	} // end foreach fieldname
	echo($linea . "\n");
} // end csvtitles

function csvline($rs, $index) {
	global $fldshow;
	global $fldtot;
	global $fldtotsuma;
	global $fldtotcuenta;
	$i = $index;

	$linea = "";
	for($j = 0; $j < count($fldshow[$i]); $j++) {
		$linea .= ($j == 0 ? '' : ',') .
			'"' . str_replace('"', '""', $rs->fields[$fldshow[$i][$j]]) . '"';
		// totalizar si corresponde
		if (isset($fldtot[$i][$j])) {
			$fldtotsuma[$i][$j] += (is_numeric($rs->fields[$fldshow[$i][$j]]) ?
				$rs->fields[$fldshow[$i][$j]] : 0);
			$fldtotcuenta[$i][$j]++;
		} // end if totalizar
	} // end foreach fieldname
	echo($linea . "\n");
} // end csvline

function csvtotal ($index) {
	global $fldshow;
	global $fldtot;
	global $fldtotsuma;
	global $fldtotcuenta;
	$i = $index;

	// totales (si corresponden)
	if (count($fldtot[$i]) > 0) {
		$linea = "Totales\n";
		for ($j = 0; $j < count($fldshow[$i]); $j++) {
			if (strtolower($fldtot[$i][$j]) == strtolower(CSV_EXP_SUMA)) {
				$tmp = $fldtotsuma[$i][$j];
			} else if (strtolower($fldtot[$i][$j]) == strtolower(CSV_EXP_CUENTA)) {
				$tmp = $fldtotcuenta[$i][$j];
			} else if (strtolower($fldtot[$i][$j]) == strtolower(CSV_EXP_PROMEDIO)) {
				if ($fldtotcuenta[$i][$j] > 0) {
					$tmp = sprintf("%1.2f", $fldtotsuma[$i][$j] / $fldtotcuenta[$i][$j]);
				} else {
					$tmp = 0;
				} // end if cuenta = 0
			} else {
				$tmp = "";
			} // end if tipo total
			$linea .= ($j == 0 ? '' : ',') . '"' . $tmp . '"';
		} // end recorrida totales
		echo($linea . "\n");
	} // end if haytotales
} // end csvtotal


// comienzo de ejecucion
$fldtit = array();
$fldshow = array();
$fldtot = array();
$fldtotsuma = array();
$fldtotcuenta = array();

// enviar header
header("content-type: text/csv; charset=ISO-8859-1");
header("content-disposition: attachment; filename= " . $_POST["archivo"]);

// anteponer titulos si fueron pasados por parametro
if (is_array($_POST["titulo"])) {
	foreach($_POST["titulo"] as $titulo) {
		echo($titulo . "\n");
	} // end foreach titulo
	echo("\n");
} // end if array titulo

// armar un array con todas las consultas a devolver
if (is_array($_POST["sql"])) {
	$sqlarr = $_POST["sql"];
} else {
	$sqlarr = array($_POST["sql"]);
} // end if isarray sql

// idem para los titulos de columna
if (isset($_POST["titulosql"])) {
	if (is_array($_POST["titulosql"])) {
		$titarr = $_POST["titulosql"];
	} else {
		$titarr = array($_POST["titulosql"]);
	} // end if isarray titulosql
} // end if titulosql


$rs = $conn->Execute(stripslashes($sql[0]));
$mostrado_sub = 0;
// recorrer el resultado
while (!$rs->EOF) {
	if ($mostrado_sub) {
		csvtitles($rs, $titarr[0], 0);	// imprimir / repetir titulos de consulta principal
		$mostrado_sub = false;
	}

	csvline($rs, 0); // imprimir fila de datos de consulta principal

	// determinar el valor del campo de la consulta exterior que relaciona los queries
	if ($_POST["campotipo"] == CSV_REL_FLD_INT) {
		$fieldval = sqlint($rs->field($_POST["camporel"]));
	} elseif ($_POST["campotipo"] == CSV_REL_FLD_STRING) {
		$fieldval = sqlstring($rs->field($_POST["camporel"]));
	} elseif ($_POST["campotipo"] == CSV_REL_FLD_DATE) {
		$fieldval = sqldate($rs->field($_POST["camporel"]));
	} // end tipo

	// reemplazar el placeholder en la consulta interna por el valor correspondiente
	$sql2 = str_replace(CSV_REL_FIELD, $fieldval, $sql[1]);

	// ejecutar, si !eof mostrar titulos y contenido
	$rs2 = $conn->Execute($sql2);
	if (!$rs2->EOF) {
		csvtitles($rs2, $titarr[1], 1);

		while (!$rs2->EOF) {
			csvline($rs2, 1);
			$rs2->MoveNext();
		} // end while !rs2.eof

		csvtotal(1);

		$mostrado_sub = true;
	} // end if !rs2.eof

	$rs->MoveNext();
} // end while !eof

csvtotal(0);

$conn->SetFetchMode($fm);
?>
