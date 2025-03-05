function MultiSelectOrder(sId)
{
	// Propiedades.
	this.InternalId		  = sId;
	this.TheSource		  = null;
	this.TheContainer	  = null;
	this.Estilo			  = "";
	this.EstiloTitulo	  = "";
	this.TitleSource	  = "Origen";
	this.TitleDestination = "Destino";
	this.Type			  = 1; // 1: Solo MultiSelect - 2: Solo Order - 3: Multiselect y Order.
	this.Url			  = "";

	// Metodos.	
	this.BotonesOrdenar = function()
	{
		var strHtml		= '';
		var strIdSource = this.InternalId + "_source";

		strHtml	= strHtml + '<table>';
		strHtml	= strHtml +		'<tr>'
		strHtml = strHtml +			'<td><a href="JavaScript:SwitchPos(' + "'" + this.InternalId + "'" + ', -1);"><img src="' + this.Url + 'images/bot_up.gif" border="0"></a><td>';
		strHtml	= strHtml +		'</tr>';
		strHtml	= strHtml +		'<tr>';
		strHtml = strHtml +			'<td><a href="JavaScript:SwitchPos(' + "'" + this.InternalId + "'" + ', 1);"><img src="' + this.Url + 'images/bot_down.gif" border="0"></a><td>';
		strHtml	= strHtml +		'</tr>';
		strHtml	= strHtml + '</table>';
		return(strHtml);
	}

	this.Draw = function() 
	{
		var strHtml     = '';
		var strIdSource = this.InternalId + "_source";
		var j;

		if(this.TheSource)
		{
			if(this.Type == 1 || this.Type == 3)
			{
				strHtml = strHtml + '<table>';
				strHtml = strHtml +		'<tr>';
				strHtml = strHtml +			'<td class="' + this.EstiloTitulo + '">' + this.TitleSource + '</td>';
				strHtml = strHtml +			'<td>&nbsp;</td>';
				strHtml = strHtml +			'<td class="' + this.EstiloTitulo + '">' + this.TitleDestination + '</td>';
				strHtml = strHtml +			'<td>&nbsp;</td>';
				strHtml = strHtml +		'</tr>';							
				strHtml = strHtml +		'<tr>'
				strHtml = strHtml +			'<td valign="middle">';
				strHtml = strHtml +				'<select id="' + strIdSource + '" class="' + this.Estilo + '" multiple onDblClick="Move(' + "'" + strIdSource + "', '" + this.InternalId + "'" + ');">';

													// Lleno el source.
													for(j = 0; j < this.TheSource.options.length; j++)
													{
														// Solo los no seleccionados.
														if(!this.TheSource.options[j].selected)
															strHtml = strHtml + '<option value="' + this.TheSource.options[j].value + '">' + this.TheSource.options[j].text + '</option>';
													}

				strHtml = strHtml +				'</select>';
				strHtml = strHtml +			'</td>';
				strHtml = strHtml +			'<td align="center" valign="middle"><table>';
				strHtml = strHtml +				'<tr>'
				strHtml = strHtml +					'<td><a href="JavaScript:SelectAll(' + "'" + strIdSource + "'" + '); Move(' + "'" + strIdSource + "', '" + this.InternalId + "'" + ');"><img src="' + this.Url + 'images/bot_all.gif" border="0"></a><td>';
				strHtml = strHtml +				'</tr>';
				strHtml = strHtml +				'<tr>';
				strHtml = strHtml +					'<td><a href="JavaScript:Move(' + "'" + strIdSource + "', '" + this.InternalId + "'" + ');"><img src="' + this.Url + 'images/bot_right.gif" border="0"></a><td>';
				strHtml = strHtml +				'</tr>';
				strHtml = strHtml +				'<tr>';
				strHtml = strHtml +					'<td><a href="JavaScript:Move(' + "'" + this.InternalId + "', '" + strIdSource + "'" + ');"><img src="' + this.Url + 'images/bot_left.gif" border="0"></a><td>';
				strHtml = strHtml +				'</tr>';
				strHtml = strHtml +				'<tr>';
				strHtml = strHtml +					'<td><a href="JavaScript:SelectAll(' + "'" + this.InternalId + "'" + '); Move(' + "'" + this.InternalId + "', '" + strIdSource + "'" + ');"><img src="'+this.Url+'images/bot_none.gif" border="0"></a><td>';
				strHtml = strHtml +				'</tr>';
				strHtml = strHtml +			'</table></td>';
				strHtml = strHtml +			'<td valign="middle">';
				strHtml = strHtml +				'<select id="' + this.InternalId + '" name="' + this.InternalId + '" + class="' + this.Estilo + '" multiple onDblClick="Move(' + "'" + this.InternalId + "', '" + strIdSource + "'" + ');">';

													// Lleno el destination.
													for(j = 0; j < this.TheSource.options.length; j ++)
													{
														// Solo los seleccionados.
														if(this.TheSource.options[j].selected)
															strHtml = strHtml + '<option value="' + this.TheSource.options[j].value + '">' + this.TheSource.options[j].text + '</option>';
													}

				strHtml = strHtml +				'</select>';
				strHtml = strHtml +			'</td>';				

				// Botones ordenar.
				if(this.Type == 3)					
					strHtml = strHtml +		'<td align="center" valign="middle">' + this.BotonesOrdenar () + '</td>';
				else
					strHtml = strHtml +		'<td>&nbsp;</td>';

				strHtml = strHtml +		'</tr>';
				strHtml = strHtml + '</table>';
			}
			else
			{
				strHtml = strHtml + '<table>';
				strHtml = strHtml +		'<tr>';
				strHtml = strHtml +			'<td valign="middle">';
				strHtml = strHtml +				'<select id="' + this.InternalId + '" name="' + this.InternalId + '" class="' + this.Estilo + '" multiple>';

													// Lleno el source.
													for(j = 0; j < this.TheSource.options.length; j ++)
													{
														// Solo los no seleccionados.
														if(!this.TheSource.options[j].selected)
															strHtml = strHtml + '<option value="' + this.TheSource.options[j].value + '">' + this.TheSource.options[j].text + '</option>';
													}

				strHtml = strHtml +				'</select>';
				strHtml = strHtml +			'</td>';
				strHtml = strHtml +			'<td valign="middle">' + this.BotonesOrdenar () + '</td>';
				strHtml = strHtml +		'</tr>';
				strHtml = strHtml + '</table>';
			}

			// Escupo el HTML.
			this.TheContainer.innerHTML = strHtml;
		}
	}
}

function SwitchPos(strId, intStep)
{
	var objLst = document.getElementById (strId);
	var strVal, strText;
	var intSource	   = objLst.selectedIndex > 0? objLst.selectedIndex : 0;
	var intDestination = intSource + intStep;

	if(objLst.options.length >= 2)
	{
		if(intDestination == -1)
			intDestination = objLst.options.length - 1;
		else
		{
			if(intDestination == objLst.options.length)
				intDestination = 0;
		}
	
		strVal  = objLst.options[intSource].value;
		strText = objLst.options[intSource].text;

		objLst.options[intSource].value = objLst.options[intDestination].value;
		objLst.options[intSource].text  = objLst.options[intDestination].text;

		objLst.options[intDestination].value = strVal;
		objLst.options[intDestination].text  = strText;
		
		objLst.selectedIndex = intDestination;
	}
}

function SelectAll(strId)
{
	var objLst = document.getElementById (strId);
	for(var j = 0; j <= objLst.options.length - 1; j ++)
		objLst.options[j].selected = true;
}
	
function AddItem(strId, val, text)
{
	var objLst = document.getElementById (strId);
	objLst.options[objLst.options.length] = new Option(text, val);
}
function DeleteItem(strId, intItem)
{
	var objLst = document.getElementById (strId);
	objLst.remove(intItem);
}

function Move(strIdSource, strIdDestination)
{
	var j;
	var blnSelected		  = false;
	var objLstSource	  = document.getElementById (strIdSource);
	var objLstDestination = document.getElementById (strIdDestination);

	if(objLstSource.options.length > 0) 
	{
		// Agrego en destino.	
		for(j = 0; j <= objLstSource.options.length - 1; j ++)
		{
			if(objLstSource.options[j].selected)
			{
				AddItem(strIdDestination, objLstSource.options[j].value, objLstSource.options[j].text);
				blnSelected	= true;
			}
		}
	
		// Si no hubo ninguno seleccioando paso el 1º.		
		if(!blnSelected)
		{
			objLstSource.options[0].selected = true;
			AddItem(strIdDestination, objLstSource.options[0].value, objLstSource.options[0].text);
		}

		objLstDestination.selectedIndex = 0;

		// Quito en origen.
		for(j = objLstSource.options.length - 1; j >= 0; j --)
		{
			if(objLstSource.options[j].selected)
				DeleteItem(strIdSource, j);
		}
		objLstSource.selectedIndex = 0;
	}
}