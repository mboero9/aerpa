<?php
require_once("../includes/lib.php");
require_once("../phpprintipp/PrintIPP.php");

function printers_full() {
	global $conn;

	$a = array();
	$sql = "Select IMP_NOMBRE,IMP_HOST,IMP_PORT,IMP_PATH
		From IMPRESORA
		Order by IMP_DEFAULT desc,IMP_NOMBRE";
	$rs = $conn->Execute($sql);
	while (!$rs->EOF) {
		$a[$rs->fields["IMP_NOMBRE"]] = array("host" => $rs->fields["IMP_HOST"],
			"port" => $rs->fields["IMP_PORT"], "uri" => $rs->fields["IMP_PATH"]);
		$rs->MoveNext();
	} // end while !eof

	return $a;
} // end available_printers

function printers() {
	return array_keys(printers_full());
} // end printers

function print_text($printer, $text) {

	$prns = printers_full();
	$prn = $prns[$printer];
	$ipp = new PrintIPP();
	$ipp->debug_level=1;
	$ipp->setHost($prn["host"]);
	$ipp->setPort($prn["port"]);
	$ipp->setPrinterURI($prn["uri"]);
	$ipp->setData($text);
	$ipp->printJob();
/*	echo sprintf(_("Job status: %s"),$ipp->printJob());
	$ipp->getDebug();
	$ipp->printDebug();	*/
	return;
} // end print_text
?>