<?php
require_once("../includes/lib.php");
if (check_auth($usrid, $perid) && check_permission($permiso)) {
	/* Cierre Rendicion Final */
	?>
<head>
	<?php require_once("../includes/inc_header.php"); ?>
<script type="text/javascript">
function ajax(url) {
//alert(url);
	http.open("GET", url, false);
	http.send(null);
}
function validarFormEntregas()
{
	
		var ok=true;
		var errores="";
		nro = new RegExp("^[0-9]*$","i");
		var nombre = new RegExp("^[a-z ]*$","i");
		if(document.getElementById('nro_rem').value=="")
		{
			
			errores +="--No ha introducido el número de Remito Origen.\n";
			ok=false;
			
		}
		if(document.getElementById('fecha_cierre').value=="")
		{
			errores +="--No ha introducido la Fecha de Cierre.\n";
			ok=false;
			
		}
		if(!parseDate(document.cierre.fecha_cierre,'%d/%m/%Y',true)) 
		{
							
				errores += "-- El Formato de la Fecha  de Cierre no es Valido.\n";
				ok = false;
													
		}
		if(!nro.test(document.getElementById('nro_rem').value))
		{
			errores +="--El número de Remito contiene caracteres invalidos.\n ";
			ok=false;
			
		}
		if(!nombre.test(document.getElementById('responsable').value))
		{
			errores +="--El campo Responsable contiene caracteres invalidos.\n ";
			ok=false;
			
		}
		if(document.cierre.fecha_cierre.value !="")
		{
			dia = dia_habil(document.cierre.fecha_cierre.value);
			if(dia!="1")
			{
				ok = false;
				errores += "Fecha invalida: "+dia+"\n"; 
			}
		}
		
		if(!ok)
		{
			alert(errores);
		}
		
		return ok;

}//Cierro Validar Form


function  validarFormDevoluciones(){
	
		var ok=true;
		var errores="";
		nro = new RegExp("^[0-9]*$","i");
		var nombre = new RegExp("^[a-z ]*$","i");
		if(document.getElementById('nro_rem_dev').value=="")
		{
			
			errores +="--No ha introducido el número de Remito de Devolución a ORIGEN.\n";
			ok=false;
			
		}
		if(document.getElementById('fecha_cierre_dev').value=="")
		{
			errores +="--No ha introducido la Fecha de Cierre.\n";
			ok=false;
			
		}
		if(!parseDate(document.cierre_dev.fecha_cierre_dev,'%d/%m/%Y',true)) 
		{
							
				errores += "-- El Formato de la Fecha  de Cierre no es Valido.\n";
				ok = false;
													
		}
		if(!nro.test(document.getElementById('nro_rem_dev').value))
		{
			errores +="--El número de Remito de Devolución contiene caracteres invalidos.\n ";
			ok=false;
			
		}
		if(!nombre.test(document.getElementById('responsable_dev').value))
		{
			errores +="--El campo Responsable contiene caracteres invalidos.\n ";
			ok=false;
			
		}
		if(document.cierre_dev.fecha_cierre_dev.value !="")
		{
			dia = dia_habil(document.cierre_dev.fecha_cierre_dev.value);
			if(dia!="1")
			{
				ok = false;
				errores += "Fecha invalida: "+dia+"\n"; 
			}
		}
		
		if(!ok)
		{
			alert(errores);
		}
		
		return ok;
	
}//Cierro Validar Form dev
</script>
<!-- Objeto Ajax -->
<script type="text/javascript" language="JavaScript"
	src="../includes/ajaxobjt.js"></script>
<!--amTramites.js contiene la funcion para validar dias habiles-->
<script type="text/javascript" language="JavaScript" src="amTramites.js"></script>
<!--calendario-->
<script type="text/javascript" language="JavaScript"
	src="../includes/fecha.js"></script>
<style type="text/css">
@import url(../calendar/calendar-win2k-1.css);
</style>
<script type="text/javascript" src="../calendar/calendar.js"></script>
<script type="text/javascript" src="../calendar/lang/calendar-es.js"></script>
<script type="text/javascript" src="../calendar/calendar-setup.js"></script>
</head>

<? 
	if(!isset($_POST["cierre_dev"]) && !isset($_POST["cierre"])){
		$tipo =  "" ;
	}else{
		$tipo = isset($_POST["cierre"]) ? "" : "_dev";
	}	
?>
<body onLoad="document.cierre<? echo $tipo ?>.nro_rem<? echo $tipo ?>.focus();">
	<? require_once("../includes/inc_topleft.php");
	/* Contenido */
	$opcion=$_POST['opcion'];
	$pagina="RendFinal.php";
	require_once('../includes/inc_titulo.php');
	?>
<br>
<!-- tabla cierre -->
<table align="center" class="tablaconbordes" width="30%">
	<tr>
		<td class=celdatitulo align=center>Entregas</td>
	</tr>
	<tr>
		<td  >
		<form name="cierre" action="RendFinal.php" method="post"
			onSubmit="return (validarFormEntregas());">
		<table  cellpadding="0" cellspacing="0" border="0" >
			<input type="hidden" id="cierre" name="cierre">
			<tr>
				<td align="center" class="celdatexto" height="30">Nro de remito
				ORIGEN:</td>
				<td align="center"><input type="text" id="nro_rem" name="nro_rem"
					maxlength="15" size="15"></td>
			</tr>
			<tr>
				<td align="center" class="celdatexto" height="30">Fecha de Cierre:</td>
				<td align="center"><input type="text" id="fecha_cierre"
					name="fecha_cierre" maxlength="10" size="15"
					onBlur="parseDate(this,'<?php echo(FMT_DATE_CAL); ?>',true);"></td>
				<td align="center"><img src="../imagenes/calendario.png"
					name="selfecha2" id="selfecha2" title="Calendario" alt=""
					style="cursor: pointer;"></td>
			</tr>
			<tr>
				<td align="center" class="celdatexto" height="30">Responsable:</td>
				<td align="center"><input type="text" id="responsable"
					name="responsable" maxlength="30" size="15"></td>
			</tr>
			<tr>
				<td align="center" colspan="3"><input type="submit" class="botonout"
					value=<?=CONFIRMO ?> name="Confirmar" 
					onMouseOver="this.className = 'botonover';"
					onMouseOut="this.className = 'botonout';"></td>
			</tr>
		</table>
		</form>
		</td>
	</tr>
</table>
<!-- mensaje -->
<table align='center' width='100%' border="0" cellpadding="0" cellspacing="0">
	<tr> 
		<td width="100%" >
				<div id="div_cierre"  style="padding-bottom: 5px;height: 25px;"></div>
		</td>
	</tr>	
</table>
<!-- tabla devolucion -->
<table align="center" class="tablaconbordes" width="30%">
	<tr>
		<td class=celdatitulo align=center>Devoluciones</td>
	</tr>
	<tr>
		<td >
		<form name="cierre_dev" action="RendFinal.php" method="post"
			onSubmit="return (validarFormDevoluciones());">
		<table cellpadding="0" cellspacing="0" border="0" >
			<input type="hidden" id="cierre_dev" name="cierre_dev">
			<tr>
				<td align="center" class="celdatexto" height="30" width="100">Nro de remito
				Devoluci&oacute;n a ORIGEN:</td>
				<td align="center"><input type="text" id="nro_rem_dev"
					name="nro_rem_dev" maxlength="15" size="15"></td>
			</tr>
			<tr>
				<td align="center" class="celdatexto" height="30">Fecha de Cierre:</td>
				<td align="center"><input type="text" id="fecha_cierre_dev"
					name="fecha_cierre_dev" maxlength="10" size="15"
					onBlur="parseDate(this,'<?php echo(FMT_DATE_CAL); ?>',true);"></td>
				<td align="center"><img src="../imagenes/calendario.png"
					name="selfecha2_dev" id="selfecha2_dev" title="Calendario" alt=""
					style="cursor: pointer;"></td>
			</tr>
			<tr>
				<td align="center" class="celdatexto" height="30">Responsable:</td>
				<td align="center"><input type="text" id="responsable_dev"
					name="responsable_dev" maxlength="30" size="15"></td>
			</tr>
			<tr>
				<td align="center" colspan="3"><input type="submit" class="botonout"
					value=<?=CONFIRMO ?> name="Confirmar"
					onMouseOver="this.className = 'botonover';"
					onMouseOut="this.className = 'botonout';"></td>
			</tr>
		</table>
	</form>
	</td>
	</tr>
</table>
<div id="div_cierre_dev"></div>
<script type="text/javascript">
	Calendar.setup( { inputField: "fecha_cierre", ifFormat: "%d/%m/%Y", button: "selfecha2" } );
	Calendar.setup( { inputField: "fecha_cierre_dev", ifFormat: "%d/%m/%Y", button: "selfecha2_dev" } );
			
</script>
</body>
<?php
	if (isset($_POST["cierre_dev"]) || isset($_POST["cierre"])){

		$cierre = isset($_POST['cierre']) ? "cierre" : "cierre_dev";
		$nro_rem = isset($_POST['nro_rem']) ? $_POST['nro_rem'] : $_POST['nro_rem_dev'];
		$fecha_cierre = isset($_POST['fecha_cierre']) ? $_POST['fecha_cierre'] : $_POST['fecha_cierre_dev'];
		$responsable = isset($_POST['responsable']) ? $_POST['responsable'] : $_POST['responsable_dev'];
		$whereTipo = isset($_POST['cierre']) ? " and rem_tipo= '".ORIGEN."'" : " and rem_tipo= '".DEVOLUCION."'";

		$conn->StartTrans();
		try
		{
			$sql="SELECT rem_numero,rem_estado,".$conn->SQLDate(FMT_DATE_DB,"rem_fecha_generacion"). 
				" as rem_fecha_generacion from remito where rem_numero=".sqlint($nro_rem).
					$whereTipo."  and rem_fecha_cierre is null";
	
			$rs = $conn->execute($sql);

			if(!$rs->EOF)
			{
				if($rs->fields['rem_estado']==ANULADO)
				{
?>
<script type="text/javascript">
	document.getElementById('div_<? echo $cierre?>').innerHTML="<table align='center' class='tablaconbordes' width='40%'><tr><td class='textoerror' align='center'>El remito se encuentra anulado</td></tr></table>"
</script>
<?						
				}else{
					$dif = compara_fechas($fecha_cierre,$rs->fields['rem_fecha_generacion']);
				
					if($dif !=1 && $dif !=3)
					{
?>
<script type="text/javascript">
	document.getElementById('div_<? echo $cierre?>').innerHTML="<table align='center' width='80%' class='tablaconbordes'><tr><td align='center' class='textoerror'>La fecha de cierre es inferior ala fecha de generacion del Remito.</td></tr></table>"
</script>
<?php
					}else{
						$tmp1 = new Date($fecha_cierre);
						$sql="UPDATE remito
										set rem_fecha_cierre=".sqldate($tmp1->format(FMT_DATE_ISO)).
										", rem_estado=".sqlstring(CERRADO).
										", rem_nombre_conformidad= ".sqlstring($responsable).
										" where rem_numero= ".sqlint($nro_rem).$whereTipo;
					
		
						$conn->execute($sql);
?>
<script type="text/javascript">
	document.getElementById('div_<? echo $cierre?>').innerHTML="<table align='center' width='30%' class='tablaconbordes'><tr><td align='center' class='celdatexto'>Fecha de cierre ingresada para remito: <?=$nro_rem ?></td></tr></table>"
</script>
<?
					}
				}
			}else{
?>
<script type="text/javascript">
	document.getElementById('div_<? echo $cierre?>').innerHTML="<table align='center' width='40%' class='tablaconbordes'><tr><td align='center' class='textoerror'>No se encontro el remito ingresado.</td></tr></table>"
</script>
<?
			}
		}catch(exception $e){
?>
<script type="text/javascript">
	document.getElementById('div_<? echo $cierre?>').innerHTML="<table align='center' width='30%' class='tablaconbordes'><tr><td align='center' class='textoerror'>No se pudo realizar la operación.</td></tr></table>"
</script>
<?
		}
		$conn->CompleteTrans();
	}//cierro if de validacion

}//Cierro if de seguridad
?>