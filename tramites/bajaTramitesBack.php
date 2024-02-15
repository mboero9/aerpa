<?
require_once("../includes/lib.php");
if (check_auth($usrid, $perid) && check_permission($permiso, "bajaTramites.php")) {
?>
<HTML><HEAD>
<? require_once("../includes/inc_header.php"); ?>
<script type="text/javascript">
function inicializar(id) {
	document.getElementById('divFormDatos').style.display='';
	document.getElementById('divMensaje').style.display='none';
	document.form.botreset.style.display='none';
	document.form.modificado.value='';
	document.getElementById('subformMod').style.display='';
	parametros="tipo=buscarxid"+
			   "&traCodigo="+id;
//tipo=altaitem&id=0&idsecc=10&nomitem=prueba item1&descitem=prueba desc&pagina=pagina&directorio=direc&parametro=param&valparametro=valparam&ordenitem=&despuesde=&targetitem=&tippermitem=&separador=true&default=valse&asignable=false
	  url="altaTramites_ajax.php?"+parametros;
//	  alert(url);
	  ajax(url);
	  if (http.readyState == 4) {
		results = http.responseText.split(";");
//alert(results);
		if (results[0]=='Inexistente')  {
			alert("Tramite "+results[0]+" Inexistente. Ingreselo Nuevamente.\n");
			goMenu('bajaTramites.php','opcion', <?=$_POST['opcion'];?>);
		}else{
			document.form.idregmod.value      =results[0];
			document.form.CodRegOrig.value    =results[1];
			document.getElementById('spanDesRegOrig').innerHTML=results[2];
			document.form.CodRegDest.value    =results[3];
			document.getElementById('spanDesRegDest').innerHTML=results[4];
			document.form.FecRetiro.value     =results[5];
			document.form.FecEntrega.value    =results[6];
			
			document.form.NroTramite.value    =results[8];
			document.form.NroVoucher.value    =results[9];			
			document.form.idremito_ori.value  =results[10];						
			document.form.idremito_des.value  =results[11];									
			document.form.FecCierre.value     =results[12];							
     		document.form.CodRegOrig.disabled=true;
		    document.form.CodRegDest.disabled=true;
		    document.form.NroTramite.disabled=true;
		    document.form.NroVoucher.disabled=true;		   		   
		   document.form.FecRetiro.disabled=true; 
   		   document.form.FecEntrega.disabled=true;
		}//if	
				
	  }//if
}
function borraTramite() {
	document.form.botconfirma.disabled=true;
	 var mensaje = ConsAjax('validacion_baja','');
			if(mensaje[0]=="con remito origen"){
				alert ("El tramite ingresado ya posee Remito Origen generado. No podrá darse de baja.");				
			}else if(mensaje[0]=="con fecha entrega"){
				if(mensaje[1]){
						mensajeTipo='Devolución';
				}else{
						mensajeTipo='Entrega';
				}	
				alert ("El tramite ingresado ya posee fecha de "+mensajeTipo+". No podrá darse de baja.");
			}else if(mensaje[0]=="con remito destino"){
				var confirma_baja = confirm ("El tramite ingresado ya posee Remito Destino generado, desea dar de baja el tramite y anular el Remito Destino?.");				
				
				if (confirma_baja){
						 var cant_tramitesxrem = ConsAjax('tramitesxrem','');		
					if (cant_tramitesxrem == 1){								
							/* anula remito*/
							 var msj_anula_remito = ConsAjax('anula_remito','');								
							 if (!msj_anula_remito){
							 	alert (" No se pudo Anular el remito");						 
							 }else{
							 	
								alert("El Remito "+msj_anula_remito+" Anulado. No se generará un nuevo Remito, el mismo solo incluía un solo tramite.");	
								borrarTramite();	
							}			 
					}else if (cant_tramitesxrem > 1){						
						/* anulo remito*/
		
							 var msj_anula_remito = ConsAjax('anula_remito','');
							 if (!msj_anula_remito){
							 		alert (" No se pudo Anular el remito");						 
							 }else{									
								/* genero nuevo remito con los tramites restantes del remito anterior*/	
								
								 var msj_nuevo_remito = ConsAjax('nuevo_remito','');
								 
								 if (msj_nuevo_remito[0]!='ok'){
								 		alert (" No se pudo generar remito");						 
								 }else{	
									/*asocio los tamites al nuevo remito */

									var id_nuevo_remito = msj_nuevo_remito[1];
									var nro_nuevo_remito = msj_nuevo_remito[2];			
									 var msj_actualizo_tramite = ConsAjax('actualizo_tramite',id_nuevo_remito);
									 
									 if (!msj_actualizo_tramite){
									 		alert (" No se pudo actualizar el tramite");						 
									 }else{
										
										alert("El Remito "+msj_anula_remito+" Anulado. Se genero el nuevo Remito Destino "+nro_nuevo_remito);	
								 		
										if (ConsAjax('borrar','')) {
												imprime(nro_nuevo_remito);
												document.getElementById('grabado').innerHTML='El Tramite '+document.form.NroTramite.value.toUpperCase()+
					  											   ' '+document.form.FecRetiro.value+'<BR>Ha sido Eliminado Correctamente';				
												setTimeout("goMenu('bajaTramites.php','opcion', <?=$_POST['opcion'];?>);",1500);
					
										};	
			
										document.form.botconfirma.disabled=false;
										return false;
																				

											
									 };	//actualizo tramite
								 }; //nuevo remito	
							 }; //anulo remito	 				
					}; //cantidad de tramites									
				}; // confirm
			}else{
					borrarTramite();		
					alert("El Tramite "+document.form.NroTramite.value+" a sido dado de baja.");				
       		};
       		 		
}
       		
function borrarTramite(){
			
				if (ConsAjax('borrar','')) {
							
					document.getElementById('grabado').innerHTML='El Tramite '+document.form.NroTramite.value.toUpperCase()+
					  											   ' '+document.form.FecRetiro.value+'<BR>Ha sido Eliminado Correctamente';				
					setTimeout("goMenu('bajaTramites.php','opcion', <?=$_POST['opcion'];?>);",1500);
					
				};	
			
	document.form.botconfirma.disabled=false;
	return false;
}

function imprime(nro_remito){
       		
       	document.descarga.sql.value=
			""+
				"Select a.TRA_DOMINIO, "+
					//"STUFF('0000000', 8-LEN(b.REM_NUMERO), LEN(b.REM_NUMERO), b.REM_NUMERO) as REM_NUMERO, "+
					"a.TRA_NRO_VOUCHER, s.REG_COD_INT as REG_COD_INT_ORI, s.REG_DESCRIP as REG_DESCRIP_ORI"+
					", r.REG_DESCRIP as REG_DESCRIP_DES, "+
					"r.REG_COD_INT  as REG_COD_INT_DES, "+
					"f.REG_DESCRIP as REG_DESCRIP_FAM, "+
					"f.REG_COD_INT  as REG_COD_INT_FAM, "+
					document.descarga.fecharetiro.value+" as TRA_FECHA_RETIRO "+
				"From REG_AUTOM f "+
				"inner Join REG_AUTOM r "+
					"left join "+
						"TRAMITE a "+
						"Inner Join REMITO b on b.rem_id = a.rem_id_des and b.rem_numero BETWEEN #_DESDE_# and #_HASTA_# and rem_tipo = "+"'destino'"+" "+
						"Inner Join REG_AUTOM s on s.reg_codigo = a.reg_codigo_ori "+
					"on r.reg_codigo = a.reg_codigo_des "+
				"on r.reg_familia = f.reg_cod_int "+
				"where f.reg_cod_int in("+
					"select distinct sr.reg_familia "+
						"from REG_AUTOM sr "+
						"inner join TRAMITE sa on sr.reg_codigo = sa.reg_codigo_des "+
						"inner Join REMITO sb on sb.rem_id = sa.rem_id_des and sb.rem_numero BETWEEN #_DESDE_# and #_HASTA_# and rem_tipo = "+"'destino'"+" "+
				")"+
			" Order by REG_COD_INT_DES, TRA_DOMINIO";	
       		
       	
       	document.descarga.archivo2.value = '../tramites/remitodes.xml';
		document.descarga.propiedadesreport.value = 
		"PageHeader|4|"+'ENVIO A DESTINO'+"\n"+
		"PageHeader|5|"+'ORIGINAL - Para Registro Destino'+"\n"+
		"PageHeader|7|#_REMITO_#\n"+
		"PageHeader|8|"+'Registro Cabecera:'+"\n"+
		"PageHeader|9|#_REG_COD_INT_FAM_#\n"+
		"PageHeader|10|#_REG_DESCRIP_FAM_#\n"+
		"MainGroup|SubGroup|GroupHeader|0|Registro Destino:\n"+
		"MainGroup|SubGroup|GroupHeader|5|Registro Origen\n";
		document.descarga.primer_remito.value = nro_remito;
		document.descarga.ultimo_remito.value = nro_remito;
		document.descarga.tipo.value = 'destino';	
	
		document.descarga.propiedadesreport1.value = "ReportHeader|4|"+nro_remito+"\n"+"ReportHeader|6|"+nro_remito;
   		window.open(href="../export/imprime_ps5.php?desde="+nro_remito+"&hasta="+nro_remito, this.target, "width=250,height=140,left=260,top=230,resizable=yes");	

}

function ajax(url) {
//alert(url);
	http.open("GET", url, false);
	http.send(null);
}
</script>
<script type="text/javascript" language="JavaScript" src="bajaTramites.js"></script>
<!-- Objeto Ajax -->
<script type="text/javascript" language="JavaScript" src="../includes/ajaxobjt.js"></script>
</HEAD>
<body onLoad="inicializar(<?=$_POST['idtramite'];?>);" >
<? require_once("../includes/inc_topleft.php");
/* Contenido */
$opcion=$_POST['opcion'];
$pagina="bajaTramites.php";
require_once('../includes/inc_titulo.php');
include('bajaTramites_form.php');
?>
<form action="" method="post" name="descarga">
<input type="hidden" name="sql">
<input type="hidden" name="archivo2" />
<input type="hidden" name="propiedadesreport">
<input type="hidden" name="propiedadesreport1">
<input type="hidden" name="primer_remito">
<input type="hidden" name="ultimo_remito">
<input type="hidden" name="tipo">
<input type=hidden name=fecharetiro value="<?=$conn->SQLDate(FMT_DATE_DB, 'a.TRA_FECHA_RETIRO');?>">
</form>

<!--Contenido-->
<? require_once("../includes/inc_bottom.php"); ?>
</BODY></HTML>
<?
} //Cierro if autorizacion
?>
