<div id=divFormDatos style="display:none">
<form name="form" action="" onsubmit="return false;">
<!--<form name="form" onsubmit="return (validaFormulario());">-->
<table align="center" width="70%" class=tablaconbordes>
<tr><td class="celdatitulo" align="center">Desea dar de baja el siguiente tramite?</td></tr>
<tr><td>
<table align="center" width="420">
	<tr><td class="celdatexto" width="120" align="right">Registro Origen:</td>
	    <td width="90"><input type=text class=textochico name=CodRegOrig size=5 maxlength="5" onChange="document.form.modificado.value=1; ConsAjax('desregistro','O');"></td>
		<td width="210"><span id=spanDesRegOrig class="celdatexto"></span></td>
	</tr>
	<tr><td class="celdatexto" align="right">Registro Destino:</td>
	    <td><input type=text class=textochico name=CodRegDest size=5 maxlength="5" onChange="document.form.modificado.value=1; ConsAjax('desregistro','D');"></td>
		<td><span id=spanDesRegDest class="celdatexto"></span></td>
	</tr>
	<tr><td class="celdatexto" align="right">N&uacute;mero de Tr&aacute;mite:</td>
	    <td><input type=text class=textochico name=NroTramite size=10 maxlength="8" style="text-transform:uppercase" onchange="document.form.modificado.value=1;"></td>
	</tr>		
	<tr><td class="celdatexto" align="right">N&uacute;mero de Voucher:</td>
	    <td><input type=text class=textochico name=NroVoucher size=10 maxlength="8" onchange="document.form.modificado.value=1;"></td>
	</tr>		
	<tr><td class="celdatexto" align="right">Fecha de Retiro:</td>
		<td valign="middle"><input type="text" class=textochico size=8 maxlength="10" name="FecRetiro" id="FecRetiro" value="<?=date('d/m/Y');?>" style="text-align:right;" onchange="document.form.modificado.value=1;">		
		</td>				
	</tr>	
</table>
<div id=subformMod style="display:none">
<table align="center" width="420">
	<tr><td class="celdatexto" width="120" align="right" id=fecentdev>Fecha de Entrega:</td>
		<td width="90"><input type="text" class=textochico size=8 maxlength="10" name="FecEntrega" id="FecEntrega" value="" style="text-align:right;" onchange="document.form.modificado.value=1;" disabled>
		</td>				
		<td width="210"></td>
	</tr>	
	
	  
		<input type=hidden name=idregmod />	
	
	<tr><td class="celdatexto" align="right">Fecha de Cierre:</td>
		<td><input type="text" class=textochico size=8 maxlength="10" name="FecCierre" id="FecCierre" value="" style="text-align:right;" disabled></td>
	</tr>
</table>	
</div>
<input type=hidden name=fecHoy value="<?=date('Ymd');?>"/>
<input type=hidden name=usuario value="<?=$usrid;?>"/>
<input type=hidden name=modificado />
<input type=hidden name=idtramite value="<?=$_POST['idtramite'];?>"/>
<input type=hidden name=idremito_ori />
<input type=hidden name=idremito_des />

</td></tr>
</table>
<table align="center" width="69%">
	<tr><td align=center>
	<input type=button class="botonout" name=botvolver     value="<?=VOLVER; ?>"  onMouseOver="this.className = 'botonover';" onMouseOut="this.className = 'botonout';" onclick="goMenu('bajaTramites.php','opcion', <?=$_POST['opcion'];?>);">
	<input type=button class="botonout" name=botreset      value="<?=CANCELO; ?>" onMouseOver="this.className = 'botonover';" onMouseOut="this.className = 'botonout';" onclick="inicializar();">
	<input type=submit class="botonout" name=botconfirma   value="<?=CONFIRMO;?>" onMouseOver="this.className = 'botonover';" onMouseOut="this.className = 'botonout';" onclick="borraTramite();">
	</td></tr>
</table>
</form>
</div>
<div id=divMensaje style="display:none">
<table cellspacing="0" width=578 cellpadding="0" align=center class=tablaconbordes>
	<tr><td align="center" valign="middle" height="80" class=grabando>
	<div id=grabado></div>
</td></tr>
</table>
</div>

