<?php
# ---------------------------------------------
# Procedimientos.
# ---------------------------------------------
function subTplSetVar($objTpl)
{
	# Global.
	$objTpl->SetGlobal("url",				URL);
	$objTpl->SetGlobal("script_src_common",	"../common/js/");
	$objTpl->SetGlobal("script_src",		"js/");
	$objTpl->SetGlobal("img_src",			"images/");
	# Estructura.
	$objTpl->SetVar("title",	 TITLE);
	$objTpl->SetVar("link_href", "styles/");
	# Bottom.
	$objTpl->SetVar("BOTTOM.host", HOST);
}

function subTplSetVarPopUp($objTpl)
{
	# Set Global.
	$objTpl->SetGlobal("img_src", URL_IMG);
	# Estructura.
	$objTpl->SetVar("title",	 TITLE);
	$objTpl->SetVar("link_href", URL_STYLES);
}

function fncGenerateUniqueFileName($arrFiles, $intId, $strExt)
{
	$intCount = count($arrFiles); 
	$j 	 	  = 0;

	do
	{
		$strFileName 		= $intId . "_" . ($j + 1);
		$strFileNameWithExt = $strFileName . "." . $strExt;
		$j ++;
		$blnExist = array_key_exists($strFileNameWithExt, $arrFiles);
	}
	while(($blnExist) && ($j <= $intCount));

	/*
	echo("<br/>");
	echo("<br/>");
	echo('$intCount: ' . $intCount);
	echo("<br/>");
	echo('$j : ' . $j);
	echo("<br/>");
	echo('$blnExist: ' . (int) $blnExist);
	echo("<br/>");
	echo('$strFileName: ' . $strFileName);
	echo("<br/>");
	echo("<br/>");
	flush();
	*/

	return($strFileName);
}

function subAuthenticationAndSetConstants()
{
	session_start();
	
	$intAdministrador = (int) $_SESSION["id_administrador"];

	# ---------------------------------------------
	# Verificaci�n de si esta logeado.
	# ---------------------------------------------
	if($intAdministrador == 0){header("location: session-expirada.php");}	
	# ---------------------------------------------
	
	$strPage    = strtolower(fncPage(false));
	$arrAux     = fncSplit($strPage, "-");
	$strTable   = $arrAux[0];
	$strPageL   = $strTable . "-l.php";
	$strPageF   = $strTable . "-f.php";
	$strPageABM = $strTable . "-abm.php";

	# ---------------------------------------------
	# Permiso.
	# ---------------------------------------------
        $strPermiso = "";
	if($strPage != "home.php")
	{
		if($intAdministrador == -1)
			$strPermiso = "a";
		else
		{
			$rs = $GLOBALS["objDBCommand"]->Rs
			(
				"sp_be_permiso_xAdministradorAndPagina",
				array(
					"intId_administrador" => $intAdministrador,
					"strPage" 			  => $strTable
				)		
			);
			if($rs->RecordCount() == 0)		
				$strPermiso = "d";
			else
				$strPermiso = strtolower($rs->Field("permiso")->Value());		
			unset($rs);
		}
		if($strPermiso == "d"){header("location: sin-permiso.php");}
	}
	# ---------------------------------------------

	define("PAGE", 			$strPage);
	define("ADMINISTRADOR", $intAdministrador);
	define("PAGE_L",   		$strPageL);
	define("PAGE_F",   		$strPageF);
	define("PAGE_ABM", 		$strPageABM);
	define("TABLE",    		$strTable);
	define("PERMISO", 		$strPermiso);

	/*
	echo("PAGE: " 		   . PAGE);
	echo("<br/>");
	echo("ADMINISTRADOR: " . ADMINISTRADOR);
	echo("<br/>");
	echo("PAGE_L: "		   . PAGE_L);
	echo("<br/>");
	echo("PAGE_F: " 	   . PAGE_F);
	echo("<br/>");
	echo("PAGE_ABM: "	   . PAGE_ABM);
	echo("<br/>");
	echo("TABLE: " 		   . TABLE);
	echo("<br/>");
	echo("PERMISO: "	   . PERMISO);
	*/
}
# ----------------------------------------

# ----------------------------------------
# Fucniones.
# ----------------------------------------
function fncFormatDateFromMySql($strDate, $blnTime = false)
{
	if($strDate == "0000-00-00 00:00:00")
		return("");
	else
	{
		$arrDate = fncDateMySqlToArray($strDate);
		if($blnTime)
			return(fncDateFillWith0($arrDate["d"]) . "." . fncDateFillWith0($arrDate["m"]) . "." . $arrDate["y"] . " " . fncDateFillWith0($arrDate["h"]) . ":" . fncDateFillWith0($arrDate["mi"]) . ":" . fncDateFillWith0($arrDate["s"]));
		else
			return(fncDateFillWith0($arrDate["d"]) . "." . fncDateFillWith0($arrDate["m"]) . "." . $arrDate["y"]);
	}
}

function subGetAuditoriaAdministradorLogueado()
{	
	$intAdministrador = (int) $_SESSION["id_administrador"];

	if($intAdministrador == -1)
		$strAdministrador = "[Oculto]";
	else
	{
		$arrParameters = array("intId" => $intAdministrador);
	 	$rs = $GLOBALS["objDBCommand"]->Rs("sp_be_administradores_get", $arrParameters);
	 	$strAdministrador = $rs->Field("username")->Value() . "|" . $rs->Field("nombre")->Value()  . "|" . $rs->Field("apellido")->Value();
	} 
	
	return($strAdministrador);
}

function subGetAuditoriaAdministrador(&$rs)
{	
	$arr    = explode("|", $rs->Field("auditoria_administrador")->Value());
	$strRes = $arr[0];

	if(!fncIsEmptyOrNull($arr[1])){$strRes .= " - " . $arr[1];}
	if(!fncIsEmptyOrNull($arr[2])){$strRes .= " - " . $arr[2];}

	$strRes .= " - " . fncFormatDateFromMySql($rs->Field("auditoria_fecha")->Value());
	
	return($strRes);
}
# ----------------------------------------

#funcion para imprimir las fechas en formato adecuado
function format_date_mysql_to_local($date_string,$separator="/") {
    $values = explode("-", $date_string);
    if (count($values) == 3) {
        $values = array_reverse($values);
        return implode($separator, $values);
    }
    return null;
}

?>
