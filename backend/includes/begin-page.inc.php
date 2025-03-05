<?php
require_once("common.inc.php");

# ---------------------------------------------
# DBCommand.
# ---------------------------------------------
$GLOBALS["objDBCommand"] = new DBCommand(DB_HOST, DB_USER, DB_PASS, DB_NAME, PATH_DESCRIPTOR);
# ---------------------------------------------

# ---------------------------------------------
# Template.
# ---------------------------------------------
$objTpl = new Template(PATH_TEMPLATES . "estructura.html");

$objTpl->SetFile("TOP",	   PATH_TEMPLATES . "estructura-top.html");
$objTpl->SetFile("BOTTOM", PATH_TEMPLATES . "estructura-bottom.html");

# SetVar.
subTplSetVar($objTpl);
# ---------------------------------------------
?>