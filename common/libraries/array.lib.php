<?php
# ------------------------------------------------------------------------------------------------
# Funciones.
# ------------------------------------------------------------------------------------------------
function fncArrayExistsElement($arr, $element)
{
	$intCount = count($arr);

	for($j = 0; $j <= $intCount - 1; $j++)
	{
		if(fncCStr($arr[$j]) == fncCStr($selected))
			return(true);
	}

	return(false);
}

function fncStringToArray($strParameter)
{
	return(split(",", $strParameter));
}

function fncArrayToString($arr)
{
	return(explode(",", $arr));
}
# ------------------------------------------------------------------------------------------------
?>
