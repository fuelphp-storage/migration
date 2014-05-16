<?php
/**
 * Part of the FuelPHP framework.
 *
 * @package   Fuel\Migration\Storage
 * @version   2.0
 * @license   MIT License
 * @copyright 2010 - 2014 Fuel Development Team
 */

namespace Fuel\Migration\Storage;

/**
 * Defines a common interface for storing run migrations.
 *
 * @package Fuel\Migration\Storage
 * @since   2.0
 * @author  Fuel Development Team
 */
abstract class Storage implements StorageInterface
{

	protected $newMigrations = array();

	/**
	 * Adds a string to be saved.
	 *
	 * @param  string $string
	 * @return $this
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
	 * @return $this
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
	 * @return $this
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
