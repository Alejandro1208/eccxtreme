<?php
# ---------------------------------------------
# Procedimientos.
# ---------------------------------------------
function subFechaInicioCara($objTpl)
{
	$strDate = date("Y-m-d H:i:s", mktime());
	subHidden($objTpl, "fecha_inicio_carga", $strDate);	
}

function subAuditar($intId, $strOp)
{
	$dteInicio = fncRequest(REQUEST_METHOD_POST, "hdn_fecha_inicio_carga", REQUEST_TYPE_STRING, "");	
	$strDate   = date("Y-m-d H:i:s", mktime());
		
	$arrParameters = array
	(
		"strTabla"     	   => TABLE,							
		"intId"       	   => $intId,
		"strAdministrador" => subGetAuditoriaAdministradorLogueado(),
		"strAccion"		   => $strOp,
		"dteInicio"		   => fncIsEmptyOrNull($dteInicio)? $strDate : $dteInicio,
		"dteFin"		   => $strDate
 	);
 	$GLOBALS["objDBCommand"]->Execute("sp_be_auditoria_a", $arrParameters);
} 

function subFechaInicioFin_f(&$objTpl, $dteInicio, $dteFin)
{
	$arrDateInicio = fncDateToArray($dteInicio);
	$arrDateFin    = fncDateToArray($dteFin);

	$objTplFecha = subAuxInit($objTpl, "fecha-inicio-fin", "ValidarFechaInicioFin");

	$objTplFecha->SetVar("d_inicio", $arrDateInicio["d"]);
	$objTplFecha->SetVar("m_inicio", $arrDateInicio["m"]);
	$objTplFecha->SetVar("y_inicio", $arrDateInicio["y"]);

	$objTplFecha->SetVar("FromYear", date("Y"));
	$objTplFecha->SetVar("ToYear",   date("Y") + 5);
	
	$objTplFecha->SetVar("d_fin", $arrDateFin["d"]);
	$objTplFecha->SetVar("m_fin", $arrDateFin["m"]);
	$objTplFecha->SetVar("y_fin", $arrDateFin["y"]);

	subAuxParse($objTpl, $objTplFecha);
}

function subFechaVigencia_f(&$objTpl, $dteInicio, $dteFin)
{
	$arrDateInicio = fncDateToArray($dteInicio);
	$arrDateFin    = fncDateToArray($dteFin);

	$objTplFecha = subAuxInit($objTpl, "fecha-vigencia", "ValidarFechaVigencia");

	$objTplFecha->SetVar("d_desde", $arrDateInicio["d"]);
	$objTplFecha->SetVar("m_desde", $arrDateInicio["m"]);
	$objTplFecha->SetVar("y_desde", $arrDateInicio["y"]);

	$objTplFecha->SetVar("FromYear", date("Y"));
	$objTplFecha->SetVar("ToYear",   date("Y") + 5);
	
	$objTplFecha->SetVar("d_hasta", $arrDateFin["d"]);
	$objTplFecha->SetVar("m_hasta", $arrDateFin["m"]);
	$objTplFecha->SetVar("y_hasta", $arrDateFin["y"]);

	subAuxParse($objTpl, $objTplFecha);
}

function subHorarios_f(&$objTpl, $strSp, $intFk)
{
	$objTplHorarios = subAuxInit($objTpl, "horarios", "ValidarHorarios");

	$arrParameters = array("intFk" => $intFk);
	$rs 		   = $GLOBALS["objDBCommand"]->Rs($strSp, $arrParameters);
	while(!$rs->EOF())
	{
		$objTplHorarios->SetVar("DIA.nombre", substr(fncDateDayName($rs->Field("dia")->Value(), "esp"), 0, 2));
		$objTplHorarios->SetVar("DIA.numero", $rs->Field("dia")->Value());
		$objTplHorarios->SetVar("DIA.value",  $rs->Field("descripcion")->Value());
		$objTplHorarios->Parse("DIA");
		$rs->MoveNext();
	}

	subAuxParse($objTpl, $objTplHorarios);
}
function subHorarios_abm($strOp, $strSpBaja, $strSpAlta, $intFk)
{
	if($strOp == "m")
	{
		$arrParameters = array("intFk" => $intFk);
		$GLOBALS["objDBCommand"]->Execute($strSpBaja, $arrParameters);
	}
	
	for($intDia = 1; $intDia <= 7; $intDia ++)
	{
		$strHorario = fncRequest(REQUEST_METHOD_POST, "txt_horario_" . $intDia, REQUEST_TYPE_STRING, "");
		if(!empty($strHorario))
		{
			$arrParameters = array
			(
				"intFk" => $intFk,
				"intDia" => $intDia,
				"strDescripcion" => $strHorario
			);
			$GLOBALS["objDBCommand"]->Execute($strSpAlta, $arrParameters);
		}
	}
}
# ---------------------------------------------
?>
