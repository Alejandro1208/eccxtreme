<?php
require_once("common.inc.php");

# ---------------------------------------------
# Constantes.
# ---------------------------------------------
subAuthenticationAndSetConstants();
# ---------------------------------------------

# ---------------------------------------------
# DBCommand.
# ---------------------------------------------
$objDBCommand = new DBCommand(DB_HOST, DB_USER, DB_PASS, DB_NAME, PATH_DESCRIPTOR);
# ---------------------------------------------

# ---------------------------------------------
# Template.
# ---------------------------------------------
$objTpl = new Template(PATH_TEMPLATES . PAGE . ".html");
# ---------------------------------------------

# ---------------------------------------------
# Template.
# ---------------------------------------------
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Exportación.xls");
# ---------------------------------------------
?>