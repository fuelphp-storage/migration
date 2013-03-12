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
 * Basic implementation for displaying log messages
 *
 * @package FuelPHP\Migration\Message
 * @since   2.0.0
 * @author  Fuel Development Team
 */
class Basic extends Log
{

	protected $cliColours = array(
		Log::NORMAL => '1;37',
		Log::WARN => '1;33',
		Log::ERROR => '0;31',
	);
	protected $hexColours = array(
		Log::NORMAL => '#000',
		Log::WARN => '#FFF700',
		Log::ERROR => '#F00',
	);

	public function log($message, $level = Log::NORMAL)
	{
		if ( PHP_SAPI == 'cli' )
		{
			$this->logCli($message, $level);
		}
		else
		{
			$this->logOther($message, $level);
		}
	}

	protected function logCli($message, $level)
	{
		print("\033[" .
			$this->cliColours[$level] .
			"m" .
			$message .
			"\033[0m"
		);
	}

	protected function logOther($message, $level)
	{
		echo '<span style="color: .' .
		$this->hexColours[$level] .
		'">' .
		$message .
		'</span>';
	}

}
