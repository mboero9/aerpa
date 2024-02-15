<?php
require_once("../includes/lib.php");
if (check_auth($usrid, $perid) && check_permission($permiso)) {
    
$datos = $_POST;

?>
<html>
<head>
    <?php require_once("../includes/inc_header.php"); ?>
</head>
<body>

<?php 


require_once("../includes/inc_topleft.php"); 
/* Contenido */
$opcion=$_POST['opcion'];
$pagina="bajaremitos.php";
//require_once('../includes/inc_titulo.php');
?>
<table width="100%" cellspacing="0" cellpadding="0" border="0" align="center">
    <tbody><tr><td valign="middle" height="35" align="center" class="titulo1" colspan="2">Detalle de tramites del remito anulado</td></tr>
</tbody></table>
<center>
<br />
<table align="center" width="70%">
    <tr class=celdatexto><td align="center">
    Detalle de tramites para el Remito: <b><?=$datos['rem']?></b></td></tr>
</table>
<br />
<b>Tramites activos</b>
<?php

    /* Cargo el comboBox de operadores */
    $sql_operadores = "SELECT usr_id, usr_apellido + ' ' + usr_nombre as nombre, usr_username FROM usuario ORDER BY nombre";
    $rs_operadores = $conn->Execute( $sql_operadores );
    $combo_operadores = "";
    $usuarios = array();
    
    while(!$rs_operadores->EOF){
        
        $combo_operadores.= "<option value='".$rs_operadores->fields['usr_id']."'>".$rs_operadores->fields['nombre']."</option>";
        $usuarios[$rs_operadores->fields['usr_id']]= $rs_operadores->fields['usr_username'];
        $rs_operadores->movenext();
    }


    // Traigo la lista de tramites del registro de BAJA del Remito
    
    $sql_lista_tramites = "SELECT rem_tramites FROM baja_remito WHERE rem_numero = '".$datos['rem']."'";
    $rs_lista_tramites = $conn->Execute($sql_lista_tramites);
    $lista_tramites = $rs_lista_tramites->fields['rem_tramites'];


    //Luego armo el listado de los tramites que puedan estar vivos
    //$sql_tramites = "SELECT * FROM tramite WHERE tra_codigo IN (".$lista_tramites.") ORDER BY tra_codigo DESC";
    
    $sql_tramites = "SELECT *, ro.reg_descrip as reg_ori, rd.reg_descrip as reg_des  
                        FROM tramite , reg_autom as ro  , reg_autom as rd 
                        WHERE  tramite.reg_codigo_ori = ro.reg_codigo
                        AND tramite.reg_codigo_des = rd.reg_codigo
                        AND tra_codigo IN (".$lista_tramites.")
                        ORDER BY tra_codigo DESC";

    if ( !trim( $lista_tramites )  == "" ) {$rs_tramites = $conn->Execute($sql_tramites);}
    
    if ( ($rs_tramites->EOF) || (  trim( $lista_tramites ) ) == "" ){?>
        <br /><br />
        <center><font color="red"><b>El remito anulado no posee tramites activos.</b></font></center>
    <?php
        
    }else{
        
 ?>


<table class="tablaconbordes" >
    <tr>
        <th class="celdatitulo">Dominio</th>
        <th class="celdatitulo">Voucher</th>
        <th class="celdatitulo">Usr carga</th>
        <th class="celdatitulo">Reg Origen</th>
        <th class="celdatitulo">Reg Destino</th>
    </tr>
<?php

    $clase = "1";
    while ( !$rs_tramites->EOF ){
    ?>
        <tr class="fondotabla<?=$clase?>" onmouseout="this.className = 'fondotabla<?=$clase?>';" onmouseover="this.className = 'fondoconfirmacion';">
            <td class="celdatexto" align="center"><?=$rs_tramites->fields['tra_dominio']?></td>
            <td class="celdatexto" align="center"><?=$rs_tramites->fields['tra_nro_voucher']?></td>
            <td class="celdatexto" align="center"><?=$usuarios[$rs_tramites->fields['usr_id_carga']]?></td>
            <td class="celdatexto" align="center"><?=$rs_tramites->fields['reg_ori']?></td>
            <td class="celdatexto" align="center"><?=$rs_tramites->fields['reg_des']?></td>
        </tr>    
    <?php
        $rs_tramites->MoveNext();
        if ( $clase == "1" ){$clase = "2";}else{$clase = "1";}
    }//end While iteracion de registros

?>

</table>

<?php       
        
        
    } //end IF datos vacios
?>

<br /><br />
<b>Tramites dados de baja</b>

<?php
    
    //$sql_bajas = "SELECT * FROM baja_tramite WHERE tra_codigo IN (".$lista_tramites.") ORDER BY tra_codigo DESC";
    
    $sql_bajas = "SELECT *, ro.reg_descrip as reg_ori, rd.reg_descrip as reg_des , convert(varchar,baja_fecha,  105)+ ' '+convert(varchar,baja_fecha,   108 ) as baja_fecha2 
                        FROM baja_tramite , reg_autom as ro  , reg_autom as rd 
                        WHERE  baja_tramite.reg_codigo_ori = ro.reg_codigo
                        AND baja_tramite.reg_codigo_des = rd.reg_codigo
                        AND tra_codigo IN (".$lista_tramites.")
                        ORDER BY baja_fecha DESC";
    
    if ( !trim( $lista_tramites )  == "" ) {$rs_bajas = $conn->Execute( $sql_bajas );}
    
        if ( ($rs_bajas->EOF) || (  trim( $lista_tramites ) ) == "" ){?>    
        <center><font color="red"><b>El remito anulado no posee tramites dados de baja.</b></font></center>
<?php   }else{
    
?>


<table class="tablaconbordes" >
    <tr>
        <th class="celdatitulo">Dominio</th>
        <th class="celdatitulo">Voucher</th>
        <th class="celdatitulo">Usr carga</th>
        <th class="celdatitulo">Reg Origen</th>
        <th class="celdatitulo">Reg Destino</th>
        <th class="celdatitulo">Usuario Baja</th>
        <th class="celdatitulo">Fecha Baja</th>
    </tr>
<?php

    $clase = "1";
    while ( !$rs_bajas->EOF ){
    ?>
        <tr class="fondotabla<?=$clase?>" onmouseout="this.className = 'fondotabla<?=$clase?>';" onmouseover="this.className = 'fondoconfirmacion';">
            <td class="celdatexto" align="center"><?=$rs_bajas->fields['tra_dominio']?></td>
            <td class="celdatexto" align="center"><?=$rs_bajas->fields['tra_nro_voucher']?></td>
            <td class="celdatexto" align="center"><?=$usuarios[$rs_bajas->fields['usr_id_carga']]?></td>
            <td class="celdatexto" align="center"><?=$rs_bajas->fields['reg_ori']?></td>
            <td class="celdatexto" align="center"><?=$rs_bajas->fields['reg_des']?></td>
            <td class="celdatexto" align="center"><?=$usuarios[$rs_bajas->fields['baja_usuario_id']]?></td>
            <td class="celdatexto" align="center"><?=$rs_bajas->fields['baja_fecha2']?> Hs</td>
        </tr>    
    <?php
        $rs_bajas->MoveNext();
        if ( $clase == "1" ){$clase = "2";}else{$clase = "1";}
    }//end While iteracion de registros
} //end IF datos vacios
?>
</table>
</center>  

<center>
    <br /><br />
    <input type=button class="botonout" name=botvolver value="Volver"  onMouseOver="this.className = 'botonover';" onMouseOut="this.className = 'botonout';" onClick="goMenu('bajaremitos.php');">
</center>
<?php



}?>