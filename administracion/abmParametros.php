<?
require_once("../includes/lib.php");
if (check_auth($usrid, $perid) && check_permission($permiso)) {
?>
<HTML>
<HEAD>
<? require_once("../includes/inc_header.php"); ?>
<script type="text/javascript">
function Valid(valor,tipo) {
	//Validador de campos
	if (tipo=='n') {
		var validos = /[1-9]/;
    }else{
		var validos = /^[a-zA-Z0-9��������������][a-zA-Z0-9�������������� \/\.\,\_\-@]*$/;	
	}
	if(!validos.test(valor)) {
		return false;
	}else{
		return true;
	}
}//Cierro ValidNum
function modificar(id) {
  var par = "valor"+id;
  var des = "despar"+id;
  var mod = "mod"+id;        
  document.getElementById(par).readOnly=false;
  document.getElementById(des).readOnly=false;
  document.getElementById(par).style.background='white';
  document.getElementById(des).style.background='white';
  document.getElementById(mod).value='1';        
  document.getElementById(des).focus();
}
function confirmo(tipo) {
	var errores="";
	switch(tipo) {
	case 'alta':
	    var repetidos=0;
		for (i=0; i<=document.formalta.totcarga.value; i++) {
			var des          = 'Nvodespar'+i;
			var valor       = 'Nvovalor'+i;	
			var linerr       = 0;
			var erroreslinea = "";			
//valido si completo alguno de los datos, sino ignoro el registro
			if 	(document.getElementById(des).value!=""||
			     document.getElementById(valor).value!="") {
				 if (!Valid(document.getElementById(des).value,'t')) { erroreslinea+=' - Descripcion Invalida'; linerr=1;}				 
				 if (!Valid(document.getElementById(valor).value,'t')) { erroreslinea+=' - Valor Invalido'; linerr=1;}				 				 				 
				  url="abmParametros_ajax.php?tipo=desexistente&desc="+document.getElementById(des).value; 
				  ajax(url);
				  if (http.readyState == 4) { 
					results = http.responseText.split(";"); 
					if (results[0]=='existe') { erroreslinea+=' - Descripcion ya Existe'; linerr=1;}
				  }				 
//** validar que el codigo ya no exista				 x ajax	 
	 			 if (linerr==1) { errores+='Linea:'+i+erroreslinea+'\n'; }
			}//if
			if (document.formalta.totcarga.value>0) {
			for (j=0; j<=document.formalta.totcarga.value; j++) {
				var des2          = 'Nvodespar'+j;
			    if ((document.getElementById(des).value==document.getElementById(des2).value)&&(j!=i)) {
				   repetidos=1;
				   break;
				}//if
			}//for
			}//if			
		}//for
		if (repetidos==1) {	errores+='No Ingrese Descripciones Iguales\n'; }
		var f = document.formalta;
		break;
   case 'modificacion':
		var hubomod=0;					
		for (i=1; i<=document.formmod.cantreg.value; i++) {
			var des          = 'despar'+i;
			var valor       = 'valor'+i;	
			var linerr       = 0;
			var erroreslinea = "";
			var mod          = "mod"+i;
//valido si modifico, sino ignoro el registro					
			if 	(document.getElementById(mod).value=='1') {
				 if (!Valid(document.getElementById(des).value,'t')) { erroreslinea+=' - Descripcion Invalida'; linerr=1;}				 
				 if (!Valid(document.getElementById(valor).value,'t')) { erroreslinea+=' - Valor Invalido'; linerr=1;}				 				 				 
//** validar que el codigo ya no exista				 x ajax	 
	 			 if (linerr==1) { errores+='Linea:'+i+erroreslinea+'\n'; }
				 hubomod=1;
			}//if
		}//for
		if (hubomod==0) { return false; } //no hubo modificaciones no pasa nada
		var f = document.formmod;		
		break
   case 'baja':	
		var hubomod=0;					
		for (i=1; i<=document.formmod.cantreg.value; i++) {   
		    var chek = 'bajpar'+i
			if (document.getElementById(chek).checked) {		
//validar por ajax si se puede borrar el reg.			
			    hubomod=1;						
				break; //salgo del for ya se que hay algo parar borrar
			}//if
		}//for
		if (hubomod==0) { return false; } //no checkearon nada para borrar
		var f = document.formmod;		
		break		
   }//switch
	if (errores!="") {
		alert("Hubo errores en los datos:\n\n"+errores);
		return false;
	}else{	
	   f.tipo.value=tipo;
	   f.submit();
	}
   
}
function borrar() {
	document.getElementById('spantitbaja').innerHTML='Borrar'
	for (i=1; i<=document.formmod.cantreg.value; i++) {
		var chek  = 'divColbajas'+i;
		document.getElementById(chek).style.display='';
	}
	document.formmod.botbajas.style.display='none';
	document.formmod.botconfirmarmod.style.display='none';	
	document.formmod.botconfirmarbaja.style.display='';			
}
function ajax(url) {
//alert(url);
	http.open("GET", url, false); 
	http.send(null);
}
function volver() {
    setTimeout("goMenu('abmParametros.php')",1500);
}
</script>
<script type="text/javascript" language="JavaScript" src="../includes/ajaxobjt.js"></script> 
</HEAD>	
<!-- Contenido -->
<?
if (empty($_POST['tipo'])){ 
?>
<BODY>
<?
require_once('../includes/inc_topleft.php');
$pagina="abmParametros.php";
require_once('../includes/inc_titulo.php');
?>
<!-- Contenido -->
<div id=divCons <? if (isset($_POST['totcarga'])) { echo 'style="display:none"';} ?>>
<form name=formmod method=post action="abmParametros.php">
<table width=35% align=center cellpadding="0" cellspacing="1">
	<tr class=celdatituloColumna height=12>
    <th><span id=spantitbaja></span></th>
	<th>Parametro</th>
    <th>Valor</th></tr>
<?
	$query = "SELECT PAR_NOMBRE,
				     PAR_VALOR 
				FROM PARAMETRO
			ORDER BY PAR_NOMBRE";
	$parametros = $conn->execute($query);
	$cant=0;
	$clase='fondotabla1';	
	while (!$parametros->EOF) {
	 $cant++;
?>	
 	<tr class="<?=$clase;?>">
	    <td style="background:#fdf7e6">
		<div id=divColbajas<?=$cant;?> style="display:none">
		<table>
		<tr>
		<td><input type=checkbox name=bajpar<?=$cant;?> id=bajpar<?=$cant;?>></td>
		</tr>
		</table>
		</div>
		</td>
	    <td class=celdatexto><input type=hidden name=id<?=$cant;?> value=<?=$parametros->fields["PAR_NOMBRE"]?>>
							 <input type=text class=textochico name=despar<?=$cant;?> id=despar<?=$cant;?> value="<?=$parametros->fields["PAR_NOMBRE"];?>" size=35 maxlength="50" onDblClick="modificar(<?=$cant;?>);" style="text-transform:uppercase; background:#D3D3D3;" readonly>
        </td>			
		<td class=celdatexto align=center>
			<input type=text class=textochico align="right" name=valor<?=$cant;?> id=valor<?=$cant;?> value="<?=$parametros->fields["PAR_VALOR"]?>" size=30 maxlength="60" onDblClick="modificar(<?=$cant;?>);" readonly style="background:#D3D3D3">
			<input type=hidden name=mod<?=$cant;?> id=mod<?=$cant;?>>
		</td>
<!--	<td><input type=button class=botonout name=botmod<?=$cant;?> value="Modificar" onClick="modificar(<?=$cant;?>)"></td>
-->	</tr>
<?			
		    if ($clase=='fondotabla1') { $clase='fondotabla2';
		    } else { $clase='fondotabla1'; }
			$parametros->movenext();
	}//Cierro while !$parametros->eof
?>	
	<input type=hidden name=tipo>
	<input type=hidden name=cantreg value=<?=$cant;?>>
</table>
<table width=50% align=center>
<tr><td align=center>
	<input type=button class=botonout name=botbajas value="Borrar" onClick="borrar();"  onMouseOver="this.className='botonover';" onMouseOut="this.className='botonout';">	
	<input type=button class=botonout name=botaltas value="Nuevo" onClick="document.getElementById('divCons').style.display='none'; document.getElementById('divAltas').style.display=''; document.formalta.Nvodespar0.focus();"  onMouseOver="this.className='botonover';" onMouseOut="this.className='botonout';">	
	<input type=button class=botonout name=botconfirmarmod value="Confirmar" onClick="confirmo('modificacion');"  onMouseOver="this.className='botonover';" onMouseOut="this.className='botonout';">	
	<input type=button class=botonout name=botconfirmarbaja value="Confirmar" onClick="confirmo('baja');" style="display:none"  onMouseOver="this.className='botonover';" onMouseOut="this.className='botonout';">		
</td></tr>
</table>
</form>
</div>
<div id=divAltas <? if (!isset($_POST['totcarga'])) { echo 'style="display:none"';} ?>>
<form name=formalta method=post action="abmParametros.php">
<table width=35% align=center cellpadding="0" cellspacing="1">
	<tr class=celdatitulo height=12>
	<th>Parametro</th>
    <th>Valor</th></tr>

<?	
	$clase='fondotabla1';
	if (!isset($_POST['totcarga'])) { 	$totcarga=0; 
	}else{ $totcarga=($_POST['totcarga']+1); }
	for ($cant=0; $cant<=$totcarga; $cant++) {
?>	
 	<tr class="<?=$clase;?>">
	    <td class=celdatexto><input type=text class=textochico name=Nvodespar<?=$cant;?> id=Nvodespar<?=$cant;?> value="<?=$_POST['Nvodespar'.$cant];?>" size=35 maxlength="50" style="text-transform:uppercase;">
        </td>			
		<td class=celdatexto align=center>
			<input type=text class=textochico align="right" name=Nvovalor<?=$cant;?> id=Nvovalor<?=$cant;?> value="<?=$_POST['Nvovalor'.$cant];?>" size=30 maxlength="50">			
		</td>
		<td style="background:#fdf7e6">
			<? if ($cant==$totcarga) { ?>
			   <input type=submit class=botonout name=botmas<?=$cant;?> id=botmas<?=$cant;?> value="+" onClick="unomas(<?=$totcarga;?>);"  onMouseOver="this.className='botonover';" onMouseOut="this.className='botonout';">		    
		    <? } ?>
		</td>		
<!--	<td><input type=button class=botonout name=botmod<?=$cant;?> value="Modificar" onClick="modificar(<?=$cant;?>)"></td>
-->	</tr>
<?			
	    if ($clase=='fondotabla1') { $clase='fondotabla2';
	    } else { $clase='fondotabla1'; }
	}//Cierro for	
?>	
	<input type=hidden name=tipo id=tipo>				
	<input type=hidden name=totcarga value=<?=$totcarga;?>>					
</table>
<table width=50% align=center>
<tr><td align=center>
	<input type=button class=botonout name=botvolver value="Volver" onClick="document.getElementById('divCons').style.display=''; document.getElementById('divAltas').style.display='none';" onMouseOver="this.className='botonover';" onMouseOut="this.className='botonout';">	
	<input type=button class=botonout name=botconfirmaralta value="Confirmar" onClick="confirmo('alta');" onMouseOver="this.className='botonover';" onMouseOut="this.className='botonout';">	
</td></tr>
</table>
</form>
</div>
<?
} else { 
//echo '<BODY>';
echo '<BODY onload="volver();">';
require_once('../includes/inc_topleft.php');
$pagina="abmParametros.php";
require_once('../includes/inc_titulo.php');
$ok="no";
	switch ($_POST['tipo']) {
	  case 'alta':
		for ($i=0;$i<=$_POST['totcarga'];$i++) {
			if ((!empty($_POST['Nvodespar'.$i]))&&
			    ($_POST['Nvovalor'.$i]!="")) {
	        $Sql="insert into PARAMETRO (PAR_NOMBRE,										  
										  PAR_VALOR)
	                              values (".sqlstring($_POST['Nvodespar'.$i]).",
									      ".sqlstring($_POST['Nvovalor'.$i]).")";					
				$rs = $conn->execute($Sql);					
//			echo "<br>".$Sql;
				$ok="si";
			}// if	($_POST['mod'.$i]=='1')
		}//for
		break;
	  case 'modificacion':
		for ($i=1;$i<=$_POST['cantreg'];$i++) {
			if ($_POST['mod'.$i]=='1') {
				$Sql= "update PARAMETRO 
						  set PAR_NOMBRE=".sqlstring($_POST['despar'.$i]).", 
						      PAR_VALOR=".sqlstring($_POST['valor'.$i])."						  
						where PAR_NOMBRE=".sqlstring($_POST['id'.$i]);
				$rs = $conn->execute($Sql);					
//			echo "<br>".$Sql;
				$ok="si";
			}// if	($_POST['mod'.$i]=='1')
		}//for
		break;
	  case 'baja':
		for ($i=1;$i<=$_POST['cantreg'];$i++) {
			if ($_POST['bajpar'.$i]=='on') {
				$Sql= "delete PARAMETRO 
						where PAR_NOMBRE=".sqlstring($_POST['despar'.$i]);
				$rs = $conn->execute($Sql);					
//			echo "<br>".$Sql;
				$ok="si";
			}// if	($_POST['mod'.$i]=='1')
		}//for
		break;
		
	}//switch
	if ($ok=="si") {
//		$conn->commit();
		require_once('../includes/inc_grabado.php');		
	}//if ($ok=="si")		
}//if (!isset($_POST['cant']))
?>
<!-- Fin de contenido -->
<?
/* Pie de pagina */
require_once("../includes/inc_bottom.php");
?>
</body>
</html>
<?
}//Cierro if de autorizacion
?>