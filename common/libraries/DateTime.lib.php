<?php
# ------------------------------------------------------------------------------------------------
# Nombre:		 fncDateFormat.
# Funcionalidad: Da formato a un parametro de tipo date.
# Parametros:	 datParameter - Date   - Fecha.
# 				 strFormat    - String - Formato.
# ------------------------------------------------------------------------------------------------
/*
function fncDateTimeFormat($datValue, $strFormat)
{
	$arrDate 	  = getdate($datValue);

	$intDay       = $arrDatea["mday"];
	$intMonth     = $arrDatea["mon"];
	$intYear      = $arrDatea["year"];
	$intHour_24   = $arrDatea["hours"];
	$intHour_24   = $arrDatea["minutes"];
	$intHour_24   = $arrDatea["sconds"];

	$strDD	      = fncDateFillWith0($intDay);
	$strMM	      = fncDateFillWith0($intMonth);
	$strYY        = substr($intYear, 2);
	$strHH_24     = fncDateFillWith0($intHour_24);
	$strMiMi      = fncDateFillWith0($intMinute);
	$strSS        = fncDateFillWith0($intSecond);	

	$intWeekDay   = weekday($datParameter);	
	$strDay       = fncDateDayName($intWeekDay, "eng");
	$strDia       = fncDateDayName($intWeekDay, "spa");

	$strMonth     = fncMonthName($intMonth, "eng");
	$strMes       = fncMonthName($intMonth, "spa");

	if($intHour_24 > 12)
	{
		$intHour_AmPm = $intHour_24 - 12;
		$strHH_AmPm   = fncDateFillWith0($intHour_AmPm);
		$strAmPm	  = "pm";
	}
	else
	{
		$intHour_AmPm = $intHour_24;
		$strHH_AmPm   = $strHH_24;
		$strAmPm	  = "am";
	}

	$strAux = $strFormat;

	$strAux = str_replace("[D]",    $intDay, 		 $strAux);
	$strAux = str_replace("[DD]",   $strDD, 		 $strAux);

	$strAux = str_replace("[M]",    $intMonth, 		 $strAux);
	$strAux = str_replace("[MM]",   $strMM, 		 $strAux);

	$strAux = str_replace("[YY]",   $strYY, 		 $strAux);
	$strAux = str_replace("[YYYY]", $intYear, 		 $strAux);

	$strAux = str_replace("[DAY]",    $strDay, 		 $strAux);
	$strAux = str_replace("[DIA]",    $strDia, 		 $strAux);

	$strAux = str_replace("[MONTH]",  $strMonth, 	 $strAux);
	$strAux = str_replace("[MES]",    $strMes, 		 $strAux);

	$strAux = str_replace("[HAMPM]",  $intHour_AmPm, $strAux);
	$strAux = str_replace("[HHAMPM]", $strHH_AmPm,   $strAux);

	$strAux = str_replace("[H24]",    $intHour_24,   $strAux);
	$strAux = str_replace("[HH24]",   $strHH_24,     $strAux);

	$strAux = str_replace("[MI]",     $intMinute,    $strAux);
	$strAux = str_replace("[MIMI]",   $strMiMi,      $strAux);

	$strAux = str_replace("[S]",      $intSecond,    $strAux);
	$strAux = str_replace("[SS]",     $strSS,        $strAux);

	$strAux = str_replace("[AMPM]",   $strAmPm,      $strAux);

	return($strAux);
}
*/
# ------------------------------------------------------------------------------------------------

# ------------------------------------------------------------------------------------------------
# Nombre:		 fncDateFillWith0.
# Funcionalidad: Completa con 0 un valor que se le pasa como parametro simpre y cuando sea menor a 10.
# Parametros:	 intParameter - Integer.
# ------------------------------------------------------------------------------------------------
function fncDateFillWith0($intParameter)
{
	if($intParameter < 10)
		return("0" . $intParameter);
	else
		return((string) $intParameter);
}
# ------------------------------------------------------------------------------------------------

# ------------------------------------------------------------------------------------------------
# Nombre:		 fncDateSacar0.
# Funcionalidad: Saca el 1º 0.
# Parametros:	 strParameter - String.
# ------------------------------------------------------------------------------------------------
function fncDateSacar0($strParameter)
{
	if((strlen($strParameter) == 2) && (substr($strParameter, 0, 1) == "0"))
		return((int) substr($strParameter, 1));
	else
		return((int) $strParameter);
}
# ------------------------------------------------------------------------------------------------

# ------------------------------------------------------------------------------------------------
#
# ------------------------------------------------------------------------------------------------
function fncDayName($intWeekDay, $strLanguage)
{
	$arrDays[1]["esp"] = "Lunes";
	$arrDays[2]["esp"] = "Martes";
	$arrDays[3]["esp"] = "Miercoles";
	$arrDays[4]["esp"] = "Jueves";
	$arrDays[5]["esp"] = "Viernes";
	$arrDays[6]["esp"] = "Sabado";
	$arrDays[7]["esp"] = "Domingo";

	$arrDays[1]["eng"] = "Monday";
	$arrDays[2]["eng"] = "Tuesday";
	$arrDays[3]["eng"] = "Wednesday";
	$arrDays[4]["eng"] = "Thursday";
	$arrDays[5]["eng"] = "Friday";
	$arrDays[6]["eng"] = "Saturday";
	$arrDays[7]["eng"] = "Sunday";	

	$arrDays[1]["por"] = "segunda-feira";
	$arrDays[2]["por"] = "terça-feira";
	$arrDays[3]["por"] = "Quarta-feira";
	$arrDays[4]["por"] = "quinta-feira";
	$arrDays[5]["por"] = "sexta-feira";
	$arrDays[6]["por"] = "Sábado";
	$arrDays[7]["por"] = "Domingo";	
		
	return($arrDays[$intWeekDay][strtolower($strLanguage)]);
}
# ------------------------------------------------------------------------------------------------

# ------------------------------------------------------------------------------------------------
# 
# ------------------------------------------------------------------------------------------------
function fncMonthName($intMonth, $strLanguage)
{
	$arrMonths[1]["esp"]  = "Enero";
	$arrMonths[2]["esp"]  = "Febrero";
	$arrMonths[3]["esp"]  = "Marzo";
	$arrMonths[4]["esp"]  = "Abril";
	$arrMonths[5]["esp"]  = "Mayo";
	$arrMonths[6]["esp"]  = "Junio";
	$arrMonths[7]["esp"]  = "Julio";
	$arrMonths[8]["esp"]  = "Agosto";
	$arrMonths[9]["esp"]  = "Septiembre";
	$arrMonths[10]["esp"] = "Octubre";
	$arrMonths[11]["esp"] = "Noviembre";
	$arrMonths[12]["esp"] = "Diciembre";

	$arrMonths[1]["eng"]  = "January";
	$arrMonths[2]["eng"]  = "Febrauary";
	$arrMonths[3]["eng"]  = "March";
	$arrMonths[4]["eng"]  = "April";
	$arrMonths[5]["eng"]  = "May";
	$arrMonths[6]["eng"]  = "June";
	$arrMonths[7]["eng"]  = "July";
	$arrMonths[8]["eng"]  = "August";
	$arrMonths[9]["eng"]  = "September";
	$arrMonths[10]["eng"] = "October";
	$arrMonths[11]["eng"] = "November";
	$arrMonths[12]["eng"] = "December";

	$arrMonths[1]["por"]  = "Janeiro";
	$arrMonths[2]["por"]  = "Fevereiro";
	$arrMonths[3]["por"]  = "Março";
	$arrMonths[4]["por"]  = "Abril";
	$arrMonths[5]["por"]  = "poder";
	$arrMonths[6]["por"]  = "Junho";
	$arrMonths[7]["por"]  = "Julho";
	$arrMonths[8]["eng"]  = "Agosto";
	$arrMonths[9]["por"]  = "Setembro";
	$arrMonths[10]["por"] = "Outubro";
	$arrMonths[11]["por"] = "Novembro";
	$arrMonths[12]["por"] = "Dezembro";
	
	return($arrMonths[$intMonth][strtolower($strLanguage)]);
}
# ------------------------------------------------------------------------------------------------

# ------------------------------------------------------------------------------------------------
# Nombre:		 fncDateToArray.
# Funcionalidad: Devuelve un array asociativo. sus claves son: 'd', 'm', 'y', 'h', 'mi', 's'.
# Parametros:	 $value - string yyyy-mm-dd hh:mm:ss (Como vine del MySql) o int que representa una fecha. 
# ------------------------------------------------------------------------------------------------
function fncDateToArray($value)
{
	if(fncIsEmptyOrNull($value))
	{
		# Fecha.
		$iD  = 0;
		$iM  = 0;
		$iY  = 0;
		# Tiempo.
		$iHo = 0;
		$iMi = 0;
		$iSe = 0;
	}
	else
	{
		if(gettype($value) == "string")
		{
			# $value -> string yyyy-mm-dd hh:mm:ss (Como vine del MySql).			
			# Separa la fecha del tiempo.
			$arrDateTime = explode(" ", $value);

			# Fecha.
			$strDate = $arrDateTime[0]; # yyyy-mm-dd.
			$arrDate = explode("-", $strDate);

			# Tiempo.
			$strTime = $arrDateTime[1]; # hh:mm:ss.
			$arrTime = explode(":", $strTime);
					
			# Fecha.
			$iD  = (int) fncDateSacar0($arrDate[2]);
			$iM  = (int) fncDateSacar0($arrDate[1]);
			$iY  = (int) $arrDate[0];
			# Tiempo.
			$iHo = (int) fncDateSacar0($arrTime[0]);
			$iMi = (int) fncDateSacar0($arrTime[1]);
			$iSe = (int) fncDateSacar0($arrTime[2]);
		}
		else
		{
			// value -> integer -> Representa fecha.
			# Fecha.
			$iD  = (int) fncDateSacar0(date("d", $value));
			$iM  = (int) fncDateSacar0(date("m", $value));
			$iY  = date("Y", $value);
			# Tiempo.
			$iHo = (int) fncDateSacar0(date("h", $value));
			$iMi = (int) fncDateSacar0(date("i", $value));
			$iSe = (int) fncDateSacar0(date("s", $value));			
		}
	}
	
	$arrRes = Array
	(
		# Fecha.
		"d"  => $iD,
		"m"  => $iM,
		"y"  => $iY,
		# Tiempo.
		"h"  => $iHo,
		"mi" => $iMi,
		"s"  => $iSe
	);
				
	return($arrRes);
}
# ------------------------------------------------------------------------------------------------

# ------------------------------------------------------------------------------------------------ 
function fncDateMySqlToUnixTimeStamp($strDate)
{
	# strDate = yyyy-mm-dd hh:mm:ss.
	if(fncIsEmptyOrNull($strDate))
	{
		return(mktime());
	}
	else
	{
		# Separa la fecha del tiempo.
		$arrDateTime = explode(" ", $strDate);
		# Fecha.
		$strDate = $arrDateTime[0]; # yyyy-mm-dd.
		$arrDate = explode("-", $strDate);
		$intY	 = $arrDate[0];
		$intM	 = $arrDate[1];
		$intD	 = $arrDate[2];
		# Tiempo.
		$strTime = $arrDateTime[1]; # hh:mm:ss.
		if(fncIsEmptyOrNull($strTime))
		{	
			$intHo = 0;
			$intMi = 0;
			$intSe = 0;
		}
		else
		{
			$arrTime = explode(":", $strTime);
			$intHo   = $arrTime[0];
			$intMi   = $arrTime[1];
			$intSe   = $arrTime[2];
		}

		/*
		echo($intD . "/" . $intM . "/" . $intY . " " . $intMi . ":" . $intHo . ":" . $intSe);
		die;
		*/
		
		return
		(
			mktime
			(
				$intHo, 
				$intMi, 
				$intSe, 
				$intM, 
				$intD, 
				$intY
			)		
		);
	}
}
# ------------------------------------------------------------------------------------------------

# ------------------------------------------------------------------------------------------------
function fncDateMySqlToArray($strDate)
{
	$d = fncDateMySqlToUnixTimeStamp($strDate);
	return(fncDateToArray($d));
}
# ------------------------------------------------------------------------------------------------

# ------------------------------------------------------------------------------------------------
function fncDaysDiff($dte1, $dte2)
{		
	$intDif = $dte2 - $dte1;
	$intDif = $intDif / (60 * 60 * 24);
	$intDif = abs($intDif);
	$intDif = floor($intDif);
	return($intDif);
}
# ------------------------------------------------------------------------------------------------

# ------------------------------------------------------------------------------------------------
function fncAddDays($dte, $intD)
{		
	return($dte + (60 * 60 * (24 * $intD)));
}
# ------------------------------------------------------------------------------------------------

# ------------------------------------------------------------------------------------------------
function fncSpanishFormatDateToCanonicalDateStrinig($str){
	if(fncIsEmptyOrNull($str))
		return(null);
	else
	{
		$arrDate = explode("/", $str);
		return("$arrDate[2]-$arrDate[1]-$arrDate[0]");
	}
}

function fncCanonicalStringDateToSpanishDate($str){
	if(fncIsEmptyOrNull($str))
		return("");
	else
	{
		$arrDate = explode("-", $str);
		return("$arrDate[2]/$arrDate[1]/$arrDate[0]");
	}
}
# ------------------------------------------------------------------------------------------------
?>
