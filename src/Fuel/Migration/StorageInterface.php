<?php
/**
 * Part of the FuelPHP framework.
 *
 * @package   Fuel\Migration\Storage
 * @version   2.0
 * @license   MIT License
 * @copyright 2010 - 2014 Fuel Development Team
 */

namespace Fuel\Migration;

/**
 * Defines a common interface for storing run migrations.
 *
 * @package Fuel\Migration\Storage
 * @since   2.0
 * @author  Fuel Development Team
 */
interface StorageInterface
{

	/**
	 * Adds a string to be saved.
	 *
	 * @param  string $string
	 *
	 * @return $this
	 */
	public function add($string);

	/**
	 * Resets the list of strings
	 *
	 * @return $this
	 */
	public function reset();

	/**
	 * Returns a list of strings that have already been stored
	 *
	 * @return array
	 */
	public function getPast();

	/**
	 * Removes a string
	 *
	 * @param  string $string
	 *
	 * @return $this
	 */
	public function remove($string);

	/**
	 * Gets a list of strings that will be added if the save() method is called
	 *
	 * @return array
	 */
	public function get();

	/**
	 * When called will save the list of strings
	 */
	public function save();

}
