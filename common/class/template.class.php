<?php
	/**
	* Este archivo contiene las clases {@link Template}, encargada de procesar los templates, y {@link NodoTemplate},
	* encargada de procesar la información de los bloques (nodos).
	*
	* @package NASA
	*
	* @author Marcelo Mottalli <marcelo dot mottalli at avatarla dot com>
	* @author Nicolas Bottarini <nicobotta at hotmail dot com>
	* + Collaborator: Paul Martens <martens_paul at hotmail dot com>
	*/

	/**
	* Clase para templates.
	*
	* Clase encargada de procesar los templates.
	*
	* @package NASA
	* @access public
	*
	* @history Versión inicial: 1.0.0 - 2003/05/27: Marcelo Mottalli
	* + 1.1.0 - 2003/06/03: Marcelo Mottalli:  Se agregó la función setTemplate. Se cambió la estructura.
	* + 1.3.0 - 2003/11/03: Nicolás Bottarini: Se agregó soporte para utilizar namespaces ({@link useNamespace}, {@link discardNamespace}).
	* + 1.3.0 - 2003/11/03: Nicolás Bottarini: Se solucionó bug que impedía el parseo de un template si algún cierre de bloque no terminaba en \n.
	* + 1.3.0 - 2003/11/03: Nicolás Bottarini: Se agregó la función setVars.
	* + 1.3.0 - 2003/11/03: Nicolás Bottarini: Se borraron variables innecesarias.
	* + 1.3.0 - 2003/11/03: Nicolás Bottarini: Se agregó la opción de guardar las páginas procesadas en caché.
	* + 1.3.1 - 2003/11/28: Nicolás Bottarini: Se agregó la función isVarSet.
	* + 1.3.2 - 2003/12/05: Nicolás Bottarini: Se agregó caché de punteros a bloques.
	* + 1.3.3 - 2004/01/26: Nicolás Bottarini: Se agregó la función parseRecordset.
	* + 1.3.4 - 2005/10/30: Paul Martens:      Se corrgió el nombre de una variable en la función isVarSet.
	* + 1.3.4 - 2005/10/31: Paul Martens:      Se agregó valor de retorno a la función setTemplate.
	* + 1.3.4 - 2005/10/31: Paul Martens:      Se corrigió el nombre de un array en la función parseRecordset.
	* + 1.3.5 - 2007/04/20: Paul Martens:      Se completó la documentación para phpDoc.
	*
	* @author Marcelo Mottalli <marcelo dot mottalli at avatarla dot com>
	* @author Nicolas Bottarini <nicobotta at hotmail dot com>
	* + Collaborator: Paul Martens <martens_paul at hotmail dot com>
	*
	* @version 1.3.5 - 2007/04/20
	*/
	class Template {
		/**
		* Objeto para manejar bloques (nodos).
		*
		* @access private
		* @var object
		*/
		private $_objRootNode;

		/**
		* Namespaces establecidos.
		*
		* @access private
		* @var array
		*/
		private $_arrNamespaces = array();

		/**
		* Variables globales.
		*
		* @access private
		* @var array
		*/
		private $_arrGlobales = array();

		/**
		* Caché de bloques.
		*
		* @access private
		* @var array
		*/
		private $_arrRefsBloques = array();

		/**
		* Constructor.
		*
		* @access public
		* @param string $sFilename Nombre del archivo que contiene el template.
		* @param bool $bUseCache Indica si se desea que las páginas se cacheen en el servidor.
		*/
		public function __construct($sFilename, $bUseCache = FALSE) {
			if(strlen($sFilename) < 1024 && is_file($sFilename)) {
				$sTemplate = implode("", file($sFilename));
			} else {
				$bUseCache = FALSE;
				$sTemplate = $sFilename; // Esta copia permite el pasaje por referencia.
			}

			if($bUseCache && class_exists("CCache")) {
				$oTree  = new CIncludeNode($sFilename);
				$oCache = new CCache($oTree, "cache/");

				if($oCache->IsModified()) {
					$this->_objRootNode = new NodoTemplate($sTemplate);
					$oCache->SetContents(serialize($this->_objRootNode));
				} else {
					$this->_objRootNode = unserialize($oCache->GetContents());
				}
			} else {
				$this->_objRootNode = new NodoTemplate($sTemplate);
			}

			$this->useNamespace("", FALSE); // Namespace vacío es la raíz.
		}

		/**
		* Establece un namespace.
		*
		* Se pueden establecer varios namespaces.
		* Si se establece más de uno, el orden de búsqueda va desde el último definido hasta el primero.
		*
		* @access public
		* @param string $sNamespace Nombre del bloque para el cual se desea establecer el namespace.
		* @param boolean $bUseNamespace Indica si el bloque para el cual se desea establecer el namespace debe buscarse a partir del namespace actual.
		* @since 1.3.0
		*/
		public function useNamespace($sNamespace, $bUseNamespace = TRUE) {
			if(!$this->blockExists($sNamespace, $bUseNamespace)) {
				$this->_error("useNamespace: No existe el bloque " . $sNamespace);
			}

			// Agrega el namespace al principio del array.
			array_unshift($this->_arrNamespaces, $sNamespace);
		}

		/**
		* Deja de utilizar un namespace.
		*
		* @access public
		* @param string $sNamespace Nombre del namespace que se desea dejar de utilizar.
		* @since 1.3.0
		*/
		public function discardNamespace($sNamespace) {
			for($i = 0; $i < sizeof($this->_arrNamespaces); $i++) {
				if(strtolower($this->_arrNamespaces[$i]) == strtolower($sNamespace)) {
					unset($this->_arrNamespaces[$i]);
					// No corta el bucle porque puede haber más de un namespace para el mismo bloque.
				}
			}
		}

		/**
		* Parsea un bloque.
		*
		* @access public
		* @param string $sBlock Nombre del bloque que se desea parsear.
		* @param boolean $bClearBuffer Indica si luego del parseo debe borrarse el buffer.
		*/
		public function parse($sBlock = "", $bClearBuffer = TRUE) {
			$objDestino = $this->_buscarBloque($sBlock);

			if($objDestino === NULL)           $this->_error("Parse: No está definido el bloque " . $sBlock);
			elseif($objDestino->_blnEsArchivo) $this->_error("Parse: No se puede parsear un archivo");
			else $objDestino->parse($this->_arrGlobales, $bClearBuffer);
		}

		/**
		* Indica si una variable está seteada.
		*
		* @access public
		* @param string $sPathVariable Variable a evaluar.
		* @return boolean
		* @since 1.3.1
		*/
		public function isVarSet($sPathVariable) {
			list($sBloque, $sVar) = $this->_extraerBloqueYVariable($sPathVariable);
			$objDestino = $this->_buscarBloque($sBloque);

			if($objDestino === NULL) {
				$this->_error("isVarSet: No est&aacute; definido el bloque " . $sBloque);
			}

			return $objDestino->isVarSet($sVar);
		}

		/**
		* Indica si existe un determinado bloque.
		*
		* @access public
		* @param string $sBlock Bloque a evaluar.
		* @param boolean $bUseNamespace Indica si el bloque debe buscarse a partir del namespace actual.
		* @return boolean
		*/
		public function blockExists($sBlock, $bUseNamespace = TRUE) {
			$objBloqueDestino = $this->_buscarBloque($sBlock, $bUseNamespace);
			return !is_null($objBloqueDestino);
		}

		/**
		* Genera un mensaje de error.
		*
		* @access private
		* @param string $sError Mensaje de error.
		* @param integer $iTipoError Tipo de error.
		*/
		private function _error($sError, $iTipoError = E_USER_ERROR) {
			trigger_error("<b>Error Template:</b> " . $sError, $iTipoError);
		}

		/**
		* Devuelve el contenido del buffer luego de llamar a la función parse.
		*
		* @access public
		* @return string
		*/
		public function getParseBuffer() {
			return $this->_objRootNode->getParseBuffer();
		}

		/**
		* Parsea el template principal.
		*
		* @access public
		* @param boolean $bOutput Indica si se desea mostrar el contenido parseado.
		*/
		public function parseAll($bOutput = TRUE) {
			$this->_objRootNode->parse($this->_arrGlobales);
			if($bOutput) echo $this->_objRootNode->getParseBuffer();
		}

		/**
		* Asigna un valor a una variable del template o de un bloque de este.
		*
		* @access public
		* @param string $sPathVariable Variable a la cual se desea asignar un valor.
		* @param string $sValor Valor a asignar.
		* @return boolean Indica si el valor fue asignado correctamente a la variable.
		*/
		public function setVar($sPathVariable, $sValor = "") {
			list($sBloque, $sVariable) = $this->_extraerBloqueYVariable($sPathVariable);
			$oBloqueDestino = $this->_buscarBloque($sBloque);

			// Si no se encuentra el bloque que debería contener a la variable.
			if($oBloqueDestino == NULL) {
				$this->_error("setVar: No existe el bloque '" . $sBloque . "'");
			}

			return $oBloqueDestino->setVar($sVariable, $sValor);
		}

		/**
		* Asigna valores a variables del template o de bloques de este.
		*
		* @access public
		* @param array $aVars Pares de datos variable => valor.
		* @return bool Indica si todos los valores fueron asignados correctamente a las variables.
		* @since 1.3.0
		*/
		public function setVars($aVariables) {
			$bReturn = TRUE;
			foreach($aVariables as $sVariable => $sValue) {
				$bReturn = $this->setVar($sVariable, $sValue) && $bReturn;
			}

			return $bReturn;
		}

		/**
		* Asigna un valor a todas las instancias de una variable del template en cualquier bloque.
		*
		* @access public
		* @param string $sVariable Variable a la cual se desea asignar un valor.
		* @param string $sValor Valor a asignar.
		*/
		public function setGlobal($sVariable, $sValor = "") {
			$this->_arrGlobales[$sVariable] = $sValor;
		}

		/**
		* Devuelve una referencia a un bloque.
		*
		* @access private
		* @param string $sBlock Bloque a buscar.
		* @param boolean $bUseNamespace Indica si el bloque debe buscarse a partir del namespace actual.
		* @return object
		*/
		private function & _buscarBloque($sBloque, $bUseNamespace = TRUE) {
			// Busca si el bloque al que se está accediendo está en la caché de bloques.
			if($sBloque != "" && !$bUseNamespace && array_key_exists($sBloque, $this->_arrRefsBloques)) {
				return $this->_arrRefsBloques[$sBloque];
			}

			// Si hay que buscar en el namespace.
			if($bUseNamespace && sizeof($this->_arrNamespaces) > 1) {
				foreach($this->_arrNamespaces as $sNamespace) {
					// Busca el path absoluto del bloque (incluido el namespace).
						 if($sNamespace == "" && $sBloque != "") $sBloqueABuscar = $sBloque; // Primero la opción más probable.
					elseif($sNamespace == "" && $sBloque == "") $sBloqueABuscar = "";
					elseif($sNamespace != "" && $sBloque == "") $sBloqueABuscar = $sNamespace;
					elseif($sNamespace != "" && $sBloque != "") $sBloqueABuscar = $sNamespace . "." . $sBloque;

					$oResult = $this->_buscarBloque($sBloqueABuscar, FALSE);

					// Busca hasta que encuentra el primero que no es null.
					if(!is_null($oResult)) return $oResult;
				}

				return NULL; // Si no encontró el bloque.
			}

			// Si no hay que buscar en el namespace, se busca en el árbol, recorriéndolo desde la raíz.
			$oResult = $this->_objRootNode;

			if($sBloque == "") {
				return $oResult;
			} else {
				$arrRecorrido = split("\\.", $sBloque);

				foreach($arrRecorrido as $sNombreHijo) {
					if(!array_key_exists($sNombreHijo, $oResult->_arrBloquesHijos)) {
						return null;
					} else {
						$oResult = $oResult->_arrBloquesHijos[$sNombreHijo]["oBloque"];
					}
				}

				$this->_arrRefsBloques[$sBloque] = $oResult; // Guarda en caché.
				return $oResult;
			}
		}

		/**
		* Descompone el nombre completo de una variable y devuelve el par de datos (bloques, variable).
		*
		* @access private
		* @param string $sPathVariable Nombre completo de la variable (con los bloques que la contienen).
		* @return array
		*/
		private function _extraerBloqueYVariable($sPathVariable) {
			preg_match("/(.*)\\.([^.]*)\$/", $sPathVariable, $aMatches);

			if(empty($aMatches)) return array("", $sPathVariable);

			return array($aMatches[1], $aMatches[2]);
		}

		/**
		* Asigna un archivo template a una variable.
		*
		* @access public
		* @param string $sPathVariable Variable a la cual se desea asignar el archivo template.
		* @param string $strFileName Nombre del archivo template a asignar.
		* @return boolean Indica si el archivo template fue asignado correctamente a la variable.
		*/
		public function setFile($sPathVariable, $sFileName) {
			list($sBloque, $sVariable) = $this->_extraerBloqueYVariable($sPathVariable);
			$oBloqueDestino = $this->_buscarBloque($sBloque);

			if($oBloqueDestino === null) return FALSE;

			return $oBloqueDestino->setFile($sVariable, $sFileName);
		}

		/**
		* Asigna el contenido del buffer de otro objeto a una variable.
		*
		* @access public
		* @param string $sPathVariable Variable a la cual se desea asignar el contenido del buffer de otro objeto.
		* @param &object $oTemplate Objeto cuyo buffer va a ser asignado a la variable.
		* @return boolean Indica si el el contenido del buffer fue asignado correctamente a la variable.
		* @since 1.1.0
		*/
		public function setTemplate($sPathVariable, &$oTemplate) {
			list($sBloque, $sVariable) = $this->_extraerBloqueYVariable($sPathVariable);

			$oBloqueDestino = $this->_buscarBloque($sBloque);

			if($oBloqueDestino === FALSE) return FALSE;

			return $oBloqueDestino->setVar($sVariable, $oTemplate->getParseBuffer());
		}

		/**
		* Parsea un bloque con los contenidos de un recordset.
		* Al finalizar la función el puntero del recordset queda en el final.
		*
		* @access public
		* @param &object $oRecorset Recordset con los datos a parsear.
		* @param string $sBloque Bloque que se va a parsear con los contenidos del recordset.
		* @param array $aFunciones Pares (variable, función) que determinan cual función aplicar a cual variable del recordset antes del parseo.
		* @since 1.3.3
		*/
		public function parseRecordset(&$oRecordset, $sBloque, $aFunciones = NULL) {
			if(!is_a($oRecordset, "crecordset")) $this->_error("parseRecordset: El primer parámetro no es un recordset válido");

			$oRecordset->Reset();

			while($oRecordset->MoveNext()) {
				$aVars = $oRecordset->Get();

				foreach($aVars as $sVariable => $sValue) {
					if(is_array($aFunciones) && array_key_exists($sVariable, $aFunciones)) {
						$sValue = call_user_func($aFunciones[$sVariable], $sValue);
					}

					$this->setVar($sBloque . "." . $sVariable, $sValue);
				}

				$this->parse($sBloque);
			}
		}

		/**
		* Borra el buffer de un bloque.
		*
		* Cuando se parsea varias veces el mismo bloque (generalmente en iteraciones),
		* clearBuffer permite reutilizar el bloque como si fuese la primera vez.
		*
		* @access public
		* @param string $sBlockName Nombre del bloque para el cual se desea borrar el buffer.
		*/
		public function clearBuffer($sBlockName) {
			$oBloqueDestino = $this->_buscarBloque($sBlockName);

			if($oBloqueDestino === FALSE) {
				$this->_error("clearBuffer: No está definido el bloque " . $sBlock);
			}

			$oBloqueDestino->clearBuffer();
		}

		/**
		* Muestra el árbol de nodos.
		*
		* @access public
		*/
		public function debug() {
			$this->_objRootNode->printNode();
		}
	}

	/**
	* Clase encargada de procesar los bloques (nodos). Sólo debe instanciarse desde la clase {@link Template}.
	*
	* @package fulbo.com.ar
	* @access private
	*
	* @author Marcelo Mottalli <marcelo.mottalli@avatarla.com>
	* @version 1.3.3 - 2004/01/26
	*/
	class NodoTemplate {
		/**
		* Bloques dentro del bloque actual.
		*
		* @access public
		* @var array
		*/
		public $_arrBloquesHijos = array();

		/**
		* Nombre del bloque actual.
		*
		* @access private
		* @var string
		*/
		private $_strNombreBloque;

		/**
		* Contenido del bloque actual.
		*
		* @access private
		* @var string
		*/
		private $_strContenido = "";

		/**
		* @access public
		* @var boolean
		*/
		public $_blnEsArchivo; 

		/**
		* Orden en que se van a parsear los hijos.
		*
		* @access private
		* @var array
		*/
		private $_arrOrdenParseoHijos = array();

		/**
		* Array asociativo (pares key => value) cuyas "keys" son las variables que ya
		* tienen valor asignado y cuyos "values" son los valores para cada variable.
		*
		* @access private
		* @var array
		*/
		private $_arrVars = array();

		/**#@+
		* @access private
		* @var array
		*/
		private $_arrParsed = array();
		private $_arrFiles  = array();
		/**#@-*/

		/**#@+
		* @access private
		* @var string
		*/
		private $_strCode        = "";
		private $_strParseBuffer = "";
		/**#@-*/

		/**
		* Constructor.
		*
		* @access public
		* @param string $strData Contiene el texto de este bloque. Puede contener otros bloques anidados.
		* @param string $strNombreBloque Nombre asignado al bloque. Se usa solamente para debug.
		*/
		public function __construct($strData, $strNombreBloque = "", $blnEsArchivo = false) {
			$this->_strNombreBloque = $strNombreBloque;
			$this->_blnEsArchivo    = $blnEsArchivo;

			// Se parsea la información, se crean los objetos correspondientes, y se pasa la información.
			$strTexto = $strData; 

			// El siguiente bloque de código se encarga de procesar todos los bloques que puedan haber dentro
			// del bloque actual. Al mismo tiempo, actualiza el array "global" (que en realidad pertenece a la
			// clase Template) de referencias a bloques para que apunten a los bloques internos. Se hace recursión
			// porque puede ser que los bloques internos de éste bloque TAMBIÉN tengan otros bloques internos.
			// Por cada bloque, se guardan 3 datos:
			// - El texto que se muestra antes de mostrar el bloque procesado,
			// - El nombre del bloque,
			// - Un puntero al objeto del bloque (de la clase NodoTemplate)
			// Además de esto, se guarda aparte el texto que no contiene ningún bloque en la variable interna
			// $_strContenido. En realidad, el contenido es el texto que hay entre el final del último sub-bloque
			// y el fin del bloque actual.
			$arrResultado         = $this->_parseSecuencial($strTexto);
			$strUltimoTextoPrevio = $arrResultado["strTextoPrevio"];

			while($arrResultado["bMasBloques"]) {
				$strTexto      = $arrResultado["strTextoPosterior"]; // Para seguir el ciclo, que sigue hasta que no haya más texto a procesar
				$strNombreHijo = $arrResultado["strNombreBloque"];
			
				$this->_arrBloquesHijos[$strNombreHijo]["strTextoPrevio"]  = $arrResultado["strTextoPrevio"];
				$this->_arrBloquesHijos[$strNombreHijo]["strNombreBloque"] = $arrResultado["strNombreBloque"]; 

				// Procesa recursivamente los bloques internos que pueda tener el bloque hijo
				$this->_arrBloquesHijos[$strNombreHijo]["oBloque"] = new NodoTemplate($arrResultado["strTextoIntermedio"], $strNombreHijo); 

				// Guarda el puntero al nuevo nodo hijo creado en el array principal de punteros
				$this->_arrOrdenParseoHijos[] = $strNombreHijo; 

				// Procesa el próximo bloque
				$arrResultado         = $this->_parseSecuencial($arrResultado["strTextoPosterior"]);
				$strUltimoTextoPrevio = $arrResultado["strTextoPrevio"];
			}

			// Cuando no hay más bloques por procesar,
			// strUltimoTexto es el texto plano. Entonces, guarda ese texto sin bloques en la variable local.
			$this->_strContenido = $strUltimoTextoPrevio;
		}
				
		/**
		* Asigna un valor a una variable del bloque.
		*
		* @access public
		* @param string $sVariable Variable a la cual se desea asignar un valor.
		* @param string $sValor Valor a asignar.
		* @return boolean Indica si el valor fue asignado correctamente a la variable.
		*/
		public function setVar($sVariable, $sValor) {
			$bEstaDefinida = FALSE;

			$aHijos = array_keys($this->_arrBloquesHijos);
			$sRegexp = "/{" . $sVariable . "}/i";

			foreach($aHijos as $sNombreHijo) {
				if(preg_match($sRegexp, $this->_arrBloquesHijos[$sNombreHijo]["strTextoPrevio"])) {
					$bEstaDefinida = TRUE;
					break;
				}
			}

			if(!$bEstaDefinida && !preg_match($sRegexp, $this->_strContenido)) {
				return FALSE;
			}

			$this->_arrVars[$sVariable] = $sValor;
			
			return TRUE;
		}

		/**
		* Parsea el bloque actual
		*
		* Asigna a las variables que encuentre los valores especificados como parámetros.
		* Notar que esta función puede ser llamada varias veces seguidas con variables
		* distintas, y en el buffer interno se irán <b>concatenando</b> los resultados si no
		* se borra el buffer primero.
		*
		* @access public
		* @param array $arrVarsExtra Variables extras a setear
		* @param bool $bClearBuffer Indica si se desea vaciar el buffer luego de parsear el bloque
		*/
		public function parse(&$arrVarsExtra, $bClearBuffer = TRUE) { 
			// Parsea los bloques hijos en el orden correcto
			foreach($this->_arrOrdenParseoHijos as $strNombreHijo) {
				// Primero parsea sólo los archivos de este nodo.
				if($this->_arrBloquesHijos[$strNombreHijo]["oBloque"]->_blnEsArchivo) {
					$this->_arrBloquesHijos[$strNombreHijo]["oBloque"]->parse($arrVarsExtra);
				}

				$this->_strParseBuffer .= $this->_parseString($this->_arrBloquesHijos[$strNombreHijo]["strTextoPrevio"], $arrVarsExtra);
				$this->_strParseBuffer .= $this->_arrBloquesHijos[$strNombreHijo]["oBloque"]->getParseBuffer();
			} 

			$this->_strParseBuffer .= $this->_parseString($this->_strContenido, $arrVarsExtra);

			if($bClearBuffer) {
				$arrNombresHijos = array_keys($this->_arrBloquesHijos);

				foreach($arrNombresHijos as $strNombreHijo) {
					$this->_arrBloquesHijos[$strNombreHijo]["oBloque"]->clearBuffer();
				}
			}

			// Borro las variables del bloque
			$this->_arrVars = array();
		}

		private function _error($strError, $tipoError = E_USER_ERROR) {
			trigger_error("<strong>Error NodoTemplate:</strong> " . $strError, $tipoError);
		}

		/**
		* Parsea un pedazo de string
		* 
		* Dada una string y un array como en la función {@link parse}, reemplaza las variables
		* dentro del string (expresadas como "{variable}" por su valor correspondiente
		* 
		* @param string $strString 
		* @param array $arrVarasValores
		*/
		private function _parseString($strString, &$arrVarsExtra) {
			$strBuffer = $strString; // Por si $strString se pasa por referencia

			foreach ($this->_arrVars as $strVarName => $varVarValue) {
				$strBuffer = str_replace("{".$strVarName."}", $varVarValue, $strBuffer);
			}

			foreach ($arrVarsExtra as $strVarName => $varVarValue) {
				$strBuffer = str_replace("{".$strVarName."}", $varVarValue, $strBuffer);
			}

			// Borra las variables que no se usaron
			$strBuffer = eregi_replace("{ *[a-zA-Z0-9_-]*} *", "", $strBuffer);
			return $strBuffer;
		}

		/**
		* Asigna un archivo a una variable
		* 
		* Hace que una variable dentro del template referencie a un archivo. Notar que la variable
		* especificada pasa a convertirse en un bloque más. No se puede hacer que una variable que
		* aparece dos veces dentro de un bloque referencie al mismo archivo, la referencia a un archivo es <b>única</b>
		* @param string $strVarName Variable a la cual se le va a asignar el archivo
		* @param string $strFileName Nombre del archivo a asingar
		*/
		public function setFile($strVarName, $strFileName) {
			if(!file_exists($strFileName)) {
				$this->_error("setFile: No existe el archivo ".$strFileName);
			} 

			$arrNombresHijos = array_keys($this->_arrBloquesHijos);

			$blnPudoInsertar = FALSE;

			foreach($arrNombresHijos as $strNombreHijo) {
				$strTextoPrevio = &$this->_arrBloquesHijos[$strNombreHijo]["strTextoPrevio"];
				$blnPudoInsertar = $this->_insertarArchivo($strTextoPrevio, $strVarName, $strFileName, $strNombreHijo);

				if($blnPudoInsertar) return TRUE;
			}

			if(!$blnPudoInsertar) {
				return $this->_insertarArchivo($this->_strContenido, $strVarName, $strFileName);
			}
		}

		/**
		* Indica si una variable está seteada.
		*
		* @access public
		* @param string $sPathVariable Variable a evaluar.
		* @return boolean
		*/
		public function isVarSet($sPathVariable) {
			return array_key_exists($strVar, $this->_arrVars);
		}

		/**
		* Enchufa un bloque que referencia a un archivo dentro de una cadena de texto
		* 
		* La cadena <b>debe</b> estar pasada por referencia
		* @access private
		* @param string $strTexto Texto al cual se le va a incrustar el archivo en el medio
		* @param string $strVarName Nombre de la variable que indica dónde se va a insertar el archivo
		* @param string $strFileName Archivo que va a ser referenciado por la variable
		* @param string $strNombreHijo Nombre del bloque que va a estar después de insertar el archivo (si es que se
		* 	inserta el archivo o existe un hijo). Esto es necesario para indicarle al parser que antes de parsear el
		* 	hijo indicado tiene que parsear el archivo.
		* @return bool Indica si se pudo insertar el archivo. Si es true, también $strTexto va a ser modificado a que contenga
		* 	el texto que hay entre el final del archivo y el final del string original.
		*/
		private function _insertarArchivo(&$strTexto, &$strVarName, &$strFileName, $strNombreHijo = "") {
			$strCadena = "{".$strVarName."}";
			$varPos = strpos($strTexto, $strCadena);
			if($varPos === false) return false;

			$strTextoPrevio = substr($strTexto, 0, $varPos);
			$strTextoPosterior = substr($strTexto, $varPos + strlen($strCadena), strlen($strTexto));
			$strTextoArchivo = implode("", file($strFileName));

			$strNombreBloqueArchivo = $strVarName;

			$this->_arrBloquesHijos[$strNombreBloqueArchivo]["oBloque"]         = new NodoTemplate($strTextoArchivo, $strNombreBloqueArchivo, TRUE);
			$this->_arrBloquesHijos[$strNombreBloqueArchivo]["strTextoPrevio"]  = $strTextoPrevio;
			$this->_arrBloquesHijos[$strNombreBloqueArchivo]["strNombreBloque"] = $strNombreBloqueArchivo;

			// Inserta el nombre del bloque del archivo a parsear en el orden correspondiente
			// (PHP no tiene ninguna manera fácil de insertar un elemento en el medio de un array)
			if($strNombreHijo == "") {
				$this->_arrOrdenParseoHijos[] = $strNombreBloqueArchivo;
			} else {
				$arrTempArray = $this->_arrOrdenParseoHijos;
				$this->_arrOrdenParseoHijos = array();
				foreach ($arrTempArray as $strProximoHijo) {
					// Inserta el bloque actual antes del próximo hijo
					if($strProximoHijo == $strNombreHijo) $this->_arrOrdenParseoHijos[] = $strNombreBloqueArchivo;
					$this->_arrOrdenParseoHijos[] = $strProximoHijo;
				}
			}

			$strTexto = $strTextoPosterior; // Recordar que $strTexto está pasado por referencia!
			return TRUE;
		}

		public function setTemplate($strVarName, &$objTemplate) {
			$arrNombresHijos = array_keys($this->_arrBloquesHijos);

			$blnPudoInsertar = FALSE;

			foreach($arrNombresHijos as $strNombreHijo) {
				if($blnPudoInsertar) return TRUE;

				$strTextoPrevio = &$this->_arrBloquesHijos[$strNombreHijo]["strTextoPrevio"];
				$blnPudoInsertar = $this->_insertarTemplate($strTextoPrevio, $strVarName, $objTemplate, $strNombreHijo);

				if($blnPudoInsertar) return TRUE;
			}

			if(!$blnPudoInsertar) return $this->_insertarTemplate($this->_strContenido, $strVarName, $objTemplate);
		}

		private function _insertarTemplate(&$strTexto, &$strVarName, &$objTemplate, $strNombreHijo = "") {
			$strCadena = "{".$strVarName."}";
			$varPos    = strpos($strTexto, $strCadena);

			if($varPos === FALSE) return FALSE;

			$strTextoPrevio    = substr($strTexto, 0, $varPos);
			$strTextoPosterior = substr($strTexto, $varPos + strlen($strCadena), strlen($strTexto));

			$strNombreBloqueTemplate = $strVarName;
			$objTemplate->_objRootNode->_strNombreBloque = $strVarName;

			$this->_arrBloquesHijos[$strNombreBloqueTemplate]["oBloque"]         = $objTemplate->_objRootNode;
			$this->_arrBloquesHijos[$strNombreBloqueTemplate]["strTextoPrevio"]  = $strTextoPrevio;
			$this->_arrBloquesHijos[$strNombreBloqueTemplate]["strNombreBloque"] = $strNombreBloqueTemplate;

			// Inserta el nombre del bloque del template a parsear en el orden correspondiente
			// (PHP no tiene ninguna manera fácil de insertar un elemento en el medio de un array)
			if($strNombreHijo == "") {
				$this->_arrOrdenParseoHijos[] = $strNombreBloqueTemplate;
			} else {
				$arrTempArray = $this->_arrOrdenParseoHijos;

				$this->_arrOrdenParseoHijos = array();

				foreach ($arrTempArray as $strProximoHijo) {
					// Inserta el bloque actual antes del próximo hijo
					if($strProximoHijo == $strNombreHijo) $this->_arrOrdenParseoHijos[] = $strNombreBloqueTemplate;
					$this->_arrOrdenParseoHijos[] = $strProximoHijo;
				}
			}

			$strTexto = $strTextoPosterior; // Recordar que $strTexto está pasado por referencia
		}

		/**
		* Devuelve el contenido parseando hasta el momento.
		*
		* @access public
		* @return string
		*/
		public function getParseBuffer() {
			return $this->_strParseBuffer;
		}

		/**
		* Borra el buffer del bloque actual.
		*
		* Cuando se parsea varias veces el mismo bloque (generalmente en iteraciones),
		* clearBuffer permite reutilizar el bloque como si fuese la primera vez.
		*/
		public function clearBuffer() {
			$this->_strParseBuffer = "";

			$arrNombresHijos = array_keys($this->_arrBloquesHijos);

			foreach($arrNombresHijos as $strNombreHijo) {
				$this->_arrBloquesHijos[$strNombreHijo]["oBloque"]->clearBuffer();
			}
		}

		/**
		* Recibe un texto como parámetro, y parsea el primer bloque que encuentra (si es que encuentra alguno)
		* @access private
		* @param string $strTexto
		* @return array Un array con los siguientes campos:
		* 	+ "strTextoPrevio" => Texto previo al primer bloque. Si no hay ningún bloque, contiene todo el texto pasado como parámetro
		* 	+ "strNombreBloque" => Nombre del primer bloque encontrado (si lo hubo)
		* 	+ "strTextoIntermedio" => Texto que hay entre el <!-- BEGIN bloque --> y el <!-- END bloque -->.
		* 		Notar que en este parámetro pueden haber otros bloques que también deberán ser parseados.
		* 	+ "strTextoPosterior" => Texto que hay entre el fin del primer bloque y el final del string. Acá también
		* 		pueden haber otros bloques
		* 	+ "bMasBloques" => Boolean que indica si todavía hay más información por procesar luego del primer bloque encontrado
		*/
		private function _parseSecuencial($strTexto) {
			$arrReturn = array();

			$strRegexp = "/<!--\s*BEGIN\s+([^>]*)\s*-->/i";

			preg_match($strRegexp, $strTexto, $arrMatches); // Busca el PRIMER bloque dentro del bloque actual

			// Si no hay ningún bloque, devuelve solamente el texto dentro del bloque actual
			if(empty($arrMatches)) {
				$arrReturn["strTextoPrevio"]     = $strTexto;
				$arrReturn["strNombreBloque"]    = "";
				$arrReturn["strTextoIntermedio"] = "";
				$arrReturn["strTextoPosterior"]  = "";
				$arrReturn["bMasBloques"]        = FALSE;

				return $arrReturn;
			}

			// Si hay un bloque, extrae el nombre y el tag que se usó para abrir el bloque
			list($strMatch, $strTagName)  = $arrMatches;
			$strTagName                   = trim($strTagName);
			$arrReturn["strNombreBloque"] = $strTagName;
			$intLongMatch                 = strlen($strMatch);

			$intPosicionPrimerTag = strpos($strTexto, $strMatch); 

			// Busca el texto que había antes del bloque
			$arrReturn["strTextoPrevio"] = substr($strTexto, 0, $intPosicionPrimerTag); 

			// Busca el fin del bloque
			$strRegexpFin = "/<!--\s*END\s+".$strTagName."\s*-->/i";

			preg_match($strRegexpFin, $strTexto, $arrMatches); 

			// Si no se cerró el bloque (¡no debería pasar!)
			if(empty($arrMatches)) $this->_error("Falta cerrar el bloque " . $strTagName);

			list($strMatch) = $arrMatches;
			$intPosicionTagFinal = strpos($strTexto, $strMatch); 

			// Extrae el texto dentro del bloque y el texto que hay después del bloque
			$arrReturn["strTextoIntermedio"] = substr($strTexto, $intPosicionPrimerTag + $intLongMatch, $intPosicionTagFinal - ($intPosicionPrimerTag + $intLongMatch));
			$arrReturn["strTextoPosterior"] = substr($strTexto, $intPosicionTagFinal + strlen($strMatch));
			$arrReturn["bMasBloques"] = TRUE;

			return $arrReturn;
		}

		/**
		* Muestra el árbol de nodos del bloque.
		*
		* @access public
		*/
		public function printNode() {
			echo "<ul>";
			echo "<li>" . ($this->_strNombreBloque == "" ? "ROOT" : $this->_strNombreBloque) . "</li>";

			foreach($this->_arrOrdenParseoHijos as $sNombreHijo) {
				$this->_arrBloquesHijos[$sNombreHijo]["oBloque"]->printNode();
			}

			echo "</ul>";
		}
	}
?>