<?php

/**
 * Part of the FuelPHP framework.
 *
 * @package   FuelPHP\Migration
 * @version   2.0
 * @license   MIT License
 * @copyright 2010 - 2013 Fuel Development Team
 */

namespace FuelPHP\Migration\Logger;

use Psr\Log\AbstractLogger;
use Psr\Log\LogLevel;

/**
 * Basic implementation for displaying log messages. Intended really only for
 * testing.
 *
 * @package FuelPHP\Migration\Message
 * @since   2.0.0
 * @author  Fuel Development Team
 */
class Basic extends AbstractLogger
{

	/**
	 * Defines message colours for each log level based on bash colours
	 */
	protected $cliColours = array(
		LogLevel::DEBUG => '1;30', //Dark grey
		LogLevel::NOTICE => '1;37', //white
		LogLevel::INFO => '1;37',
		LogLevel::WARNING => '1;33', //yellow
		LogLevel::ERROR => '0;31', //Red
		LogLevel::CRITICAL => '0;31',
		LogLevel::EMERGENCY => '0;31',
		LogLevel::ALERT => '0;34', //Blue
	);

	/**
	 * Defines hex colour codes for each log level
	 */
	protected $hexColours = array(
		LogLevel::DEBUG => '#797979', //Dark grey
		LogLevel::NOTICE => '#000', //white
		LogLevel::INFO => '#000',
		LogLevel::WARNING => '#FFF700', //yellow
		LogLevel::ERROR => '#F00', //Red
		LogLevel::CRITICAL => '#F00',
		LogLevel::EMERGENCY => '#F00',
		LogLevel::ALERT => '#4041BF', //Blue
	);

	/**
	 * Logs a message to the command line
	 * 
	 * @param string $message
	 * @param string $level
	 */
	protected function logCli($message, $level)
	{
		print("\033[" .
			$this->cliColours[$level] .
			"m" .
			$message .
			"\033[0m\n"
		);
	}

	/**
	 * Logs a message to the browser
	 * 
	 * @param string $message
	 * @param string $level
	 */
	protected function logOther($message, $level)
	{
		echo '<span style="color: .' .
		$this->hexColours[$level] .
		'">' .
		$message .
		'</span><br />';
	}

	/**
	 * Main logging method.
	 * 
	 * @param string $level   Level from Psr\Log\LogLevel
	 * @param string $message The message to log
	 * @param array  $context Message context
	 */
	public function log($level, $message, array $context = array())
	{
		$logMessage = $this->interpolate($message, $context);
		if ( PHP_SAPI == 'cli' )
		{
			$this->logCli($logMessage, $level);
		}
		else
		{
			$this->logOther($logMessage, $level);
		}
	}

	/**
	 * Interpolates context values into the message placeholders.
	 * Stolen from psr3 logging interface specification
	 */
	public function interpolate($message, array $context = array())
	{
		// build a replacement array with braces around the context keys
		$replace = array();
		foreach ( $context as $key => $val )
		{
			$replace['{' . $key . '}'] = $val;
		}

		// interpolate replacement values into the message and return
		return strtr($message, $replace);
	}

}
