<?php
function fncCStrSql($value)
{
	if(fncIsEmptyOrNull(parameter))
		return("''");
	else
	{
		$strRes = $value;
		$strRes = str_replace("'", "''", $strRes);
		$strRes = "'" . $strRes . "'";
		return($strRes);
	}
}

function fncCDteSql($y, $m, $d)
{
	if((fncIsEmptyOrNull($y)) || (fncIsEmptyOrNull($m)) || (fncIsEmptyOrNull($d)))
		return("null");
	else
		return($y . "-" . $m . "-" . $d);
}

function fncCDblSql($value)
{
	if(fncIsEmptyOrNull($value))
		return("Null");
	else
		return(str_replace(",", ".", $value));
}

function fncCStr($value)
{
	if(fncIsEmptyOrNull($value))
		return("");
	else
		return((string) $value);
}

function fncCInt($value)
{
	$strAux = fncCStr($value);
	
	if($strAux == "")
		return(0);
	else
		return((int) $strAux);
}

function fncCDbl($value)
{
	$strAux   = fncCStr($value);
	$intPunto = strpos($strAux, ".");
	$intPunto = strpos($strAux, ",");

	if($strAux == "")
		return((double) 0);
	else
	{
		# ¿Cual es el separado de decimal (El punto (.) la coma (,) del parametro [value].?
		if($intComa > 0 && $intPunto > 0)
		{
			if($intComa > $intPunto)
			{
				$strAux = str_replace(".", "", 					  $strAux);
				$strAux = str_replace(",", fncSeparadorDecimal(), $strAux);
			}
			else
			{
				$strAux = str_replace(",", "", 					  $strAux);
				$strAux = str_replace(".", fncSeparadorDecimal(), $strAux);
			}
		}
		else
		{
			if($intComa > 0)
				$strAux = str_replace(",", fncSeparadorDecimal(), $strAux);
			else
				$strAux = str_replace(".", fncSeparadorDecimal(), $strAux);
		}
		return((double) $strAux);
	}
}

function fncCNullString($value)
{
	if(fncIsEmptyOrNull($value))
		return("Null");
	else
		return($value);
}

function fncCNull($value)
{
	if(fncIsEmptyOrNull($value))
		return(Null);
	else
		return($value);
}

function fncRsToArray($value)
{
	if(is_string($value))		
		$rs = $GLOBALS["objDBCommand"]->Rs($value); # Value es un string.		
	else
		$rs = $value; # Values es un Recordset. 

	while(!$rs->EOF())
	{
		$key 	   = $rs->Field(0)->Value();
		$val	   = $rs->Field(1)->Value();
		$arr[$key] = $val; 		
		$rs->MoveNext();
	}
	
	$rs = null;

	return($arr);
}

function fncToHtmlEntities($value)
{
	$arrHtmlEntities = array
	(
		"á" => "&aacute;",
		"Á" => "&Aacute;",
	
		"é" => "&eacute;",
		"É" => "&Eacute;",
	
		"í" => "&iacute;",
		"Í" => "&Iacute;",
	
		"ó" => "&oacute;",
		"Ó" => "&Oacute;",
	
		"ú" => "&uacute;",
		"Ú" => "&Uacute;",

		"ñ" => "&ntilde;",
		"Ñ" => "&Ntilde;",		
	);

	$strRes = $value;	
	foreach($arrHtmlEntities as $strCaracters => $strEntidad)
	{
		$strRes = str_replace($strCaracters, $strEntidad, $strRes);
	}

	return($strRes);
}

function fncStringToXmlEntities($s){
	$sRes = $s;

	$sRes = str_replace("á", "aacute", $sRes);
	$sRes = str_replace("é", "eacute", $sRes);
	$sRes = str_replace("í", "iacute", $sRes);
	$sRes = str_replace("ó", "oacute", $sRes);
	$sRes = str_replace("ú", "uacute", $sRes);

	$sRes = str_replace("Á", "Aacute", $sRes);
	$sRes = str_replace("É", "Eacute", $sRes);
	$sRes = str_replace("Í", "Iacute", $sRes);
	$sRes = str_replace("Ó", "Oacute", $sRes);
	$sRes = str_replace("Ú", "Uacute", $sRes);

	$sRes = str_replace("ñ", "ntilde", $sRes);
	$sRes = str_replace("Ñ", "Ntilde", $sRes);
	
	$sRes = str_replace("-", "#8212", $sRes);
		
	return($sRes);
}

function fncXmlEntitiesToString($s){
	$sRes = $s;

	$sRes = str_replace("aacute", "á", $sRes);
	$sRes = str_replace("eacute", "é", $sRes);
	$sRes = str_replace("iacute", "í", $sRes);
	$sRes = str_replace("oacute", "ó", $sRes);
	$sRes = str_replace("uacute", "ú", $sRes);

	$sRes = str_replace("Aacute", "Á", $sRes);
	$sRes = str_replace("Eacute", "É", $sRes);
	$sRes = str_replace("Iacute", "Í", $sRes);
	$sRes = str_replace("Oacute", "Ó", $sRes);
	$sRes = str_replace("Uacute", "Ú", $sRes);

	$sRes = str_replace("ntilde", "ñ", $sRes);
	$sRes = str_replace("Ntilde", "Ñ", $sRes);
	
	$sRes = str_replace("#8212", "-",  $sRes);
	
	return($sRes);
}
?>
