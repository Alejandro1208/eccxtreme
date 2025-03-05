<?php
require_once("includes/begin-page-f.inc.php");

# ---------------------------------------------
# Proceso.
# ---------------------------------------------
if($strOp != "a")
{
	$strUserName	 = $rs->Field("username")->Value();
	$strPassword	 = $rs->Field("password")->Value();
	$strNombre		 = $rs->Field("nombre")->Value();
	$strApellido	 = $rs->Field("apellido")->Value();
	$intFk_id_perfil = $rs->Field("fk_id_perfil")->Value();
}

subTextBox($objTpl, "username", 50, $strUserName, "Username", true,  "string");
subTextBox($objTpl, "password", 50, $strPassword, "Password", true,  "string");
subTextBox($objTpl, "nombre",   50, $strNombre,   "Nombre",   false, "string");
subTextBox($objTpl, "apellido", 50, $strApellido, "Apellido", false, "string");
subSelect($objTpl, "perfil", "sp_be_perfiles_combo", $intFk_id_perfil, "Perfil", true);
# ---------------------------------------------

# ---------------------------------------------
# Parse.
# ---------------------------------------------
$objTpl->Parse("BLK_VALIDAR");       #  Js.
$objTpl->Parse("BLK_INPUTS.BLK_TR"); #  Html.
# ---------------------------------------------

require_once("includes/end-page-f.inc.php");	
?>

