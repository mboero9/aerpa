<?php
//-----------
// Constants
//-----------
// alignment
define("RPT_ALIGN_LEFT",0);
define("RPT_ALIGN_RIGHT",1);
define("RPT_ALIGN_CENTER",2);

// font family
define("RPT_HELVETICA",0);
define("RPT_TIMES",1);
define("RPT_COURIER",2);

// font style
define("RPT_NORMAL",0);
define("RPT_BOLD",1);
define("RPT_ITALIC",2);
define("RPT_BOLDITALIC", RPT_BOLD + RPT_ITALIC);

// system assigned values
define("RPT_SYSPAGENO",0);
define("RPT_SYSDATE",1);

// page sizes
define("RPT_A4",0);
define("RPT_LETTER",1);
define("RPT_LEGAL",2);

// page orientation
define("RPT_PORTRAIT",0);
define("RPT_LANDSCAPE",1);

// calculated fields operations
define("RPT_COUNT",0);
define("RPT_MIN",1);
define("RPT_MAX",2);
define("RPT_AVG",3);
define("RPT_SUM",4);
?>