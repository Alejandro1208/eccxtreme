<?php
function fncFormatHtml($value)
{
	$strValue = str_replace(Chr(13), "<br>",   $value);
	$strValue = str_replace(Chr(32), "&nbsp;", $value);
	return($value);
}

function fncFormatCurrency($value)
{
	$dblAux = fncCDbl($value);
	return(number_format($dblAux, 2, ",", "."));
}
?>
