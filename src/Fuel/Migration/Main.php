<?php
/**
 * Part of the FuelPHP framework.
 *
 * @package   Fuel\Migration
 * @version   2.0
 * @license   MIT License
 * @copyright 2010 - 2014 Fuel Development Team
 */

namespace Fuel\Migration;

use Fuel\Common\Arr;
use Fuel\Migration\Storage\StorageInterface;
use Psr\Log\NullLogger;
use Psr\Log\LoggerInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LogLevel;
use WebDriver\Storage;

/**
 * Main entry point for running migrations
 *
 * @package Fuel\Migration
 * @since   2.0
 * @author  Fuel Development Team
 */
class Main implements LoggerAwareInterface
{

	/**
	 * @var DependencyCompiler
	 */
	protected $dc;

	/**
	 * @var LoggerInterface
	 */
	protected $log;

	/**
	 * @var array
	 */
	protected $runStack;

	/**
	 * @var StorageInterface
	 */
	protected $storage;

	public function __construct(StorageInterface $storage)
	{
		//Set up the dependency compiler
		$this->setStorage($storage);

		$this->setLogger(new NullLogger);
		$this->dc = new DependencyCompiler($this->storage, $this->log);
	}

	/**
	 * Sets the Storage logic class.
	 *
	 * @param StorageInterface $storage
	 */
	public function setStorage(StorageInterface $storage)
	{
		$this->storage = $storage;
	}

	/**
	 * Gets the storage logic.
	 *
	 * @return StorageInterface
	 */
	public function getStorage()
	{
		return $this->storage;
	}

	/**
	 * Sets the implementation to use for logging.
	 *
	 * @param  LoggerInterface $log PSR-3 compatible logging class to use
	 * @return $this
	 */
	public function setLogger(LoggerInterface $log)
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
			catch ( RecursiveDependencyException $exc )
			{
				//Break and die here
				$this->logRecursiveDepError($exc);
				return false;
			}
		}

		$toRun = $this->dc->getList();

		$this->log->info('Migrations to run in total: ' . count($toRun));

		$this->runStack = [];

		$totalMigrations = count($toRun);
		//Start running the migrations

		foreach ( $toRun as $class => $migration )
		{
			/** @var Migration $migration */

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
				default:
					$status = 'Unknown migration status';
					$logFlag = LogLevel::ERROR;
					break;
			}

			$this->runStack[$class] = $migration;
			$this->storage->add($class);
			$this->log->log(
				$logFlag,
				(count($this->runStack)) . '/' . $totalMigrations . ': ' . $class . ' ' . $status
			);

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
	 * Logs out a RecursiveDependencyException exception and debug stack trace
	 *
	 * @param RecursiveDependencyException $exc
	 */
	public function logRecursiveDepError(RecursiveDependencyException $exc)
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
