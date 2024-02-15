<?php
require_once("../includes/lib.php");

if (check_auth($usrid, $perid) && check_permission($permiso, "remitoReg.php")) {
// permiso ok

$conn->StartTrans();
try {
// Datos posteados
$dt_desde = new Date($_POST["desde"]);
$v_desde = $dt_desde->format(FMT_DATE_ISO);
$v_desde_rpt = $dt_desde->format(FMT_DATE_DB);

$dt_hasta = new Date($_POST["hasta"] . " 23:59:59");
$v_hasta = $dt_hasta->format(FMT_DATE_ISO);
$v_hasta_rpt = $dt_hasta->format(FMT_DATE_DB);

$v_region = $_POST["region"];
$v_region_desc = $_POST["region_desc"];

// Marcar tramites asociados al remito
$tram = array();
$sql = "Update TRAMITE
	Set TRA_FECHA_REMITO = " . sqldate(dbdate()) . "
	Where REG_CODIGO_DES In (
		Select REG_CODIGO From REG_AUTOM
		Where RGI_CODIGO = " . sqlstring($v_region) . ")
	And TRA_FECHA_CARGA >= " . sqldate($v_desde) . "
	And TRA_FECHA_CARGA <= " . sqldate($v_hasta);
$conn->Execute($sql);

} catch (exception $e) {
	dbhandleerror($e);
}
$conn->CompleteTrans();

if (!dberror()) {
	$sql = "Select r.REG_COD_INT,r.REG_DESCRIP,t.TRA_DOMINIO
		From TRAMITE t
		Inner Join REG_AUTOM r On t.REG_CODIGO_DES = r.REG_CODIGO
		Where r.RGI_CODIGO =  " . sqlstring($v_region) . "
		And t.TRA_FECHA_CARGA >= " . sqldate($v_desde) . "
		And t.TRA_FECHA_CARGA <= " . sqldate($v_hasta) . "
		Order by r.REG_COD_INT,r.REG_DESCRIP,t.TRA_DOMINIO";
	$propiedadesreport = "MainGroup|GroupHeader|1|$v_region\n" .
		"MainGroup|GroupHeader|2|$v_region_desc\n" .
		"MainGroup|GroupHeader|4|$v_desde_rpt\n" .
		"MainGroup|GroupHeader|6|$v_hasta_rpt";
?>
<html>
<head>
<title>Remito por regi&oacute;n</title>
</head>
<body onload="document.frm.submit();">
<form action="../export/imprime.php" method="post" name="frm">
<input type="hidden" name="sql" value="<?php echo($sql); ?>">
<input type="hidden" name="xml" value="../tramites/remitoReg.xml">
<input type="hidden" name="returnto" value="../tramites/remitoReg.php">
<input type="hidden" name="propiedadesreport" value="<?php echo($propiedadesreport); ?>">
</form>
</body>
</html>
<?php
} // end if dberror

} // fin autorizacion
?>