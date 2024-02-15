<?php
$debug = true;
require_once("../includes/lib.php");
/*if (check_auth($usrid, $perid) && check_permission($permiso)) {*/
/*NUEVO*/
/* Anulacion de Remito por Origen/Destino */
	if (isset($_GET["ajax"])) {
		if($debug) print "Entro ajax\n";
		try {

				if ($_GET["ajax"]=='detalle') {
// se toman todos los tramites que no tienen un remito vinculado
				($_GET["par_registro"]=='origen' ? 					
				 $cond_tramite='rem_id_ori'				
				 : 
				 $cond_tramite='rem_id_des');
				 ($_GET["par_registro"]=='origen'?$condicion=' where b.REM_FECHA_CIERRE is null':$condicion=' where a.TRA_FECHA_ENTREGA is null ');
				$sql = "Select a.TRA_DOMINIO, a.TRA_NRO_VOUCHER ".
						" From TRAMITE a ".
						" Inner Join REMITO b on b.rem_id = a.".$cond_tramite." and b.rem_numero=".sqlint($_GET["nro_rem"]).
						$condicion.
						" Order by 1";
				$rs = $conn->Execute($sql);
				$out = "tramites";
				if ($rs->EOF) {
					$out .= "\n";
					
				} else {
					while ($a = $rs->FetchRow()) {
						$out .= "\n" . implode("|", $a);
					} // end fetch
				} // end if eof
				}else{ //consulta del estado del remito
				$sql = "Select rem_estado".
						" From remito ".
						" where rem_tipo= ".sqlstring($_GET["par_registro"])." and rem_numero between ".sqlint($_GET["desde"]).
						" and ".sqlint($_GET['hasta']);
				//echo($sql.'<br>');
				if($debug) print $sql."\n";
				$rs = $conn->Execute($sql);

				$out = "remito";
				if ($rs->EOF) {
					$out .= "\n";
				} else {
					while ($a = $rs->FetchRow()) {
						$out .= "\n" . implode("|", $a);
					} // end fetch
				} // end if eof
				}
//			header("content-type: text/plain; charset=iso-8859-1");
			echo($out);
			return;
		} catch (exception $e) {
			dbhandleerror($e);
		}
	} // end if AJAX
/*}//Cierro if de seguridad*/
?>