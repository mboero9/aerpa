<?php
require_once("../includes/lib.php");
if (check_auth($usrid, $perid) && check_permission($permiso)) {
?>
<html>
<head>
<? require_once("../includes/inc_header.php"); ?>
<script type="text/javascript" language="JavaScript" src="../includes/ajaxobjt.js"></script>
<script type="text/javascript">

function trim(a){
	var tmp=new Array();
	for(j=0;j<a.length;j++)
		if(a[j]!='')
			tmp[tmp.length]=a[j];
	a.length=tmp.length;
	for(j=0;j<tmp.length;j++)
		a[j]=tmp[j];
	return a;
}//Fin trim
//Verifica antes de modificar parametros
function validar_nuevos()
{
	var errmsg="Errores:\n";
	var sinerr=true;
	
	nombre = document.getElementById("nuevo_nombre").value;
	script = document.getElementById("nuevo_script").value;
	logu = document.getElementById("nuevo_log").value;
	
	periodo = document.getElementById("nuevo_periodo").value;
	cron = document.getElementById("nuevo_cron").value;
	//checkeo periodo
	Experiodo = new RegExp("^[0-9]*$","i");
	ret = Experiodo.test(periodo);
	if(!ret)
	{
		errmsg +="-El Valor '"+periodo+"' es incorrecto.\n";
		sinerr=false;
	}
	//checkeo nombre
	Exnombre= new RegExp("^[a-z0-9_ ]*$","i");
	ret = Exnombre.test(nombre);
	if(!ret)
	{
		errmsg +="-El Valor '"+nombre+"' es incorrecto.\n";
		sinerr=false;
	}
	//checkeo script
	Exscript= new RegExp("^[a-z0-9_]*[.][a-z0-9]*$","i");
	ret = Exscript.test(script);
	if(!ret)
	{
		errmsg +="-El Valor '"+script+"' es incorrecto.\n";
		sinerr=false;
	}
	//Checkeo log
	Exlog = new RegExp("^[sn]$","i");
	ret = Exlog.test(logu);
	if(!ret)
	{
		errmsg +="-El Valor '"+logu+"' es incorrecto.\n";
		sinerr=false;
	}
	//Checkeo cron
	Excron = new RegExp("^[sn]$","i");
	ret = Excron.test(cron);
	if(!ret)
	{
		errmsg +="-El Valor '"+cron+"' es incorrecto.\n";
		sinerr=false;
	}
	if(!sinerr)
	{
		alert(errmsg);
	}
	return sinerr;
}//Cerrar validar nuevos
function validar2(n_proceso)
{
	var conf;
	var peri;
	var sinerr=true;
	var errmsg="Errores:\n";	
	//Recorro los procesos para ver cuales fueron modificados
	for(x=1;x<=n_proceso;x++)
	{
		if(document.getElementById("logu"+x).disabled==false)
		{
			peri=document.getElementById("periodo"+x).value;
			//conf=new String (document.getElementById("config"+x).value);
			//cantconf = conf.split(" ");
			
	
			//valido campo periodo
			Experiodo = new RegExp("^[0-9]*$","i");
			ret = Experiodo.test(peri);
			if(!ret)
			{
				errmsg +="-El Valor '"+peri+"' es incorrecto.\n";
				sinerr=false;
			}//Cierro if !ret
		
		}//Cierro if disabled==false
		
		//Si hubo errores mostrarlos
		if(!sinerr)
		{
			alert(errmsg);
		}//Cierro if !err
		return sinerr;		
	}//Cierro for x
}//Fin validar2
//valida el en el onBlur			
function validar(valor,id)
{
	campo = new String(id);
	var sinerr = true;
	var errmsg="Errores:\n";
	if ((campo.substr(0,4))=="conf")
	{
			valconf=valor.split(" ");
			
			if((trim(valconf)).length<5)
			{
				errmsg += "-Conf. Cron: Numero de parametros invalido!\n";
				sinerr=false;
			}
			else
			{
			
			//Checkeo valores de Conf cron
			
			ExpReg = new RegExp("^[\-\/\*\,0-9]* [\-\*\/\,0-9]* [\-\*\/\,0-9]* [\*\-a-z]* [\*\-a-z]*$");
			ret = ExpReg.test(valor);
			
			if(!ret)
			{
				errmsg += "-El Valor '"+valor+" es incorrecto\n";
				sinerr=false;
			}
			}//Cierro else
	}
	else if((campo.substr(0,4))=="peri")
	{
			ExpReg = new RegExp("^[0-9]*$","i");
			ret = ExpReg.test(valor);
			if(!ret)
			{
				errmsg +="-El Valor '"+valor+"' es incorrecto.\n";
				sinerr=false;
			}
			
	}		
	else
	{
			
			errmsg +="-Error campo desconocido!\n";
	}			
	if(!sinerr)
	{	
		alert(errmsg);
	}
	return sinerr;	
}
function Habilitar(periodo,logu,cron)
{
	//Habilita parametrizacion de procesos
	document.getElementById(periodo).disabled=false;
	document.getElementById(logu).disabled=false;
	document.getElementById(cron).disabled=false;
	document.getElementById(periodo).focus();
	
}//Fin Habilitar
function ajax(url) {
	//Envio de  datos al back_end
	http.open("GET", url, false); 
	http.send(null);
}//Fin ajax
function refresh()
{
    var sURL = unescape(window.location.pathname);
    window.location.href = sURL;
}
function Crear_proceso()
{
	//Bloqueo los campos
	document.getElementById("nuevo_nombre").disabled=true;
	document.getElementById("nuevo_script").disabled=true;
	document.getElementById("nuevo_log").disabled=true;
	
	document.getElementById("nuevo_periodo").disabled=true;
	document.getElementById("nuevo_cron").disabled=true;
	
	//Traigo los valores
	parametros = document.getElementById("nuevo_nombre").value + "|";
	parametros += document.getElementById("nuevo_script").value + "|";
	parametros += document.getElementById("nuevo_log").value + "|";
	parametros += " |";
	parametros += document.getElementById("nuevo_periodo").value + "|";
	parametros += document.getElementById("nuevo_cron").value;
	
	url="back_procesos.php?crear="+parametros;
	
	ajax(url);
			if(http.readyState==4)
			{
				//Si se actualizaron 
				ok = parseInt(http.responseText);
				
				//Si no funca
				if (!ok)
				{
					alert("-- No se pudo crear el proceso! -- ");
				
				}//Cierro if !ok
				else
				{
					// Si todo ok
					alert("--El proceso fue creado con exito --");
					refresh();
					
				}//Cierro else
			
			}//Cierro if http.readyState
	
	
}//Frin Crear_proceso
function Actualizar(n_proceso)
{
	var parametros;
	var mensaje="";
	//Recorro los procesos para ver cuales fueron modificados
	for(x=1;x<=n_proceso;x++)
	{
		if(document.getElementById("logu"+x).disabled==false)
		{
			//Bloqueo los campos de escritura
			document.getElementById("logu"+x).disabled=true;
			document.getElementById("periodo"+x).disabled=true;
			document.getElementById("cron"+x).disabled=true;
			
			//Obtengo los valores a actualizar
			parametros = document.getElementById("id"+x).value + "|";
			(document.getElementById("logu"+x).checked==true)?parametros += "1" + "|":parametros += "0" + "|";
			parametros += document.getElementById("periodo"+x).value + "|";
			(document.getElementById("cron"+x).checked==true)?parametros += "1" + "|":parametros += "0" + "|";
			
			url="back_procesos.php?actualizar="+parametros;
			
			ajax(url);
			if(http.readyState==4)
			{
				//Si se actualizaron 
				new_param = http.responseText.split("|");
				
							
				if(new_param[0]!="Error")
				{	
					//Si no hay error asigno los valores actualizados al form
					for (n=0;n<=2;n++)
					{
						
						switch (n)
						{
							
							case 0:
								document.getElementById("logu"+x).value=new_param[n];
								(new_param[n]=="1")?document.getElementById("logu"+x).checked=true
								:document.getElementById("logu"+x).checked=false;
								break;
							case 1:
								document.getElementById("periodo"+x).value=new_param[n];
								break;
							case 2:
								document.getElementById("cron"+x).value=new_param[n];
								(new_param[n]=="1")?document.getElementById("cron"+x).checked=true
								:document.getElementById("cron"+x).checked=false;
								break
							
							default:
								alert("Array fuera de rango");
						}//Cierro switch
						
					}//Cierro for n				
					mensaje += "Proceso:"+x+"-- Los parametros han sido modificados --\n";
				}	
				else//En caso de error
				{
					document.getElementById("nombre"+x).innerHTML=http.responseText;
				}//Cierro else
			}//Cierro if readyState
		}//Cierro if disable==false
		else
		{
			mensaje +="Proceso"+x+"-- No se han modificado parametros! --\n";
		}//Cierro else (if desable==false)
	}//Cierro for x
 alert(mensaje);
}//Fin Actualizar
function ejecutar(n_procesos)
{
	//Obtengo el nombre del script
	
	script = document.getElementById("script"+n_procesos).innerHTML;
	url="back_procesos.php?script="+script; 		
	ajax(url);
	if (http.readyState == 4) 
	{ 
		results = http.responseText;
		if (!results)
			{alert("-- No se pudo ejecutar el proceso! --");}
		else
			{alert("-- El proceso fue ejecutado. --");}
	}//fin if readyState
}//Fin ejecutar
function muestra_nuevo()
{
	//Muestro el form para nuevos
	document.getElementById("tabla_nuevos").style.visibility="visible";
	document.getElementById("nuevo_nombre").focus();
}//Cierro muestra_nuevo
function Eliminar(id)
{
	//Elimina procesos
	proceso = document.getElementById(id).value;
	url="back_procesos.php?eliminar="+proceso;
	ajax(url);
	if (http.readyState == 4) 
	{ 
		results = parseInt(http.responseText);
		
		if (!results)
			{alert("-- No se pudo eliminar el proceso! --");}
		else
			{
				alert("-- El proceso fue eliminado. --");
				refresh();
			}
	}//fin if readyState
	
}//Cierra Eliminar
</script>
</head>
<body>
<? require_once("../includes/inc_topleft.php");
/* Contenido */
$opcion=$_POST['opcion'];
$pagina="abmprocesos.php";
require_once('../includes/inc_titulo.php');
?>

<form name="Form1">
<table align="center" width="90%" class="tablaconbordes">
<tr class="celdatituloColumna">
<td align="center">Nombre</td><td align="center">Script</td><td align="center">Max. Ejec.</td><td align="center">Inicio</td>
<td align="center">Fin</td><td align="center">Loguear</td><td align="center">Cronado</td>
<td align="center">Modificar</td><td align="center">Ejecutar</td><td align="center">Eliminar</td>
</tr>
<?
	//Busco los procesos
	$sql = "SELECT proc_id,proc_nombre,proc_script,proc_periodo,".$conn->SQLDate(FMT_DATETIME_DB,"proc_ini").
	" AS fecha_ini,".$conn->SQLDate(FMT_DATETIME_DB,"proc_fin")." AS fecha_fin ,proc_loguear,proc_cron,proc_cron_cfg ".
		"FROM proceso";
	
	$rs = $conn->Execute($sql);
	$fondo = "fondotabla1";
	$i = 0;
	while(!$rs->EOF)
	{
		$i++;
?>
	<tr class="<? echo($fondo); ?>">
	<td class="celdatexto" align="center" id="nombre<? echo($i);?>"><? echo($rs->fields["proc_nombre"]); ?></td>
	<td class="celdatexto" align="center" id="script<? echo($i);?>"><? echo($rs->fields["proc_script"]); ?></td>
	<td class="celdatexto" align="center"><input type="text" name="periodo<? echo($i);?>" id = "periodo<? echo($i);?>" maxlength="4"
	value="<? echo($rs->fields["proc_periodo"]); ?>" disabled=true size="3" onBlur="validar(this.value,this.id);">	
	</td>
	<td class="celdatexto" align="center"><? echo($rs->fields["fecha_ini"]); ?></td>
	<td class="celdatexto" align="center"><? echo($rs->fields["fecha_fin"]); ?></td>
	<td class="celdatexto" align="center"><input type="checkbox" name="logu<? echo($i);?>" id="logu<? echo($i);?>" 
	value="<? if($rs->fields["proc_cron"]==1){echo("1");}else{echo("0");}; ?>"<? if($rs->fields["proc_loguear"]==1){echo(" Checked ");}?> disabled="true" size="1">
	</td>
	<td class="celdatexto" align="center"><input type="checkbox" name="cron<? echo($i);?>" id="cron<? echo($i);?>" maxlength="1" 
	value="<? if($rs->fields["proc_cron"]==1){echo("1");}else{echo("0");}; ?>" 
	<? if($rs->fields["proc_cron"]==1){echo(" Checked ");}; ?> disabled="true" size="1">
	</td>
	<td align="center"><input  class="botonchico" type="button" name="cmdmodif<? echo($i);?>" value="Modificar" onClick="Habilitar('periodo<? echo($i)?>','logu<? echo($i)?>','cron<? echo($i)?>');" 
	title="Modificar parametros del proceso"></td>
	<td align="center"><input type="button" class="botonchico" name="cmdeject<? echo($i);?>" 
	value="Ejecutar" title="Ejecutar proceso" onClick="ejecutar(<? echo($i);?>)"></td>
	<td align="center"><input type="button" name="cmdborrar" value="Eliminar" class="botonchico" 
	title="Eliminar proceso" onClick="Eliminar('id'+<? echo($i)?>);"></td>
	</tr>
	<input type="hidden" name="id<? echo($i)?>" id="id<? echo($i)?>" value="<? echo($rs->fields["proc_id"]); ?>">
<?
	if ($fondo=="fondotabla1")
	{
		$fondo="fondotabla2";
	}else
	{
		$fondo="fondotabla1";
	}
	$rs->movenext();
	}//Ciero while EOF
?>
</table>
<table align="center" width="90%">
<tr>
<td align="right" colspan="5">
<br>
<input type="button" name="cmdactualizar" id="cmdactualizar" value="Actualizar proc." class="botonout"  onMouseOver="this.className = 'botonover';" onMouseOut="this.className = 'botonout';" onClick="if(validar2(<? echo($i);?>)){Actualizar(<? echo($i);?>)};">
</td>
<td align="left" colspan="5">
<br>
<input type="button" name="cmdnuevo" id="cmdnuevo" value="Agregar proc." class="botonout"  onMouseOver="this.className = 'botonover';" onMouseOut="this.className = 'botonout';" onClick="muestra_nuevo();">
</td>
</tr>
</table>
</form>

<!--Formulario para agregar procesos -->
<br>


<form name="Form2">
<table align="center" width="30%" class="tablaconbordes"  style="visibility:hidden" id="tabla_nuevos" cellspacing="0">
<tr class="celdatitulo">
<td align="center" colspan="3">Ingrese los parametros para el proceso</td>
</tr>
<tr class="fondotabla1">
<td align="center" class="celdatexto">Nombre</td>
<td align="center" class="celdatexto"><input type="text" id="nuevo_nombre" size="10" maxlength="30">
<td align="center" class="celdatexto"><br></td>
</tr>
<tr class="fondotabla2">
<td align="center" class="celdatexto">Script</td>
<td align="center" class="celdatexto"><input type="text" id="nuevo_script" size="10" maxlength="30">
<td align="center" class="celdatexto"><br></td>
</tr>
<tr class="fondotabla1">
<td align="center" class="celdatexto">Log(S/N)?</td>
<td align="center" class="celdatexto"><input type="text" id="nuevo_log" size="10" maxlength="1">
<td align="center" class="celdatexto"><br></td>
</tr>
<tr class="fondotabla2">
<td align="center" class="celdatexto">Cron(S/N)?</td>
<td align="center" class="celdatexto"><input type="text" id="nuevo_cron" size="10" maxlength="1">
<td align="center" class="celdatexto"><br></td>
</tr>
<tr class="fondotabla1">
<td align="center" class="celdatexto">Periodo</td>
<td align="center" class="celdatexto"><input type="text" id="nuevo_periodo" size="10" maxlength="3">
<td align="center" class="celdatexto"><br></td>
</tr>
<tr>
<td align="center" colspan="3"><input type="button" name="nuevo_proc" id="cmdnuevo_cron" value="Crear Proceso" class="botonout"  onMouseOver="this.className = 'botonover';" onMouseOut="this.className = 'botonout';" 
onClick="if(validar_nuevos()){Crear_proceso();};"></td>
</tr>
</table>
</form>
</body>
</html>
<?
require_once("../includes/inc_bottom.php");
}//If de autorizacion
?>