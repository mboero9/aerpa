<?
require_once("../includes/lib.php");
if (check_auth($usrid, $perid) && check_permission($permiso)) {
?>
<HTML>
<HEAD>
<? require_once("../includes/inc_header.php"); ?>
<script type="text/javascript">
var newwindow=null;
function validar()
{
	var ok = true;
	var errores="";
	var nro_valido = new RegExp("^[0-9]*$","i");
	if(document.Voucher.nro_voucher.value=="")
	{
		errores +="--No ha ingresado el Nro de Voucher --";
		ok=false;
	}
	if(!nro_valido.test(document.Voucher.nro_voucher.value))
	{
		errores +="--Ha ingresado caracteres incorrectos--"
		ok=false;
	}
	if(!ok)
	{
		alert(errores);
	}
	
	return ok;
	
}
</script>
</HEAD>
<BODY onLoad="document.Voucher.nro_voucher.focus();" onFocus="if(newwindow){newwindow.focus();}">
<? require_once("../includes/inc_topleft.php"); 
/* Contenido */
$opcion=$_POST['opcion'];
$pagina="Voucher.php";
require_once('../includes/inc_titulo.php');
?>
<form name="Voucher" id="Voucher" action="Voucher.php" method="post" onSubmit="return validar();">
<table align="center" width="40%" class="tablaconbordes" <? if(!empty($_POST['nro_voucher'])){?> style="display:none" <? }?>>
<tr>
<th align="center" class="celdatitulo" colspan="3">Ingrese el n�mero de Voucher a Imprimir:</th>
</tr>
<tr>
<td>&nbsp;</td><td align="center"><input type="text" name="nro_voucher" id="nro_voucher" size="10" maxlength="8"></td><td>&nbsp;</td>
</tr>
<tr>
<td>&nbsp;</td><td align="center"><input type="submit" class="botonout" value=<?=CONFIRMO ?> name="Confirmar" onMouseOver="this.className = 'botonover';" onMouseOut="this.className = 'botonout';"></td><td>&nbsp;</td>
</tr>
</table>
</form>
<?
if(!empty($_POST['nro_voucher']))
{
	try
	{
/*		$Sql="SELECT     T.TRA_NRO_VOUCHER, T.TRA_DOMINIO,"
		.$conn->SQLDate(FMT_DATE_DB, 'T.TRA_FECHA_RETIRO')." AS TRA_FECHA_RETIRO,"
		.$conn->SQLDate(FMT_DATE_DB,'T.TRA_FECHA_ENTREGA')." AS TRA_FECHA_ENTREGA, R.REG_COD_INT AS N_ORIGEN, X.REG_COD_INT AS N_DESTINO, R.REG_CPA AS CP_ORIGEN, X.REG_CPA AS CP_DESTINO, RE.REM_NUMERO, T.TRA_NRO_IMP 
		FROM         dbo.TRAMITE T INNER JOIN
                      dbo.REG_AUTOM R ON T.REG_CODIGO_ORI = R.REG_CODIGO INNER JOIN
                      dbo.REG_AUTOM X ON T.REG_CODIGO_DES = X.REG_CODIGO 
					  INNER JOIN REMITO RE ON T.REM_ID_ORI = RE.REM_ID
			WHERE     (T.TRA_NRO_VOUCHER = ".sqlint($_POST['nro_voucher']).") and T.TRA_FECHA_ENTREGA is not null and REM_ID_ORI is not null";			*/
		$Sql="SELECT     T.TRA_NRO_VOUCHER, T.TRA_DOMINIO,"
		.$conn->SQLDate(FMT_DATE_DB, 'T.TRA_FECHA_RETIRO')." AS TRA_FECHA_RETIRO,"
		.$conn->SQLDate(FMT_DATE_DB,'T.TRA_FECHA_ENTREGA')." AS TRA_FECHA_ENTREGA, R.REG_COD_INT AS N_ORIGEN, X.REG_COD_INT AS N_DESTINO, R.REG_CPA AS CP_ORIGEN, X.REG_CPA AS CP_DESTINO, T.TRA_NRO_IMP, T.REM_ID_DES, RE.REM_NUMERO   
		FROM         TRAMITE T INNER JOIN
                      REG_AUTOM R ON T.REG_CODIGO_ORI = R.REG_CODIGO INNER JOIN
                      REG_AUTOM X ON T.REG_CODIGO_DES = X.REG_CODIGO
					  INNER JOIN REMITO RE ON T.REM_ID_DES = RE.REM_ID 
			WHERE    (T.TRA_NRO_VOUCHER = ".sqlint($_POST['nro_voucher']).") and REM_ID_DES is not null";			
		
		//echo($Sql);
		$rs=$conn->execute($Sql);
		$mensaje="";
		if(!$rs->EOF)
		{
				
			$nroimpr= $rs->fields['TRA_NRO_IMP'] + 1;
			
			?>
			<form name="descarga" action="../export/csv.php" method="post">
			<table align="center" width="70%" class="tablaconbordes">
			<tr>
			<th class="celdatitulo" align="center" colspan="3">Se imprimira Nro. de Voucher: </th>
			</tr>
			<tr>
			<td>&nbsp;</td><td align="center" class="celdatexto"><?=$rs->fields['TRA_NRO_VOUCHER'];?></td><td>&nbsp;</td>
			</tr>
			<tr>
			<td align="center" colspan="3"><input type=button class="botonout" name=botvolver     value="<?=VOLVER; ?>"  onMouseOver="this.className = 'botonover';" onMouseOut="this.className = 'botonout';" onClick="goMenu('Voucher.php');">
			<input type=button class="botonout" name=botconfirma   value="<?=IMPRIMIR;?>" onMouseOver="this.className = 'botonover';" onMouseOut="this.className = 'botonout';"  onClick="newwindow=window.open(href='../export/imprime_ps3.php', this.target, 'width=250,height=140,left=260,top=230,resizable=yes');"></td>
			</tr>
			</table>
			<input type="hidden" name="nro_impr" id="nro_impresion" value="<?=$nroimpr;?>">
			<input type=hidden name="sql" value="<? echo($Sql); ?>">
			<input type=hidden name="archivo2" value="../reportes/Voucher.xml">
			<input type=hidden name="propiedadesreport" value="<?php echo($propiedadesreport); ?>">
			<input type="hidden" name="nro_voucher" value="<?=$rs->fields['TRA_NRO_VOUCHER'];?>">
			</form>
			<?	
			
		}//cierro if!$rs->EOF
		else
		{
		    $mensaje='El Nro de Voucher no se encuentra disponible para impresi�n.';			
		}//Cierro else
		if ($mensaje!="") {				
		?>
			<table align="center" width="40%" class="tablaconbordes">
			<tr>
			<td align="center" class="textoerror"><?=$mensaje;?></td>
			</tr>
			<tr>
			<td align="center"><input type=button class="botonout" name=botvolver     value="<?=VOLVER; ?>"  onMouseOver="this.className = 'botonover';" onMouseOut="this.className = 'botonout';" onClick="goMenu('Voucher.php');"></td>
			</tr>
			</table>
		<?
		}//Cierro if($mensaje!="")
	}//Cierro try
	catch(exception $e)
	{
		?>
		<table align="center" width="40%" class="tablaconbordes">
		<tr>
		<td align="center" class="textoerror">Error al realizar la operaci�n.</td>
		</tr>
		</table>
		<?
	}
}//Cierro if !empty
?>
</BODY>
</HTML>
<?
}//Cierro if de autorizacion
?>