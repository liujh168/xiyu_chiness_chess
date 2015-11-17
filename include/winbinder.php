<?php

/*******************************************************************************

 WINBINDER - A native Windows binding for PHP

 Copyright © 2004-2006 Hypervisual - see LICENSE.TXT for details
 Author: Rubem Pechansky (http://www.hypervisual.com/winbinder/contact.php)

 Main inclusion file for WinBinder

*******************************************************************************/

$supported_php_version = preg_match(
    '"^(' . '4\.(3\.(10|11)|4\..)' . '|' . '5\.(0\.[3-5]|1\..)' . ')$"', PHP_VERSION
);

if (!$supported_php_version)
	die("WinBinder does only support the following PHP versions:\n"
		. "- 4.3.10 up to excluding 5.0.0\n- 5.0.3  up to excluding 6.0.0\n");

if(!extension_loaded('winbinder'))
	if(!dl('php_winbinder.dll'))
		trigger_error("WinBinder extension could not be loaded.\n", E_USER_ERROR);

$_mainpath = pathinfo(__FILE__);
$_mainpath = $_mainpath["dirname"] . "/";

// WinBinder PHP functions

include $_mainpath . "wb_windows.inc.php";
include $_mainpath . "wb_generic.inc.php";
include $_mainpath . "wb_resources.inc.php";

//------------------------------------------------------------------ END OF FILE

?>
