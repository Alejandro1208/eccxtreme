mulario en particular
	*
	* @ignore
	*/
function getFormConditionById(theForm, sConditionID)
{
	// Traigo las condiciones de este formulario
	var theConditions = getFormConditions(theForm);
	if (!theConditions) return null;
		
	// Busco la condicion que me estan pidiendo
	var theCondition = null;
	for (var i=0;i<theConditions.length;i++)
	{
		if (theConditions[i].getAttribute(sAtrConditionID)==sConditionID) theCondition = theConditions[i];
	}
	
	return theCondition;
}

/**
	* Interna llamada por "isConditionTrue" para hacer su trabajo
	*
	* @ignore
	*/ 
function isActualConditionTrue(theForm, theCondition)
{
	// Dos casos: condicion evaluable directamente (caso base) o condicion compuesta (caso recursivo)
	if (theCondition.nodeName.toUpperCase()==sTagConditionEMPTY.toUpperCase() || theCondition.nodeName.toUpperCase()==sTagConditionNOTEMPTY.toUpperCase() || theCondition.nodeName.toUpperCase()==sTagConditionVALID.toUpperCase() || theCondition.nodeName.toUpperCase()==sTagConditionVALUE.toUpperCase())
	{
		// CASO BASE
		// Busco el control del formulario a validar
		var theControl = formControl(theForm,theCondition.getAttribute(sAtrConditionCtrlID));
		if (!theControl) return false;
		
		// Evaluo...
		if (theCondition.nodeName.toUpperCase()==sTagConditionEMPTY.toUpperCase() || theCondition.nodeName.toUpperCase()==sTagConditionNOTEMPTY.toUpperCase())
		{
			var bIsEmpty = isControlEmpty(theControl);
			return (theCondition.nodeName.toUpperCase()==sTagConditionEMPTY.toUpperCase()?bIsEmpty:!bIsEmpty);
		}
		else if (theCondition.nodeName.toUpperCase()==sTagConditionVALID.toUpperCase())
		{
			return isControlValid(theForm, theControl);
		}
		else if (theCondition.nodeName.toUpperCase()==sTagConditionVALUE.toUpperCase())
		{
			return (theControl.value==theCondition.getAttribute(sAtrConditionCtrlVAL));
		}
		else
		{
			return false;
		}		
	}
	else
	{
		// CASO RECURSIVO
		
		// Si no es de las condiciones permitidas, no vale...
		if (!theCondition.nodeName.toUpperCase()==sTagConditionAND.toUpperCase() && !theCondition.nodeName.toUpperCase()==sTagConditionOR.toUpperCase() && !theCondition.nodeName.toUpperCase()==sTagConditionNOT.toUpperCase()) return false;
		
		// Recorro los hijos de la condicion y evaluo cada uno
		var theChildren = theCondition.childNodes;
		
		// Evaluo recursivamente...
		var bValid;
		switch (theCondition.nodeName.toUpperCase())
		{
			case sTagConditionAND.toUpperCase(): 
				bValid = true;
				break;
			case sTagConditionOR.toUpperCase():
				bValid = false;
				break;
		}
		
		for (var i=0;i<theChildren.length;i++)
		{
			// Si no es de tipo texto, sigo
			if (!nodeIsTypeText(theChildren[i]))
			{
				// Diferentes casos segun la condicion
				switch (theCondition.nodeName.toUpperCase())
				{
					case sTagConditionAND.toUpperCase():
						bValid = bValid && isActualConditionTrue(theForm, theChildren[i]);
						break;
					case sTagConditionOR.toUpperCase():
						bValid = bValid || isActualConditionTrue(theForm, theChildren[i]);
						break;
			  	case sTagConditionNOT.toUpperCase():
			  		bValid = !isActualConditionTrue(theForm, theChildren[i]);
			  		break;
			  }
			}
		}
		return bValid;
	}
}

/**
	* Para obtener el primer nodo hijo de un nodo que no sea de tipo texto (cross-browser)
	*
	* @ignore
	*/
function getFirstChildNode(theNode)
{
	var theChildren = theNode.childNodes;
	for (var i=0;i<theChildren.length;i++)
	{
		if (!nodeIsTypeText(theChildren[i])) return theChildren[i]; // El primero que no sea de tipo texto
	}	
	return null;
}

/**
	* Se explica por si sola...
	*
	* @ignore
	*/
function nodeIsTypeText(theNode)
{
	return (theNode.nodeType==3); // Tipo texto = 3
}

// *** DEPRECATED ***
/*function isChecked(theForm, sControl, theValue)
{
	if (sControl != null)
	{
		var bChecked = false;
		var bAnyChecked = false;
		for (z=0;z<theForm.elements.length;z++)
		{
			ctrl = theForm.elements.item(z);
			if (ctrl.tagName=='INPUT' && (ctrl.getAttribute('type').toUpperCase()=='RADIO' || ctrl.getAttribute('type').toUpperCase()=='CHECKBOX') && ctrl.id==sControl)
			{
				if (ctrl.checked)
					bAnyChecked = true;
				bChecked = bChecked || (ctrl.checked && ctrl.value==theValue);
			}
		}
		if (!bAnyChecked)
			return false;
		else
			return bChecked;
	}
	else
		return false;
}*/

/**
	* Que el texto que se pasa tenga alguna de las extensiones ...
	*
	*/
function hasAnyExtension(sTexto,sExtensions)
{
	// Obtengo las extensiones
	var aExtensions = sExtensions.split(",");
	// Traigo la extension del archivo
	var iPunto = sTexto.lastIndexOf(".");
	// Si no tiene extension, entonces nope...
	if (iPunto==-1) return false;
	var sExtension = sTexto.substr(iPunto+1);
	// Es alguna?
	for (var i=0;i<aExtensions.length;i++)
	{
		if (sExtension.toUpperCase()==aExtensions[i].toUpperCase()) return true;
	}
	// Nope...
	return false;
}

/**
	* Que sea una imagen de alguna de las extensiones y aparte tenga el tamaneo
	* que se pide
	*/
function imageIsOfSizeAndExtension(sId,sTexto,sExtensions,sMinHeight,sMaxHeight,sMinWidth,sMaxWidth)
{
	if (!hasAnyExtension(sTexto,sExtensions)) return false;
	// Tamaneos
	var iMinHeight = -1;
	if (sMinHeight!="") iMinHeight = parseInt(sMinHeight,10);
	var iMaxHeight = -1;
	if (sMaxHeight!="") iMaxHeight = parseInt(sMaxHeight,10);
	var iMinWidth = -1;
	if (sMinWidth!="") iMinWidth = parseInt(sMinWidth,10);
	var iMaxWidth = -1;
	if (sMaxWidth!="") iMaxWidth = parseInt(sMaxWidth,10);
	// Verifico el tamaneo de la imagen
	if (sTexto.length != 0) 
	{
		var oImage = window.document.getElementById(sId+sVSufAuxImage); // La imagen supuestamente preloadeada
		var oNewImage = new Image;
		oNewImage.src = oImage.src;
		if (oNewImage.height<iMinHeight && iMinHeight!=-1) return false;
		if (oNewImage.width<iMinWidth && iMinWidth!=-1) return false;
		if (oNewImage.height>iMaxHeight && iMaxHeight!=-1) return false;
		if (oNewImage.width>iMaxWidth && iMaxWidth!=-1) return false;
		return true;
	}
	else return false;
}

/**
	* Funcion que debe ser llamada desde el input type="file" en el onChange si se utiliza validacion de tipo
	* "imageofsize"
	*/
function fvFileChanged(oFile)
{
	// Hago un preload de la imagen si respeta las extensiones
	if (hasAnyExtension(oFile.value,oFile.getAttribute(sAtrExtensionsAllowed)))
	{
		window.document.getElementById(oFile.id+sVSufAuxImage).src = oFile.value;
	}
}ndicion
	bConditionIsTrue = isConditionTrue(theForm, sCondition);
	return (sTruthValue.substr(0,1).toUpperCase()=="T"?bConditionIsTrue:!bConditionIsTrue);
}

/**
	* Para verificar la validez de una condicion
	*
	* @ignore
	*/ 
function isConditionTrue(theForm, sConditionID)
{
	// Traigo la condicion que me piden evaluar
	var theCondition = getFormConditionById(theForm, sConditionID);
	if (!theCondition) return false;
	
	// Esta en cache??
	if (theCondition.getAttribute("inCache")=="true")
	{
		// Devuelvo el valor del cache directo...
		return (theCondition.getAttribute("truthValue")=="true"?true:false);
	}
	else
	{
		// Que garron... tengo que evaluar...
		// Busco el primer hijo y trabajo con ese (es decir, a los que sigan no se les da bola - que dicho sea de paso, no deberia haber)
		if (!theCondition.hasChildNodes()) return false;
		var theChild = getFirstChildNode(theCondition);
		
		// Ahora simplemente la evaluo...
		var truthValue = isActualConditionTrue(theForm, theChild);
		
		// Seteo el cache
		theCondition.setAttribute("inCache", "true");
		theCondition.setAttribute("truthValue", (truthValue?"true":"false"));
		
		return truthValue;
	}
}

/**
	* Obtiene la coleccion de condiciones que tiene un formulario particular.
	*
	* @ignore
	*/
function getFormConditions(theForm)
{
	// Busco la data island
	var theIsland = window.document.getElementById(sFVIslandID);
	if (!theIsland) return null;
	
	// Busco las condiciones de este formulario
	var allFormsConditions = theIsland.getElementsByTagName(sTagConditions);
	if (allFormsConditions.length==0) return null;
	allFormsConditions = allFormsConditions[0].getElementsByTagName(sTagConditionsFor);
	if (!allFormsConditions) return null;
	var theConditions = null;
	for (var i=0;i<allFormsConditions.length;i++)
	{
		if (allFormsConditions[i].getAttribute(sAtrConditionsForForm).toUpperCase()==theForm.id.toUpperCase())
		{
			theConditions = allFormsConditions[i].childNodes;
			break;
		}
	}
	
	return theConditions;
}

/**
	* Obtiene la condicion con un id en particular de un for