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
$strSql .= 		"a.id_administrador, ";
$strSql .= 		"a.username, ";
$strSql .= 		"a.nombre AS administrador_nombre, ";
$strSql .= 		"a.apellido, ";
$strSql .= 		"p.nombre     AS perfil_nombre, ";
$strSql .= 		"a.habilitado AS habilitado ";
$strSql .= "FROM ";
$strSql .= 		"be_perfiles p INNER JOIN be_administradores a ";
$strSql .= 		"ON p.id_perfil = a.fk_id_perfil ";
$strSql .= 	"ORDER BY ";
$strSql .= 		"a.username";

subList(
	$objTpl,
	$strSql,
	"Username,Nombre,Apellido,Perfil",
	"25,25,25,25",
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