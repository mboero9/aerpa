<?php
require_once("../adodb/adodb-exceptions.inc.php");
require_once("../adodb/adodb.inc.php");

// Crear conexion a base de datos
$conn = ADONewConnection("odbc_mssql");
$dsn = "DSN=AERPA-desarrollo;Database=AERPA;UID=Usr_Web_Desa;PWD=632541";
//$dsn = "DSN=AERPA-produccion;Database=Aerpa;UID=Usr_Web_Prod;PWD=632541";
$conn->Connect($dsn);
$conn->SetFetchMode(ADODB_FETCH_BOTH);

// Mostrar los queries por pantalla o no
$conn->debug = false;

// Mostrar los errores por pantalla o no
$desarrollo = true;

// Array para acumular las excepciones que se pudieran producir en BD
$err = array();

// Respetar sistema de seguridad o no
$ignorarpermisos = true;
?>