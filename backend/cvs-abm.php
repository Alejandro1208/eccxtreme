<?php
require_once("includes/begin-page-abm.inc.php");

# ---------------------------------------------
# Proceso.
# ---------------------------------------------
$arrParameters["intId"] = $intId;
$GLOBALS["objDBCommand"]->Execute($strSql, $arrParameters);
$strMensaje = fncMensajeOk($strOp);
# ---------------------------------------------

require_once("includes/end-page-abm.inc.php");	
?>

