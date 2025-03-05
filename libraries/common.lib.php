<?php
# ---------------------------------------------
# Funciones.
# ---------------------------------------------
function formatDateTime($sDateTime){
	if(fncIsEmptyOrNull($sDateTime)){
		return("");
	}
	else{
		$sDateTime = fncDateToArray($sDateTime);
		return(fncDateFillWith0($sDateTime["d"]) . "/" . fncDateFillWith0($sDateTime["m"]) . "/" . $sDateTime["y"] . " " . $sDateTime["h"] . ":" . $sDateTime["mi"]);	
	}
}

function formatDate($sDateTime){
	if(fncIsEmptyOrNull($sDateTime)){
		return("");
	}
	else{
		$sDateTime = fncDateToArray($sDateTime);
		return(fncDateFillWith0($sDateTime["d"]) . "/" . fncDateFillWith0($sDateTime["m"]) . "/" . $sDateTime["y"]);
	}
}

function formatTime($sDateTime){
	if(fncIsEmptyOrNull($sDateTime)){
		return("");
	}
	else{
		$sDateTime = fncDateToArray($sDateTime);
		return($sDateTime["h"] . ":" . $sDateTime["mi"] . ":" . $sDateTime["s"]);
	}
}

function encodeUrl($s){
	$sRes = ucwords($s);
	
	$sRes = str_replace("á", "a", $sRes);
	$sRes = str_replace("é", "e", $sRes);
	$sRes = str_replace("í", "i", $sRes); 	
	$sRes = str_replace("ó", "o", $sRes);
	$sRes = str_replace("ú", "u", $sRes);
	$sRes = str_replace("ñ", "n", $sRes);
	$sRes = str_replace(" ", "",  $sRes);
	$sRes = str_replace(".", "",  $sRes);
	$sRes = str_replace("¨", "",  $sRes);
	$sRes = str_replace(",", "",  $sRes);
	$sRes = str_replace("–", "-", $sRes);

	return($sRes);
}

function sqlWhereFriedlyUrl($sFieldName, $sValue){
	return("
		REPLACE(
		REPLACE(
		REPLACE(
		REPLACE(
		REPLACE(
		REPLACE(
		REPLACE(
		REPLACE(
		REPLACE(
		REPLACE(
		REPLACE(
		LOWER($sFieldName)
		, 'á', 'a')
		, 'é', 'e')
		, 'í', 'i')
		, 'ó', 'o')
		, 'ú', 'u')
		, 'ñ', 'n')
		, ' ', '')
		, '.', '')
		, '¨', '')
		, ',', '')
		, '–', '-')  
		= LOWER('$sValue')
	");
}
# ---------------------------------------------

# ---------------------------------------------
# Procedimientos.
# ---------------------------------------------
function tplSetVars(){	
	global $objTpl;

	$objTpl->SetGlobal("sJsDirCommon", 	RAIZ . "common/js/");
	$objTpl->SetGlobal("sJsDir",		JS_DIR);
	$objTpl->SetGlobal("sImgDir",		IMG_DIR);
	$objTpl->SetGlobal("sCssDir", 		STYLES_DIR);
	$objTpl->SetGlobal("sSwfDir",		FLASH_DIR);	
}
# ---------------------------------------------
?>
