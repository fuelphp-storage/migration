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
use Psr\Log\NullLogger;
use Psr\Log\LoggerInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LogLevel;

/**
 * Main entry point for running migrations
 *
 * @package FuelPHP\Migration
 * @since   2.0.0
 * @author  Fuel Development Team
 */
class Main implements LoggerAwareInterface
{

	protected $dc = null;
	protected $log = null;
	protected $runStack = null;
	protected $storage = null;
	protected $config = array(
		'storage' => array(
			'type' => 'FuelPHP\Migration\Storage\File',
		),
	);

	public function __construct($config = array())
	{
		//Set a default file location
		$this->config['storage']['location'] = __DIR__ . DIRECTORY_SEPARATOR . '../../../resources/migrations.list';
		$this->config = Arr::merge($this->config, $config);

		//Set up the dependency compiller
		$stroage = Arr::get($this->config, 'storage.instance', false);
		if ( $stroage === false )
		{
			$storageDriver = Arr::get($this->config, 'storage.type');

			if ( !is_subclass_of($storageDriver, 'FuelPHP\Migration\Storage\Storage') )
			{
				throw new \InvalidArgumentException('Storage driver must extend Storage\Storage');
			}
			$storage = new $storageDriver($this->config['storage']);
		}
		$this->storage = $storage;

		$this->setLogger(new NullLogger);
		$this->dc = new DependencyCompiller($storage, $this->log);
	}

	/**
	 * Sets the implementation to use for logging.
	 * 
	 * @param  LoggerInterface $log PSR-3 compatable logging class to use
	 * @return FuelPHP\Migration\Main
	 */
	public function setLogger(LoggerInterface $log)
	{
		$this->log = $log;
		return $this;
	}
	
	/**
	 * Gets the storage instance that is being used.
	 * @return FuelPHP\Migration\Storage\Storage
	 */
	public function getStorage()
	{
		return $this->storage;
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
		$this->log->info('Migrations started');

		$total = count($migrations);
		$this->log->info($total . ' migration' . (($total == 1) ? '' : 's') . ' to install');

		$this->log->info('Compiling dependencies');

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

		$this->log->info('Migrations to run in total: ' . count($toRun));

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
					$logFlag = LogLevel::INFO;
					break;
				case Migration::UGLY:
					$status = 'ugly';
					$logFlag = LogLevel::WARNING;
					break;
				case Migration::BAD:
					$status = 'BAD';
					$logFlag = LogLevel::ERROR;
					break;
			}

			$this->runStack[$class] = $migration;
			$this->storage->add($class);
			$this->log->log($logFlag,
				(count($this->runStack)) . '/' . $totalMigrations . ': ' . $class . ' ' . $status);

			//If this is a bad migration then start the rollback process.
			if ( $result == Migration::BAD )
			{
				$this->log->info($class . ' failed to up. Rolling back changes.');
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
		$this->log->error('Recursive dependency detected, aborting!');
		$debugStack = $exc->getStack();

		$this->log->error('Stack trace:');
		for ( $step = 0; $step < count($debugStack); $step++ )
		{
			$class = $debugStack[$step];
			$this->log->error($step . ': ' . $class);
		}
	}

}
