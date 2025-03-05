<?php
# ------------------------------------------------------------------------------------------------
# Funciones.
# ------------------------------------------------------------------------------------------------
function fncCharCount($value, $strChar)
{
	$strValue = fncCStr($value);
	$intLen   = strlen($strValue);
	$intCont  = 0;

	for($j = 1; $j <= $intLen; $j++)
	{
		if(substr($strValue, $j, 1) == $strChar)
			$intCont ++;
	}

	return($intCont);
}
	
function fncImageYesOrNo($strDir, $strFileName, $strImgNo = "")
{
	if($strFileName == "")
	{
		# No hay imagen asignada.
		if(fncIsEmptyOrNull($strImgNo))			
			return(IMG_NO); 
		else
			return($strImgNo);
	}
	else
	{
		$strPath = PATH . $strDir;
	 	$strUrl  = URL  . $strDir;

		if(file_exists($strPath . $strFileName)) 
			return($strUrl . $strFileName);
		else
		{
			# Hay imagen asignada, pero no exite fisicamente.
			if(fncIsEmptyOrNull($strImgNo))			
				return(IMG_NO); 
			else
				return($strImgNo);
		}
	}
}

function fncSubString($strValue, $strBegin, $blnBeginLeft, $intBeginMove, $strEnd, $blnEndLeft, $intEndMove)
{
	if($blnBeginLeft) 
		$intBegin = strpos($strValue, $strBegin) + $intBeginMove;
	else
		$intBegin = strripos($strValue, $strBegin) + $intBeginMove;

	if(fncIsEmptyOrNull($strEnd))
		return(substr($strValue, $intBegin));
	else
	{
		if($blnEndLeft) 
			$intEnd = strpos($strValue, $strEnd) + $intEndMove;
		else
			$intEnd = strripos($strValue, $strEnd) + $intEndMove;

		$strAux1 = substr($strValue, $intBegin);
		$strAux2 = substr($strValue, $intEnd);

		return(substr($strAux1, 1, strlen($strAux1) - strlen($strAux2)));
	}
}

function fncFriedlyUrl()
{
	$strRequestUri = $_SERVER["REQUEST_URI"];	
	$intLen        = strlen($strRequestUri);
	$intLastChar   = substr($strRequestUri, $intLen - 1);
	return(($intLastChar == "/")? substr($strRequestUri, 0, $intLen - 1) : $strRequestUri);
}

function fncUrl()
{
	return($_SERVER["REQUEST_URI"]);
}

function fncPage($blnQueryString = false)
{
	$strScript = str_replace("\\", "/", $_SERVER["SCRIPT_FILENAME"]);
	$intBarra  = strripos($strScript, "/");
	$strPage   = substr($strScript, $intBarra + 1);

	if($blnQueryString)
	{
		$strQueryString = $_SERVERS["QUERY_STRING"];

		if($strQueryString == "")
			return($strPage);
		else
			return(strPage . "?" . $strQueryString);
	}
	else
		return($strPage);

}

function fncSplit($strValue, $sDelimiter)
{
	# Esto es para que me devuelva un array vacio en el caso de que strValue = "". 
	# Si no me devolvia un array con un solo elemento.
	if(fncIsEmptyOrNull($strValue))
		return(array());		
	else
		return(explode($sDelimiter, $strValue));
}

function fncStrPos($strCadena, $strCaracter)
{
	# Esto es para que me devuelva -1 si no encuentra el elemento. 
	# Si no me devolvia 0 (que es la 1� pocision).
	$intPos = strpos($strCadena, $strCaracter);
	return($intPos === false? -1 : $intPos);
}

function fncRequest($strMethod, $strName, $strType, $default)
{
	switch($strMethod)
	{
		case "p":
		{
			$val = isset($_POST[$strName])?$_POST[$strName]:$default;
			break;
		}
		case "g":
		{
			$val = isset($_GET[$strName])?$_GET[$strName]:$default;
			break;
		}
		default:
		{
			if(fncIsEmptyOrNull($_POST[$strName]))
				$val = $_GET[$strName];
			else
				$val = $_POST[$strName];
		}
	}
	
	if(fncIsEmptyOrNull($val))
		return($default);
	else
	{
		switch($strType)
		{
			# Integer.
			case "i":
			{
				if(fncIsInteger($val))
					return((int) $val);
				else
					return((int) $default);
				break;
			}			
			# Float (Real).
			case "f":
			{
				if(fncIsFloat($val))
					return((double) $val);
				else
					return((double) $default);
				break;
			}
			# Currency.
			case "c":
			{
				if(fncIsCurrency($val))
					return((double) $val);
				else
					return((double) $default);
				break;
			}
			# Boolean.
			case "b":
			{
				return((bool) $val);
				break;
			}			
			default:
			{
				return($val);
				break;			
			}
		}
	}
}

function fncComboToStringSqlDate($strName)
{
	$intY = fncRequest(REQUEST_METHOD_POST, $strName . "_y", REQUEST_TYPE_INTEGER, 0);
	$intM = fncRequest(REQUEST_METHOD_POST, $strName . "_m", REQUEST_TYPE_INTEGER, 0);
	$intD = fncRequest(REQUEST_METHOD_POST, $strName . "_d", REQUEST_TYPE_INTEGER, 0);

	if(($intY == 0) || ($intM == 0)  || ($intD == 0))
		return(null);
	else
		return(fncCDteSql($intY, $intM, $intD));
}

function fncUrlEncode($strValue)
{
	$str = $strValue;
	$str = urlencode($str);
	$str = str_replace("+", "_", $str);
	return($str);
}

function fncUrlDecode($strValue)
{
	$str = $strValue;
	$str = urldecode($str);
	$str = str_replace("_", " ", $str);
	return($str);
}

function fncCutText($str, $intMaxLen)
{
	if(fncIsEmptyOrNull($str))
	{
		return("");
	}
	else
	{
		$intLen = strlen($str);
		if($intLen > $intMaxLen)
		{
			return(substr($str, 0, $intMaxLen - 1) . " ...");
		}
		else
			return($str);
	}
}
# ------------------------------------------------------------------------------------------------

# ------------------------------------------------------------------------------------------------
# Procedimientos.
# ------------------------------------------------------------------------------------------------
function subError($strPhp, $strComentarios)
{
	if(!fncIsEmptyOrNull($strComentarios))
	{
		$strComments    = "";
		$arrComentarios = fncSplit($strComentarios, "|");
		$intCount	    = count($arrComentarios);
		for($j = 0; $j <= $intCount - 1; $j++)
			$strComments = $strComments . "* " . $arrComentarios[$j] . " - <br/>";
	}

	$objTpl = new Template(PATH . "common/templates/error.html");

	$objTpl->SetVar("php",         $strPhp);
	$objTpl->SetVar("comentarios", $strComments);

	$objTpl->ParseAll(true);

	flush();
	die();
}

function subFillSelectWithTpl_FromSql($strSql, $objTpl, $strTplBlk, $selected)
{
	/*
	 * @todo
	 * Hacer que le puedas mandar parametros al sql.
	 */
	$rs = $GLOBALS["objDBCommand"]->Rs($strSql, null);
	while(!$rs->EOF())
	{
		$objTpl->SetVar($strTplBlk . ".val", $rs->Field(0)->Value());
		$objTpl->SetVar($strTplBlk . ".des", $rs->Field(1)->Value());

		if(fncCStr($rs->Field(0)->Value()) == fncCStr($selected)){
			$objTpl->SetVar($strTplBlk . ".selected", "selected");
		}
		else
			$objTpl->SetVar($strTplBlk . ".selected", "");

		$objTpl->Parse($strTplBlk);

		$rs->MoveNext();
	}
	;
	$rs = null;
}

function subFillSelectWithTpl_FromArray($arr, $objTpl, $strTplBlk, $selected)
{
	foreach($arr as $key => $val)
	{
		$objTpl->SetVar($strTplBlk . ".val", $key);
		$objTpl->SetVar($strTplBlk . ".des", $val);

		if(fncCStr($key) == fncCStr($selected))
			$objTpl->SetVar($strTplBlk . ".selected", "selected");
		else{
			$objTpl->SetVar($strTplBlk . ".selected", "");
		}

		$objTpl->Parse($strTplBlk);
	}
}

function fncCapitalize($str){
	$strRes = strtoupper(substr($str, 0, 1)) . strtolower(substr($str, 1));
	return($strRes);
}
# ------------------------------------------------------------------------------------------------
?>
