<?php
require_once("common.inc.php");

# ---------------------------------------------
# DBCommand.
# ---------------------------------------------
$GLOBALS["objDBCommand"] = new DBCommand(DB_HOST, DB_USER, DB_PASS, DB_NAME, PATH_DESCRIPTOR);
# ---------------------------------------------

# ---------------------------------------------
# Constantes.
# ---------------------------------------------
subAuthenticationAndSetConstants();
# ---------------------------------------------

# ---------------------------------------------
# Template.
# ---------------------------------------------
$objTpl = new Template(PATH_TEMPLATES . "estructura-interna.html");

$objTpl->SetFile("TOP",	   PATH_TEMPLATES . "estructura-top.html");
$objTpl->SetFile("MENU",   PATH_TEMPLATES . "estructura-menu.html");
$objTpl->SetFile("BOTTOM", PATH_TEMPLATES . "estructura-bottom.html");

# SetVar.
subTplSetVar($objTpl);
$objTpl->SetVar("MENU.url", URL);
# ---------------------------------------------

# ---------------------------------------------
# Proceso.
# ---------------------------------------------
if(ADMINISTRADOR == -1)
{
	# Administrador Oculto.	
	$objTpl->SetVar("MENU.administrador", ":: Administrador Oculto ::");	
	$strSql 	   = "sp_be_menu_xAdministradorOculto"; # Select "modulos".
	unset($arrParameters);	
}
else
{	
	$rs = $GLOBALS["objDBCommand"]->Rs
	(
		"sp_be_administradores_get",
		array("intId" => ADMINISTRADOR)		
	);
	$objTpl->SetVar("MENU.administrador", $rs->Field("username")->Value());
	unset($rs);

	$strSql 	   = "sp_be_menu_xAdministrador"; # Select "modulos".
	$arrParameters = array("intId_administrador" => ADMINISTRADOR);	
}

# Select "modulos".
$rs = $objDBCommand->Rs($strSql, $arrParameters);
do
{
	$strPage = $rs->Field("pagina")->Value() . ".php";
	
	if(strtolower($rs->Field("permiso")->Value()) == "d")
	{
		$objTpl->SetVar("MENU.BLK_OPTIONGROUP.label", $rs->Field("descripcion")->Value());
		$objTpl->Parse("MENU.BLK_OPTIONGROUP");
	}
	else
	{
		$objTpl->SetVar("MENU.BLK_OPTION.val", $strPage);
		$objTpl->SetVar("MENU.BLK_OPTION.des", $rs->Field("descripcion")->Value());

		if((fncStrpos($strPage, PAGE_L) > -1) || (fncStrpos($strPage, PAGE_F) > -1))
		{
			$objTpl->SetVar("MENU.BLK_OPTION.selected", "selected"); 					# Selected.			
			$objTpl->SetVar("title_1", strtoupper($rs->Field("descripcion")->Value())); # Titulo.
		}
		else
			$objTpl->SetVar("MENU.BLK_OPTION.selected", "");

		$objTpl->Parse("MENU.BLK_OPTION");
	}
	$rs->MoveNext();
}
while(!$rs->EOF());
unset($rs);
# ---------------------------------------------
?>