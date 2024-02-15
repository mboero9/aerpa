<?
require_once("../includes/lib.php");
if (!empty($_GET['tipo'])) {
	switch($_GET['tipo']) {
	case 'papel':
		if (!empty($_GET['impresora'])) {
				$Sql="Select IMP_PAPEL
					    From IMPRESORA
					   where IMP_NOMBRE=".sqlstring($_GET['impresora']);		  
				$rs = $conn->execute($Sql);
				 if (!$rs->EOF) {
				  	echo $rs->fields["IMP_PAPEL"];
				 }else{ 
				  	echo "Inexistente";
				 }
		}//if vino parametro eleccion
		break;
	}//switch		
}//if (!empty($tipo))	

?>
