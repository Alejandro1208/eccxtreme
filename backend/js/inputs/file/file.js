var intINDICE_FILE = 1;

function subLoad(strName, strDefaultFile, strNewFile, intINDICE_FILE, blnChkEliminar)
{
	var objTd1 = document.getElementById ("td_id_" + intINDICE_FILE + "-1");
	var objTd2 = document.getElementById ("td_id_" + intINDICE_FILE + "-2");

	if(strNewFile == "")
	{
		if(strDefaultFile != "")
		{
			if(isImage(strDefaultFile))
				objTd1.innerHTML = '<a href="JavaScript:;" onClick="subOpenPopUp(' + "'" + strDefaultFile + "'" + ');"><img src="' + strDefaultFile + '" width="100" height="50" border="0" alt="Ver en tamaño original"></a>';
			else
				objTd1.innerHTML = '<a href="JavaScript:;" onClick="subOpenPopUp(' + "'" + strDefaultFile + "'" + ');"><img src="' + FILE_URL + 'images/' + fncGetExtension(strDefaultFile).toLowerCase() + '.gif' + '" width="32" height="32" border="0" alt="Abrir"></a>';
					
			if(blnChkEliminar)
				objTd2.innerHTML = "<input type='checkbox' name='chk_eliminar_" + strName + "' value='true'>Eliminar?";					
		}
	}
	else
	{
		/*
		if(isImage(strNewFile))
			objTd1.innerHTML = "<img src='" + strNewFile + "' width='100' height='50' border='0'>";
		else
			objTd1.innerHTML = "<img src='" + FILE_URL + "images/" + fncGetExtension(strNewFile).toLowerCase() + ".gif'" + "' width='32' height='32' border='0'>";
		*/

		objTd1.innerHTML = "<img src='" + FILE_URL + "images/" + fncGetExtension(strNewFile).toLowerCase() + ".gif'" + "' width='32' height='32' border='0'>";
	}
}

function subFile(strName, strSrcFile, blnChkEliminar)
{
	document.write ("<table cellpadding='3' cellspacing='0' border='0'>");
	document.write   ("<tr>");
	document.write	   ("<td><table cellpadding='0' cellspacing='0' border='0'>");
	document.write	     ("<tr>");
	document.write	       	("<td id='td_id_" + intINDICE_FILE + "-1'></td>");
	document.write			("<td id='td_id_" + intINDICE_FILE + "-2' class='txt' valign='bottom'></td>");
	document.write	     ("</tr>");
	document.write	   ("</table></td>");
	document.write	 ("</tr>");			
	document.write	 ("<tr>");
	document.write	   ('<td><input type="file" name="' + strName + '" size="50" maxlength="50" onChange="subLoad(' + "'" + strName + "','" + strSrcFile + "',this.value," + intINDICE_FILE + "," + blnChkEliminar + ');"></td>');
	document.write	 ("</tr>");
	document.write ("</table>");

	subLoad(strName, strSrcFile, '', intINDICE_FILE, blnChkEliminar);

	intINDICE_FILE ++;
}	
