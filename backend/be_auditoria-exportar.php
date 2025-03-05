<?php
# ---------------------------------------------
# Constantes.
# ---------------------------------------------
define("PAGE", "be_auditoria-exportar");
# ---------------------------------------------

require_once("includes/begin-page-exportar.inc.php");

# ---------------------------------------------
# Variables.
# ---------------------------------------------
$strSql = fncRequest(REQUEST_METHOD_POST, "hdn_sql", REQUEST_TYPE_STRING, "");
# ---------------------------------------------

# ---------------------------------------------
# Proceso.
# ---------------------------------------------
if(!fncIsEmptyOrNull($strSql))
{
	$rs = $GLOBALS["objDBCommand"]->Rs($strSql);
	while(!$rs->EOF())
	{
		$dteInicio   = fncDateMySqlToUnixTimeStamp($rs->Field("fecha_inicio")->Value());
		$dteFin	     = fncDateMySqlToUnixTimeStamp($rs->Field("fecha_fin")->Value());
		$intSegundos = $dteFin - $dteInicio;
		
		$objTpl->SetVar("ITEM.modulo", 		  $rs->Field("tabla")->Value());
		$objTpl->SetVar("ITEM.administrador", $rs->Field("administrador")->Value());
		$objTpl->SetVar("ITEM.accion", 		  $rs->Field("accion")->Value());
		$objTpl->SetVar("ITEM.fecha_inicio",  fncFormatDateFromMySql($rs->Field("fecha_inicio")->Value()));
		$objTpl->SetVar("ITEM.fecha_fin",     fncFormatDateFromMySql($rs->Field("fecha_fin")->Value()));
		$objTpl->SetVar("ITEM.segundos",	  $intSegundos);
		$objTpl->Parse("ITEM");
		$rs->MoveNext();
	}
}
# ---------------------------------------------

require_once("includes/end-page-exportar.inc.php");	
?>

