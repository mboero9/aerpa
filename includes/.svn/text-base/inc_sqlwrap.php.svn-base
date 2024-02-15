<?php
/*************************************************\
*** Funciones para envio de info a base de datos **
***                                              **
*** Diego Gutierrez (diego@minter.com.ar)        **
\*************************************************/
require_once('inc_date.php');

function sqlstring($dato)
{
	$dato = stripslashes($dato);
	return "'" . str_replace("'", "''", $dato) . "'";
}

function sqldate($dato)
{
	global $conn;
	$dato = stripslashes($dato);
	if (!is_numeric($dato) && ($dato = strtotime($dato)) === -1) {
		return 'NULL';
	} else {
		return $conn->DBTimeStamp($dato);
	}
}

function sqlfloat($dato)
{
	if (!is_numeric($dato)) {
		return 'NULL';
	} else {
		return sprintf('%f', $dato);
	}
}

function sqlint($dato)
{
	if (!is_numeric($dato)) {
		return 'NULL';
	} else {
		return $dato;
	}
}

function sqlnum($dato)
{
	if (!is_numeric($dato)) {
		return 'NULL';
	} else {
		return $dato;
	}
}

function sqlboolean($dato) {
	if (is_bool($dato) && ($dato)) {
		return 1;
	} else {
		return 0;
	}
}
?>