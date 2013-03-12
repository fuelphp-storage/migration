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
	protected $runStack = null;
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
		$this->dc = new DependencyCompiller(
			new $storageDriver($this->config['driver']),
			$this->log
		);
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
	 * @return boolean
	 */
	public function up($migrations)
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
				//Break and die here
				$this->logRecursiveDepError($exc);
				return false;
			}
		}
		
		$toRun = $this->dc->getList();
		
		$this->log->log('Migrations to run in total: ' . count($toRun));

		$this->runStack = array();

		$totalMigrations = count($toRun);
		//Start running the migrations

		foreach ( $toRun as $class => $migration )
		{
			$result = $migration->up();

			switch ( $result )
			{
				//TODO: find a nicer way to do this
				case Migration::GOOD:
					$status = 'good';
					$logFlag = Log::NORMAL;
					break;
				case Migration::UGLY:
					$status = 'ugly';
					$logFlag = Log::WARN;
					break;
				case Migration::BAD:
					$status = 'BAD';
					$logFlag = Log::ERROR;
					break;
			}

			$this->runStack[$class] = $migration;
			$this->log->log((count($this->runStack)) . '/' . $totalMigrations . ': ' . $class . ' ' . $status,
				$logFlag);
			
			//If this is a bad migration then start the rollback process.
			if($result == Migration::BAD)
			{
				$this->log->log($class. ' failed to up. Rolling back changes.');
				return false;
			}
		}

		return true;
	}

	/**
	 * Logs out a RecursiveDependency exception and debug stacktrace
	 * 
	 * @param \FuelPHP\Migration\Exception\RecursiveDependency $exc
	 */
	public function logRecursiveDepError(RecursiveDependency $exc)
	{
		$this->log->log('Recursive dependency detected, aborting!', Log::ERROR);
		$debugStack = $exc->getStack();

		$this->log->log('Stack trace:', Log::ERROR);
		for ( $step = 0; $step < count($debugStack); $step++ )
		{
			$class = $debugStack[$step];
			$this->log->log($step . ': ' . $class, Log::WARN);
		}
	}

}
