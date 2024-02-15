<?
header('Content-type: text/html; charset=ISO-8859-1');
require_once("../includes/lib.php");

/**
 * Recupero el codigo postal de la familia.
 */
if (!empty($_GET['familia'])) {
	$Sql="Select REG_DESCRIP, REG_TIPO, REG_CPA,reg_familia,reg_cod_int
					    From REG_AUTOM
					   where REG_FAMILIA=".sqlstring($_GET['familia'])." and reg_familia = reg_cod_int";		  
	$rs = $conn->execute($Sql);
	if (!$rs->EOF) {
		echo substr(utf8_encode($rs->fields["REG_DESCRIP"]),0,15)."|".
		$rs->fields["REG_TIPO"]."|".$rs->fields["REG_CPA"];
	}else{
		echo "Inexistente";
	}
}

?>