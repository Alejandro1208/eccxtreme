<?php
function fncIsEmptyOrNull($value)
{
	# El empty   abarca el 0.
	# El is_null abarca el 0.
	if(($value == "0") || ($value === "0000-00-00 00:00:00"))
		return(false);
	else{
		if((!isset($value)) || (empty($value)) || (is_null($value)))
			return(true);
		else
			return(false);
	}
}

function fncIifEmptyOrNull($value, $TruePart, $FalsePart)
{
	if(fncIsEmptyOrNull($value))
		return($TruePart);
	else
		return($FalsePart);
}

function fncIsString($value, $intMinimo, $intMaximo)
{
	$intLen = strlen($value);

	if($intLen < $intMinimo || $intLen > $intMaximo)
		return(false);
	else
		return(true);
}

function fncIsDigit($value)
{
	$strValue = fncCStr($value);

	if(($strValue >= "0") && ($strValue <= "9"))
		return(true);
	else
		return(false);
}

function fncIsInteger($value)
{
	$j	    = 0;
	$intLen = strlen($value);

	do
	{
		$strChar    = substr($value, $j, 1);
		$blnIsDigit = fncIsDigit($strChar);
		$j ++;
	}
	while(($blnIsDigit) && ($j <= $intLen - 1));

	if(($blnIsDigit) && ($j == $intLen))
		return(true);
	else 
		return(false);
}

function fncIsFloat($value)
{
	$val      = $value;

	$intPunto = fncStrPos($val, ".");
	$intComa  = fncStrPos($val, ",");
	
	if($intPunto > 0)
		$val = str_replace(".", "", $value);
	else
	{
		if($intComa > 0)
			$val = str_replace(",", "", $value);
	}
	
	return(fncIsInteger($val));
}

function fncIsCurrency($value)
{
	$val      = $value;

	$intPunto = fncStrPos($val, ".");
	$intComa  = fncStrPos($val, ",");

	if(fncIsFloat($val))
	{
		if($intPunto > 0)
			$val = substr($value, $intPunto + 1);
		else
			$val = substr($value, $intComa  + 1);

		return(strlen($val) <= 2);
	}
	else
		return(false);	
}

function fncSeparadorDecimal()
{
	$dblAux = 0.50;
	return(substr($dblAux, 1, 1));
}

?>
