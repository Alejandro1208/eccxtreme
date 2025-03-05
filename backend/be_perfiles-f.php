<?php
require_once("includes/begin-page-f.inc.php");

# ---------------------------------------------
# Aux.
# ---------------------------------------------
$objTplPerfiles = subAuxInit($objTpl, "be_perfiles");
# ---------------------------------------------

# ---------------------------------------------
# Proceso.
# ---------------------------------------------
if($strOp != "a")
	$objTplPerfiles->SetVar("nombre", $rs->Field("nombre")->Value());

$rs = $GLOBALS["objDBCommand"]->Rs("sp_be_modulos_GetAll");
do
{
	$objTplPerfiles->SetVar("BLK_MODULO.des", $rs->Field("descripcion")->Value());
	$objTplPerfiles->SetVar("BLK_MODULO.id",  $rs->Field("id_modulo")->Value());

	$strPermiso = "D";

	if($strOp == "m")
	{
		$rsPermiso = $GLOBALS["objDBCommand"]->Rs
		(
			"sp_be_permiso_xPerfilAndModulo",
			array(
				"intFk_id_prefil" => $intId,
				"intFk_id_modulo" => $rs->Field("id_modulo")->Value()
			)		
		);
		if(!$rsPermiso->EOF()){$strPermiso = $rsPermiso->Field("permiso")->Value();}
		$rsPermiso = null;
	}
	 
	$objTplPerfiles->SetVar("BLK_MODULO.checked_a", 						 "");
	$objTplPerfiles->SetVar("BLK_MODULO.checked_l",						     "");
	$objTplPerfiles->SetVar("BLK_MODULO.checked_d", 						 "");
	$objTplPerfiles->SetVar("BLK_MODULO.checked_" . strtolower($strPermiso), "checked");
	$objTplPerfiles->Parse("BLK_MODULO");
	$rs->MoveNext();
}
while(!$rs->EOF());
$rs = null;
# ---------------------------------------------

# ---------------------------------------------
#  Aux -> Parse.
# ---------------------------------------------
subAuxParse($objTpl, $objTplPerfiles);
# ---------------------------------------------

require_once("includes/end-page-f.inc.php");	
?>

