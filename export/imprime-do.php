<?php
set_time_limit(0);
require_once("../includes/lib.php");
require_once("../report/report.php");

$sql = stripslashes($_POST["sql"]);
$xml = $_POST["xml"];
$printer = $_POST["printer"];
$papersize = $_POST["papersize"];
$copies = $_POST["copies"];
$propiedadesreport = $_POST["propiedadesreport"];
$returnto = $_POST["returnto"];

$rs = $conn->Execute($sql);
$rpt =& new Report();
$rpt->Recordset = $rs;
$rpt->LoadFromXML($xml);
$rpt->PageSize = $papersize;
if ($_POST["propiedadesreport"] != "") {
	$props = explode("\n", stripslashes($_POST["propiedadesreport"]));
	foreach($props as $prop) {
		$prp = explode("|", $prop);
		// el trabalenguas que sigue termina interpretando algo como:
		// $rpt->ReportHeader->objects[3]->text = "pirulo"
		// Sirve para fijar valores en secciones del report
		// donde todavia no se hizo un fetch
		$j = &$rpt;
		for ($i = 0; $i < count($prp) - 2; $i++) {
			$j = &$j->$prp[$i];
		}
		$j->objects[$prp[$i]]->Text = $prp[$i+1];
	} // end foreach prop
} // end if propiedadesreport
$rpt->Generate();
$psfile = $rpt->SendToFile();
print_text($printer, $psfile);
unlink($psfile);

header("location: $returnto");
?>