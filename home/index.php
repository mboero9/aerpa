<?php
require_once("../includes/lib.php");

if (check_auth($usrid, $perid) && check_permission($permiso)) {
// permiso ok
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>

<head>
<?php require_once("../includes/inc_header.php"); ?>
</head>

<body>
<?php require_once('../includes/inc_topleft.php'); ?>
<table cellpadding="10" cellspacing="0" align="center" width="80%" height="350">
<tr><td align="center" valign="bottom"></td></tr>
<tr><td align="center"></td></tr>
</table>

<?php require_once("../includes/inc_bottom.php"); ?>
</body>

</html>
<?php
} // fin autorizacion
?>