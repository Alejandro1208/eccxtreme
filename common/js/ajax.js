function Ajax(){
	// --------------------------------------
	// Parte privada.
	// --------------------------------------
	// Constantes.
	// ReadyState: Estado del requerimiento hecho al servidor.
	var READY_STATE_UNINITIALIZED = 0;  // No inicializado (el método open no a sido llamado).
	var READY_STATE_LOADING		  = 1;  // Cargando        (se llamó al método open).
	var READY_STATE_LOADED		  = 2;  // Cargado         (se llamó al método send y ya tenemos la cabecera de la petición HTTP y el status).
	var READY_STATE_INTERACTIVE	  = 3;  // Interactivo     (la propiedad responseText tiene datos parciales).
	var READY_STATE_COMPLETE	  = 4;  // Completado      (la propiedad responseText tiene todos los datos pedidos al servidor)
	// Status: Código del estado de la petición HTTP.
	var STATUS_OK				  = 200; // Ok.
	var STATUS_PAGE_NOT_FOUND     = 404; // Page not found.

	// Variables.
	var oXmlHttp    	    = null;

	var bIsMethodPost       = false;
	var sPage		        = "";
	var bIsAsynchronous     = true;
	var sReadyStateComplete = "";
	var sReadyStateLoading  = "";
	// La variable tiempo guarda una referencia al temporizador, con el objetivo de poderlo detener si la 
	// respuesta demora menos de 5 segundos.
	var iTimeout		  	= 0;	
	var iSeconds			= 5000;

	
	// Metodos.
	GetXMLHttpRequest = function(){
		if(window.ActiveXObject){
			// Navegadores obsoletos: Internet Explorer 6 y anteriores.
			// El objeto XMLHttpRequest no esta incorporado en JavaScript sino que se trata de una ActiveX.		
			oXmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
		}
		else{
			// Navegadores que siguen los estándares: Firefox, Safari, Opera, Internet Explorer 7 y 8.
			// El objeto XMLHttpRequest esta incorporado en JavaScript.				
			if(window.XMLHttpRequest) 
				oXmlHttp = new XMLHttpRequest();
		}	
	}
	
	Funcion = function(){
		var sResponse, sParameters, sName, sError;
		var iComa, iParentesis1, iParentesis2;
		
		if(oXmlHttp.readyState == READY_STATE_COMPLETE){
			if(oXmlHttp.status == STATUS_OK){
				sResponse    = (oXmlHttp.responseText == "")? "oXmlHttp.responseXML" : "oXmlHttp.responseText"; 
				iParentesis1 = sReadyStateComplete.indexOf("(");
				iParentesis2 = sReadyStateComplete.lastIndexOf(")");
				if((iParentesis1 == 0) || (iParentesis2 == 0))
					alert("Error: La funcion debe tener ()");
				else{
					// Traigo el nombre de la funcion (Sin los parametros).
					sName	     = sReadyStateComplete.substring(0, iParentesis1);
					// Traigo los parametros (Lo que esta entre parentesis ()).
					sParameters  = sReadyStateComplete.substring(iParentesis1 + 1, iParentesis2);
					// Saco los espacios (" ").
					sParameters  = sParameters.replace(" ", "");
					iComa1		 = sParameters.indexOf(",");					
					if(iComa1 == -1){
						// No tiene coma (,).
						sParameters = sResponse;
					}
					else{
						// Tiene coma (,).
						// Saco el 1º parametro.
						sParameters = sParameters.substr(iComa + 1);
						// Al 1º parametro le pongo sResponse.						
						sParameters = sResponse + "," + sParameters;
					}
					eval(sName + "(" + sParameters + ");");
				}
			}
			else{
				sError  = "";
				sError += "Error";
				sError += "\n";
				sError += "Nº: " + oXmlHttp.status;
				sError += "\n";
				sError += "Descripción: " + oXmlHttp.statusText;
				sError += "\n";
				sError += "Pagina: " + sPage;
				alert(sError);
			}
		} 
		else{
			if(sReadyStateLoading != "")
				eval(sReadyStateLoading + "();");
		}
		clearTimeout(iTimeout);
	}

	Abort = function(){
		oXmlHttp.abort ();
		alert("Intente nuevamente más tarde, el servidor esta sobrecargado.");			
	}
	// --------------------------------------
	
	// --------------------------------------
	// Parte publica.
	// --------------------------------------
	// Geters y Seters.
	this.SetIsMethodPost = function(bIsMethodPost_){
		bIsMethodPost = bIsMethodPost_;
	}
	this.GetIsMethodPost = function(){
		return(bIsMethodPost);
	}	
	
	this.SetPage = function(sPage_){
		sPage = sPage_;
	}
	this.GetPage = function(){
		return(sPage);				
	}
	
	this.SetIsAsynchronous = function(bIsAsynchronous_){
		bIsAsynchronous = bIsAsynchronous_;
	}
	this.GetIsAsynchronous = function(){
		return(bIsAsynchronous);				
	}

	this.SetReadyStateComplete = function(sReadyStateComplete_){
		sReadyStateComplete = sReadyStateComplete_;
	}
	this.GetReadyStateComplete = function(){
		return(sReadyStateComplete);
	}

	this.SetTimeout = function(iTimeout_){
		iTimeout = iTimeout_;
	}
	this.GetTimeout = function(){
		return(iTimeout);
	}

	this.SetSeconds = function(iSeconds_){
		iSeconds = iSeconds_ * 1000;
	}
	this.GetSeconds = function(){
		return(iSeconds);
	}	

	// Metodos.
	this.Send = function(){
		var sMethod  = (this.GetIsMethodPost())? "POST" : "GET";
		var sPagina;
		var iTimeout = setTimeout("Abort()", this.GetSeconds());
		var iSigno;

		this.SetTimeout(iTimeout); 

		if(this.GetPage() == "")
			alert("La propiedad [Page] no puede estar vacia.")
		else{
			if(this.GetReadyStateComplete() == "")
				alert("La propiedad [ReadyStateComplete] no puede ser null.")
			else{
				// Almacena el nombre de la función que se ejecutará cuando el objeto XMLHttpRequest cambie de estado.
				oXmlHttp.onreadystatechange = Funcion;				
				if(this.GetIsMethodPost()){
					// Abre un requerimiento HTTP al servidor.
					iSigno 	    = this.GetPage().indexOf("?");
					sPagina     = this.GetPage().substr(0, iSigno);
					sParameters = this.GetPage().substr(iSigno + 1);
					oXmlHttp.open(sMethod, sPagina, this.GetIsAsynchronous());										
					oXmlHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
					// Envía el requerimiento al servidor.				
					oXmlHttp.send(sParameters);
				}
				else{
					// Abre un requerimiento HTTP al servidor.
					oXmlHttp.open(sMethod, this.GetPage(), this.GetIsAsynchronous());					
					oXmlHttp.send(null);
				}
			}
		}
	}	
	// --------------------------------------
	
	GetXMLHttpRequest();
}