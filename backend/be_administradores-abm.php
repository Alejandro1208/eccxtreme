<?php
require_once("includes/begin-page-abm.inc.php");

# ---------------------------------------------
# Procedimientos.
# ---------------------------------------------
function subExiste($strParameters, $strOp, $strUsername)
{
	subExist($strParameters, $strOp, "Ya existe un registro con username igual a \'" . $strUsername . "\'.");	
}
# ---------------------------------------------

# ---------------------------------------------
# Proceso.
# ---------------------------------------------
switch($strOp)
{
	case "a":
	case "m":
	{
		$strUsername = fncRequest(REQUEST_METHOD_POST, "txt_username", REQUEST_TYPE_STRING, "");

		$arrParameters = array
						(
							"strUsername" => $strUsername,
							"intId" 	  => $intId 
				 	    );

		subExiste($arrParameters, $strOp, $strUsername);

		$arrParameters = array
						(
							"strUsername"     	=> $strUsername,
							"strPassword"  	  	=> fncRequest(REQUEST_METHOD_POST, "txt_password",   REQUEST_TYPE_STRING,  ""),
							"strNombre"    	  	=> fncRequest(REQUEST_METHOD_POST, "txt_nombre", 	 REQUEST_TYPE_STRING,  ""),
							"strApellido" 	 	=> fncRequest(REQUEST_METHOD_POST, "txt_apellido",   REQUEST_TYPE_STRING,  ""),
							"intFk_id_perfil"   => fncRequest(REQUEST_METHOD_POST, "cmb_perfil",     REQUEST_TYPE_INTEGER, null),
							"strAuditoriaAdmin" => subGetAuditoriaAdministradorLogueado(),
							"blnHabilitado"     => fncRequest(REQUEST_METHOD_POST, "cmb_habilitado", REQUEST_TYPE_INTEGER, 1)							
						);

		if($strOp == "a")
		{
			$GLOBALS["objDBCommand"]->Execute($strSql, $arrParameters);
			$intId = fncLastId();
		}
		else
		{
			$arrParameters["intId"] = $intId;
			$GLOBALS["objDBCommand"]->Execute($strSql, $arrParameters);
		}

		$strMensaje = fncMensajeOk($strOp);
		break;
	}
	case "b":
	{
		$arrParameters["intId"] = $intId;
		$GLOBALS["objDBCommand"]->Execute($strSql, $arrParameters);
		$strMensaje = fncMensajeOk($strOp);
		break;
	}
	case "h":
	{
		$rs = $GLOBALS["objDBCommand"]->Rs("sp_" . TABLE . "_get", array("intId" => $intId));
		$GLOBALS["objDBCommand"]->Execute($strSql, array("intId" => $intId));
		$strMensaje = fncMensajeOk($strOp);
		break;
	}
	case "dh":
	{
		$GLOBALS["objDBCommand"]->Execute($strSql, array("intId" => $intId));
		$strMensaje = fncMensajeOk($strOp);
		break;
	}
}
# ---------------------------------------------

require_once("includes/end-page-abm.inc.php");	
?>

