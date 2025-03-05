<?php
require_once("common.inc.php");

# ---------------------------------------------
# Header.
# ---------------------------------------------
header("Content-Type: text/x-json; charset=utf-8");
# ---------------------------------------------

# ---------------------------------------------
# DBCommand.
# ---------------------------------------------
$GLOBALS["objDBCommand"] = new DBCommand(DB_HOST, DB_USER, DB_PASS, DB_NAME, PATH_DESCRIPTOR);
# ---------------------------------------------
?>