<?php
function fncBodyEmail($strBody, $blnEnriquecido)
{
	$objTpl = new Template(PATH . "common/templates/" . ($blnEnriquecido? "estructura-email-enriquecido.html" : "estructura-email-simple.html"));
	$objTpl->SetGlobal("strImgDir", IMG_DIR);
	$objTpl->SetGlobal("strCssDir", STYLES_DIR);	
	$objTpl->SetVar("strBody", $strBody);
	$objTpl->Parse("");
	return($objTpl->GetParseBuffer());
	empty($objTpl);
}

function fncUnir($strName, $strEmail)
{
	if(fncIsEmptyOrNull($strName))
		return($strEmail);
	else
		return($strName . "<" . $strEmail . ">");
}

function subEmail($strFromName, $strFromEmail, $strToName, $strToEmail, $strSubject, $strBody, $blnHtmlBody)
{
	$strFrom = fncUnir($strFromName, $strFromEmail);
	$strTo   = fncUnir($strToName, $strToEmail);

	$sHeader = "";
	if(!fncIsEmptyOrNull($strFrom))
	{
		$sHeader .= "From: "     . $strFrom . "\r\n";
    	$sHeader .= "Reply-To: " . $strFrom . "\r\n";
	}
	if($blnHtmlBody)
	{
		$sHeader .= "MIME-Version: 1.0\r\n";
		$sHeader .= "Content-type: text/html; charset=iso-8859-1\r\n";
	}

	/*
	$sHeader .= 'To: María <maria@example.com>, Kelly <kelly@example.com>' . "\r\n";
	$sHeader .= 'Cc: archivo@example.com' 								   . "\r\n";
	$sHeader .= 'Bcc: chequeo@example.com' 								   . "\r\n";
	*/

	mail($strTo, $strSubject, $strBody, $sHeader);
}
?>
