function fncIsDigit(strValue)
{
	var j;
	
	arrDigits = new Array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9);
	
	
	for(j = 0; j <= arrDigits.length - 1; j ++)
	{
		if(arrDigits[j] == Number(strValue))
			return(true);
	}
	
	return(false);
}

function subOnChange(intSelectedIndex, strId)
{
	if(intSelectedIndex > 0)
		document.getElementById (strId).focus ();
}

function subOnKeyUp(objTxt, strId)
{
	var strChar;


	strChar = objTxt.value.substring (objTxt.value.length - 1);
	if(!fncIsDigit(strChar))
	{
		alert("Este campo solo acepta caracteres numericos enteros.");
		objTxt.value = objTxt.value.substring (0, objTxt.value.length - 1);
	}
		
	if(objTxt.value.length == 8)
		document.getElementById(strId).focus();
}

function subCUIT(strName, CUIT)
{
	var strCUIT = new String(CUIT);
	var strC1, strC2, strC3;
	
	var arr = new Array(20, 23, 27, 30, 33);	
	
	
	var j


	strC1 = strCUIT.substr (0,  2);
	strC2 = strCUIT.substr (2,  8);
	strC3 = strCUIT.substr (10, 1);					
		
	// Select 1
	document.write ("<select id='id_cmb_'" + strName + "_1 name='cmb_" + strName + "_1' onChange=subOnChange(this.selectedIndex,'id_txt_" + strName + "_2');>");
	document.write		("<option value='' selected>&nbsp;</option>");
	
	for(j = 0; j <= arr.length - 1; j ++)
	{	
		document.write ("<option value='" + arr[j] + "'"); 

		if(arr[j] == Number(strC1)) 
			document.write (" selected");

		document.write (">" + arr[j] + "</option>");
	}
	
	document.write ("</select>");
	
	// Separador.
	document.write (" - ");

	// TextBox.
	document.write ("<input type='text' id='id_txt_" + strName + "_2' name='txt_" + strName + "_2' size='9' maxlength='8' onKeyUp=subOnKeyUp(this,'id_cmb_" + strName + "_3'); value='" + strC2 + "'>");
	
	// Separador.
	document.write (" / ");
	
	// Select 2.
	document.write ("<select id='id_cmb_" + strName + "_3' name='cmb_" + strName + "_3'>")
	document.write		("<option value='' selected>&nbsp;</option>")
		
	for(j = 1; j <= 9; j ++)
	{
		document.write	("<option value='" + j + "'")
		
		if(j == Number(strC3)) 
			document.write (" selected");
		
		document.write (">" + j + "</option>");
	}
		
	document.write ("</select>");
}
