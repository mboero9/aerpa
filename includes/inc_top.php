<?php
if (is_numeric($usrid)) {
	try {
	$sql =
		"Select U.USR_USERNAME,U.USR_NOMBRE,U.USR_APELLIDO,P.PER_DESCRIPCION," .
		$conn->SQLDate(FMT_DATETIME_DB, "S.SES_FECHA") . " as SES_FECHA " .
		"From USUARIO U " .
		"Inner Join PERFIL P on U.PER_ID = P.PER_ID " .
		"Inner Join SEGSESION S on U.USR_ID = S.USR_ID " .
		"Where S.SES_RAND = " . $_COOKIE[SES_COOKIE];
	$rs = $conn->Execute($sql);
	if (!$rs->EOF) {
		$usrusername = $rs->fields["USR_USERNAME"];
		$usrnombre = $rs->fields["USR_NOMBRE"];
		$usrapellido = $rs->fields["USR_APELLIDO"];
		$perdescripcion = $rs->fields["PER_DESCRIPCION"];
		$sesfecha = $rs->fields["SES_FECHA"];
	} // end if eof
	} catch (exception $e) {
		dbhandleerror($e);
	}
} // end if is_numeric usrid
?>

<table border=0 width=100% cellpadding=0 cellspacing=0>
<tr class=fondotop>
<td style="height: 64px" align="center" colspan=2>
    <table class="azul">
        <tr><td></td></tr>
    </table>
	<table border=0 width=100% cellpadding=2 cellspacing=0 class="topbottom">

	<tr>
		<td width=35% align=left class=menuback><img src="../imagenes/logo_sup2.png" border=0></td>
		<td width=40% align=center valign=middle class="menuback tituloprincipal">
		AAERPA
		</td>
		<td width=25% align=right class="menuback textochico">
		<font color=#15539b>
<?php
if (is_numeric($usrid)) {
	echo(htmlentities($usrapellido . ", " . $usrnombre)); ?>
	&nbsp;(<?php echo(htmlentities($usrusername)); ?>)&nbsp;&nbsp;<br>
	<i>Inicio de sesi&oacute;n:</i> <?php echo($sesfecha); ?>&nbsp;&nbsp;<br>
<?php
} // end if usrid
?>
		</font>
		</td>
	</tr>
	<tr class=fondotopbar>
		<td width=35% nowrap style="height: 18px" >&nbsp;</td>
		<td width=40% align=center style="height: 18px">
		<table align=center border=0 style="height: 18px" cellspacing=0 cellpadding=0 >
			<tr>
				<td><a title="Vuelve a la p&aacute;gina principal" href="../home/" class=linktop>INICIO</a></td>
					<td width=30 align=center class=linktop>|</td>
					<td><a title="Cierra la sesi&oacute;n y vuelve a la p&aacute;gina de Ingreso" href="../seguridad/logout-do.php" class=linktop>CERRAR SESION</a></td>
			</tr>
		</table>
		</td>
		<td width=25%>&nbsp;
		</td>
	</tr>
	</table>
    </td>
    </tr>

</table>