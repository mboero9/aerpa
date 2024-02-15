<?php 
require_once("../includes/lib.php");
if (check_auth($usrid, $perid) && check_permission($permiso)) {
/* Anulacion de Remito por Origen/Destino */
?>
<html>
<head>
<?php require_once("../includes/inc_header.php"); ?>
<script type="text/javascript">
function ValidarRem(nro_remito)
{
	if(nro_remito=="")
	{
		alert("-- No ha ingresado el n�mero de remito! --");
		ok=false;
	}
	nro_valido = new RegExp("^[0-9]*$","i");
	ok = nro_valido.test(nro_remito);
	if(!ok)
		alert("-- El N� de Remito contiene caracteres invalidos! -- ");
		
	return ok;
}//Cierro validar remito
</script>
</head>
<body onLoad="document.form1.nro_rem.focus();">
<?php require_once("../includes/inc_topleft.php"); 
/* Contenido */
$opcion=$_POST['opcion'];
$pagina="AnuRemito.php";
require_once('../includes/inc_titulo.php');
?>
<!-- Form para ingresar numero de remito -->
<form name="form1" id="form1" action="AnuRemito.php" method="post" onSubmit="return ValidarRem(document.getElementById('nro_rem').value);">
<table align="center" width="30%" class="tablaconbordes" <? if(!empty($_POST['nro_rem'])){?> style="display:none" <? }?>>
<tr>
<th colspan="6" align="center" class="celdatitulo">Ingrese n�mero y tipo de Remito</th>
</tr>
<tr>
<td class="celdatexto" align="center">Origen</td>
<td align="center"><input type="radio" name="tipo_remito" id="tipo_remito1" value="origen"></td>
<td align="center" class="celdatexto">Destino</td>
<td align="center"><input type="radio" name="tipo_remito" id="tipo_remito2" value="destino" checked="checked"></td>
<td align="center" class="celdatexto">Devoluci&oacute;n a Origen</td>
<td align="center"><input type="radio" name="tipo_remito" id="tipo_remito3" value="devolucion" ></td>
</tr>
<tr>
<td colspan=6 align="center" class="celdatexto">N� Remito: <input type="text" name="nro_rem" id="nro_rem" maxlength="10"></td>
</tr>
<tr>
<td colspan="4" align="center">
<input type="submit" class="botonout" name=botvolver     value="<?=ANULAR; ?>"  onMouseOver="this.className = 'botonover';" onMouseOut="this.className = 'botonout';">
</td>
</tr>
</table>
</form>
<!--Fin Form -->
<?php

if(!empty($_POST['tipo_remito']) and (!empty($_POST['nro_rem'])))
{
	$conn->StartTrans();
	try
	{
		
		switch($_POST['tipo_remito']){
			
			case 'origen':
				$remito_id_tipo = "t.rem_id_ori";
				$fecha_entrega = " AND t.tra_fecha_entrega IS NOT NULL ";
				$estado = " AND r.rem_estado <> '".CERRADO."'";
				$motivo = " AND t.mot_codigo IS NULL ";			
				break;
				
			case 'destino':
				$remito_id_tipo = " t.rem_id_des ";
				$fecha_entrega = " AND t.tra_fecha_entrega IS NULL ";
				$estado = "";
				$motivo = "";	
				break;
				
			case 'devolucion':	
				$remito_id_tipo = " t.rem_id_ori ";
				$fecha_entrega = " AND t.tra_fecha_entrega IS NOT NULL ";
				$estado = " AND r.rem_estado <> '".CERRADO."'";
				$motivo = " AND t.mot_codigo IS NOT NULL ";			
				break;		
				
		}
		
		
		
		//busco los remitos tramites que tengan asociado numero y tipo de remito que no tengan las fechas en null
		$sql="SELECT ".$remito_id_tipo.", t.tra_codigo, t.tra_fecha_entrega
		, t.tra_nro_voucher, t.tra_dominio, ".$conn->SQLDate(FMT_DATE_DB, "t.tra_fecha_retiro")." as tra_fecha_retiro 
			FROM dbo.TRAMITE t INNER JOIN
			 dbo.REMITO r ON ".$remito_id_tipo."= r.rem_id
					WHERE     (r.rem_tipo = ".sqlstring($_POST['tipo_remito']).") 
						AND (r.rem_numero = ".sqlint($_POST['nro_rem']).") 
						". $fecha_entrega.
						" AND r.rem_estado <> '".ANULADO."'".$estado.$motivo;
		
		$rs = $conn->Execute($sql);
		if(!$rs->EOF)
		{ ?>
				<br>
				<!-- Form de confirmacio de remito a eliminar -->
				<form name="eliminar" action="AnuRemito.php" method="post">
				<table align="center" width="80%" class="tablaconbordes">
				<tr>
				<th align="center" colspan="3" class="celdatitulo">Confirma?</th>
				</tr>
				<tr>
				<th colspan="3" align="left" class="celdatitulo">Nro. de Remito: <?=$_POST['nro_rem'];?> Tipo: <?=$_POST['tipo_remito'];?></th>
				</tr>
				<tr class="celdatitulo">
				<th align="center">Nro de Tramite</th>
				<th align="center">Nro de Voucher</th>
				<th align="center">Fecha de Retiro</th>
				</tr>
				<input type="hidden" name="confirmado" value="si">
				<!--id remito -->
				<input type="hidden" name="id_remito" value="<?= $rs->fields[(($_POST['tipo_remito']==ORIGEN||$_POST['tipo_remito']==DEVOLUCION )?"rem_id_ori":"rem_id_des")]?>">
				<!-- tipo remito -->
				<input type="hidden" name="t_remito" value="<?=$_POST['tipo_remito'];?>">	
		<?php
		
			$fondo="fondotabla1";
			$ta_codigo="";
			while(!$rs->EOF)
			{
			?>							
				<tr class=<?=$fondo?>>
				<td align="center" class="celdatexto"><?=$rs->fields['tra_dominio'];?></td>
				<td align="center"class="celdatexto"><?=$rs->fields['tra_nro_voucher'];?></td>
				<td align="center" class="celdatexto"><?=$rs->fields['tra_fecha_retiro'];?></td>
				</tr>
				
			<?php
				$tra_codigo .= "|".$rs->fields['tra_codigo'];
				if($fondo=="fondotabla1")
				{$fondo="fondotabla2";}
				else
				{$fondo="fondotabla1";}
				$rs->movenext();
			
			}
		?>
		<!-- numeros de tramite -->
		<input type="hidden" name="tra_codigo" value="<?=$tra_codigo; ?>">
		
		<?php
		?>
				<tr>
				<td align="center" colspan="4"><input type="submit" class="botonout" value=<?=CONFIRMO ?> name="Confirmar" onMouseOver="this.className = 'botonover';" onMouseOut="this.className = 'botonout';"><input type=button class="botonout" name=botvolver     value="<?=VOLVER; ?>"  onMouseOver="this.className = 'botonover';" onMouseOut="this.className = 'botonout';" onClick="goMenu('AnuRemito.php');"></td>
				</tr>
				</table>
				</form>
		<?php
		}
		else
		{ 
			$sql="SELECT rem_estado from remito where (rem_tipo = ".sqlstring($_POST['tipo_remito']).") 
						AND (rem_numero = ".sqlint($_POST['nro_rem']).")";
			
			$rs = $conn->execute($sql);
			if(!$rs->EOF)
			{
			?>
			<br>		
			<table align="center" class="tablaconbordes" width="35%">
			<tr>
			<td class="textoerror" align="center">No se pudo anular remito: </td>
			<td align="center" class="textoerror">Tipo: <?php echo(strtoupper($_POST['tipo_remito'])."  <br/> Nro: ".$_POST['nro_rem']); ?></td>
			</tr>
			<tr>
			<td class="textoerror" align="center" colspan="2"><?php if($rs->fields['rem_estado']==GENERADO){echo("&nbsp;");}else{echo("El Remito ya fue: ".$rs->fields['rem_estado']);}?></td>
			</tr>
			<tr>
			<td align="center" colspan="2"><input type=button class="botonout" name=botvolver     value="<?=VOLVER; ?>"  onMouseOver="this.className = 'botonover';" onMouseOut="this.className = 'botonout';" onClick="goMenu('AnuRemito.php');"></td>
			</tr>
			</table>	
				<?
			}else
			{ ?>
				
				<br>		
			<table align="center" class="tablaconbordes" width="35%">
			<tr>
			<td class="textoerror" align="center">No se pudo anular remito: </td>
			<td align="center" class="textoerror">Tipo: <?php echo(strtoupper($_POST['tipo_remito'])."  <br/> Nro: ".$_POST['nro_rem']); ?></td>
			</tr>
			<tr>
			<td class="textoerror" align="center" colspan="2">Remito inexistente.</td>
			</tr>
			<tr>
			<td align="center" colspan="2"><input type=button class="botonout" name=botvolver     value="<?=VOLVER; ?>"  onMouseOver="this.className = 'botonover';" onMouseOut="this.className = 'botonout';" onClick="goMenu('AnuRemito.php');"></td>
			</tr>
			</table>
			<?php 
			}
	}
	
	}catch(exception $e)
	{
		?>
		<table align="center" class="tablaconborders" width="30%">
		<tr>
		<td class="textoerror" align="center">No se pudo realizar la operacion</td>
		</tr>
		</table>
		<?php
		
	}
	$conn->CompleteTrans();
}
if(!empty($_POST['confirmado']))
{
	$conn->StartTrans();
	try
	{
	   
        //Antes de poner el remito en ANULADO hago el LOG correspondiente de la operacion
         //Primero junto los ID de los tramites que pueda tener
         $sql_lista_tramites = "SELECT tra_codigo FROM tramite WHERE (rem_id_ori = '".$_POST['id_remito']."' or  rem_id_des = '".$_POST['id_remito']."')";
         $rs_lista_tramites = $conn->Execute($sql_lista_tramites);
         
         $lista_tramites = "";
         while ( !$rs_lista_tramites->EOF ){
            
            $lista_tramites.= $rs_lista_tramites->fields["tra_codigo"].",";
            
            $rs_lista_tramites->MoveNext();
         }
         $lista_tramites = substr ($lista_tramites, 0, strlen($lista_tramites) - 1);
        
         //Traigo el registro completo del REMITO
         $sql_remito = "SELECT * FROM REMITO WHERE rem_id = ".$_POST['id_remito'];
         
         $rs_remito = $conn->execute($sql_remito);
       
         //Hago el Insert en la tabla de log de BAJA_REMITO
         $sql_insert_remito = "INSERT INTO BAJA_REMITO
                                   (baja_usuario_id
                                   ,baja_fecha
                                   ,rem_id
                                   ,rem_numero
                                   ,rem_tipo
                                   ,rem_fecha_generacion
                                   ,rem_fecha_cierre
                                   ,rem_nombre_conformidad
                                   ,rem_estado
                                   ,usr_id
                                   ,rem_fecha_act
                                   ,rem_tramites)
                             VALUES
                                   (".sqlint($usrid)."
                                   ,".sqldate(dbtime())."
                                   ,".$_POST['id_remito']."
                                   ,".sqlint($rs_remito->fields["rem_numero"])."
                                   ,".sqlstring($rs_remito->fields["rem_tipo"])."
                                   ,".sqldate($rs_remito->fields["rem_fecha_generacion"])."
                                   ,".sqldate($rs_remito->fields["rem_fecha_cierre"])."
                                   ,".sqlstring($rs_remito->fields["rem_nombre_conformidad"])."
                                   ,".sqlstring($rs_remito->fields["rem_estado"])."
                                   ,".sqlint($rs_remito->fields["usr_id"])."
                                   ,".sqldate($rs_remito->fields["rem_fecha_act"])."
                                   ,".sqlstring($lista_tramites).")";
                                   
         $conn->execute($sql_insert_remito);
        
        
        	//pongo el remito en estado anulado
        	$sql="UPDATE remito set rem_estado="."'".ANULADO."'"." where rem_id=".$_POST['id_remito'];
        	$conn->execute($sql);
        	
        	$tramites = explode("|",$_POST['tra_codigo']);
        	
        	foreach($tramites as $tram)
        	{
        		//blanqueo el id de remito del tramite
        		if(!empty($tram))
        		{
        			$sql="UPDATE tramite set ";
        			switch ($_POST['t_remito']) {
        				case DEVOLUCION:
        				case ORIGEN:
        					$sql .= "rem_id_ori = null ";
        					break;
        				case DESTINO:
        					$sql .= "rem_id_des = null ";
        					break;
        			}
        			$sql .= "where tra_codigo=".$tram;
        			$conn->execute($sql);
        		}
        	}
        	?>
        	<br>
        	<table align="center" width="50%" class="tablaconbordes">
        	<tr> 
        	<td align="center" class="celdatexto">El remito ha sido anulado </td>
        	</tr>
        	</table>
	<?
	}
	catch(exception $e)
	{
		?>
		<table align="center" class="tablaconbordes" width="50%">
		<tr>
		<td class="textoerror" align="center">No se pudo realizar la operacion.</td>
		</tr>
		</table>
		<?
	}		

	$conn->CompleteTrans();
}//Fin confirmado
?>
<?php require_once("../includes/inc_bottom.php"); ?>
</body>
</html>
<?php
}//Cierro if de seguridad
?>