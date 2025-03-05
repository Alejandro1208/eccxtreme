<?php 
unset($GLOBALS["objDBCommand"]);

if($strOp=="b"){
    $intId="";
}

if(defined("ACTION")) 
	$strAction = ACTION;
else
	$strAction = PAGE_L;

# ---------------------------------------------
# Form.
# ---------------------------------------------
echo('<body onLoad="document.frm.submit ();">');
echo	('<form name="frm" action="' . $strAction . '" method="post">');
echo		('<input type="hidden" name="hdn_op"		   value="' . $strOp					 													  . '">');
echo		('<input type="hidden" name="hdn_id"           value="' . $intId					 													  . '">');
echo		('<input type="hidden" name="hdn_page"         value="' . $intPage														 				  . '">');
echo		('<input type="hidden" name="hdn_search_field" value="' . fncRequest(REQUEST_METHOD_POST, "hdn_search_field",   REQUEST_TYPE_STRING, "")  . '">');
echo		('<input type="hidden" name="hdn_search_signo" value="' . fncRequest(REQUEST_METHOD_POST, "hdn_search_signo",   REQUEST_TYPE_STRING, "")  . '">');
echo		('<input type="hidden" name="hdn_search_text"  value="' . fncRequest(REQUEST_METHOD_POST, "hdn_search_text",	REQUEST_TYPE_STRING, "")  . '">');
echo		('<input type="hidden" name="hdn_search_fk"	   value="' . fncRequest(REQUEST_METHOD_POST, "hdn_search_fk", 		REQUEST_TYPE_STRING, "")  . '">');
echo		('<input type="hidden" name="hdn_search_chk"   value="' . fncRequest(REQUEST_METHOD_POST, "hdn_search_chk",	    REQUEST_TYPE_STRING, "")  . '">');
echo		('<input type="hidden" name="hdn_OrderBy"      value="' . fncRequest(REQUEST_METHOD_POST, "hdn_search_OrderBy", REQUEST_TYPE_STRING, "")  . '">');
echo		('<input type="hidden" name="hdn_message"	   value="' . $strMensaje																 	  . '">');
echo	('</form>');
echo('</body>');
# ---------------------------------------------
?>