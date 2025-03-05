<?php
require_once("begin-page-interna.inc.php");
include("./../backend/updateform/form_functions.php");
# ---------------------------------------------
# Permiso.
# ---------------------------------------------
if(PERMISO == "d"){header("location: sin-permiso.php");}
# ---------------------------------------------

# ---------------------------------------------
# Funciones.
# ---------------------------------------------
function fncWidth($intW1, $intW2)
{
	return(round(($intW2 * $intW1) / 100));
}

function fncFormatDateToSql($strDate)
{
	$arr = fncSplit($strDate, "/");
	return("'" . $arr[2] . "-" . $arr[1] . "-" . $arr[0] . "'");
}

function fncRsFieldType($strType)
{
	switch($strType)
	{
		case "string": return("str");
		case "int"   : return("int");
		case "date"  : return("dte");
		default		 : return("");
	}
}

function fncIsFieldImage($strName_p)
{
	$strName = strtolower($strName_p);
	$pos	 = strpos($strName, "_");
	if($pos !== false){$strName = substr($strName, 0, $pos);}

	return
	(
	($strName == "img")     ||
	($strName == "image")   ||
	($strName == "imagen")  ||
	($strName == "picture") ||
	($strName == "foto")
	);
}

function fncIsFieldFile($strName_p)
{
	$strName = strtolower($strName_p);
	$pos	 = strpos($strName, "_");
	if($pos !== false){$strName = substr($strName, 0, $pos);}

	return
	(
	($strName == "file")     ||
	($strName == "archivo")  ||
	($strName == "adjunto")  ||
	($strName == "attach")   ||
	($strName == "txt")      ||
	($strName == "doc")      ||
	($strName == "xls")      ||
	($strName == "pdf")
	);
}
# ---------------------------------------------

# ---------------------------------------------
# Procedimientos.
# ---------------------------------------------
function subSepararPipe($str1, &$str2, &$str3)
{
	$intPipe = strpos($str1, "|");
	if($intPipe > 0)
	{
		$str2 = substr($str1, 0, $intPipe);
		$str3 = substr($str1, $intPipe + 1);
	}
}

function subList(&$objTpl, $strSql_p, $strTitles, $strWidths, $strAcciones, $strAccionesAux, $strBuscador1, $strBuscador2, $strBuscador3, $blnBuscadorEstado, $strDirs)
{
	# ---------------------------
	# Declaraci�n de constantes.
	# ---------------------------
	define("WIDTH_ACCION", 5);
	# ---------------------------

	# ---------------------------
	# Inicializaci�n de variables.
	# ---------------------------
//	$intHeight 		      = fncRequest(REQUEST_METHOD_POST, "hdn_height", REQUEST_TYPE_INTEGER, 0);
	$strSql    		      = $strSql_p;
	$j	       		      = 0;

	# Arrays.
	$arrTitles   	      = fncSplit($strTitles, 				",");
	$arrWidths   	      = fncSplit($strWidths, 				",");
	$arrAcciones 	      = fncSplit(strtoupper($strAcciones),  ",");
	$arrAccionesAux       = fncSplit($strAccionesAux, 		 	",");
	$arrBuscador1 	      = fncSplit($strBuscador1, 			",");
	$arrBuscador2 	      = fncSplit($strBuscador2, 			",");
	$arrBuscador3 	      = fncSplit($strBuscador3, 			",");
	$arrDirs	 	      = fncSplit($strDirs, 				    ",");

	$intCount_titles      = count($arrTitles);
	$intCount_acciones    = count($arrAcciones);
	$intCount_AccionesAux = count($arrAccionesAux);
	$intCountBuscador1    = count($arrBuscador1);
	$intCountBuscador2    = count($arrBuscador2);
	$intCountBuscador3    = count($arrBuscador3);

        // Obtengo valores para los parametros de busqueda
	$intAbsolutePage      = fncRequest(REQUEST_METHOD_POST, "hdn_page", 		  REQUEST_TYPE_INTEGER, 1);
	$strSearchField       = fncRequest(REQUEST_METHOD_POST, "hdn_search_field",   REQUEST_TYPE_STRING,  "");
	$strSigno		      = fncRequest(REQUEST_METHOD_POST, "hdn_search_signo",   REQUEST_TYPE_STRING,  "");
	$strText   	          = fncRequest(REQUEST_METHOD_POST, "hdn_search_text",    REQUEST_TYPE_STRING,  "");
	$strFk                = fncRequest(REQUEST_METHOD_POST, "hdn_search_fk",      REQUEST_TYPE_STRING,  "");
	$strChk			      = fncRequest(REQUEST_METHOD_POST, "hdn_search_chk",     REQUEST_TYPE_STRING,  "");
//	$intEstado 		      = fncRequest(REQUEST_METHOD_POST, "hdn_search_estado",  REQUEST_TYPE_INTEGER, -1);
	$strOrderByField      = fncRequest(REQUEST_METHOD_POST, "hdn_OrderBy", 	      REQUEST_TYPE_STRING,  "");
        // foreign key de la tabla idiomas
        $idioma_fk = fncRequest(REQUEST_METHOD_POST, "hdn_search_idioma", REQUEST_TYPE_INTEGER, -1);
        //flag que indica si la búsqueda fue solicitada por el usuario
        $searchRequested = fncRequest(REQUEST_METHOD_POST, "hdn_search_requested", REQUEST_TYPE_STRING, "");


//        echo "search requested?: ".$searchRequested;
        if(!empty($searchRequested)){
            // almaceno los valores obtenidos en la sesión para ser utilizados en caso de que se este volviendo al listado
            // desde otra página del backend (ej. editar CV)
            $search_params = array("intAbsolutePage" => $intAbsolutePage,
                                 "strSearchField" => $strSearchField,
                                 "strSigno" => $strSigno,
                                 "strText" => $strText,
                                 "strFk" => $strFk,
                                 "strChk" => $strChk,
                                 "strOrderByField" => $strOrderByField,
                                 "idioma_fk" => $idioma_fk);
            $_SESSION['search_params']= $search_params;
//            echo "session registered";
        } else {
            //uso las variables botenidas de la sesion para los parametros de busqueda
            // si no se encuentran valores en el form del buscador, uso los almacenados en la session
            if(isset($_SESSION['search_params'])){
//                            echo "session is present";
                $search_params = $_SESSION['search_params'];
                $intAbsolutePage      = $search_params['intAbsolutePage'];
                $strSearchField       = $search_params['strSearchField'];
                $strSigno             = $search_params['strSigno'];
                $strText   	      = $search_params['strText'];
                $strFk                = $search_params['strFk'];
                $strChk               = $search_params['strChk'];
                $strOrderByField      = $search_params['strOrderByField'];
                $idioma_fk            = $search_params['idioma_fk'];
            }
        }


        #########################################################################
        # hack para buscar por idioma                                           #
        # se agrega esto por ser practicamente imposible el agregado del filtro #
        # por medio de los parametros de esta "funcion"                         #
        #########################################################################


	subSepararPipe($strSearchField, $strSearchFieldName, $strSearchFieldType);

	$strBuscadorJs = "";
	# ---------------------------

	# ---------------------------
	# Template.
	# ---------------------------
	# Pongo las variables en el JS subBuscar().
	$objTpl->SetVar("BLK_BUSCADOR.cant_fields",  count($arrBuscador1));
	$objTpl->SetVar("BLK_BUSCADOR.cant_fk",      count($arrBuscador2));
	$objTpl->SetVar("BLK_BUSCADOR.cant_chk",     count($arrBuscador3));
	$objTpl->SetVar("BLK_BUSCADOR.estados",      ($blnBuscadorEstado)? "true" : "false");

	# Pongo las variables en el form.
//	$objTpl->SetVar("INPUTS.height",	    $intHeight);
	$objTpl->SetVar("INPUTS.op",		    "");
	$objTpl->SetVar("INPUTS.id",		    "");
	$objTpl->SetVar("INPUTS.page",	     	$intAbsolutePage);
	$objTpl->SetVar("INPUTS.search_field", 	$strSearchField);
	$objTpl->SetVar("INPUTS.search_signo",  $strSigno);
	$objTpl->SetVar("INPUTS.search_text",   $strText);
	$objTpl->SetVar("INPUTS.search_fk",		$strFk);
	$objTpl->SetVar("INPUTS.search_chk",	$strChk);
//	$objTpl->SetVar("INPUTS.search_estado",	$intEstado);
	$objTpl->SetVar("INPUTS.OrderBy",	    $strOrderByField);
      	$objTpl->SetVar("INPUTS.search_idioma",  $idioma_fk);
      	$objTpl->SetVar("INPUTS.search_requested",  $searchRequested);
	# ---------------------------

	# ---------------------------
	# Defino el page size.
	# ---------------------------
	$intPageSize = 50;
	# ---------------------------

	# ---------------------------
	# Armo el Sql.
	# ---------------------------
	# SELECT _ FROM _ WHERE _ GROUPBY _ ORDER BY _.
	$intInStr_from    = strpos($strSql, "FROM");
	$intInStr_where   = strpos($strSql, "WHERE");
	$intInStr_GroupBy = strpos($strSql, "GROUP BY");
	$intInStr_OrderBy = strpos($strSql, "ORDER BY");

	# Order By.
	if($intInStr_OrderBy > 0)
	{
		$strSql_OrderBy = substr($strSql, $intInStr_OrderBy + 8);
		$strSql		    = substr($strSql, 0, $intInStr_OrderBy - 1);
	}

	# Group By.
        $strSql_GroupBy = "";
	if($intInStr_GroupBy > 0)
	{
		$strSql_GroupBy = substr($strSql, $intInStr_GroupBy + 8);
		$strSql		    = substr($strSql, 0, $intInStr_GroupBy - 1);
	}

	# Where.
	if($intInStr_where > 0)
	{
		$strSql_where = substr($strSql, $intInStr_where + 6);
		$strSql		  = substr($strSql, 0, $intInStr_where - 1);
	}

	# From.
	$strSql_from = substr($strSql, $intInStr_from + 5);
	$strSql		 = substr($strSql, 0, $intInStr_from - 1);

	# Select.
	$strSql_select = substr($strSql, 7);

	$strSql  = "";
	$strSql .= "SELECT ";
	$strSql .= 		$strSql_select . " ";
	$strSql .= "FROM ";
	$strSql .= 		$strSql_from   . " ";
	$strSql .= "WHERE ";
	$strSql .= 		"0 = 0";

	if($intInStr_where > 0)
		$strSql .= " AND " . $strSql_where;
	
	# Where (Buscador 1).
	switch($strSigno)
	{
		case(""): break;
		case("Like"):
		{
			$strSql .= " AND LOWER(" . $strSearchFieldName . ") LIKE '%" . $strText . "%'";
			break;
		}
		case("Igual"):
		{
			$strSql .= " AND LOWER(" . $strSearchFieldName . ") = LOWER('" . $strText . "')";
			break;
		}
		default:
		{
			if(($strSearchFieldType == "datetime") || ($strSearchFieldType == "timestamp"))
				$strSql .= " AND " . $strSearchFieldName . " " . $strSigno . " " . fncFormatDateToSql($strText);
			else
				$strSql .= " AND " . $strSearchFieldName . " " . $strSigno . " " . $strText;
		}
	}

	# Where (Buscador 2).
	if($strFk != "")
	{
		$arr1  = fncSplit($strFk, ",");
		$intTo = count($arr1) - 1;
		for($j = 0; $j <= $intTo; $j ++)
		{
			if(!fncIsEmptyOrNull($arr1[$j]))
			{
				$arr2 = fncSplit($arrBuscador2[$j], "|");
				if(count($arr2) == 3)
				{
					if(fncIsInteger($arr1[$j]))
						$strSql .= " AND " . $arr2[2] . " = " . $arr1[$j];
					else
						$strSql .= " AND " . $arr2[2] . " = '" . $arr1[$j] . "'";
				}
			}
		}
	}

	# Where (Buscador 3).
	if($strChk != "")
	{
		$arr1  = fncSplit($strChk, ",");
		$intTo = count($arr1) - 1;
		for($j = 0; $j <= $intTo; $j ++)
		{
			if(!fncIsEmptyOrNull($arr1[$j]))
				$strSql .= " AND " . $arr1[$j] . " = 1";
		}
	}
	
	# Where (Buscador 4 - Estado).
//	if($intEstado > -1)
//		$strSql .= " AND habilitado = " . $intEstado;

        # hack para agregar filtro de de busqueda por idioma
        # where (buscador por idioma)
        $id_idioma = (int)$idioma_fk;
        if ($id_idioma != -1){
         $strSql .= " AND cvs.id_cv in (select distinct cvs.id_cv from cvs, cvs_idiomas_niveles where cvs.id_cv = fk_id_cv  and fk_id_idioma = ".$id_idioma.")";
        }
        
	# Group By.
	if($strSql_GroupBy != "")
	{
		$strSql .= " GROUP BY ";
		$strSql .= 		$strSql_GroupBy . " ";
	}

	# Order By.
	if($strOrderByField != "")
		$strSql .= " ORDER BY " . $strOrderByField;
	else
	{
		if($strSql_OrderBy != "")
			$strSql .= " ORDER BY " . $strSql_OrderBy;
	}

 //           var_dump($strSql_p);

	$objTpl->SetVar("sql", $strSql);
	# ---------------------------
//            var_dump($strSql);

	# ---------------------------
	# Rs.
	# ---------------------------
	$rs = $GLOBALS["objDBCommand"]->Rs($strSql, null, $intPageSize, $intAbsolutePage);
	$intRecordCount = $rs->RecordCount();
	$intPageCount   = $rs->PageCount();

	$objTpl->SetVar("RecorCount", $intRecordCount);
	# ---------------------------

	# ---------------------------
	# Armo el buscador.
	# ---------------------------
	if($intCountBuscador1 > 0 || $intCountBuscador2 > 0 || $intCountBuscador3 > 0 || $blnBuscadorEstado)
	{
		# Buscador 1.
		if($intCountBuscador1 > 0)
		{
			for($j = 0; $j <= $intCountBuscador1 - 1; $j ++)
			{
				subSepararPipe($arrBuscador1[$j], $str1, $str2);

				$strType			 	   = $rs->Field($str1)->Type();
				$strFieldNameAndType 	   = $str1 . "|" . $strType;
				$strBuscadorJs		 	   = $strBuscadorJs . $strFieldNameAndType . ",";
				$arr[$strFieldNameAndType] = $str2;
			}
			subFillSelectWithTpl_FromArray($arr, $objTpl, "BLK_BUSCADOR.BLK_TR_1.BLK_CMB_SEARCH_FIELDS_OPTION", 0);
			$objTpl->SetVar("BLK_BUSCADOR.BLK_TR_1.text", $strText);
			$objTpl->Parse("BLK_BUSCADOR.BLK_TR_1");
		}

		$strBuscadorJs = substr($strBuscadorJs, 0, strlen($strBuscadorJs) - 1);

		$objTpl->SetVar("BLK_BUSCADOR.BuscadorJs", $strBuscadorJs);

		# Buscador 2.
		if($intCountBuscador2 > 0)
		{
			for($j = 0; $j <= $intCountBuscador2 - 1; $j ++)
			{
				$arr1 = fncSplit($arrBuscador2[$j], "|");

				$objTpl->SetVar("BLK_BUSCADOR.BLK_TR_2.BLK_CMB_SEARCH_FK.index", $j + 1);
				$objTpl->SetVar("BLK_BUSCADOR.BLK_TR_2.BLK_CMB_SEARCH_FK.title", $arr1[0]);
				subFillSelectWithTpl_FromSql($arr1[1], $objTpl, "BLK_BUSCADOR.BLK_TR_2.BLK_CMB_SEARCH_FK.BLK_CMB_SEARCH_FK_OPTION", 0);
				$objTpl->Parse("BLK_BUSCADOR.BLK_TR_2.BLK_CMB_SEARCH_FK");
			}
			$objTpl->Parse("BLK_BUSCADOR.BLK_TR_2");
		}

		# Buscador 3.
		if($intCountBuscador3 > 0)
		{
			for($j = 0; $j <= $intCountBuscador3 - 1; $j ++)
			{
				subSepararPipe($arrBuscador3[$j], $str1, $str2);

				$objTpl->SetVar("BLK_BUSCADOR.BLK_TR_3.BLK_CHK_SEARCH.index", $j + 1);
				$objTpl->SetVar("BLK_BUSCADOR.BLK_TR_3.BLK_CHK_SEARCH.des",   $str2);
				$objTpl->SetVar("BLK_BUSCADOR.BLK_TR_3.BLK_CHK_SEARCH.val",   $str1);

				$objTpl->Parse("BLK_BUSCADOR.BLK_TR_3.BLK_CHK_SEARCH");
			}
			$objTpl->Parse("BLK_BUSCADOR.BLK_TR_3");
		}

		# Estado.
		if($blnBuscadorEstado)
		{
			if($rs->Field("habilitado")->Value())
				$objTpl->SetVar("BLK_BUSCADOR.BLK_TR_4.selected_habilitado", "selected");
			else
				$objTpl->SetVar("BLK_BUSCADOR.BLK_TR_4.selected_deshabilitado", "selected");
				
			$objTpl->Parse("BLK_BUSCADOR.BLK_TR_4");
		}

                #injerto para mostrar campo de busqueda por idioma
                $options_idiomas =  generateOptions(getCmbIdiomasOptions(),$idioma_fk);

                $objTpl->SetVar("BLK_BUSCADOR.BLK_SRCH_IDIOMA.options",$options_idiomas);
                $objTpl->Parse("BLK_BUSCADOR.BLK_SRCH_IDIOMA");


		$objTpl->Parse("BLK_BUSCADOR");
	}
	# ---------------------------

	if($intRecordCount == 0)
		$objTpl->Parse("BLK_NOITEMS");
	else
	{
		/*
		$blnFile 	   = fncStrPos($strSql_select, "file")	     > 0;
		$blnDestacado  = fncStrPos($strSql_select, "destacado")  > 0;
		*/
		$blnEnabled    = fncStrPos($strSql_select, "habilitado") > 0;

		# Width de file, destacado, habilitado y relaci�n.
		$intW1 = /* ((int) $blnFile * WIDTH_ACCION * 2) + ((int) $blnDestacado * WIDTH_ACCION * 2) + */ ((int) $blnEnabled * WIDTH_ACCION * 2) + ($intCount_AccionesAux * 15);
		# Width de acciones.		
		$intW2 = WIDTH_ACCION * $intCount_acciones;
		# Width restante.
		$intW3 = 100 - ($intW1 + $intW2);
		// echo($intW1 . " " . $intW2 . " " . $intW3);
		
		# ---------------------------
		# Titulos.
		# ---------------------------
		for($k = 0; $k <= $intCount_titles - 1; $k ++)
		{
			$objTpl->SetVar("BLK_LIST.BLK_TD_TITLE.width", fncWidth($intW3, $arrWidths[$k]));
			$objTpl->SetVar("BLK_LIST.BLK_TD_TITLE.title", $arrTitles[$k]);

			$strFieldName = $rs->Field($k + 1)->Name();

			switch($strOrderByField)
			{
				case $strFieldName:
					{
						$objTpl->SetVar("BLK_LIST.BLK_TD_TITLE.field",				    $strOrderByField . " DESC");
						$objTpl->SetVar("BLK_LIST.BLK_TD_TITLE.BLK_IMGORDEN.img_orden", "upsimple.png");
						$objTpl->Parse("BLK_LIST.BLK_TD_TITLE.BLK_IMGORDEN");
						break;
					}
				case $strFieldName . " DESC":
					{
						$objTpl->SetVar("BLK_LIST.BLK_TD_TITLE.FIELD",				   	 $strFieldName);
						$objTpl->SetVar("BLK_LIST.BLK_TD_TITLE.BLK_IMGORDEN.img_orden",  "downsimple.png");
						$objTpl->Parse("BLK_LIST.BLK_TD_TITLE.BLK_IMGORDEN");
						break;
					}
				default:
					{
						$objTpl->SetVar("BLK_LIST.BLK_TD_TITLE.field",			  	     $strFieldName);
						$objTpl->SetVar("BLK_LIST.BLK_TD_TITLE.BLK_IMGORDEN.img_orden",  "blank.png");
						break;
					}
			}
			$objTpl->Parse("BLK_LIST.BLK_TD_TITLE");
		}

		/*
		if($blnFile)	  {$objTpl->Parse("BLK_LIST.BLK_TD_TITLE_FILE");	  }
		if($blnDestacado) {$objTpl->Parse("BLK_LIST.BLK_TD_TITLE_DESTACADO"); }
		*/		
		if($blnEnabled)   {$objTpl->Parse("BLK_LIST.BLK_TD_TITLE_HABILITADO");}

		# Width de acciones.
		if($intCount_acciones > 0)
		{
			$objTpl->SetVar("BLK_LIST.BLK_TD_TITLE_ACCIONES.width", $intW2);
			$objTpl->Parse("BLK_LIST.BLK_TD_TITLE_ACCIONES");
		}
		
		# Acciones aux.
		for($k = 0; $k <= $intCount_AccionesAux - 1; $k ++)
		{
			$arr = fncSplit($arrAccionesAux[$k], "|");
			/*$objTpl->SetVar("BLK_ACCION_AUX.descripcion", $arr[1]);
			$objTpl->SetVar("BLK_ACCION_AUX.action",$arr[0] . ".php");
			$objTpl->SetVar("BLK_ACCION_AUX.op","");
			$objTpl->Parse("BLK_ACCION_AUX");*/
			$objTpl->Parse("BLK_LIST.BLK_TD_TITLE_ACCION_AUX");
		}
		# ---------------------------

		# ---------------------------
		# Listado.
		# ---------------------------
		do
		{
			$intId = $rs->Field(0)->Value();

			$objTpl->SetVar("BLK_LIST.BLK_TR_VALUE.id", $intId);

			for($k = 1; $k <= $intCount_titles; $k ++)
			{
				$strName = $rs->Field($k)->Name();
				$value   = $rs->Field($k)->Value();

				if(fncIsFieldImage($strName))
				{
					if(fncIsImage($value))
					{
						$objTpl->SetVar("BLK_LIST.BLK_TR_VALUE.BLK_TD_VALUE.BLK_IMG.src", URL . $arrDirs[0] . $value);
						$objTpl->Parse("BLK_LIST.BLK_TR_VALUE.BLK_TD_VALUE.BLK_IMG");
					}
					else
					{
						$objTpl->SetVar("BLK_LIST.BLK_TR_VALUE.BLK_TD_VALUE.BLK_FILE.ext", fncFileGetExtension($value));
						$objTpl->Parse("BLK_LIST.BLK_TR_VALUE.BLK_TD_VALUE.BLK_FILE");
					}
				}
				else
				{
					if(fncIsFieldFile($strName))
					{
						# Nada.
					}
					else
					{
						switch($rs->Field($k)->Type())
						{
							case("string"):
								{
									$objTpl->SetVar( "BLK_LIST.BLK_TR_VALUE.BLK_TD_VALUE.BLK_COMMON.align", "left");
									$objTpl->SetVar("BLK_LIST.BLK_TR_VALUE.BLK_TD_VALUE.BLK_COMMON.value",  $value);
									break;
								}
							case("int"):
								{
									$objTpl->SetVar("BLK_LIST.BLK_TR_VALUE.BLK_TD_VALUE.BLK_COMMON.align", "right");
									$objTpl->SetVar("BLK_LIST.BLK_TR_VALUE.BLK_TD_VALUE.BLK_COMMON.value", $value);
									break;
								}
							case("date"):
								{
									$objTpl->SetVar("BLK_LIST.BLK_TR_VALUE.BLK_TD_VALUE.BLK_COMMON.align", "center");
									$objTpl->SetVar("BLK_LIST.BLK_TR_VALUE.BLK_TD_VALUE.BLK_COMMON.value", fncFormatDateFromMySql($value));
									break;									
								}
							case("timestamp"):							
							case("datetime"):
								{
									$objTpl->SetVar("BLK_LIST.BLK_TR_VALUE.BLK_TD_VALUE.BLK_COMMON.align", "center");
									$objTpl->SetVar("BLK_LIST.BLK_TR_VALUE.BLK_TD_VALUE.BLK_COMMON.value", fncFormatDateFromMySql($value));
									break;
								}
							case("currency"):
								{
									$objTpl->SetVar("BLK_LIST.BLK_TR_VALUE.BLK_TD_VALUE.BLK_COMMON.align", "right");
									$objTpl->SetVar("BLK_LIST.BLK_TR_VALUE.BLK_TD_VALUE.BLK_COMMON.value", "10.35"); // fncFormatCurrency($rs->Field($k)->Value(), ".", ","));
									break;
								}
							case("boolean"):
								{
									$objTpl->SetVar("BLK_LIST.BLK_TR_VALUE.BLK_TD_VALUE.BLK_COMMON.align", "center");
									$objTpl->SetVar("BLK_LIST.BLK_TR_VALUE.BLK_TD_VALUE.BLK_COMMON.value", "1"); // fncIIF($rs->Field($k)->Value(), "X", ""));
									break;
								}
							default:
								{
									$objTpl->SetVar("BLK_LIST.BLK_TR_VALUE.BLK_TD_VALUE.BLK_COMMON.align", "left");
									$objTpl->SetVar("BLK_LIST.BLK_TR_VALUE.BLK_TD_VALUE.BLK_COMMON.value", ($value!=0)?$value:"");
								}
						}
						$objTpl->Parse("BLK_LIST.BLK_TR_VALUE.BLK_TD_VALUE.BLK_COMMON");
					}
				}
				$objTpl->Parse("BLK_LIST.BLK_TR_VALUE.BLK_TD_VALUE");
			}

			# -------------------
			# Botones.
			# -------------------
			if(PERMISO == "a")
			{
				/*
				# Destacado.
				if($blnDestacado)
				{
					if($rs->Field("destacado")->Value())
					{
						$objTpl->SetVar("BLK_LIST.BLK_TR_VALUE.BLK_DESTACADO_E.id", $intId);
						$objTpl->Parse("BLK_LIST.BLK_TR_VALUE.BLK_DESTACADO_E");
					}
					else
					{
						$objTpl->SetVar("BLK_LIST.BLK_TR_VALUE.BLK_DESDESTACADO_E.id", $intId);
						$objTpl->Parse("BLK_LIST.BLK_TR_VALUE.BLK_DESDESTACADO_E");
					}
				}
				*/
				
				# Habilitado.
				if($blnEnabled)
				{
					if($rs->Field("habilitado")->Value())
					{
						$objTpl->SetVar("BLK_LIST.BLK_TR_VALUE.BLK_HABILITADO_E.id", $intId);
						$objTpl->Parse("BLK_LIST.BLK_TR_VALUE.BLK_HABILITADO_E");
					}
					else
					{
						$objTpl->SetVar("BLK_LIST.BLK_TR_VALUE.BLK_DESHABILITADO_E.id", $intId);
						$objTpl->Parse("BLK_LIST.BLK_TR_VALUE.BLK_DESHABILITADO_E");
					}
				}

				# Acciones.
				if($intCount_acciones > 0)
				{
					for($k = 0; $k <= $intCount_acciones - 1; $k++)
					{
						$objTpl->SetVar("BLK_LIST.BLK_TR_VALUE.BLK_ACCIONES.BLK_" . $arrAcciones[$k] . "_E.id", $intId);
						$objTpl->Parse("BLK_LIST.BLK_TR_VALUE.BLK_ACCIONES.BLK_" . $arrAcciones[$k] . "_E");
					}
					$objTpl->Parse("BLK_LIST.BLK_TR_VALUE.BLK_ACCIONES");
				}
			}
			else
			{
				/*
				# Destacado.
				if($blnDestacado)
				{
					if($rs->Field("destacado")->Value())
					$objTpl->Parse("BLK_LIST.BLK_TR_VALUE.BLK_DESTACADO_D");
					else
					$objTpl->Parse("BLK_LIST.BLK_TR_VALUE.BLK_DESDESTACADO_D");
				}
				*/

				# Habilitado.
				if($blnEnabled)
				{
					if($rs->Field("habilitado")->Value())
						$objTpl->Parse("BLK_LIST.BLK_TR_VALUE.BLK_HABILITADO_E");
					else
						$objTpl->Parse("BLK_LIST.BLK_TR_VALUE.BLK_DESHABILITADO_E");
				}

				# Acciones.
				if($intCount_acciones > 0)
				{
					for($k = 0; $k <= $intCount_acciones - 1; $k++)
						$objTpl->Parse("BLK_LIST.BLK_TR_VALUE.BLK_ACCIONES.BLK_" . $arrAcciones[$k] . "_D");
					
					$objTpl->Parse("BLK_LIST.BLK_TR_VALUE.BLK_ACCIONES");
				}
			}

			# Acciones aux.
			for($k = 0; $k <= $intCount_AccionesAux - 1; $k ++)
			{
				$arr = fncSplit($arrAccionesAux[$k], "|");
				$objTpl->SetVar("BLK_LIST.BLK_TR_VALUE.BLK_ACCION_AUX.id",$intId);
				$objTpl->SetVar("BLK_LIST.BLK_TR_VALUE.BLK_ACCION_AUX.accion",$arr[0]);
				$objTpl->SetVar("BLK_LIST.BLK_TR_VALUE.BLK_ACCION_AUX.op",$arr[2]);
				$objTpl->SetVar("BLK_LIST.BLK_TR_VALUE.BLK_ACCION_AUX.descripcion", $arr[1]);
				$objTpl->Parse("BLK_LIST.BLK_TR_VALUE.BLK_ACCION_AUX");
                        }
			# -------------------

			$objTpl->Parse("BLK_LIST.BLK_TR_VALUE");
			$rs->MoveNext();
		}
		while(!$rs->EOF());
		# ---------------------------

		unset($rs);

		if($intPageCount > 1)
		{
			# -------------------
			# Paginador.
			# -------------------
			if($intAbsolutePage == 1)
			{
				$intFirstPage = $intPageCount;
				$intPrevPage  = $intPageCount;
			}
			else
			{
				$intPrevPage  = $intAbsolutePage - 1;
				$intFirstPage = 1;
			}

			if($intAbsolutePage == $intPageCount)
			{
				$intNextPage = 1;
				$intLastPage = 1;
			}
			else
			{
				$intNextPage = $intAbsolutePage + 1;
				$intLastPage = $intPageCount;
			}

			$objTpl->SetVar("BLK_PAGINADOR.PageCount", $intPageCount);
			$objTpl->SetVar("BLK_PAGINADOR.FirstPage", $intFirstPage);
			$objTpl->SetVar("BLK_PAGINADOR.PrevPage",  $intPrevPage);

			for($k = 1; $k <= $intPageCount; $k ++)
			{
				if($k == $intAbsolutePage)
				$objTpl->SetVar("BLK_PAGINADOR.BLK_CMB_PAGINAS.selected", "selected");
				else
				$objTpl->SetVar("BLK_PAGINADOR.BLK_CMB_PAGINAS.selected", "");

				$objTpl->SetVar("BLK_PAGINADOR.BLK_CMB_PAGINAS.page", $k);
				$objTpl->Parse("BLK_PAGINADOR.BLK_CMB_PAGINAS");
			}

			$objTpl->SetVar("BLK_PAGINADOR.pages", $intPageCount);

			$objTpl->SetVar("BLK_PAGINADOR.NextPage", $intNextPage);
			$objTpl->SetVar("BLK_PAGINADOR.LastPage", $intLastPage);

			$objTpl->Parse("BLK_PAGINADOR");
			# -------------------
		}

		$objTpl->Parse("BLK_LIST");
	}
}

function subA(&$objTpl)
{
	if(PERMISO == "a")
	$objTpl->Parse("BLK_A_E");
	else
	$objTpl->Parse("BLK_A_D");
}

function subOtro(&$objTpl, $strAction, $blnEncTypeMultipart, $strOp, $strBot, $strAlt)
{
	$strBlk = (PERMISO == "a")? "BLK_OTRO_E" : "BLK_OTRO_D";

	$objTpl->SetVar("action_otro", $strAction . ".php");
	if(blnEncTypeMultipart) $objTpl->Parse("BLK_OTRO_ENCTYPE_MULTIPART");
	$objTpl->SetVar("op_otro", $strOp);

	$objTpl->SetVar($strBlk . ".bot", $strBot);
	$objTpl->SetVar($strBlk . ".alt", $strAlt);

	$objTpl->Parse($strBlk);
}
# ---------------------------------------------

# ---------------------------------------------
# Template.
# ---------------------------------------------
$objTpl->SetVar("title_2", "Listado");
$objTpl->SetVar("message", fncRequest(REQUEST_METHOD_POST, "hdn_message", REQUEST_TYPE_STRING, ""));

$objTpl->SetFile("MIDDLE",	      PATH_TEMPLATES . "estructura-interna-l.html");
$objTpl->SetFile("MIDDLE.INPUTS", PATH_TEMPLATES . "estructura-inputs.html");

$objTpl->UseNamespace("MIDDLE", true);

$objTpl->SetVar("action_f",   PAGE_F);
$objTpl->SetVar("action_abm", PAGE_ABM);
# ---------------------------------------------
?>