<?

/*Devuelve el tipo de mesa (Masculina, Femenina, Mixta))*/
/*fSex(tipo de mesa))*/
function fSex($tipo_mesa)
{
    if ($tipo_mesa != "")
    $tipo_mesa = strtoupper($tipo_mesa);
    {
        if ($tipo_mesa == "M")
        {
            $tipo_mesa = "Masculina";
            return $tipo_mesa;
        }
        if ($tipo_mesa == "F")
        {
            $tipo_mesa = "Femenina";
            return $tipo_mesa;
        }
        if ($tipo_mesa == "X")
        {
            $tipo_mesa = "Mixta";
            return $tipo_mesa;
        }
    }
}

/*Devuelve fecha en formato especificado*/
/*verfecha(fecha,caracter separador,mostrar hora)*/
function verFecha($fecha,$separa="",$formato="",$hora=0){
	global $conn;

	if($hora!=0){
		$fecha_hora=substr($fecha,10);
	} else{
		$fecha_hora="";
	}

	$fecha=substr($fecha,0,10);
	$array=explode("-",$fecha);

	$array_format=explode("-",$formato);

	if($array_format[0]=="dd"){$pos1=2;}
	else if($array_format[0]=="mm"){$pos1=1;}
	else if($array_format[0]=="yyyy"){$pos1=0;}

	if($array_format[1]=="dd"){$pos2=2;}
	else if($array_format[1]=="mm"){$pos2=1;}
	else if($array_format[1]=="yyyy"){$pos2=0;}

	if($array_format[2]=="dd"){$pos3=2;}
	else if($array_format[2]=="mm"){$pos3=1;}
	else if($array_format[2]=="yyyy"){$pos3=0;}

	$fecha="$array[$pos1]$separa$array[$pos2]$separa$array[$pos3]".$fecha_hora;

	return $fecha;
}

/********************************************************************************/
//Devuelve "$caracter" si la cadena está vacía, sino devuelve la cadena

function checkVar($str,$caracter="&nbsp;"){
	if($str==""){return $caracter;}
	else{return $str;}
}

/*********************************************************************/
/*function getParametro($nombre){
	global $conn;
	global $eleid;

	if (is_numeric($eleid)) {
		$sql = "select parValor From parametroseleccion Where eleid=$eleid and parnombre='$nombre'";
		$rspar = $conn->execute($sql);
		if (!$rspar->eof) {
			return $rspar->field("parValor");
		}
	}

	$sql = "select parValor from parametros where parNombre='$nombre'";
	$rspar = $conn->execute($sql);

	return $rspar->field("parValor");
}*/

/*********************************************************************/
function getParametroUsuario($nombre,$usuario){
	global $conn;

	$sql = "select paruValor from paramUsuarios where paruNombre='$nombre' and usrID=$usuario ";
	$rsparu = $conn->execute($sql);

	return $rsparu->field("paruValor");
}

/*********************************************************************/

function getErrMsg($nombre){
	global $conn;

	$sql = "select emsgTexto from errorMsg where emsgNombre='$nombre'";
	$rspar = $conn->execute($sql);

	return $rspar->field("emsgTexto");
}

/*********************************************************************/
//se usa para correjir formato de archivos separados por tabulaciones
//convierte ["xxxx ""x"" xxx"] en [xxxx "x" xxx]
function tabFormat($var){
	$first = substr($var,0,1);
	if($first == "\""){
		$var = substr(substr($var,1),0,-1);
		$var = str_replace("\"\"","\"",$var);
	}
	return $var;
}

/*********************************************************************/
//se usa para correjir formato al crear archivos csv
//convierte [xxxx "x" xxx] en ["xxxx ""x"" xxx"]
function tabFormat2($var){
	$var = "\"".str_replace("\"","\"\"",$var)."\"";
	return $var;
}

/*********************************************************************/
// esta funcion me devulve el nombre y la razon social con el guioncito
// en el medio cuando corresponda

function NombreyRazonSoc($Nombre,$RazonSoc){

	$rslt = $Nombre;

	if ($Nombre && $RazonSoc){
		$rslt .= " - ";
		}

	$rslt .= $RazonSoc;

	return $rslt;
	}

/*********************************************************************/
//valida caracteres alfanumericos
function validar_alfa($valor)
{
	return (!ereg("[^0-9 A-Z a-z ñ Ñ á é í ó ú \n\r\. _-]",$valor));
}

/*********************************************************************/
//valida caracteres alfanumericos, la barra del 7 (/) y la comilla simple
function validar_Localidad($valor)
{
	return (!ereg("[^0-9 A-Z a-z ñ Ñ á é í ó ú / ' \n\r\. _-]",$valor));
}

/*********************************************************************///valida caracteres alfanumericos y la barra del 7 (/)
function validar_alfaPlus($valor)
{
	return (!ereg("[^0-9 A-Z a-z ñ Ñ á é í ó ú / \n\r\. _-]",$valor));
}

/*********************************************************************/
//valida letras

function validar_letras($valor)
{
	return (!ereg("[^A-Z a-z ñ Ñ á é í ó ú \n\r\. _-]",$valor));
}

/*********************************************************************/
//valida numeros

function validar_numero($valor)
{
	return (!ereg("[^0-9]",$valor));
}

/*********************************************************************/
//valida id

function validar_id($valor)
{
	return (!ereg("[^0-9]",$valor));
}

/*********************************************************************/
//valida codigo postal

function validar_codPostal($valor)
{

	return(ereg("(^[1-9]{1}[0-9]{3}$)|(^[A-Z]{1}[1-9]{1}[0-9]{3}[A-Z]{3}$)",$valor));

	return(false);
}

/*********************************************************************/
//valida fecha (yyyy-mm-dd hh:mm:ss)

function validar_fecha($date){
	return (ereg ('^[0-9]{1,4}-[0-9]{1,2}-[0-9]{1,2} [0-2]{0,1}[0-9]{1}:[0-5]{0,1}[0-9]{1}:[0-5]{0,1}[0-9]{1}$', $date));
}

/*********************************************************************/
//valida mails

function validar_email($valor)
{
	 return (ereg("^[a-z0-9][a-z0-9\_\.\%\-]+@[a-z0-9\-]+(\.[a-z0-9\-]+)+$",$valor));
}

/*********************************************************************/
//reemplaza caracteres en textos para compatibilidad con el Oracle

function checkText($valor){
	global $con_motor;

    if($con_motor=="ORACLE"){
	    $valor = str_replace("'","''",$valor);
	    $valor = str_replace("\\","\\\\",$valor);
    }else if($con_motor=="MYSQL"){
	    $valor = str_replace("'","''",$valor);
	    $valor = str_replace("\\","\\\\",$valor);
	    $valor = str_replace("\"","\\\"",$valor);
    }

	return $valor;
}

/*********************************************************************/
function getErrImport($valor){
	global $conn;

	$sql = "select eriDescripcion
		from errorImportacion
		where eriID=$valor";
	$rsimp = $conn->execute($sql);

	return $rsimp->field("eriDescripcion");
}

/*********************************************************************/
//muestra tamaños de archivo con formato (KB, MB) segun corresponda
function verSize($bytes){
	if($bytes < 1000){
		return "$bytes B";
	}
	if(($bytes > 1000 || $bytes == 1000)&& $bytes < 1000000){
		$kbytes=number_format($bytes/1000, 2, ",",".");
		return "$kbytes KB";
	}
	if(($bytes > 1000000 || $bytes == 1000000)){
		$Mbytes=number_format($bytes/1000000, 2, ",",".");
		return "$Mbytes MB";
	}
}

/*********************************************************************/
//separa valores del in() del delete de a 50 y va ejecutando el query
//split_in(array de valores del in(), query sin el in() ej:'delete from noticias where notID in '  , cantidad de valores que coloca en cada in() generado )
//se usan porque el oracle no permite mas de 1000 caracteres dentro del in()

function delete_in($arrayID,$sql,$part="50"){
	global $conn;

	for($i=0,$l=1,$sep="",$in=""; $i<count($arrayID) ;$i++,$l++){
		$in .= $sep.$arrayID[$i];
		if($l==$part){
			$conn->execute($sql."($in)");
			//echo $sql."($in)<br>";
			$l = 1;
			$sep = "";
			$in = "";
		}else{
			$sep = ",";
		}
	}

	if($in){
		$conn->execute($sql."($in)");
		//echo $sql."($in)<br>";
	}
	return;
}
/*********************************************************************/
//separa valores del in() del select de a 50 , va ejecutando el query, y devuelve un array con los valores del campo seleccionado
//split_in(array de valores del in(), query sin el in() ej:'select notID from noticias where notID in ' , campo del select que devuelvo , cantidad de valores que coloca en cada in() generado )
//se usan porque el oracle no permite mas de 1000 caracteres dentro del in()

function select_in($arrayID,$sql,$campos,$part="50",$condicion=""){
	global $conn;

	$arrayCampos = explode(",",$campos);

	for($i=0,$l=1,$sep="",$in=""; $i<count($arrayID) ;$i++,$l++){
		$in .= $sep.$arrayID[$i];

		if($l==$part){
			$rs = $conn->execute($sql."($in)".$condicion);
			while(!$rs->eof){
				for($j=0; $j<count($arrayCampos) ;$j++){
					$arraySQL[$arrayCampos[$j]][] = $rs->field($arrayCampos[$j]);
				}
				$rs->movenext();
			}
			$l = 1;
			$sep = "";
			$in = "";
		}else{
			$sep = ",";
		}
	}

	if($in){
			$rs = $conn->execute($sql."($in)".$condicion);
		while(!$rs->eof){
			for($j=0; $j<count($arrayCampos) ;$j++){
				$arraySQL[$arrayCampos[$j]][] = $rs->field($arrayCampos[$j]);
			}
			$rs->movenext();
		}
	}
	return $arraySQL;
}
/***************************************************************************/
function diaEsp($cadena)
{

	$dia = ucfirst(trim(substr($cadena,0,strpos(trim($cadena),"&"))));
	$fecha = substr($cadena,strpos(trim($cadena),"&"),strlen($cadena));

	switch($dia)
	{
		case "Lunes":
		case "Monday":
			return "Lunes $fecha";

		case "Martes":
		case "Tuesday":
			return "Martes $fecha";

		case "Miércoles":
		case "Miercoles":
		case "Wednesday":
			return "Miércoles $fecha";

		case "Jueves":
		case "Thursday":
			return "Jueves $fecha";

		case "Viernes":
		case "Friday":
			return "Viernes $fecha";

		case "Sábado":
		case "Sabado":
		case "Saturday":
			return "Sábado $fecha";

		case "Domingo":
		case "Sunday":
			return "Domingo $fecha";

		default:
			return "";
	}
}
/***************************************************************************/
// Funciones de correccion de lenguaje

function ReplaceHtmlSpecialChars($str){
	//primero reemplazo el & sino reemplaza los & de los ya reemplazados
	$char_orig = array("&","Á","É","Í","Ó","Ú","á","é","í","ó","ú","Ü","ü","ñ","Ñ","º","°","ª","¡","¿","´","<",">");
	$char_dest = array("&amp;","&Aacute;","&Eacute;","&Iacute;","&Oacute;","&Uacute;","&aacute;","&eacute;","&iacute;","&oacute;","&uacute;","&Uuml;","&uuml;","&ntilde;","&Ntilde;","&ordm;","&deg;","&ordf;","&iexcl;","&iquest;","&acute;","&lt;","&gt;");

	$str=str_replace($char_orig,$char_dest,$str);

	//$str=htmlentities($str,ENT_NOQUOTES);
	return($str);
}

function RestoreHtmlSpecialChars($str){
	//ultimo reemplazo el & sino reemplaza los & de los ya reemplazados
	$char_orig = array("&Aacute;","&Eacute;","&Iacute;","&Oacute;","&Uacute;","&aacute;","&eacute;","&iacute;","&oacute;","&uacute;","&Uuml;","&uuml;","&ntilde;","&Ntilde;","&ordm;","&deg;","&ordf;","&iexcl;","&iquest;","&acute;","&lt;","&gt;","&amp;");
	$char_dest = array("Á","É","Í","Ó","Ú","á","é","í","ó","ú","Ü","ü","ñ","Ñ","º","°","ª","¡","¿","´","<",">","&");

	$str=str_replace($char_orig,$char_dest,$str);
	return($str);
}

function htmlspecialchars2($str){
	//primero reemplazo el & sino reemplaza los & de los ya reemplazados
	$char_orig = array("\"","'");
	$char_dest = array("&quot;","&#039;");

	$str=str_replace($char_orig,$char_dest,$str);

	//$str=htmlentities($str,ENT_NOQUOTES);
	return($str);
}


/***************************************************************************/
//proporciona imagen a rectangulo $w,$h

function imageAdjust($w,$h,$image){
	global $app_path,$var_path;

	if(file_exists($app_path.$image)){
		$imgdat = getimagesize($app_path.$image);
		$imgw = $imgdat[0];
		$imgh = $imgdat[1];

		if($imgw>$w){
			$prop = $w/$imgw;
	    $imgw = $w;
	    $imgh = $imgh*$prop;
		}
		if($imgh>$h){
			$prop = $h/$imgh;
			$imgh = $h;
			$imgw = $imgw*$prop;
		}

		$imgw = " width=$imgw ";
		$imgh = " height=$imgh ";

		return "<img border=0 ".$imgw.$imgh." src='".$var_path.$image."'>";
	}else{
		return false;
	}
}

/***************************************************************************/
//recibe una cadena de texto y arma un array de palabras , devuelve la cantidad de palabras reales y tasables
//si $errormsg esta en 1 devuelve dos valores, si falla devuelve cual fue el caracter invalido
//si validar esta en uno ademas chequea que las palabras no tengan caracteres
//ni palabras inválidas
function countWords($texto,$validar=0,$errormsg=0){
	global $conn;

		//independientemente de lo como llega $validar veo si el parametro indica validar los caracteres y palabra
		if(getParametro("VALIDAR_TEXTO_TEL")=='N'){$validar=0;}

	if($validar==1){
		//chequeo palabras invalidas
		$sql = "select * from palabrasInv";
		$rspalinv = $conn->execute($sql);
		while(!$rspalinv->eof){
			if(strpos($texto,$rspalinv->field("palabra"))!==false){
							if($errormsg<>1){return false;}
							else{
								return array(false,$rspalinv->field("palabra"));
							}
			}
			$rspalinv->movenext();
			}
	}

	unset($arr_words);
	unset($arr_carval);

	$arr_words=preg_split("/\s+/",trim($texto),-1, PREG_SPLIT_NO_EMPTY);
	$cantWords["reales"] = count($arr_words);
	$cantWords["tasables"] = 0;
	for($i=0;$i<count($arr_words);$i++){
		$cantWords["tasables"] += ceil(strlen(RestoreHtmlSpecialChars($arr_words[$i]))/13);
		if($validar==1){
		//chequeo caracteres válidos
		$sql = "select * from caracteresVal";
		$rscarval = $conn->execute($sql);
			while(!$rscarval->eof){
				$arr_carval[] = trim($rscarval->field("caracterhtml"));
				$rscarval->movenext();
				}
				for($k=0;$k<strlen($arr_words[$i]);){
					unset($char_html);
					//echo $char_html."--".$char_html."--".strlen($char_html)."--".$arr_words[$i][$k-1]."--".strlen($arr_words[$i])."<br>";
					while((!isset($char_html) || $char_html=="&" || (strlen($char_html)>1 && $arr_words[$i][$k-1]!=";") || ($char_html=="&amp;" && $arr_words[$i][$k]=="#")) && ($k<strlen($arr_words[$i]))){
					$char_html .= $arr_words[$i][$k];
					$k++;
										}
									//echo $char_html."<br>";
									if(!in_array($char_html,$arr_carval)){
										if($errormsg<>1){return false;}
										else{
											return array(false,str_replace("&amp;","&",$char_html));
										}

									}
				}
			}
	}
	if($errormsg<>1){
			return $cantWords;
		}else{
			return array(true,$cantWords);
		}
}
/***************************************************************************/

//chequeo palabras invalidas
//no se esta usando
function checkWords($string){
			/*
			$sql = "select * from caracteresVal";
			$rscarval = $conn->execute($sql);

			while(!$rscarval->eof){
				$pos = strpos($string,$rscarval->field("caracter"));
				if ($pos === false){
					return false;
					}
				$rscarval->movenext();
			}
			*/
			//chequeo palabras invalidas
			$sql = "select * from palabrasInv";
			$rspalinv = $conn->execute($sql);

			while(!$rspalinv->eof){
				$pos = strpos($string,$rspalinv->field("palabra"));
				if ($pos !== false){
					return false;
					}
				$rspalinv->movenext();
			}
	}
/*********************************************************************/
function getProvincia($provID){
	global $conn;

	$sql = "select provNombre from provincias where provID='$provID'";
	$rsprov = $conn->execute($sql);

	return $rsprov->field("provNombre");
}
/*********************************************************************/
function getTelprodID(){
	global $conn,$ck_divID;

	$sql = "select prodID from productos WHERE prodCodigo=".getParametro("PRODUCTO_TEL")." AND divID=$ck_divID";
	$rsprod = $conn->execute($sql);
	//echo $sql."<br>";

	return $rsprod->field("prodID");
}

/**********************************************************************************/
//permite agregar alarmas y envia mails a los responsables
function alarmaAdd($mensaje,$param,$tipo,$eleID="",$atach="")
{
        global $conn,$condate,$desarrollo;
		$PHP_SELF = $_SERVER["PHP_SELF"];
		$mensaje = substr($mensaje, 0, 4000);
        //busco el parametro
        $sql = "select paal_Valor from paramAlarma where paal_Nombre='$param'";
        $rs = $conn->execute($sql);

        if($rs->numrows)
        {
                $email = $rs->field("paalValor");
                if($atach == ""){
					mail($email, "Alarma proceso [".substr($PHP_SELF,strrpos($PHP_SELF,"/")+1,-4)."]", $mensaje,"From: ".getParametro("MAIL_FROM_ALARMAS"));
				}else{
					//Envio archivo por mail
					$msg = $mensaje."\r\n";
					$to = $email;
					$from = getParametro("MAIL_FROM_ALARMAS");
					$subj = "Alarma proceso [".substr($PHP_SELF,strrpos($PHP_SELF,"/")+1,-4)."]";

					$fattach = substr($atach,strrpos($atach,"/")+1);

					$a_name = "phpmail";
					$timer = time();
					$abound = "00-".$a_name."-".$timer."";
					$stime = date("r",time());
					$mhead = "Date: ".$stime."\r\n";
					$mhead .= "From: ".$from."\r\n";
					//$mhead .= "To: ".$to."\r\n";
					$mhead .= "X-Priority: 1 (High)\r\n";
					$mhead .= "X-Mailer: <PHP MAILER>\r\n";
					$mhead .= "MIME-Version: 1.0\r\n";
					$mhead .= "Content-Type: multipart/mixed; boundary=\"$abound\"\r\n";
					$mhead .= "Content-Transfer-Encoding: 8bit\r\n";

					//quito los \r del mensaje porque a algunos servidores no les gusta
					$msg = preg_replace("/\r\n/i", "\n", $msg);
					$msgbody = "--".$abound."";
					$msgbody .= "\r\n";
					$msgbody .= "Content-Type: text/plain; charset=\"ISO-8859-1\"\r\n";
					$msgbody .= "Content-Transfer-Encoding: 8bit;\r\n\r\n";
					$msgbody .= "$msg";
					$msgbody .= "\r\n";
					$msgbody .= "\r\n";
					$msgbody .= "\r\n";
					$ahead = "--".$abound."";
					$ahead .= "\r\n";
					$ahead .= "Content-Type: application/octet-stream";
					$ahead .= "\r\n";
					$ahead .= "Content-Transfer-Encoding: base64";
					$ahead .= "\r\n";
					$ahead .= "Content-Disposition: attachment; filename=\"$fattach\"";
					$ahead .= "\r\n\r\n";
					set_magic_quotes_runtime(0);
					$attachment = fread(fopen("$atach", "rb"), filesize("$atach"));
					$attachment = chunk_split(base64_encode($attachment));

					//quito los \r del attach porque a algunos servidores no les gusta
					$attachment = preg_replace("/\r\n/i", "\n", $attachment);
					$ahead .= "$attachment";
					$ahead .= "\r\n";
					$msgbody .= "$ahead";
					set_magic_quotes_runtime(get_magic_quotes_gpc());
					$msgbody .= "--".$abound."--";

					mail($to, $subj, $msgbody, $mhead);

					/*FIN ENVIO MAIL*/
				}
        }

        if($eleID=="")
        {
                $tmp1 = new Date(date("Y-m-d H:i:s",time()));
				$tmp2 = new Date(date("Y-m-d H:i:s",time()));
			
				$sqins = "insert into alarma (alm_ID,alm_Fec_Gen,alm_Fec_Estado,alm_Descripcion) "
                                        ." values (".numerador('alarma').",".sqldate($tmp1->format(FMT_DATE_ISO)).",".sqldate($tmp2->format(FMT_DATE_ISO)).",'".substr($mensaje,0,3995)."')";
        }
        else
        {
                $tmp1 = new Date(date("Y-m-d H:i:s",time()));
				$tmp2 = new Date(date("Y-m-d H:i:s",time()));
				$sqins = "insert into alarma (alm_ID,alm_Fec_Gen,alm_FecEstado,alm_Descripcion,proc_id) "
                                        ." values (.".numerador('alarma').",".sqldate($tmp1->format(FMT_DATE_ISO)).",".sqldate($tmp1->format(FMT_DATE_ISO)).",'".substr($mensaje,0,3995)."',$eleID)";
        }
        $conn->execute($sqins);

        if($desarrollo)
        {
                echo "<br>$mensaje<br>";
        }

        if($conn->error!="")
                return false;
        else
                return true;



}

?>
