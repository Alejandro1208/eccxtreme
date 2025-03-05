function subDateHour()
{
	function fncFillWithCeros(intParameter)
	{
		if(intParameter < 10)
			return("0" + String(intParameter));
		else
			return(String(intParameter));
	}

	var strHH, strMM, strAmPm;

	var ArrDays   = new Array("Domingo", "Lunes", "Martes", "Miercoles", "Jueves", "Viernes", "Sabado");
	var ArrMonths = new Array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");

	var objDate   = new Date();

	var objTdDate = document.getElementById ("id_td_date");
	var objTdHour = document.getElementById ("id_td_hour");

	var intH      = objDate.getHours ();

	// Date.
	objTdDate.innerHTML = ArrDays[objDate.getDay ()] + ", " + fncFillWithCeros(objDate.getDate ()) + " de " + ArrMonths[objDate.getMonth ()] + " de " + objDate.getFullYear (); 

	// Hour.
	if(intH < 12)
		strAmPm = "AM";
	else
	{
		strAmPm = "PM";
		intH 	= intH - 12;
	}

	if(intH < 10)
		strHH = "0" + intH;
	else
		strHH = intH;

	objTdHour.innerHTML = strHH + ":" + fncFillWithCeros(objDate.getMinutes ()) + " " + strAmPm;

	setTimeout(subDateHour, 1000);
}

function subOpenPopUp(strURL){subPopUp(strURL, true, 700, 500);}
