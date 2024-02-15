<?
require_once("../includes/lib.php");
	
if (!empty($_GET['tipo'])) {
	switch($_GET['tipo']) {
	case 'seccionitems': //Todas los items de la seccion seleccionada para el combo
		if (!empty($_GET['seccionitems'])) {
				$Sql="Select a.ADM_ABM_ID,
							 a.ADM_ABM_NOMBRE		   	     
					  From SEGADMINABM a 		  
					  Inner Join SEGADMINSECCION s On s.ADM_SEC_ID = a.ADM_SEC_ID 
					  where a.ADM_SEC_ID=".$_GET['seccionitems']." 		  
					  Order by s.ADM_SEC_ORDEN, s.ADM_SEC_ID, a.ADM_ABM_ORDEN, a.ADM_ABM_ID";		  
				$rs = $conn->Execute($Sql);
				while (!$rs->EOF) {
					echo $rs->fields["ADM_ABM_ID"].";".
						 utf8_encode($rs->fields["ADM_ABM_NOMBRE"])."|";
						 $rs->movenext();	 			 
				}//while
		}//if vino parametro seccion
		break;
	case 'secciondes': // Todos los datos de la seccion seleccionada		
		if (!empty($_GET['secciondes'])) {
				$Sql="Select ADM_SEC_NOMBRE,
						   	 ADM_SEC_ORDEN,
							 ADM_SEC_DEFAULT,
							 ADM_SEC_ASIGNABLE    
					  From SEGADMINSECCION
					  where ADM_SEC_ID=".$_GET['secciondes'];		  
				$rs = $conn->Execute($Sql);
				if (!$rs->EOF) {
					echo utf8_encode($rs->fields["ADM_SEC_NOMBRE"]).";".
						 $rs->fields["ADM_SEC_ORDEN"].";".
						 $rs->fields["ADM_SEC_DEFAULT"].";".				 
						 $rs->fields["ADM_SEC_ASIGNABLE"];
				}//if !eof
		}//if vino parametro seccion	
		break;		
	case 'itemid':	//Todos los datos del Item Seleccionado
		if (!empty($_GET['itemid'])) {
				$Sql="Select ADM_ABM_NOMBRE,
				 		     ADM_ABM_DESCRIPCION,
							 ADM_ABM_LINK,
							 ADM_ABM_LINK_DIR,
							 ADM_ABM_LINK_PARAM,
							 ADM_ABM_LINK_PARAM_VALOR,
							 ADM_ABM_TARGET,
							 ADM_ABM_TIPO_PERM,
							 ADM_ABM_SEPARADOR,
							 ADM_ABM_DEFAULT,
							 ADM_ABM_ASIGNABLE
					  From SEGADMINABM
					  where ADM_ABM_ID=".$_GET['itemid'];		  
				$rs = $conn->Execute($Sql);
				if (!$rs->EOF) {
					echo utf8_encode($rs->fields["ADM_ABM_NOMBRE"]).";".
						 utf8_encode($rs->fields["ADM_ABM_DESCRIPCION"]).";".
						 $rs->fields["ADM_ABM_LINK"].";".
						 $rs->fields["ADM_ABM_LINK_DIR"].";".
						 $rs->fields["ADM_ABM_LINK_PARAM"].";".
						 $rs->fields["ADM_ABM_LINK_PARAM_VALOR"].";".
						 $rs->fields["ADM_ABM_TARGET"].";".
						 $rs->fields["ADM_ABM_TIPO_PERM"].";".
						 $rs->fields["ADM_ABM_SEPARADOR"].";".
						 $rs->fields["ADM_ABM_DEFAULT"].";".
						 $rs->fields["ADM_ABM_ASIGNABLE"];
				}//if !eof
		}//if vino parametro seccion	
		break;		
	case 'modsecc': // Modificacion de secciones
// Busco el Orden en que quieren ubicar el Item
        if ($_GET['orden']=='primero') {
		    $orden=0;
		}else{
			if ($_GET['orden']=='despuesde') {
			    $Sql="select ADM_SEC_ORDEN
				        from SEGADMINSECCION
					   where ADM_SEC_ID=".$_GET['despuesde'];
					   $orden=1;
			}else{
			    $Sql="select ADM_SEC_ORDEN
				        from SEGADMINSECCION
					   where ADM_SEC_ID=".$_GET['id'];
					   $orden=0;
			}
		    $rs = $conn->Execute($Sql);		
//echo $Sql;
			$orden+=$rs->fields["ADM_SEC_ORDEN"];	
		}
// Verifico el orden si existe los corro a todos			 
        if (($_GET['orden']=='primero')||($_GET['orden']=='despuesde')) {
				$Sql="select ADM_SEC_ID, ADM_SEC_ORDEN 
						from SEGADMINSECCION 
						where ADM_SEC_ORDEN>=$orden 
						  and ADM_SEC_ID!=".$_GET['id'];	
				$rs = $conn->Execute($Sql);
				while (!$rs->EOF) {	
					$nvo=$rs->fields["ADM_SEC_ORDEN"]+5;
					$Sql="update SEGADMINSECCION   
							 set ADM_SEC_ORDEN=".sqlint($nvo)." 
						   where ADM_SEC_ID=".$rs->fields["ADM_SEC_ID"];
//echo $Sql;												  
					$conn->Execute($Sql);
					$rs->MoveNext();

			    }//while
		}//if	
			$conn->StartTrans();
			try {							
// realizo la modificacion solicitada en secciones			
			$Sql="update SEGADMINSECCION		   	     
				  set ADM_SEC_NOMBRE=".sqlstring($_GET['nomsecc']).",
					  ADM_SEC_ORDEN=$orden,
					  ADM_SEC_DEFAULT=".sqlstring($_GET['default'] ? s : n)."   
					  ADM_SEC_ASIGNABLE=".sqlstring($_GET['asignable'] ? s : n)."   
				  where ADM_SEC_ID=".$_GET['id'];
				  $conn->Execute($Sql);			  
		     echo "ok";	
			} catch (exception $e) {
				dbhandleerror($e);
			}				
			$conn->CompleteTrans();	
		 break;			  
//echo $Sql;
	case 'altasecc': // Alta de una seccion
// Busco el Orden en que quieren ubicar el Item
	    $orden=10;
        if ($_GET['orden']=='primero') {
		    $orden=0;
		}else{
			if ($_GET['orden']=='despuesde') {
			    $Sql="select ADM_SEC_ORDEN
				        from SEGADMINSECCION
					   where ADM_SEC_ID=".$_GET['despuesde'];
					   $orden=1;
			}else{
			    $Sql="select ADM_SEC_ORDEN
				        from SEGADMINSECCION
					   where ADM_SEC_ID=".$_GET['id'];
					   $orden=0;
			}
		    $rs = $conn->Execute($Sql);		
//echo $Sql;
			$orden+=$rs->fields["ADM_SEC_ORDEN"];	
		}
// Verifico el orden si existe los corro a todos			 
        if (($_GET['orden']=='primero')||($_GET['orden']=='despuesde')) {
				$Sql="select ADM_SEC_ID, ADM_SEC_ORDEN 
						from SEGADMINSECCION 
						where ADM_SEC_ORDEN>=$orden 
						  and ADM_SEC_ID!=".$_GET['id'];	
//echo $Sql;												  						   						  
				$rs = $conn->Execute($Sql);
				while (!$rs->EOF) {	
					$nvo=$rs->fields["ADM_ABM_ORDEN"]+5;
					$Sql="update SEGADMINSECCION   
							 set ADM_SEC_ORDEN=".sqlint($nvo)." 
						   where ADM_SEC_ID=".$rs->fields["ADM_SEC_ID"];
//echo $Sql;												  						   
					$conn->Execute($Sql);
					$rs->movenext();
//echo $Sql;												  
			    }//while
		}//if	
			$conn->StartTrans();
			try {				
			  $Sql="insert into SEGADMINSECCION (ADM_SEC_ID,
												   ADM_SEC_NOMBRE,
												   ADM_SEC_ORDEN,
												   ADM_SEC_DEFAULT,
												   ADM_SEC_ASIGNABLE) 
										  values  (".sqlint(numerador('SEGADMINSECCION')).",
												   ".sqlstring($_GET['nomsecc']).",
													$orden,
													".sqlboolean($_GET['default'] ? 1 : 0).", 
													".sqlboolean($_GET['asignable'] ? 1 : 0).")";
//	     echo "<br>".$Sql;												
			  $conn->Execute($Sql);			  
//		      											
		     echo "ok";	
			} catch (exception $e) {
				dbhandleerror($e);
			}				
			$conn->CompleteTrans();			 
		 break;
	case 'bajasecc': // Modificacion de secciones
			$conn->StartTrans();
			try {		
			  $Sql="delete SEGADMINSEGURIDADABM where ADM_ABM_ID in (select ADM_ABM_ID from SEGADMINABM where ADM_SEC_ID=".$_GET['id'].")";				  
			  $conn->Execute($Sql);		  
			  $Sql="delete SEGADMINABM where ADM_SEC_ID=".$_GET['id'];				  
			  $conn->Execute($Sql);			  	  
			  $Sql="delete SEGADMINSECCION where ADM_SEC_ID=".$_GET['id'];				  	  
			  $conn->Execute($Sql);			  	        											
		      echo "ok";
			} catch (exception $e) {
				dbhandleerror($e);
			}				
			$conn->CompleteTrans();		  				 
	  break;
	case 'moditem': // Modificacion de un item
// Busco el Orden en que quieren ubicar el Item
        if ($_GET['ordenitem']=='primero') {
		    $orden=0;
		}else{
			if ($_GET['ordenitem']=='despuesde') {
			    $Sql="select ADM_ABM_ORDEN
				        from SEGADMINABM
					   where ADM_ABM_ID=".$_GET['despuesde'];
					   $orden=1;
			}else{
			    $Sql="select ADM_ABM_ORDEN
				        from SEGADMINABM
					   where ADM_ABM_ID=".$_GET['id'];
					   $orden=0;
			}
		    $rs = $conn->Execute($Sql);		
//echo $Sql;
			$orden+=$rs->fields["ADM_ABM_ORDEN"];	
		}
// Verifico el orden si existe los corro a todos			 
        if (($_GET['ordenitem']=='primero')||($_GET['ordenitem']=='despuesde')) {
			$conn->StartTrans();
			try {			
				$Sql="select ADM_ABM_ID, ADM_ABM_ORDEN 
						from SEGADMINABM 
						where ADM_ABM_ORDEN>=$orden 
						  and ADM_SEC_ID=".$_GET['idsecc']." 
						  and ADM_ABM_ID!=".$_GET['id'];	
				$rs = $conn->Execute($Sql);
				while (!$rs->EOF) {	
					$nvo=$rs->fields["ADM_ABM_ORDEN"]+5;
					$Sql="update SEGADMINABM   
							 set ADM_ABM_ORDEN=".sqlint($nvo)." 
						   where ADM_ABM_ID=".$rs->fields["ADM_ABM_ID"];
					$conn->Execute($Sql);
					$rs->movenext();
//echo $Sql;												  
			    }//while
			} catch (exception $e) {
				dbhandleerror($e);
			}				
			$conn->CompleteTrans();				
		}//if		
// realizo la modificacion solicitada en el item		
		if (empty($_GET['targetitem']))  { $tar=' '; }else{ $tar=$_GET['targetitem'];}        
		if (empty($_GET['tippermitem'])) { $tip=' '; }else{ $tip=$_GET['tippermitem'];} 
			$conn->StartTrans();
			try {			
				$Sql="update SEGADMINABM 
					  set ADM_SEC_ID=".sqlint($_GET['idsecc']).",				  
						  ADM_ABM_NOMBRE=".sqlstring($_GET['nomitem']).",				  
						  ADM_ABM_DESCRIPCION=".sqlstring($_GET['descitem']).",				  
						  ADM_ABM_LINK=".sqlstring($_GET['pagina']).",				  
						  ADM_ABM_LINK_DIR=".sqlstring($_GET['directorio']).",				  
						  ADM_ABM_LINK_PARAM=".sqlstring($_GET['parametro']).",
						  ADM_ABM_LINK_PARAM_VALOR=".sqlstring($_GET['valparametro']).",
						  ADM_ABM_ORDEN=$orden,
						  ADM_ABM_TARGET='$tar',
						  ADM_ABM_TIPO_PERM='$tip',
						  ADM_ABM_SEPARADOR=".sqlstring($_GET['separador']).", 
						  ADM_ABM_DEFAULT=".sqlstring($_GET['default']).", 
						  ADM_ABM_ASIGNABLE=".sqlstring($_GET['asignable'])." 
					  where ADM_ABM_ID=".$_GET['id'];
//echo $Sql;			  
			  $conn->Execute($Sql);			  		      
		     echo "ok";
			} catch (exception $e) {
				dbhandleerror($e);
			}				
			$conn->CompleteTrans();			 
		 break;
	case 'altaitem': // Alta de un item
//echo "asignable:".$_GET['asignable'];
// Busco el Orden en que quieren ubicar el Item
	    $orden=0;
		if ($_GET['ordenitem']!='primero') {			
		if ($_GET['ordenitem']=='despuesde') {
			$Sql="select ADM_ABM_ORDEN as maxord
					from SEGADMINABM
				   where ADM_ABM_ID=".$_GET['despuesde'];
//echo "<br>despuesde:".$Sql;												  						  
		}else{
//el usuario no especifico la ubicacion, por lo tanto se lo ubica ultimo				
			$Sql="select max(ADM_ABM_ORDEN) as maxord
					from SEGADMINABM
				   where ADM_SEC_ID=".$_GET['idsecc'];
//echo "<br>ultimo:".$Sql;												  						  				
//echo $Sql;												  			
		}
			$rs = $conn->Execute($Sql);							   
			if (!$rs->EOF) { $orden = ($rs->fields["maxord"]+10); }				
		}
// Verifico el orden si existe los corro a todos			 
        if (($_GET['ordenitem']=='primero')||($_GET['ordenitem']=='despuesde')) {
			$conn->StartTrans();
			try {			
				$Sql="select ADM_ABM_ID, ADM_ABM_ORDEN 
						from SEGADMINABM 
						where ADM_ABM_ORDEN>=$orden 
						  and ADM_SEC_ID=".$_GET['idsecc']." 
						  and ADM_ABM_ID!=".$_GET['id'];	
//echo "<br>".$Sql;												  						  
				$rs = $conn->Execute($Sql);
				while (!$rs->EOF) {	
					$nvo=$rs->fields["ADM_ABM_ORDEN"]+10;
					if ($nvo!=$orden) {
						$Sql="update SEGADMINABM   
								 set ADM_ABM_ORDEN=".sqlint($nvo)." 
							   where ADM_ABM_ID=".$rs->fields["ADM_ABM_ID"];
//echo "<br>".$Sql;												  						  
						$conn->Execute($Sql);
					}
					$rs->movenext();
			    }//while
//				echo 'ok';		
			} catch (exception $e) {
				dbhandleerror($e);
			}				
			$conn->CompleteTrans();				
		}//if		
// realizo la modificacion solicitada en secciones			
		if (empty($_GET['targetitem']))  { $tar=' '; }else{ $tar=$_GET['targetitem'];}        
		if (empty($_GET['tippermitem'])) { $tip=' '; }else{ $tip=$_GET['tippermitem'];}        		
			$conn->StartTrans();
			try {	
				  $id=numerador('SEGADMINABM');	
				  $Sql="insert into SEGADMINABM (ADM_ABM_ID,
												 ADM_SEC_ID,
												 ADM_ABM_NOMBRE,
												 ADM_ABM_DESCRIPCION,
												 ADM_ABM_LINK,
												 ADM_ABM_LINK_DIR,
												 ADM_ABM_LINK_PARAM,
												 ADM_ABM_LINK_PARAM_VALOR,
												 ADM_ABM_ORDEN,
												 ADM_ABM_TARGET,
												 ADM_ABM_TIPO_PERM,
												 ADM_ABM_SEPARADOR,
												 ADM_ABM_DEFAULT,
												 ADM_ABM_ASIGNABLE)
										 values (".sqlint($id).",
												 ".sqlint($_GET['idsecc']).",
												 ".sqlstring($_GET['nomitem']).",
												 ".sqlstring($_GET['descitem']).",
												 ".sqlstring($_GET['pagina']).",
												 ".sqlstring($_GET['directorio']).",
												 ".($_GET['parametro']=='' ? 'null' : sqlstring($_GET['parametro'])).",
												 ".($_GET['valparametro']=='' ? 'null' : sqlstring($_GET['valparametro'])).",												 
												 $orden,
												 ".sqlstring($tar).", 
												 ".sqlstring($tip).", 												 
												 ".sqlint($_GET['separador']).", 									 
												 ".sqlint($_GET['default']).", 
												 ".sqlint($_GET['asignable']).")"; 
//echo "<br>".$Sql;												  									 
						  $conn->Execute($Sql);	
				  $Sql="insert into SEGADMINSEGURIDADABM (ADM_ABM_ID,
												 PER_ID,
												 PERM_TIPO)
										 values (".sqlint($id).",
												  1,
												 '$tip'
												 )";									  
						  $conn->Execute($Sql);										  			  		  
//echo "<br>".$Sql;												  		      
				echo 'ok';
			} catch (exception $e) {
				dbhandleerror($e);
			}				
			$conn->CompleteTrans();
		 break;		 
	case 'bajaitem':
			$conn->StartTrans();
			try {			
				$Sql="delete SEGADMINSEGURIDADABM where ADM_ABM_ID=".$_GET['id'];
				$conn->Execute($Sql);		  	
				$Sql="delete SEGADMINABM
						where ADM_ABM_ID=".$_GET['id'];	     			  
				$conn->Execute($Sql);
				echo 'ok';		
			} catch (exception $e) {
				dbhandleerror($e);
			}				
			$conn->CompleteTrans();
		break;		
//echo $Sql;	  
	}//switch		
}//if (!empty($tipo))	

?>
