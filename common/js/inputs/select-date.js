function fncAgregar0(parameter)
{
	var strParameter = new String(parameter);

	if(strParameter.length == 1)
		return("0" + strParameter);
	else
		return(strParameter);
}

function fncSacar0(parameter)
{
	var strParameter = new String(parameter);
	
	if(parameter == "")
		return(0);
	else
	{
		if(strParameter.length = 2 && strParameter.substr (0, 1) == "0")
			return(Number(strParameter.substr (1, 1)));
		else
			return(Number(strParameter));
	}
}

function fncMonth(intMonth)
{
	var arrMeses  = new Array(12);

	arrMeses[1]  = "Enero";
	arrMeses[2]  = "Febrero";
	arrMeses[3]  = "Marzo";
	arrMeses[4]  = "Abril";
	arrMeses[5]  = "Mayo";
	arrMeses[6]  = "Junio";
	arrMeses[7]  = "Julio";
	arrMeses[8]  = "Agosto";
	arrMeses[9]  = "Septiembre";
	arrMeses[10] = "Octubre";
	arrMeses[11] = "Noviembre";
	arrMeses[12] = "Diciembre";

	return(arrMeses[intMonth]);
}

function subSelectDays(strName, strDay_p)
{
	var strOnChange = "document.getElementById('" + strName + "_m').focus();";
	var intDay = fncSacar0(strDay_p);
	var j;
			
	document.write ("<select id='" + strName + "_d' name='" + strName + "_d' onChange=" + strOnChange + ">");
	
		document.write ("<option value='' selected>[Día]</option>");
		document.write ("<optgroup label=''></optgroup>");
			
		for(j = 1; j <= 31; j ++)
		{			
			document.write  ("<option value='" + j + "'");

			if(j == intDay) 
				document.write (" selected");
		
			document.write (">" + fncAgregar0(j) + "</option>");
		}
	
	document.write ("</select>");
}

function subSelectMonths(strName, strMonth)
{
	var strOnChange = "document.getElementById('" + strName + "_y').focus();";
	var intMonth = fncSacar0(strMonth);
	var j;

	document.write ("<select id='" + strName + "_m' name='" + strName + "_m' onChange=" + strOnChange + ">");
	
		document.write ("<option value='' selected>[Mes]</option>");
		document.write ("<optgroup label=''></optgroup>");
			
		for(j = 1; j <= 12; j ++)
		{			
			document.write ("<option value='" + j + "'");
		
			if(j == intMonth) 
				document.write (" selected");
		
			document.write (">" + fncMonth(j) + "</option>");
		}
	
	document.write ("</select>");
}

function subSelectYears(strName, strYear_p, intFromYear, intToYear)
{
	var strYear = new String(strYear_p);
	var intYear;
	var j;
	var dte = new Date();

	if(strYear == "")
		intYear = 0;
	else
		intYear = Number(strYear);

	document.write ("<select id='" + strName + "_y' name='" + strName + "_y'>")
	
		document.write ("<option value='' selected>[Año]</option>")
		document.write ("<optgroup label=''></optgroup>")

		for(j = intFromYear; j <= intToYear; j ++)
		{			
			document.write ("<option value='" + j + "'");
		
			if(j == intYear) 
				document.write (" selected");
		
			document.write (">" + j + "</option>");
		}
	
	document.write ("</select>");
}

function subSelectsDate(strName, d, m, y, intFromYear, intToYear)
{
	subSelectDays   (strName, d);
	document.write  (" ");	
	subSelectMonths (strName, m);
	document.write  (" ");	
	subSelectYears  (strName, y, intFromYear, intToYear);
}