<?php
require_once("../includes/lib.php");

if (check_auth($usrid, $perid) && check_permission($permiso)) {
	$ordencolumna = (array_key_exists("ordencolumna", $_POST) ? $_POST["ordencolumna"] : "2");
// permiso ok

// Armar condicion
$where = "";
$fbaja = true;
if ($_POST["f_descripcion"] != "") {
	$where .= ($where=="" ? "" : " And ")." Lower(r.REG_DESCRIP) Like " .
		sqlstring(strtolower($_POST["f_descripcion"]) . "%");
} // end if
if ($_POST["f_provincia"] != "") {
	$where .= ($where=="" ? "" : " And ")." Lower(r.PRO_CODIGO) = " . sqlstring($_POST["f_provincia"]);
} // end if
if ($_POST["f_region"] != "") {
	$where .= ($where=="" ? "" : " And ")." Lower(r.RGI_CODIGO) = " . sqlstring($_POST["f_region"]);
} // end if
if ($_POST["f_tipo"] != "") {
	$where .= ($where=="" ? "" : " And ")." Lower(r.REG_TIPO) = " . sqlstring(strtolower($_POST["f_tipo"]));
} // end if
if ($_POST["f_codigo"] != "") {
	$where .= ($where=="" ? "" : " And ")." Lower(r.REG_COD_INT) = " . sqlstring(strtolower($_POST["f_codigo"]));
} // end if
if ($where=="") {
	$where .= ($where=="" ? "" : " And ")." r.REG_FECHA_BAJA is Null";
	$fbaja = false;	
} // end if

// Set page
$page = (is_numeric($_POST["page"]) ? $_POST["page"] : 1);

// Obtener cantidad, pero no repetir la consulta si
// se esta navegando por paginas
if (!is_numeric($_POST["rows"])) {
	$sql = "Select Count(*) as CANTIDAD " .
	"From REG_AUTOM r " .
	"Inner Join PROVINCIA p On r.PRO_CODIGO = p.PRO_CODIGO " .
	"Inner Join REGION re On r.RGI_CODIGO = re.RGI_CODIGO " .
	"Where " . $where;
//echo $sql;	
	$rs = $conn->Execute($sql);
	$totalrows = $rs->fields["CANTIDAD"];
} else {
	$totalrows = $_POST["rows"];
} // end if rows
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>

<head>
<?php require_once("../includes/inc_header.php"); ?>
<script type="text/javascript">
function go(accion) {
	if ((accion != <?=(ABM_NEW); ?>) && (document.frm.id.value == "")) {
		alert("Debe seleccionar una fila");
	} else {
		document.frm.accion.value = accion;
		document.frm.submit();
	} // end if id
} // end go

function go_page(page) {
	document.frmf.page.value = page;
	document.frmf.submit();
}

function changed_filter() {
	document.frmf.rows.value = "";
}

function selrow(id) {
	if (document.frm.id.value != "") {
		document.getElementById("row_" + document.frm.id.value).className =
			document.frm.cls.value;
	}
	document.frm.id.value = id;
	rw = document.getElementById("row_" + id);
	document.frm.cls.value = rw.className;
	rw.className = "fondoconfirmacion";
} // end selrow

function ordenar(columna)
{
	var col = document.frmf.ordencolumna;

	if (col.value == columna) {
		col.value = (columna.indexOf(' desc') > -1 ? columna.replace(' desc', '') : columna + ' desc');
	} else {
		col.value = columna;
	}

	document.frmf.submit();
} // end ordenar
</script>
</head>

<body>
<?php require_once('../includes/inc_topleft.php'); ?>

<p class=titulo1>Registros Automotor</p>

<form action="registro-edit.php" method=post name=frm>
<input type=hidden name=id>
<input type=hidden name=cls>
<input type=hidden name=accion>
</form>
<?php
try {
$sql = "Select r.REG_CODIGO,r.REG_DESCRIP,r.REG_CALLE,r.REG_NUMERO,r.REG_PISO," .
	"	r.REG_CPA,r.REG_LOCALIDAD,p.PRO_DESCRIP,re.RGI_DESCRIP," .
	"	r.REG_COD_INT,r.REG_TIPO ," .
	$conn->SQLDate(FMT_DATE_DB, "r.REG_FECHA_BAJA")." as REG_FECHA_BAJA	".
	"From REG_AUTOM r " .
	"Inner Join PROVINCIA p On r.PRO_CODIGO = p.PRO_CODIGO " .
	"Inner Join REGION re On r.RGI_CODIGO = re.RGI_CODIGO " .
	"Where " . $where . " " .
	"Order by " . $ordencolumna;
//echo $sql;	
$rs = $conn->PageExecute($sql,RECS_PER_PAGE,$page);
?>
<p align=center>
<table class=tablanormal>

<thead>
<?php if ($totalrows) { ?>
<tr><td colspan="8"><?php navigationbar($page,$totalrows); ?></td></tr>
<?php } // end if totalrows ?>

<tr><td colspan="8">
	<!-- Filtros -->
	<form action="" method="post" name="frmf">
	<input type="hidden" name="page" value="">
	<input type="hidden" name="rows" value="<?=($totalrows); ?>">
	<input type="hidden" name="ordencolumna" value="<?=($ordencolumna); ?>">
	<table>
	<tr><td class="celdatexto">Codigo:</td>
		<td class="celdatexto"><select name="f_codigo" title="Codigo"
			onchange="changed_filter();">
			<option value="">&nbsp;</option>
<?php fill_combo("Select REG_COD_INT,REG_COD_INT From REG_AUTOM Order by 1",$_POST["f_codigo"]); ?>
			</select></td></tr>
	<tr><td class="celdatexto">Descripci&oacute;n:</td>
		<td class="celdatexto"><input type="text" name="f_descripcion"
			maxlength="50" value="<?php echo($_POST["f_descripcion"]); ?>"
			onchange="changed_filter();"
			title="Descripci&oacute;n o comienzo de la misma" /></td>
		<td rowspan="4"><input type="submit"
			class="botonout" value="Buscar" /></td></tr>

	<tr><td class="celdatexto">Provincia:</td>
		<td class="celdatexto"><select name="f_provincia" title="Provincia"
			onchange="changed_filter();">
			<option value="">&nbsp;</option>
<?php fill_combo("Select PRO_CODIGO, PRO_DESCRIP From PROVINCIA Order by 2",
	$_POST["f_provincia"]); ?>
			</select></td></tr>

	<tr><td class="celdatexto">Regi&oacute;n:</td>
		<td class="celdatexto"><select name="f_region" title="Regi&oacute;n"
			onchange="changed_filter();">
			<option value="">&nbsp;</option>
<?php fill_combo("Select RGI_CODIGO, RGI_DESCRIP From REGION Order by 2",
	$_POST["f_region"]); ?>
			</select></td></tr>

	<tr><td class="celdatexto">Tipo:</td>
		<td class="celdatexto"><select name="f_tipo" title="Tipo"
			onchange="changed_filter();">
			<option value="">&nbsp;</option>
<?php fill_combo_arr(array(TREG_DESTINO => TREGLBL_DESTINO, TREG_AMBOS => TREGLBL_AMBOS),
	$_POST["f_tipo"]); ?>
			</select></td></tr>
	</table>
	</form>
	<!-- Fin filtros -->
	</td></tr>

<?php if (!$totalrows) { ?>

<tr><td colspan="8" class="textoerror">No hay registros con las condiciones especificadas</td></tr>
</thead>

<?php } else { ?>

<tr><th class=celdatituloColumna><span style="cursor: pointer" onClick="ordenar('r.REG_DESCRIP');">Descripci&oacute;n</span></th>
	<th class=celdatituloColumna><span style="cursor: pointer" onClick="ordenar('r.REG_CALLE,r.REG_NUMERO,r.REG_PISO');">Direcci&oacute;n</span></th>
	<th class=celdatituloColumna><span style="cursor: pointer" onClick="ordenar('r.REG_CPA');">CPA</span></th>
	<th class=celdatituloColumna><span style="cursor: pointer" onClick="ordenar('r.REG_LOCALIDAD');">Localidad</span></th>
	<th class=celdatituloColumna><span style="cursor: pointer" onClick="ordenar('p.PRO_DESCRIP');">Provincia</span></th>
	<th class=celdatituloColumna><span style="cursor: pointer" onClick="ordenar('re.RGI_DESCRIP');">Regi&oacute;n</span></th>
	<th class=celdatituloColumna><span style="cursor: pointer" onClick="ordenar('r.REG_COD_INT');">N&uacute;mero</span></th>
	<th class=celdatituloColumna><span style="cursor: pointer" onClick="ordenar('r.REG_TIPO');">Tipo</span></th>
<? if($fbaja){ ?>
	<th class=celdatituloColumna><span style="cursor: pointer" onClick="ordenar('r.REG_FECHA_BAJA');">Fec.Baja</span></th>	
<?	} ?>
</thead>

<tbody>
<?php
	$fondo = 1;
	while (!$rs->EOF) {
		$id          = $rs->fields["REG_CODIGO"];
		$descripcion = $rs->fields["REG_DESCRIP"];
		$direccion   = $rs->fields["REG_CALLE"] . " " . $rs->fields["REG_NUMERO"] .
			           (is_null($rs->fields["REG_PISO"]) ? "" : " - " . $rs->fields["REG_PISO"]);
		$cpa         = $rs->fields["REG_CPA"];
		$localidad   = $rs->fields["REG_LOCALIDAD"];
		$provincia   = $rs->fields["PRO_DESCRIP"];
		$region      = $rs->fields["RGI_DESCRIP"];
		$numero      = $rs->fields["REG_COD_INT"];
		$fec_baja    = $rs->fields["REG_FECHA_BAJA"];				
		$tipo        = ($rs->fields["REG_TIPO"] == TREG_DESTINO ? TREGLBL_DESTINO :
			           ($rs->fields["REG_TIPO"] == TREG_AMBOS ? TREGLBL_AMBOS :
				        $rs->fields["REG_TIPO"] . " (?)"));
?>
<tr class=fondotabla<?=($fondo); ?> id="row_<?=($id); ?>"
	onclick="selrow(<?=($id); ?>);" style="cursor: pointer;" onDblClick="go(<?=(ABM_EDIT); ?>);" title="Doble Click para Modificar" >
	<td class=celdatexto><?=(htmlentities($descripcion)); ?></td>
	<td class=celdatexto><?=(htmlentities($direccion)); ?></td>
	<td class=celdatexto><?=(htmlentities($cpa)); ?></td>
	<td class=celdatexto><?=(htmlentities($localidad)); ?></td>
	<td class=celdatexto><?=(htmlentities($provincia)); ?></td>
	<td class=celdatexto><?=(htmlentities($region)); ?></td>
	<td class=celdatexto><?=(htmlentities($numero)); ?></td>
	<td class=celdatexto><?=(htmlentities($tipo)); ?></td>
<? if($fbaja){ ?>	
	<td class=celdatexto><?=(htmlentities($fec_baja)); ?></td>	
<?	} ?>	
</tr>
<?php
		$fondo = ($fondo == 1 ? 2 : 1);
		$rs->MoveNext();
	} // end while !eof
?>
</tbody>

<tfoot>
<tr><td colspan="8"><?php navigationbar($page,$totalrows); ?></td></tr>
</tfoot>
<?php } // end if totalrows ?>

</table>
</p>
<?php
} catch (exception $e) {
	dbhandleerror($e);
}
?>
<p align=center>
	<input type=button value="<?=(ABMLBL_VIEW); ?>" class=botonout onClick="go(<?=(ABM_VIEW); ?>);">
<?php if ($permiso == PERM_EDIT) { ?>
	<input type=button value="<?=(ABMLBL_EDIT); ?>" class=botonout onClick="go(<?=(ABM_EDIT); ?>);">
	<input type=button value="<?=(ABMLBL_DEL); ?>" class=botonout onClick="go(<?=(ABM_DEL); ?>);">
	<input type=button value="Nuevo" class=botonout onClick="go(<?php echo(ABM_NEW); ?>);">
<?php } // end if PERM_EDIT ?>
</p>

<?php require_once("../includes/inc_bottom.php"); ?>
</body>

</html>
<?php
} // fin autorizacion
?>