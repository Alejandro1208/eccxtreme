function subOnKeyUp(objTxt, blnDecimal)
{
	var strChar;

	strChar = objTxt.value.substring (objTxt.value.length - 1);

	if(objTxt.value != "")
	{
		if(blnDecimal)
		{
			if(!(isDigit(strChar) || strChar == "," || strChar == "."))
			{
				alert("Este campo solo acepta caracteres numericos (del 0 al 9) o el separador de decimales (',' o '.').");
				objTxt.value = objTxt.value.substring (0, objTxt.value.length - 1);
			}
		}
		else
		{
			if(!isDigit(strChar))
			{
				alert("Este campo solo acepta caracteres numericos (del 0 al 9).");
				objTxt.value = objTxt.value.substring (0, objTxt.value.length - 1);
			}
		}
	}
}

function subTextBoxNumeric(strName, size, MaxLength, value, blnDecimal)
{
	document.write ("<input type='text' id='" + strName + "' name='" + strName + "' size='" + size + "' maxlength='" + MaxLength + "' onKeyUp=subOnKeyUp(this," + blnDecimal + "); value='" + value + "'>");
}