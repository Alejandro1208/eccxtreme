<?php
require_once("includes/begin-page.inc.php");

# ---------------------------------------------
# Template.
# ---------------------------------------------
$objTpl->SetFile("MIDDLE", PATH_TEMPLATES . "session-expirada.html");
$objTpl->SetVar("titulo", "TIEMPO AGOTADO");
# ---------------------------------------------

require_once("includes/end-page.inc.php");	
?>

