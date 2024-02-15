<?php
require_once("../includes/lib.php");

if (check_auth($usrid, $perid) && check_permission($permiso, "registros.php")) {
// permiso ok
	
	$conn->StartTrans();
	try {
		$accion 	 = $_POST["accion"];
		$v_id        = $_POST["id"];
		$v_nombre    = trim($_POST["nombre"]);
		$v_calle     = trim($_POST["calle"]);
		$v_altura    = trim($_POST["altura"]);
		$v_piso      = trim($_POST["piso"]);
		$v_cpa       = trim($_POST["cpa"]);
		$v_localidad = trim($_POST["localidad"]);
		$v_provincia = trim($_POST["provincia"]);
		$v_region    = trim($_POST["region"]);
		$v_numero    = trim($_POST["numero"]);
		$v_tipo      = trim($_POST["tipo"]);
		$v_familia   = trim($_POST["familia"]);
		
		if ($accion == ABM_NEW) {
			$sql = "Insert Into REG_AUTOM (" .
				"	REG_CODIGO,REG_DESCRIP,REG_CALLE,REG_NUMERO,REG_PISO," .
				"	REG_CPA,REG_LOCALIDAD,PRO_CODIGO,RGI_CODIGO," .
				"	REG_COD_INT,REG_TIPO,USRID,REG_FECHA_ACT,REG_FAMILIA) " .
				"Values (" . sqlint(numerador("REG_AUTOM")) . "," .
				sqlstring($v_nombre) . "," .
				sqlstring($v_calle) . "," .
				sqlstring($v_altura) . "," .
				sqlstring($v_piso) . "," .
				sqlstring($v_cpa) . "," .
				sqlstring($v_localidad) . "," .
				sqlstring($v_provincia) . "," .
				sqlstring($v_region) . "," .
				sqlstring($v_numero) . "," .
				sqlstring($v_tipo) . "," .
				sqlint($usrid) . "," .
				sqldate(dbtime()) . "," .
				sqlstring($v_familia) . ")";
			$conn->Execute($sql);
		
		} elseif ($accion == ABM_EDIT) {
			$sql = "Update REG_AUTOM " .
				"Set REG_DESCRIP = " . sqlstring($v_nombre) . "," .
				"	REG_CALLE = " . sqlstring($v_calle) . "," .
				"	REG_NUMERO = " . sqlstring($v_altura) . "," .
				"	REG_PISO = " . sqlstring($v_piso) . "," .
				"	REG_CPA = " . sqlstring($v_cpa) . "," .
				"	REG_LOCALIDAD = " . sqlstring($v_localidad) . "," .
				"	PRO_CODIGO = " . sqlstring($v_provincia) . "," .
				"	RGI_CODIGO = " . sqlstring($v_region) . "," .
				"	REG_COD_INT = " . sqlstring($v_numero) . "," .
				"	REG_TIPO = " . sqlstring($v_tipo) . "," .
				"	USRID = " . sqlint($usrid) . "," .
				"	REG_FECHA_ACT = " . sqldate(dbtime()) . "," .
				"	REG_FAMILIA = " . sqlstring($v_familia) . " " .
				"Where REG_CODIGO = " . sqlint($v_id);
			$conn->Execute($sql);
		} elseif ($accion == ABM_REHABILITAR) {
			$sql = "Update REG_AUTOM " .
				"Set REG_FECHA_BAJA = NULL," .
				"	USRID = " . sqlint($usrid) .		
				"Where REG_CODIGO = " . sqlint($v_id);
			$conn->Execute($sql);	
		} else {
			// Solo baja logica
			$sql = "Update REG_AUTOM " .
				"Set USRID = " . sqlint($usrid) . "," .
				"	REG_FECHA_BAJA = " . sqldate(dbtime()) . " " .
				"Where REG_CODIGO = " . sqlint($v_id);
			$conn->Execute($sql);
		
		
		} // end if ABM_NEW/EDIT/DEL
		// volver
		header("location: registros.php");
	} catch (exception $e) {
		dbhandleerror($e);
	}
	$conn->CompleteTrans();
	
} // fin autorizacion
?>