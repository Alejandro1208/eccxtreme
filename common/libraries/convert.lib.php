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
		# �Cual es el separado de decimal (El punto (.) la coma (,) del parametro [value].?
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
		"�" => "&aacute;",
		"�" => "&Aacute;",
	
		"�" => "&eacute;",
		"�" => "&Eacute;",
	
		"�" => "&iacute;",
		"�" => "&Iacute;",
	
		"�" => "&oacute;",
		"�" => "&Oacute;",
	
		"�" => "&uacute;",
		"�" => "&Uacute;",

		"�" => "&ntilde;",
		"�" => "&Ntilde;",		
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

	$sRes = str_replace("�", "aacute", $sRes);
	$sRes = str_replace("�", "eacute", $sRes);
	$sRes = str_replace("�", "iacute", $sRes);
	$sRes = str_replace("�", "oacute", $sRes);
	$sRes = str_replace("�", "uacute", $sRes);

	$sRes = str_replace("�", "Aacute", $sRes);
	$sRes = str_replace("�", "Eacute", $sRes);
	$sRes = str_replace("�", "Iacute", $sRes);
	$sRes = str_replace("�", "Oacute", $sRes);
	$sRes = str_replace("�", "Uacute", $sRes);

	$sRes = str_replace("�", "ntilde", $sRes);
	$sRes = str_replace("�", "Ntilde", $sRes);
	
	$sRes = str_replace("-", "#8212", $sRes);
		
	return($sRes);
}

function fncXmlEntitiesToString($s){
	$sRes = $s;

	$sRes = str_replace("aacute", "�", $sRes);
	$sRes = str_replace("eacute", "�", $sRes);
	$sRes = str_replace("iacute", "�", $sRes);
	$sRes = str_replace("oacute", "�", $sRes);
	$sRes = str_replace("uacute", "�", $sRes);

	$sRes = str_replace("Aacute", "�", $sRes);
	$sRes = str_replace("Eacute", "�", $sRes);
	$sRes = str_replace("Iacute", "�", $sRes);
	$sRes = str_replace("Oacute", "�", $sRes);
	$sRes = str_replace("Uacute", "�", $sRes);

	$sRes = str_replace("ntilde", "�", $sRes);
	$sRes = str_replace("Ntilde", "�", $sRes);
	
	$sRes = str_replace("#8212", "-",  $sRes);
	
	return($sRes);
}
?>
