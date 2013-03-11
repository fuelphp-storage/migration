<?php

/**
 * Part of the FuelPHP framework.
 *
 * @package   FuelPHP\Migration
 * @version   2.0
 * @license   MIT License
 * @copyright 2010 - 2013 Fuel Development Team
 */

namespace FuelPHP\Migration;

use FuelPHP\Common\Arr;

/**
 * Main entry point for running migrations
 *
 * @package FuelPHP\Migration
 * @since   2.0.0
 * @author  Fuel Development Team
 */
class Main
{

	protected $config = array(
		'driver' => array(
			'type' => 'File',
		),
	);

	public function __construct($config = array())
	{
		//Set a default file location
		$this->config['driver']['location'] = __DIR__ . '../../../resources/migrations.list';
		$this->config = Arr::merge($config);

		//Set up the dependency compiller
		$class = 'FuelPHP\Migration\Storage\\' . $this->config['driver']['type'];
		if ( ! is_subclass_of($class, 'FuelPHP\Migration\Storage\Storage') )
		{
			throw new \InvalidArgumentException('Storage driver must extend Storage\Storage');
		}
		
		$this->dc = new DependencyCompiller(new $class($this->config['driver']));
	}

	public function runMigration($migration)
	{
		//Check if the class is a subclass of the migration parent class
		
		//Check if this migration has run
		
		
		//if not then add it to the list and load the dependencies
			// $this->runMigration(...)
	}

}
