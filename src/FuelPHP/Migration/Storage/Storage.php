<?php

/**
 * Part of the FuelPHP framework.
 *
 * @package   FuelPHP\Migration\Storage
 * @version   2.0
 * @license   MIT License
 * @copyright 2010 - 2013 Fuel Development Team
 */

namespace FuelPHP\Migration\Storage;

/**
 * Defines a common interface for storing run migrations.
 *
 * @package FuelPHP\Migration\Storage
 * @since   2.0.0
 * @author  Fuel Development Team
 */
abstract class Storage
{

	protected $newMigrations = array();

	/**
	 * Adds a string to be saved.
	 * 
	 * @param  string $string
	 * @return \FuelPHP\Migration\Storage\Storage
	 */
	public function add($string)
	{
		$this->newMigrations[$string] = $string;
		return $this;
	}

	/**
	 * Removes a string
	 * 
	 * @param  string $string
	 * @return \FuelPHP\Migration\Storage\Storage
	 */
	public function remove($string)
	{
		unset($this->newMigrations[$string]);
		return $this;
	}

	/**
	 * Gets a list of strings that will be added if the save() method is called
	 * 
	 * @return array
	 */
	public function get()
	{
		return $this->newMigrations;
	}

	/**
	 * Resets the list of strings
	 * 
	 * @return \FuelPHP\Migration\Storage\Storage
	 */
	public function reset()
	{
		$this->newMigrations = array();
		return $this;
	}

	/**
	 * When called will save the list of strings
	 */
	public abstract function save();

	/**
	 * Returns a list of strings that have already been stored
	 * 
	 * @return array
	 */
	public abstract function getPast();

}
