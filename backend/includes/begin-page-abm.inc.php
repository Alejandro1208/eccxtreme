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
# Funciones.
# ---------------------------------------------
function fncLastId()
{
	$rs    = $GLOBALS["objDBCommand"]->Rs("sp_" . TABLE . "_LastId");
	$intId = $rs->Field(0)->Value();
	$rs    = nothing;
	return($intId);
}

function fncMensajeOk($strOp)
{
	$strMensaje = "El proceso de ";

	switch(strtolower($strOp))
	{
		case("d"): 
		{
			$strMensaje .= "[Destacación] ";
			break;
		}
		case("dd"):
		{ 
			$strMensaje .= "[Desdestacación] ";
			break;
		}
	   	case("h"):
	   	{	$strMensaje .= "[Habilitación] ";
	   		break;
	   	}
	   	case("dh"):
	   	{
	   		$strMensaje .= "[Deshabilitación] ";
	   		break;
	   	}
		case("a"):
		{
			$strMensaje .= "[Alta] ";
			break;
		}
		case("b"): 
		{
			$strMensaje .= "[Baja] ";
			break;
		}
		case("m"): 
		{
			$strMensaje .= "[Modificación] ";
			break;
		}
	}

	$strMensaje .= "finalizo exitosamente.";

	return($strMensaje);
}

function fncMensajeError($strOp, $strMensaje_p)
{
	$strMensaje = "No se realizo el proceso de ";

	switch(strtolower($strOp))
	{
		case("d"):
		{
			$strMensaje .= "[Destacación] ";
			break;
		}
	    case("nd"):
	    {
	    	$strMensaje .= "[Desdestacación] ";
	    	break;
	    }
		case("h"):
		{
			$strMensaje .= "[Habilitación] ";
			break;
		}
		case("dh"):
		{
			$strMensaje .= "[Deshabilitación] ";
			break;
		}
		case("a"):
		{
			$strMensaje .= "[Alta] ";
			break;
		}
		case("b"):
		{
			$strMensaje .= "[Baja] ";
			break;
		}
		case("m"):
		{
			$strMensaje .= "[Modificación] ";
			break;
		}
	}	

	$strMensaje .= "\n";
	$strMensaje .= $strMensaje_p; 
	$strMensaje .= ".";

	return($strMensaje);
}

function fncGetField($intId, $strField)
{
	$rs  = $GLOBALS["objDBCommand"]->Rs("sp_" . TABLE . "_get", array("intId" => $intId));
	$val = $rs->Field($strField)->Value();
	$rs  = nothing;
	return($val);	
}

function fncFileNameUpload($strFileName)
{
	$strRes = $strFileName;
	$strRes = str_replace("�", "A", $strFileName);
	$strRes = str_replace("�", "a", $strFileName);
	
	$strRes = str_replace("�", "E", $strFileName);
	$strRes = str_replace("�", "e", $strFileName);
	
	$strRes = str_replace("�", "I", $strFileName);
	$strRes = str_replace("�", "i", $strFileName);
	
	$strRes = str_replace("�", "O", $strFileName);
	$strRes = str_replace("�", "o", $strFileName);
	
	$strRes = str_replace("�", "U", $strFileName);
	$strRes = str_replace("�", "u", $strFileName);
		
	$strRes = str_replace(" ", "-", $strFileName);
	return($strRes);
}
# ---------------------------------------------

# ---------------------------------------------
#  Procedimientos.
# ---------------------------------------------
function subExist($arrParameters, $strOp, $strMensaje)
{
	$rs     = $GLOBALS["objDBCommand"]->Rs("sp_" . TABLE . "_exist", $arrParameters);
	$blnEof = $rs->EOF();
	unset($rs);

	if(!$blnEof)
	{
		echo('<body onLoad="alert(' . "'" . $strMensaje . "'" . '); window.history.back(-1);">');
		echo('</body>');
		die();
	}
}

function subUpdateField($strField, $value, $intId)
{
	$GLOBALS["objDBCommand"]->Execute
	(
		"sp_" . TABLE . "_m_" . $strField, 
		array
		(
			"strValue" => $value,
			"intId"    => $intId
		)
	);
}

function subFileB($intId, $strPath, $strField)
{
	subFileDelete($strPath & fncGetField($intId, $strField));
	subUpdateField($strField, "", $intId);
}
function subFilesB($intId, $strField, $intCant, $strPath)
{
	$arrFiles = explode(",", fncGetField($intId, $strField));
	$intLen   = count($arrFiles);

	for($j = 0; $j <= $intCount; $j++)
		subFileDelete($strPath . $arrFiles[$j]);
}

function subUploadAndUpdateFile($strName, $intId, $strDir, $strValue)
{
	$strPath 	  = PATH . $strDir;
	$strInputName = "file_" . $strName;

	if(fncRequest(REQUEST_METHOD_POST, "chk_eliminar_" . $strInputName, REQUEST_TYPE_STRING, "") == "true")
		subFileB($intId, $strPath, $strName);
	else
	{		
		if(!empty($_FILES[$strInputName]["tmp_name"]))
		{			
			subFileDelete($strPath . fncGetField($intId, $strName));
			$strFile = fncFileUpload($strInputName, $strPath, $strValue);
			subUpdateField($strName, $strFile, $intId);
		}
	}
}
function subUploadsAndUpdatesFiles($intId, $strField, $intCant, $strInputName_p, $strPath, $strParameter)
{
	$strFiles = "";	
	$arrFiles = fncSplit(fncGetField($intId, $strField), ",");

	for($j = 0; $j <= $intCant - 1; $j++)
	{
		$intIndice    = $j + 1;
		$strInputName = $strInputName_p . "_" . $intIndice;
		if(fncRequest(REQUEST_METHOD_POST, "chk_eliminar_" . $strInputName, REQUEST_TYPE_STRING, "") == "true")
		{
			# Elimino.
			subFileDelete($strPath . $arrFiles[$j]);
			$arrFiles[$j] = "[NULL]";
		}
		else
		{
			$strFileName = $_FILES[$strInputName]["name"];			
			if(!empty($strFileName))
			{
				$intCount = count($arrFiles);
				
				if($j <= $intCount - 1)
				{
					# Modifico.
					$intIndice = $j; 			
					subFileDelete($strPath . $arrFiles[$k]);
				}
				else
				{
					# Agrego.
					$intIndice = ($intCount == 0)? 0 : $intCount;
				}

			  # $strGenerateUniqueFileName = fncGenerateUniqueFileName($arrFiles, $intId, fncFileGetExtension($strFileName));
				$strFileName = fncFileUpload($strInputName, $strPath, $intId . "_" . ($intIndice + 1));
				
				if($j <= $intCount - 1)					
					$arrFiles[$intIndice] = $strFileName; # Modifico.
				else
					$arrFiles[] = $strFileName;           # Agrego.					
			}
		}
	}

	# Paso el array a string.
	$strFiles = implode(",", $arrFiles);
	$strFiles = str_replace(",[NULL]", "", $strFiles);
	$strFiles = str_replace("[NULL]",  "", $strFiles);
	subUpdateField($strField, $strFiles, $intId);
}

function subMultiSelect($strSpBaja, $intId, $strInputName, $strSpAlta)
{
	$intCant = count($_POST[$strInputName]);
	
	# Eliminar.
	$arrParameters = array("intId" => $intId);
	$GLOBALS["objDBCommand"]->Execute($strSpBaja, $arrParameters);
	
	# Agregar.
	for($j = 0; $j <= $intCant - 1; $j ++)
	{
		$arrParameters = array
		(
			"intId" => $intId,
			"intFk" => (int) $_POST[$strInputName][$j],								
		);
		$GLOBALS["objDBCommand"]->Execute($strSpAlta, $arrParameters);	
	}
}

function subOrder($strInputName, $intId)
{
	$intCant = count($_POST[$strInputName]);
	
	for($j = 0; $j <= $intCant - 1; $j ++)
	{
		$arrParameters = array
		(
			"intOrden" => $j + 1,
			"intId"    => (int) ($_POST[$strInputName][$j] == 0)? $intId : $_POST[$strInputName][$j]								
		);
		$GLOBALS["objDBCommand"]->Execute("sp_" . TABLE . "_m_orden", $arrParameters);	
	}
}
# ---------------------------------------------

# ---------------------------------------------
# Inicializaci�n de variables.
# ---------------------------------------------
$strOp   = fncRequest(REQUEST_METHOD_POST, "hdn_op",   REQUEST_TYPE_STRING,  "");
$intId   = fncRequest(REQUEST_METHOD_POST, "hdn_id",   REQUEST_TYPE_INTEGER, 0);
$intPage = fncRequest(REQUEST_METHOD_POST, "hdn_page", REQUEST_TYPE_STRING,  0);

$strSql  = "sp_" . TABLE . "_" . strtolower($strOp);
# --------------------------------------------- 
?>