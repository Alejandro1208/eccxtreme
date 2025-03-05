<?php
require_once("includes/begin-page-l.inc.php");

# ---------------------------------------------
# Template.
# ---------------------------------------------
subA($objTpl); # Boton agregar.
# ---------------------------------------------

# ---------------------------------------------
# Listado.
# ---------------------------------------------
$strSql  = "";
$strSql .= "SELECT ";
$strSql .= 		"id_perfil, ";
$strSql .= 		"nombre, ";
$strSql .= 		"habilitado ";
$strSql .= "FROM ";
$strSql .= 		"be_perfiles ";
$strSql .= 	"ORDER BY ";
$strSql .= 		"nombre";

subList(
	$objTpl,
	$strSql,
	"Nombre",
	"100",
	"m,b",
	"",
	"",
	"",
	"",
	false,
	""
);
# ---------------------------------------------

require_once("includes/end-page-l.inc.php");	
?>