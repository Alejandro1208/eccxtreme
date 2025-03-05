<?php
session_start();
session_unregister("id_administrador");
# $_SESSION["id_administrador"] = 0;
header("Location: login.php");
?>
