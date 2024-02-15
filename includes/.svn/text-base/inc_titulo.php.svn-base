<? $titulo1="Titulo no encontrado";
   if (!isset($opcion)) { 
      $parametro=" and ADM_ABM_LINK_PARAM_VALOR is null"; 
   } else {
      $parametro=" and ADM_ABM_LINK_PARAM_VALOR='$opcion'";
   }	  
   $Sql="select ADM_ABM_NOMBRE from SEGADMINABM where ADM_ABM_LINK='$pagina'".$parametro;
   
   $rs = $conn->Execute($Sql);
   if (!$rs->EOF) { 
   $titulo1=$rs->fields["ADM_ABM_NOMBRE"].' '.strtoupper($rs->fields["adm_abm_link_param_valor"]); }
?>
<table border=0 cellspacing=0 width=100% cellpadding=0 align="center">
<tr><td align="center" colspan=2 class="titulo1" height="35" valign=middle><? echo $titulo1; ?></td></tr>
</table>
