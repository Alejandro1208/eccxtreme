<?php
require_once("includes/begin-page-abm.inc.php");

# ---------------------------------------------
# Procedimientos.
# ---------------------------------------------
function subExiste($arrParameters, $strOp, $strNombre)
{
	subExist($arrParameters, $strOp, "Ya existe un registro con nombre igual a \'" . $strNombre . "\'.");	
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
		$strNombre = fncRequest(REQUEST_METHOD_POST, "txt_nombre", REQUEST_TYPE_STRING, "");

		$arrParameters = array
						(
							"strNombre" => $strNombre,
							"intId" 	=> $intId 
				 	    );

		subExiste($arrParameters, $strOp, $strNombre);

		$arrParameters = array
						(
							"strNombre"     	=> $strNombre,
							"strAuditoriaAdmin" => subGetAuditoriaAdministradorLogueado(),
							"blnHabilitado" 	=> fncRequest(REQUEST_METHOD_POST, "cmb_habilitado", REQUEST_TYPE_INTEGER, 1)							
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

		$objDBCommand->Execute("sp_be_perfiles__be_modulos_b_xPerfil", array("intFk_id_perfil" => $intId));

		foreach($_POST as $strName => $value)
		{
			if(substr($strName, 0, 2) == "r_")
			{
				$objDBCommand->Execute
				(
					"sp_be_perfiles__be_modulos_a",
					array
					(	
						"intFk_id_perfil" => $intId,
						"intFk_id_modulo" => substr($strName, 2),
						strPermiso        => $value
					)
				);
			}
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

