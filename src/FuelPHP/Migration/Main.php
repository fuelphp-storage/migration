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
use FuelPHP\Migration\Exception\RecursiveDependency;

/**
 * Main entry point for running migrations
 *
 * @package FuelPHP\Migration
 * @since   2.0.0
 * @author  Fuel Development Team
 */
class Main
{

	protected $dc = null;
	
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

	public function runMigration($migrations)
	{
		//Add the migration to the DC, if it does not need to be run the list
		//returned will be empty
		foreach((array) $migrations as $migration)
		{
			try
			{
				$this->dc->addMigration($migration);
			}
			catch ( RecursiveDependency $exc )
			{
				//Break and die here
			}
		}
	}

}
