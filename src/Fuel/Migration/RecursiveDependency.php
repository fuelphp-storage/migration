<?php

/**
 * Part of the FuelPHP framework.
 *
 * @package   FuelPHP\Migration
 * @version   2.0
 * @license   MIT License
 * @copyright 2010 - 2014 Fuel Development Team
 */

namespace Fuel\Migration;

/**
 *
 *
 * @package FuelPHP\Migration
 * @since   2.0.0
 * @author  Fuel Development Team
 */
class RecursiveDependency extends \Exception
{

	protected $stack;

	public function setStack($stack)
	{
		$this->stack = $stack;
		return $this;
	}

	public function getStack()
	{
		return $this->stack;
	}

}
