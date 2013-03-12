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
use FuelPHP\Migration\Message\Log;
use FuelPHP\Migration\Message\Basic;

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
	protected $log = null;
	protected $config = array(
		'driver' => array(
			'type' => 'FuelPHP\Migration\Storage\File',
		),
	);

	public function __construct($config = array())
	{
		//Set a default file location
		$this->config['driver']['location'] = __DIR__ . DIRECTORY_SEPARATOR . '../../../resources/migrations.list';
		$this->config = Arr::merge($this->config, $config);

		//Set up the dependency compiller
		$storageDriver = Arr::get($this->config, 'driver.type');
		
		if ( !is_subclass_of($storageDriver, 'FuelPHP\Migration\Storage\Storage') )
		{
			throw new \InvalidArgumentException('Storage driver must extend Storage\Storage');
		}

		$this->setLogger(new Basic);
		$this->dc = new DependencyCompiller(new $storageDriver($this->config['driver']));
	}

	/**
	 * Sets the implementation to use for logging.
	 * 
	 * @param  FuelPHP\Migration\Message\Log $log
	 * @return FuelPHP\Migration\Main
	 */
	public function setLogger(Log $log)
	{
		$this->log = $log;
		return $this;
	}

	/**
	 * Given a list of migrations will compile dependencies and run the up method
	 * of all needed migrations.
	 * 
	 * @param  string|array $migrations
	 * @return \FuelPHP\Migration\Main
	 */
	public function runMigrations($migrations)
	{
		$migrations = (array) $migrations;
		$this->dc->reset();
		$this->log->log('Migrations started', Log::WARN);

		$total = count($migrations);
		$this->log->log($total . ' migration' . (($total == 1) ? '' : 's') . ' to install');

		$this->log->log('Compiling dependencies', Log::WARN);

		//Add the migration to the DC, if it does not need to be run the list
		//returned will be empty
		foreach ( $migrations as $migration )
		{
			try
			{
				$this->dc->addMigration($migration);
			}
			catch ( RecursiveDependency $exc )
			{
				//TODO: move this into it's own method
				//Break and die here
				$this->log->log('Recursive dependency detected, aborting!', Log::ERROR);
				$debugStack = $exc->getStack();

				$this->log->log('Stack trace:', Log::ERROR);
				for ( $step = 0; $step < count($debugStack); $step++ )
				{
					$class = $debugStack[$step];
					$this->log->log($step . ': ' . $class, Log::WARN);
				}

				return;
			}
		}

		$this->upMigrations($this->dc->getList());
		return $this;
	}
	
	protected function upMigrations(array $migrations)
	{
		$this->log->log('Migrations to run in total: ' . count($migrations));
	}

}
