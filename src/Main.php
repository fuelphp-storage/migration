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

/**
 * Main entry point for running migrations
 *
 * @package Fuel\Migration
 * @since   2.0
 * @author  Fuel Development Team
 */
class Main
{

	/**
	 * @var DependencyCompiler
	 */
	protected $dc;

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

		$this->dc = new DependencyCompiler($this->storage);
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
	 * Given a list of migrations will compile dependencies and run the up method
	 * of all needed migrations.
	 *
	 * @param string|array $migrations
	 *
	 * @return boolean
	 */
	public function up($migrations)
	{
		$migrations = (array) $migrations;
		$this->dc->reset();

		//Add the migration to the DC, if it does not need to be run the list
		//returned will be empty
		foreach ( $migrations as $migration )
		{
			$this->dc->addMigration($migration);
		}

		$toRun = $this->dc->getList();

		//Start running the migrations
		foreach ( $toRun as $class => $migration )
		{
			$result = $this->runMigration($migration);

			//If this is a bad migration then start the rollback process.
			if ( $result == Migration::BAD )
			{
				return false;
			}

			// All is good so log the migration as having run
			$this->storage->add($class);
		}

		return true;
	}

	/**
	 * Runs a single migration.
	 *
	 * @param Migration $migration
	 *
	 * @return int
	 */
	protected function runMigration(Migration $migration)
	{
		$result = $migration->up();

		$class = get_class($migration);
		$this->runStack[$class] = $migration;

		return $result;
	}

}
