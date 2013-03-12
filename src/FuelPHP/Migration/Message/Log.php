<?php

/**
 * Part of the FuelPHP framework.
 *
 * @package   FuelPHP\Migration
 * @version   2.0
 * @license   MIT License
 * @copyright 2010 - 2013 Fuel Development Team
 */

namespace FuelPHP\Migration\Message;

/**
 * Common interface for logging migration messages
 *
 * @package FuelPHP\Migration\Message
 * @since   2.0.0
 * @author  Fuel Development Team
 */
abstract class Log
{
	
	const NORMAL = 0;
	const WARN = 1;
	const ERROR = 2;
	
	public abstract function log($message, $level=Log::NORMAL);
	
}
