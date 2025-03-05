<?php
# ---------------------------------------------
#  Verificación de si esta logeado.
# ---------------------------------------------
session_start();
if(isset($_SESSION["id_administrador"]))
	header("Location: home.php");
else
	header("Location: login.php");	
# ---------------------------------------------
?>
