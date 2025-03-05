<?php
require_once("begin-page-interna.inc.php");

# ---------------------------------------------
# Variables.
# ---------------------------------------------
$strOp = fncRequest(REQUEST_METHOD_POST, "hdn_op", REQUEST_TYPE_STRING,  "");
$intId = fncRequest(REQUEST_METHOD_POST, "hdn_id", REQUEST_TYPE_INTEGER, 0);
# ---------------------------------------------

# ---------------------------------------------
# Funciones.
# ---------------------------------------------
function fncSize($intMaxLength)
{
	if($intMaxLength <= 50)
		return($intMaxLength + 2);
	else
		return(70);
}

# --------------------
# Aux.
# --------------------
function subAuxInit(&$objTpl, $strTemplate, $strSubValidar = "")
{	
	if(!fncIsEmptyOrNull($strSubValidar))
	{
		$objTpl->SetVar("BLK_VALIDAR.BLK_ITEM.BLK_OTRO.nombre", $strSubValidar);
		$objTpl->Parse("BLK_VALIDAR.BLK_ITEM.BLK_OTRO");
		$objTpl->Parse("BLK_VALIDAR.BLK_ITEM");
	}
	$objTplAux = new Template(PATH_TEMPLATES . $strTemplate . "-f.html");
	return($objTplAux);
}
# --------------------
# ---------------------------------------------

# ---------------------------------------------
# Procedimientos.
# ---------------------------------------------
function subHeight(&$objTpl, $intH)
{
	$objTpl->SetVar("BLK_HEIGHT.height", $intH);
	$objTpl->Parse("BLK_HEIGHT");
}

function subHidden(&$objTpl, $strName, $value)
{
	# Html.	
	# SetVar.
	$objTpl->SetVar("BLK_HIDDEN.name",  $strName);
	$objTpl->SetVar("BLK_HIDDEN.value", $value);
	# Parse->
	$objTpl->Parse("BLK_HIDDEN");
}

# --------------------
# Validacion JS Otro.
# --------------------
/*
function subValidarJavaScriptOtroSetFile(&$objTpl, $strModulo){
	$objTpl->SetFile("BLK_VALIDAR.OTRO", PATH_TEMPLATES . $strModulo . "-validar-f.html");
}
function subValidarJavaScriptOtro(&$objTpl, $strNombreSub){
	$objTpl->SetVar("BLK_VALIDAR.BLK_ITEM.BLK_OTRO.nombre", $strNombreSub);
	$objTpl->Parse("BLK_VALIDAR.BLK_ITEM.BLK_OTRO");
	$objTpl->Parse("BLK_VALIDAR.BLK_ITEM");
}
*/
# --------------------

# --------------------
# Aux.
# --------------------
function subAuxParse(&$objTpl, &$objTplAux)
{
	$objTplAux->Parse("");
	$strContenido = $objTplAux->GetParseBuffer();
	unset($objTplAux);

	$objTpl->SetVar("BLK_INPUTS.BLK_TR.BLK_AUX.contenido", $strContenido);
	$objTpl->Parse("BLK_INPUTS.BLK_TR.BLK_AUX");
	$objTpl->Parse("BLK_INPUTS.BLK_TR");
}
# --------------------

function subSeparador(&$objTpl)
{
	#call subHeight(objTpl, 60)
	$objTpl->Parse("BLK_INPUTS.BLK_TR.BLK_SEPARADOR");
	$objTpl->Parse("BLK_INPUTS.BLK_TR");
}

function subTitulo(&$objTpl, $strTitle)
{
	$objTpl->SetVar("BLK_INPUTS.BLK_TR.BLK_TITULO.title", $strTitle);
	$objTpl->Parse("BLK_INPUTS.BLK_TR.BLK_TITULO");
	$objTpl->Parse("BLK_INPUTS.BLK_TR");
}

function subComentario(&$objTpl, $strComentario)
{
	$objTpl->SetVar("BLK_INPUTS.BLK_TR.BLK_CAMPO.BLK_COMENTARIO.value", $strComentario);
	$objTpl->Parse("BLK_INPUTS.BLK_TR.BLK_CAMPO.BLK_COMENTARIO");
	$objTpl->Parse("BLK_INPUTS.BLK_TR.BLK_CAMPO");
	$objTpl->Parse("BLK_INPUTS.BLK_TR");	
}	

function subField(&$objTpl, $blnObligatorio, $strName)
{
	if($blnObligatorio)
	{
		$objTpl->SetVar("BLK_INPUTS.BLK_TR.BLK_CAMPO.BLK_OBLIGATORIO.name", $strName);
		$objTpl->Parse("BLK_INPUTS.BLK_TR.BLK_CAMPO.BLK_OBLIGATORIO");
	}
	else
	{
		$objTpl->SetVar("BLK_INPUTS.BLK_TR.BLK_CAMPO.BLK_OPCIONAL.name", $strName);
		$objTpl->Parse("BLK_INPUTS.BLK_TR.BLK_CAMPO.BLK_OPCIONAL");
	}
}

function subLabel(&$objTpl, $strTitle, $value)
{
	# Html.
	subField($objTpl, false, $strTitle);

	$objTpl->SetVar("BLK_INPUTS.BLK_TR.BLK_CAMPO.BLK_LABEL.value", $value);

	$objTpl->Parse("BLK_INPUTS.BLK_TR.BLK_CAMPO.BLK_LABEL");
	$objTpl->Parse("BLK_INPUTS.BLK_TR.BLK_CAMPO");
	$objTpl->Parse("BLK_INPUTS.BLK_TR");
}

function subTextBox(&$objTpl, $strName, $intMaxLength, $value, $strField, $blnObligatorio, $strType)
{
   	# call subHeight(objTpl, 60)
	
	switch(strtolower($strType))
	{
		case("email"):
		{
			# JavaScript.
			if($blnObligatorio)
			{
				$objTpl->SetVar("BLK_VALIDAR.BLK_ITEM.BLK_EMAIL_OBLIGATORIO.name",  $strName);
				$objTpl->SetVar("BLK_VALIDAR.BLK_ITEM.BLK_EMAIL_OBLIGATORIO.field", $strField, false);
				$objTpl->Parse("BLK_VALIDAR.BLK_ITEM.BLK_EMAIL_OBLIGATORIO");
				$objTpl->Parse("BLK_VALIDAR.BLK_ITEM");
			}
			else
			{
				$objTpl->SetVar("BLK_VALIDAR.BLK_ITEM.BLK_EMAIL_OPCIONAL.name",  $strName);
				$objTpl->SetVar("BLK_VALIDAR.BLK_ITEM.BLK_EMAIL_OPCIONAL.field", $strField);
				$objTpl->Parse("BLK_VALIDAR.BLK_ITEM.BLK_EMAIL_OPCIONAL");
				$objTpl->Parse("BLK_VALIDAR.BLK_ITEM");
			}

			# Html.
			subField($objTpl, $blnObligatorio, $strField);

			$objTpl->SetVar("BLK_INPUTS.BLK_TR.BLK_CAMPO.BLK_TEXT.name",	  $strName);
			$objTpl->SetVar("BLK_INPUTS.BLK_TR.BLK_CAMPO.BLK_TEXT.size",	  fncSize($intMaxLength));
			$objTpl->SetVar("BLK_INPUTS.BLK_TR.BLK_CAMPO.BLK_TEXT.maxlength", $intMaxlength);
			$objTpl->SetVar("BLK_INPUTS.BLK_TR.BLK_CAMPO.BLK_TEXT.value",	  $value);

			$objTpl->Parse("BLK_INPUTS.BLK_TR.BLK_CAMPO.BLK_TEXT");
			$objTpl->Parse("BLK_INPUTS.BLK_TR.BLK_CAMPO");
			$objTpl->Parse("BLK_INPUTS.BLK_TR");

			break;
		}
		case("string"):
		{
			# JavaScript.
			if($blnObligatorio)
			{
				$objTpl->SetVar("BLK_VALIDAR.BLK_ITEM.BLK_TEXT.name",  $strName);
				$objTpl->SetVar("BLK_VALIDAR.BLK_ITEM.BLK_TEXT.field", $strField);
				$objTpl->Parse("BLK_VALIDAR.BLK_ITEM.BLK_TEXT");
				$objTpl->Parse("BLK_VALIDAR.BLK_ITEM");
			}

			# Html.
			subField($objTpl, $blnObligatorio, $strField);

			$objTpl->SetVar("BLK_INPUTS.BLK_TR.BLK_CAMPO.BLK_TEXT.name",	  $strName);
			$objTpl->SetVar("BLK_INPUTS.BLK_TR.BLK_CAMPO.BLK_TEXT.size",	  fncSize($intMaxLength));
			$objTpl->SetVar("BLK_INPUTS.BLK_TR.BLK_CAMPO.BLK_TEXT.maxlength", $intMaxlength);
			$objTpl->SetVar("BLK_INPUTS.BLK_TR.BLK_CAMPO.BLK_TEXT.value",	  $value);

			$objTpl->Parse("BLK_INPUTS.BLK_TR.BLK_CAMPO.BLK_TEXT");
			$objTpl->Parse("BLK_INPUTS.BLK_TR.BLK_CAMPO");
			$objTpl->Parse("BLK_INPUTS.BLK_TR");

			break;
		}
		case("url"):
		{
			# JavaScript.
			if($blnObligatorio)
			{
				$objTpl->SetVar("BLK_VALIDAR.BLK_ITEM.BLK_URL_OBLIGATORIO.name",  $strName);
				$objTpl->SetVar("BLK_VALIDAR.BLK_ITEM.BLK_URL_OBLIGATORIO.field", $strField);
				$objTpl->Parse("BLK_VALIDAR.BLK_ITEM.BLK_URL_OBLIGATORIO");
				$objTpl->Parse("BLK_VALIDAR.BLK_ITEM");
			}
			else
			{
				$objTpl->SetVar("BLK_VALIDAR.BLK_ITEM.BLK_URL_OPCIONAL.name",  $strName);
				$objTpl->SetVar("BLK_VALIDAR.BLK_ITEM.BLK_URL_OPCIONAL.field", $strField);
				$objTpl->Parse("BLK_VALIDAR.BLK_ITEM.BLK_URL_OPCIONAL");
				$objTpl->Parse("BLK_VALIDAR.BLK_ITEM");
			}

			# Html.
			subField($objTpl, $blnObligatorio, $strField);

			$objTpl->SetVar("BLK_INPUTS.BLK_TR.BLK_CAMPO.BLK_TEXT.name",	  $strName);
			$objTpl->SetVar("BLK_INPUTS.BLK_TR.BLK_CAMPO.BLK_TEXT.size",	  fncSize($intMaxLength));
			$objTpl->SetVar("BLK_INPUTS.BLK_TR.BLK_CAMPO.BLK_TEXT.maxlength", $intMaxlength);
			$objTpl->SetVar("BLK_INPUTS.BLK_TR.BLK_CAMPO.BLK_TEXT.value",	  $value);

			$objTpl->Parse("BLK_INPUTS.BLK_TR.BLK_CAMPO.BLK_TEXT");
			$objTpl->Parse("BLK_INPUTS.BLK_TR.BLK_CAMPO");
			$objTpl->Parse("BLK_INPUTS.BLK_TR");
			
			break;
		}
		default: subError(true, "", "begin_page_f.inc.asp -> subTextBox | El parametro strType (" . $strType . ") no coincide con ningun tipo");
	}
}

# --------------------
# CheckBox.
# --------------------
function subCheckBox(&$objTpl, $strName, $blnChecked, $strField, $value = 1)
{
	# Html.
	subField($objTpl, false, $strField);

	$objTpl->SetVar("BLK_INPUTS.BLK_TR.BLK_CAMPO.BLK_CHECKBOX.name",    $strName);
	$objTpl->SetVar("BLK_INPUTS.BLK_TR.BLK_CAMPO.BLK_CHECKBOX.value",   $value);
	$objTpl->SetVar("BLK_INPUTS.BLK_TR.BLK_CAMPO.BLK_CHECKBOX.checked", $blnChecked? "checked" : "");
	$objTpl->SetVar("BLK_INPUTS.BLK_TR.BLK_CAMPO.BLK_CHECKBOX.field",   "");

	$objTpl->Parse("BLK_INPUTS.BLK_TR.BLK_CAMPO.BLK_CHECKBOX");
	$objTpl->Parse("BLK_INPUTS.BLK_TR.BLK_CAMPO");
	$objTpl->Parse("BLK_INPUTS.BLK_TR");
}
function subCheckBoxs(&$objTpl, $strField, $strName, $source, $blnObligatorio)
{
	# Html.
	subField($objTpl, false, $strField);

	if(is_array($source))
	{
		# Array.
	}	
	else
	{
		# Query.
		$rs = new Rs($GLOBALS["conn"], $strSql);
		if(!$rs->EOF())
		{
			do
			{
				$objTpl->SetVar("BLK_INPUTS.BLK_TR.BLK_CAMPO.BLK_CHECKBOX.name",     $strName);
				$objTpl->SetVar("BLK_INPUTS.BLK_TR.BLK_CAMPO.BLK_CHECKBOX.value",    $rs->Field(0)->Value());
				$$objTpl->SetVar("BLK_INPUTS.BLK_TR.BLK_CAMPO.BLK_CHECKBOX.checked", $rs->Field(2)->Value() == -1? "checked" : "");
				$objTpl->SetVar("BLK_INPUTS.BLK_TR.BLK_CAMPO.BLK_CHECKBOX.field",    $rs->Field(1)->Value());
				# Parse.
				$objTpl.Parse("BLK_INPUTS.BLK_TR.BLK_CAMPO.BLK_CHECKBOX.BLK_BR");
				$objTpl.Parse("BLK_INPUTS.BLK_TR.BLK_CAMPO.BLK_CHECKBOX");
	
				$rs->MoveNext();
			}
			while(!$rs->EOF());
		}
	}
	$objTpl->Parse("BLK_INPUTS.BLK_TR.BLK_CAMPO");
	$objTpl->Parse("BLK_INPUTS.BLK_TR");
}
# --------------------

# --------------------
# Radio.
# --------------------
/*
sub subRadio(&$objTpl, $strField, $strName, $strChecked)

	# Html.	
	# Titulo.
	subField($objTpl, $false, $strField);
	# SetVar.
	$objTpl->SetVar("BLK_INPUTS.BLK_TR.BLK_CAMPO.BLK_CHECKBOX.name",    $strName);
	$objTpl->SetVar("BLK_INPUTS.BLK_TR.BLK_CAMPO.BLK_CHECKBOX.checked", $strChecked);
	# Parse.
	$objTpl->Parse("BLK_INPUTS.BLK_TR.BLK_CAMPO.BLK_CHECKBOX");
	$objTpl->Parse("BLK_INPUTS.BLK_TR.BLK_CAMPO");
	$objTpl->Parse("BLK_INPUTS.BLK_TR");
}
*/
function subRadios(&$objTpl, $strName, $strField, $source, $selected, $blnObligatorio)
{
	# JavaScript.
	if($blnObligatorio)
	{
		$objTpl->SetVar("BLK_VALIDAR.BLK_ITEM.BLK_RADIO.name",  $strName);
		$objTpl->SetVar("BLK_VALIDAR.BLK_ITEM.BLK_RADIO.field", $strField);
		$objTpl->Parse("BLK_VALIDAR.BLK_ITEM.BLK_RADIO");
		$objTpl->Parse("BLK_VALIDAR.BLK_ITEM");
	}

	# Html.
	subField($objTpl, $blnObligatorio, $strField);

	if(is_array($source))
	{
		# Array.
		foreach($source as $strKey => $value)
		{
			$objTpl->SetVar("BLK_INPUTS.BLK_TR.BLK_CAMPO.BLK_RADIO.name",    $strName);
			$objTpl->SetVar("BLK_INPUTS.BLK_TR.BLK_CAMPO.BLK_RADIO.value",   $strKey);
			$objTpl->SetVar("BLK_INPUTS.BLK_TR.BLK_CAMPO.BLK_RADIO.checked", $strKey == $selected? "checked" : "");
			$objTpl->SetVar("BLK_INPUTS.BLK_TR.BLK_CAMPO.BLK_RADIO.field",   $value);
		  # $objTpl->Parse("BLK_INPUTS.BLK_TR.BLK_CAMPO.BLK_RADIO.BLK_BR");
			$objTpl->Parse("BLK_INPUTS.BLK_TR.BLK_CAMPO.BLK_RADIO");
		}		
	}
	else
	{
		# Query.
	}

	$objTpl->Parse("BLK_INPUTS.BLK_TR.BLK_CAMPO");
	$objTpl->Parse("BLK_INPUTS.BLK_TR");						
}
# --------------------

# --------------------
# File.
# --------------------
function subFile(&$objTpl, $strName, $strDir, $strFile, $strField, $blnObligatorio, $strExtenciones)
{
    # call subHeight(&$objTpl, 60);

	# JavaScript.
	if($blnObligatorio)
	{
		$objTpl->SetVar("BLK_VALIDAR.BLK_ITEM.BLK_FILE_OBLIGATORIO.name",		 $strName);
		$objTpl->SetVar("BLK_VALIDAR.BLK_ITEM.BLK_FILE_OBLIGATORIO.field",	     $strField, false);
		$objTpl->SetVar("BLK_VALIDAR.BLK_ITEM.BLK_FILE_OBLIGATORIO.extenciones", $strExtenciones);
		$objTpl->Parse("BLK_VALIDAR.BLK_ITEM.BLK_FILE_OBLIGATORIO");
	}
	else
	{
		$objTpl->SetVar("BLK_VALIDAR.BLK_ITEM.BLK_FILE_OPCIONAL.name",		  $strName);
		$objTpl->SetVar("BLK_VALIDAR.BLK_ITEM.BLK_FILE_OPCIONAL.field",	      $strField, false);
		$objTpl->SetVar("BLK_VALIDAR.BLK_ITEM.BLK_FILE_OPCIONAL.extenciones", $strExtenciones);
		$objTpl->Parse("BLK_VALIDAR.BLK_ITEM.BLK_FILE_OPCIONAL");
	}
	# Parse.
	$objTpl->Parse("BLK_VALIDAR.BLK_ITEM");

	# Html.	
	subField($objTpl, $blnObligatorio, $strField);

	$objTpl->SetVar("BLK_INPUTS.BLK_TR.BLK_CAMPO.BLK_FILE.name",     $strName);
	$objTpl->SetVar("BLK_INPUTS.BLK_TR.BLK_CAMPO.BLK_FILE.file",     fncIifEmptyOrNull($strFile, "", fncImageYesOrNo($strDir, $strFile)));
	$objTpl->SetVar("BLK_INPUTS.BLK_TR.BLK_CAMPO.BLK_FILE.eliminar", $blnObligatorio? "false" : "true");

	$objTpl->Parse("BLK_INPUTS.BLK_TR.BLK_CAMPO.BLK_FILE");
	$objTpl->Parse("BLK_INPUTS.BLK_TR.BLK_CAMPO");
	$objTpl->Parse("BLK_INPUTS.BLK_TR");
}
function subFiles(&$objTpl, $strName, $strFiles, $strField, $intObligatorioHasta, $strExtenciones, $intCant, $strPath, $strUrl)
{
	$arrFiles = fncSplit($strFiles, ",");
	$intCount = count($arrFiles);	
	
	for($j = 0; $j <= $intCant - 1; $j++)
	{
		$intIndice 	    = $j + 1;
		$strFileName 	= ($j <= $intCount - 1)? $arrFiles[$j] : "";		                   
		$blnObligatorio = ($intIndice <= $intObligatorioHasta);
		subFile($objTpl, $strName . "_" . $intIndice, $strPath, $strUrl, $strFileName, $strField . " " . $intIndice . "º", $blnObligatorio, $strExtenciones);
	}
}
# --------------------

function subTextArea(&$objTpl, $strName, $intRows, $intCols, $value, $strField, $blnEnriquecido, $blnObligatorio)
{
	# subHeight(objTpl, intRows * 20);

	# JavaScript.
	if($blnObligatorio)
	{
		if($blnEnriquecido)
		{
			$objTpl->SetVar("BLK_VALIDAR.BLK_ITEM.BLK_TEXTAREA_ENRIQUECIDO.name",  $strName);
			$objTpl->SetVar("BLK_VALIDAR.BLK_ITEM.BLK_TEXTAREA_ENRIQUECIDO.field", $strField, false);
			$objTpl->Parse("BLK_VALIDAR.BLK_ITEM.BLK_TEXTAREA_ENRIQUECIDO");			
		}
		else
		{
			$objTpl->SetVar("BLK_VALIDAR.BLK_ITEM.BLK_TEXTAREA.name",  $strName);
			$objTpl->SetVar("BLK_VALIDAR.BLK_ITEM.BLK_TEXTAREA.field", $strField, false);
			$objTpl->Parse("BLK_VALIDAR.BLK_ITEM.BLK_TEXTAREA");
		}
		$objTpl->Parse("BLK_VALIDAR.BLK_ITEM");
	}

	# Html.	
	subField($objTpl, $blnObligatorio, $strField);

	if($blnEnriquecido)
	{
		$objTpl->SetVar("BLK_INPUTS.BLK_TR.BLK_CAMPO.BLK_TEXTAREA.name",				 $strName);
		$objTpl->SetVar("BLK_INPUTS.BLK_TR.BLK_CAMPO.BLK_TEXTAREA.rows",				 $intRows);
		$objTpl->SetVar("BLK_INPUTS.BLK_TR.BLK_CAMPO.BLK_TEXTAREA.cols",				 $intCols);
		$objTpl->SetVar("BLK_INPUTS.BLK_TR.BLK_CAMPO.BLK_TEXTAREA.value",				 $value);	
		$objTpl->SetVar("BLK_INPUTS.BLK_TR.BLK_CAMPO.BLK_TEXTAREA.BLK_ENRIQUECIDO.name", $strName);
		$objTpl->Parse("BLK_INPUTS.BLK_TR.BLK_CAMPO.BLK_TEXTAREA.BLK_ENRIQUECIDO");
	}
	else
	{
		$objTpl->SetVar("BLK_INPUTS.BLK_TR.BLK_CAMPO.BLK_TEXTAREA.name",  $strName);
		$objTpl->SetVar("BLK_INPUTS.BLK_TR.BLK_CAMPO.BLK_TEXTAREA.rows",  $intRows);
		$objTpl->SetVar("BLK_INPUTS.BLK_TR.BLK_CAMPO.BLK_TEXTAREA.cols",  $intCols);	
		$objTpl->SetVar("BLK_INPUTS.BLK_TR.BLK_CAMPO.BLK_TEXTAREA.value", $value);	
	}

	$objTpl->Parse("BLK_INPUTS.BLK_TR.BLK_CAMPO.BLK_TEXTAREA");
	$objTpl->Parse("BLK_INPUTS.BLK_TR.BLK_CAMPO");
	$objTpl->Parse("BLK_INPUTS.BLK_TR");
}

function subSelect(&$objTpl, $strName, $source, $value, $strField, $blnObligatorio)
{
	# JavaScript.
	if($blnObligatorio)
	{	
		$objTpl->SetVar("BLK_VALIDAR.BLK_ITEM.BLK_SELECT.name",  $strName);
		$objTpl->SetVar("BLK_VALIDAR.BLK_ITEM.BLK_SELECT.field", $strField, false);	
		$objTpl->Parse("BLK_VALIDAR.BLK_ITEM.BLK_SELECT");
		$objTpl->Parse("BLK_VALIDAR.BLK_ITEM");
	}

	# Html.	
	subField($objTpl, $blnObligatorio, $strField);	

	$objTpl->SetVar("BLK_INPUTS.BLK_TR.BLK_CAMPO.BLK_SELECT.name",  $strName);
	$objTpl->SetVar("BLK_INPUTS.BLK_TR.BLK_CAMPO.BLK_SELECT.title", $strField);

	# Select.
	if(is_array($source))
	{
		# Array.
		subFillSelectWithTpl_FromArray($source, $objTpl, "BLK_INPUTS.BLK_TR.BLK_CAMPO.BLK_SELECT.BLK_OPTION", $value);		
	}
	else
	{
		# Query.		
		subFillSelectWithTpl_FromSql($source, $objTpl, "BLK_INPUTS.BLK_TR.BLK_CAMPO.BLK_SELECT.BLK_OPTION", $value);	
	}

	# Parse.
	$objTpl->Parse("BLK_INPUTS.BLK_TR.BLK_CAMPO.BLK_SELECT");
	$objTpl->Parse("BLK_INPUTS.BLK_TR.BLK_CAMPO");
	$objTpl->Parse("BLK_INPUTS.BLK_TR");
}

function subDate(&$objTpl, $strName, $value, $strField, $blnObligatorio, $intFromYear, $intToYear)
{
	# call subHeight(&$objTpl, 60);

	# JavaScript.
	if($blnObligatorio)
	{
		$objTpl->SetVar("BLK_VALIDAR.BLK_ITEM.BLK_DATE_OBLIGATORIO.name",  $strName);
		$objTpl->SetVar("BLK_VALIDAR.BLK_ITEM.BLK_DATE_OBLIGATORIO.field", $strField);
		$objTpl->Parse("BLK_VALIDAR.BLK_ITEM.BLK_DATE_OBLIGATORIO");
		$objTpl->Parse("BLK_VALIDAR.BLK_ITEM");
	}
	else
	{
		$objTpl->SetVar("BLK_VALIDAR.BLK_ITEM.BLK_DATE_OPCIONAL.name",  $strName);
		$objTpl->SetVar("BLK_VALIDAR.BLK_ITEM.BLK_DATE_OPCIONAL.field", $strField);
		$objTpl->Parse("BLK_VALIDAR.BLK_ITEM.BLK_DATE_OPCIONAL");
		$objTpl->Parse("BLK_VALIDAR.BLK_ITEM");
	}

	# Html.	
	subField($objTpl, $blnObligatorio, $strField);

	$objTpl->SetVar("BLK_INPUTS.BLK_TR.BLK_CAMPO.BLK_DATE.name", $strName);
	
	$arrDate = fncDateToArray($value);	
	$objTpl->SetVar("BLK_INPUTS.BLK_TR.BLK_CAMPO.BLK_DATE.d",	 	 $arrDate["d"]);
	$objTpl->SetVar("BLK_INPUTS.BLK_TR.BLK_CAMPO.BLK_DATE.m",	 	 $arrDate["m"]);
	$objTpl->SetVar("BLK_INPUTS.BLK_TR.BLK_CAMPO.BLK_DATE.y",	 	 $arrDate["y"]);
	$objTpl->SetVar("BLK_INPUTS.BLK_TR.BLK_CAMPO.BLK_DATE.FromYear", $intFromYear);
	$objTpl->SetVar("BLK_INPUTS.BLK_TR.BLK_CAMPO.BLK_DATE.ToYear", 	 $intToYear);
	$objTpl->Parse("BLK_INPUTS.BLK_TR.BLK_CAMPO.BLK_DATE");
	$objTpl->Parse("BLK_INPUTS.BLK_TR.BLK_CAMPO");
	$objTpl->Parse("BLK_INPUTS.BLK_TR");
}

function subTextBoxNumeric(&$objTpl, $strName, $intMaxLength, $value, $strField, $intDecimales, $blnObligatorio)
{
	# call subHeight(&$objTpl, 60);

	# JavaScript.	
	if($blnObligatorio)
	{
		$objTpl->SetVar("BLK_VALIDAR.BLK_ITEM.BLK_NUMBER_OBLIGATORIO.name",      $strName);
		$objTpl->SetVar("BLK_VALIDAR.BLK_ITEM.BLK_NUMBER_OBLIGATORIO.field",     $strField);
		$objTpl->SetVar("BLK_VALIDAR.BLK_ITEM.BLK_NUMBER_OBLIGATORIO.decimales", $intDecimales);
		$objTpl->Parse("BLK_VALIDAR.BLK_ITEM.BLK_NUMBER_OBLIGATORIO");
	}
	else
	{
		$objTpl->SetVar("BLK_VALIDAR.BLK_ITEM.BLK_NUMBER_OPCIONAL.name",      $strName);
		$objTpl->SetVar("BLK_VALIDAR.BLK_ITEM.BLK_NUMBER_OPCIONAL.field",     $strField);
		$objTpl->SetVar("BLK_VALIDAR.BLK_ITEM.BLK_NUMBER_OPCIONAL.decimales", $intDecimales);	
		$objTpl->Parse("BLK_VALIDAR.BLK_ITEM.BLK_NUMBER_OPCIONAL");
	}

	$objTpl->Parse("BLK_VALIDAR.BLK_ITEM");

	# Html.	
	subField($objTpl, $blnObligatorio, $strField);

	$objTpl->SetVar("BLK_INPUTS.BLK_TR.BLK_CAMPO.BLK_NUMBER.name",      $strName);
	$objTpl->SetVar("BLK_INPUTS.BLK_TR.BLK_CAMPO.BLK_NUMBER.size",      fncSize($intMaxLength));
	$objTpl->SetVar("BLK_INPUTS.BLK_TR.BLK_CAMPO.BLK_NUMBER.maxlength", $intMaxLength);
	$objTpl->SetVar("BLK_INPUTS.BLK_TR.BLK_CAMPO.BLK_NUMBER.value",     $value);
	$objTpl->SetVar("BLK_INPUTS.BLK_TR.BLK_CAMPO.BLK_NUMBER.decimales", $intDecimales > 0? "true" : "false");

	$objTpl->Parse("BLK_INPUTS.BLK_TR.BLK_CAMPO.BLK_NUMBER");
	$objTpl->Parse("BLK_INPUTS.BLK_TR.BLK_CAMPO");
	$objTpl->Parse("BLK_INPUTS.BLK_TR");
}

function subMultiSelectOrder(&$objTpl, $strField, $strName, $source, $blnOrder, $blnMultiSelect, $intW, $intH, $blnObligatorio)
{
	global $objDBCommand;

	# subHeight(&$objTpl, intH + 20);

	if((!$blnOrder) && (!$blnMultiSelect))
		subError(true, "", "begin_page_f.inc.asp -> sub subMultiSelectOrder(ByRef objTpl, ByVal strField, ByVal strName, ByRef source, ByVal blnOrder, ByVal blnMultiSelect, ByVal intW, ByVal intH, ByVal blnObligatorio)<br>Los parametros blnOrder y blnMultiSelect no pueden tener ambos de valor false.");
	else
	{		
		if(($blnOrder) && ($blnMultiSelect))
			$intType = 3; # Multiselect y order.
		else
		{
			if($blnOrder)
				$intType = 2; # Order.
			else
				$intType = 1; # Multiselect.
		}
	}

	# Styles.
	$objTpl->SetVar("BLK_MULTISELECTORDER_STYLE.name", $strName);
	$objTpl->SetVar("BLK_MULTISELECTORDER_STYLE.w",	   $intW);
	$objTpl->SetVar("BLK_MULTISELECTORDER_STYLE.h",	   $intH);
	$objTpl->Parse("BLK_MULTISELECTORDER_STYLE");

	# JavaScript.
	if($blnObligatorio)
	{
		$objTpl->SetVar("BLK_VALIDAR.BLK_ITEM.BLK_MULTISELECTORDER.name",  $strName);
		$objTpl->SetVar("BLK_VALIDAR.BLK_ITEM.BLK_MULTISELECTORDER.field", $strField);
		$objTpl->Parse("BLK_VALIDAR.BLK_ITEM.BLK_MULTISELECTORDER");
		$objTpl->Parse("BLK_VALIDAR.BLK_ITEM");
	}
	# OnLoad.
	$objTpl->SetVar("BLK_MULTISELECTORDER_ONLOAD_JS.name", $strName);
	$objTpl->SetVar("BLK_MULTISELECTORDER_ONLOAD_JS.type", $intType);
	$objTpl->Parse("BLK_MULTISELECTORDER_ONLOAD_JS");
	# BeforeSubmit.
	$objTpl->SetVar("BLK_MULTISELECTORDER_BEFORESUBMIT_JS.name", $strName);
	$objTpl->Parse("BLK_MULTISELECTORDER_BEFORESUBMIT_JS");

	# Html.
	subField($objTpl, $blnObligatorio, $strField);
	# SetVar.
	$objTpl->SetVar("BLK_INPUTS.BLK_TR.BLK_CAMPO.BLK_MULTISELECTORDER.id",   $strName);
	$objTpl->SetVar("BLK_INPUTS.BLK_TR.BLK_CAMPO.BLK_MULTISELECTORDER.name", $strName);

	if(is_array($source))
	{
		# Array.
		foreach($source as $key => $val)
		{
			$objTpl->SetVar("BLK_INPUTS.BLK_TR.BLK_CAMPO.BLK_MULTISELECTORDER.BLK_OPCION.val",		$key);
		  # $objTpl->SetVar("BLK_INPUTS.BLK_TR.BLK_CAMPO.BLK_MULTISELECTORDER.BLK_OPCION.selected", $rs->Field(2)->Value()? "selected" : "");
			$objTpl->SetVar("BLK_INPUTS.BLK_TR.BLK_CAMPO.BLK_MULTISELECTORDER.BLK_OPCION.des",		$val);
			$objTpl->Parse("BLK_INPUTS.BLK_TR.BLK_CAMPO.BLK_MULTISELECTORDER.BLK_OPCION");			
		}
	}
	else
	{
		if(is_object($source))
			$rs = $source;
		else
			$rs = $GLOBALS["objDBCommand"]->Rs($source);

		do
		{
			$objTpl->SetVar("BLK_INPUTS.BLK_TR.BLK_CAMPO.BLK_MULTISELECTORDER.BLK_OPCION.val", $rs->Field(0)->Value());
			if($blnMultiSelect){$objTpl->SetVar("BLK_INPUTS.BLK_TR.BLK_CAMPO.BLK_MULTISELECTORDER.BLK_OPCION.selected", ($rs->Field(2)->Value() == 0)? "" : "selected");}
			$objTpl->SetVar("BLK_INPUTS.BLK_TR.BLK_CAMPO.BLK_MULTISELECTORDER.BLK_OPCION.des", $rs->Field(1)->Value());
			$objTpl->Parse("BLK_INPUTS.BLK_TR.BLK_CAMPO.BLK_MULTISELECTORDER.BLK_OPCION");
			$rs->MoveNext();
		}
		while(!$rs->EOF());
		unset($rs);
	}
	
	$objTpl->Parse("BLK_INPUTS.BLK_TR.BLK_CAMPO.BLK_MULTISELECTORDER");
	$objTpl->Parse("BLK_INPUTS.BLK_TR.BLK_CAMPO");
	$objTpl->Parse("BLK_INPUTS.BLK_TR");
}
# ---------------------------------------------

# ---------------------------------------------
#  Template.
# ---------------------------------------------
# Titulo.
switch($strOp)
{
	case("a"): 
	{
		$objTpl->SetVar("title_2", "Alta");
		break;
	}
	case("m"): 
	{
		$objTpl->SetVar("title_2", "Modificación");
		break;
	}
	case("d"):
	{
		$objTpl->SetVar("title_2", "Detalle");
		break;
	}
}

$objTpl->SetVar("message", fncRequest(REQUEST_METHOD_POST, "hdn_message", REQUEST_TYPE_STRING, ""));

$objTpl->SetFile("MIDDLE",	   	  PATH_TEMPLATES . "estructura-interna-f.html");
$objTpl->SetFile("MIDDLE.INPUTS", PATH_TEMPLATES . "estructura-inputs.html");

$objTpl->UseNamespace("MIDDLE", true);

$objTpl->SetVar("action",	 	PAGE_ABM);
$objTpl->SetVar("href_listado", PAGE_L);

# Pongo las variables en el form.
$objTpl->SetVar("INPUTS.height",	   fncRequest(REQUEST_METHOD_POST, "hdn_height", 	     REQUEST_TYPE_INTEGER, 0));
$objTpl->SetVar("INPUTS.op",		   $strOp);
$objTpl->SetVar("INPUTS.id",		   $intId);
$objTpl->SetVar("INPUTS.page",	       fncRequest(REQUEST_METHOD_POST, "hdn_page",   	     REQUEST_TYPE_STRING, ""));
$objTpl->SetVar("INPUTS.search_field", fncRequest(REQUEST_METHOD_POST, "hdn_search_field",   REQUEST_TYPE_STRING, ""));
$objTpl->SetVar("INPUTS.search_signo", fncRequest(REQUEST_METHOD_POST, "hdn_search_signo",   REQUEST_TYPE_STRING, ""));
$objTpl->SetVar("INPUTS.search_text",  fncRequest(REQUEST_METHOD_POST, "hdn_search_text",  	 REQUEST_TYPE_STRING, ""));
$objTpl->SetVar("INPUTS.search_fk",    fncRequest(REQUEST_METHOD_POST, "hdn_search_fk",      REQUEST_TYPE_STRING, ""));
$objTpl->SetVar("INPUTS.search_chk",   fncRequest(REQUEST_METHOD_POST, "hdn_search_chk", 	 REQUEST_TYPE_STRING, ""));
$objTpl->SetVar("INPUTS.OrderBy",	   fncRequest(REQUEST_METHOD_POST, "hdn_search_OrderBy", REQUEST_TYPE_STRING, ""));

# Botones.
if($strOp == "d")
	$objTpl->Parse("BLK_VOLVER");
else
{
	if(PERMISO == "a")	
		$objTpl->Parse("BLK_BOTONES.BLK_ACEPTAR_E");
	else
		$objTpl->Parse("BLK_BOTONES.BLK_ACEPTAR_D");

	$objTpl->Parse("BLK_BOTONES");
}
# ---------------------------------------------

# ---------------------------------------------
# Rs.
# ---------------------------------------------
$rs = $GLOBALS["objDBCommand"]->Rs
(
	"sp_" . TABLE . "_get",
	array("intId" => $intId)		
);

$blnEnabled = $rs->ExistField("habilitado");

if($strOp == "a")
{
	# Select estado.
	if($blnEnabled)
	{
		$objTpl->SetVar("BLK_ESTADO.selected_habilitado", "selected");
		$objTpl->Parse("BLK_ESTADO");
	}
}
else
{
	if($rs->EOF()){subError(false, "La consulta devuelve cero (0) registros<br>" . strSql);}
	
	# Auditoria.
	if($strOp == "m"){$objTpl->SetVar("auditoria", subGetAuditoriaAdministrador($rs));}

	# Select estado.
	if($blnEnabled)
	{
		if($rs->Field("habilitado")->Value())
			$objTpl->SetVar("BLK_ESTADO.selected_habilitado", "selected");
		else
			$objTpl->SetVar("BLK_ESTADO.selected_deshabilitado", "selected");
		
		$objTpl->Parse("BLK_ESTADO");
	}
}
# ---------------------------------------------
?>