<?
require_once("../includes/lib.php");
if (!empty($_GET['tipo'])) {
	switch($_GET['tipo']) {
	case 'desexistente': 
		if (!empty($_GET['desc'])) {
			$Sql="Select count(*) as cant		   	     
				  From PARAMETRO  		  
				  where PAR_NOMBRE=".sqlstring($_GET['desc']);
			$rs = $conn->execute($Sql);
			if ($rs->fields["cant"]>0) {
			   echo "existe";
			}//if
		}//if vino parametro 
		break;
	}//switch		
}//if (!empty($tipo))	

?>
