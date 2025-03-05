<?php
define("PATH", str_replace("backend/includes", "", str_replace("\\", "/", dirname(__FILE__))));
require_once(PATH . "common/includes/common.inc.php");
require_once(PATH . "common/includes/config.inc.php");
require_once(PATH . "backend/includes/config.inc.php");
require_once(PATH . "backend/includes/constants.inc.php");
require_once(PATH . "backend/libraries/common.lib.php");
require_once(PATH . "backend/libraries/application.lib.php");
require_once(PATH . "backend/class/PHPWord/PHPWord.php");
?>
