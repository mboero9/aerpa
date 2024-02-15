<?php
set_time_limit(0);

require_once("../includes/lib.php");

// enviar header
header("content-type: text/csv, charset=ISO-8859-1");
header("content-disposition: attachment; filename= " . $_POST["archivo"]);

$fm = $conn->fetchMode;
$conn->SetFetchMode(ADODB_FETCH_NUM);

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

// recorrer el array, ir agregando al archivo
for ($i = 0; $i < count($sqlarr); $i++) {
	$sql = $sqlarr[$i];

	// ejecutar el query
	$rs = $conn->Execute(stripslashes($sql));

	// parsear titulos si estan presentes
	$fldtit[$i] = array();
	$fldshow[$i] = array();
	$fldtot[$i] = array();
	$fldtotsuma[$i] = array();
	$fldtotcuenta[$i] = array();
	if (!isset($titarr)) {
		// no se especifican titulos de columna, mostrar todo con los nombres de campo del query,
		// sin totalizadores
		for ($j = 0; $j < $rs->FieldCount(); $j++) {
			$fld = $rs->FetchField($j);
			$fldtit[$i][] = '"' . str_replace('"', '""', $fld->name) . '"';
			$fldshow[$i][] = $j;
		} // end for j
	} else {
		// titulos especificados, extraerlos del array
		$fldtit[$i] = explode("|", $titarr[$i]);
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
	echo(($i == 0 ? "" : "\n\n") . $linea . "\n");

	// recorrer el resultado
	while (!$rs->EOF) {
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

		$rs->MoveNext();
	} // end while !eof

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
} // end for i in sql

$conn->SetFetchMode($fm);
?>
