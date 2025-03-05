<?php
# Headers.
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
# Inludes.
require_once(PATH . "common/includes/config.inc.php");
require_once(PATH . "common/includes/constants.inc.php");
# Libraries.
require_once(PATH . "common/libraries/array.lib.php");
require_once(PATH . "common/libraries/check.lib.php");
require_once(PATH . "common/libraries/common.lib.php");
require_once(PATH . "common/libraries/convert.lib.php");
require_once(PATH . "common/libraries/DateTime.lib.php");
require_once(PATH . "common/libraries/email.lib.php");
require_once(PATH . "common/libraries/file.lib.php");
require_once(PATH . "common/libraries/format.lib.php");
# Class.
require_once(PATH . "common/class/dbcommand.class.php");
require_once(PATH . "common/class/template.class.php");
?>
