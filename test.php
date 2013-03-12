<?php

/*
This is a test file designed to be included in an interactive php terminal for quick debugging.
After this file is included a varable called $main will be set up as an instance of
FuelPHP\Migration\Main and can be used for testing.
*/

require ('vendor/autoload.php');

function __autoload($className)
{
	$className = ltrim($className, '\\');
	$fileName  = '';
	$namespace = '';
	if ($lastNsPos = strripos($className, '\\')) {
		$namespace = substr($className, 0, $lastNsPos);
		$className = substr($className, $lastNsPos + 1);
		$fileName  = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
	}
	$fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';

	require 'src'.DIRECTORY_SEPARATOR.$fileName;
}

require ('tests/src/test_migration_classes.php');

$main = new FuelPHP\Migration\Main();

