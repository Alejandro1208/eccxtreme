<?php
require_once("includes/begin-page-interna.inc.php");

# ---------------------------------------------
# Template.
# ---------------------------------------------
$objTpl->SetFile("MIDDLE", PATH_TEMPLATES . "home.html");
$objTpl->SetVar("title_1", "HOME");
$objTpl->SetVar("title_2", "Bienvenido al BackEnd");
# ---------------------------------------------

require_once("includes/end-page-interna.inc.php");	
?>

