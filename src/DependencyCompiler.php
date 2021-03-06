<?php
/**
 * Part of the FuelPHP framework.
 *
 * @package   FuelPHP\Migration
 * @version   2.0
 * @license   MIT License
 * @copyright 2010 - 2014 Fuel Development Team
 */

namespace Fuel\Migration;

/**
 * This class is responsible for keeping a list of migrations to run and
 * working out their dependencies.
 *
 * @package Fuel\Migration
 * @since   2.0
 * @author  Fuel Development Team
 */
class DependencyCompiler
{

	/**
	 * Keeps track of migrations that need to be run
	 *
	 * @var array
	 */
	protected $runStack = [];

	/**
	 * Keeps track of the current tree of migrations to allow for fetching of call traces
	 *
	 * @var array
	 */
	protected $debugStack = [];

	/**
	 * Contains a list of migrations that have already been run
	 *
	 * @var array
	 */
	protected $oldMigrations = [];

	/**
	 * @var StorageInterface
	 */
	protected $storage;

	public function __construct(StorageInterface $storage)
	{
		$this->storage = $storage;
		$this->reset();
	}

	/**
	 * Clears the debug and run stacks as well as resetting the list of run
	 * migrations.
	 */
	public function reset()
	{
		$this->runStack = array();
		$this->debugStack = array();

		//Load the list of already run migrations
		$this->oldMigrations = $this->storage->getPast();
	}

	/**
	 * Adds a migration to run and any dependencies it might have.
	 *
	 * @param  string $migration Full class name of the migration to add
	 * @throws \InvalidArgumentException
	 */
	public function addMigration($migration)
	{
		if ( ! is_subclass_of($migration, 'Fuel\Migration\Migration') )
		{
			throw new \InvalidArgumentException($migration . ' should be a subclass of Fuel\Migration\Migration');
		}

		//Check if the migration has been run before
		if ( ! $this->shouldAddMigration($migration) )
		{
			return;
		}

		// Create an instance of the migration to store and poke for more info
		$class = $migration;
		/** @var Migration $migration */
		$migration = new $migration;

		//Add the migration to the run stack and the debug stack
		$this->runStack[$class] = $migration;
		$this->debugStack[] = $class;

		//check for dependencies and add them too if not already added.
		$dependencies = $migration->dependencies();

		foreach ( $dependencies as $dependency )
		{
			//Check if the dependency has been added already
			if ( $this->migrationIsInStack($dependency) )
			{
				$exception = new RecursiveDependencyException('Recursive dependency detected!');
				$exception->setStack($this->debugStack);
				throw $exception;
			}

			$this->addMigration($dependency);
		}

		//Remove the class from the debug stack now that we are done with it
		array_pop($this->debugStack);
	}

	/**
	 * Returns a list of migrations to run in the order they should be run in.
	 *
	 * @return array
	 */
	public function getList()
	{
		return array_reverse($this->runStack);
	}

	/**
	 * Returns the current stack of migrations.
	 *
	 * @return array
	 */
	public function getDebugStack()
	{
		return $this->debugStack;
	}

	/**
	 * Returns true if the given migration is not already in the toRun list and
	 * if it has not already been run in the past.
	 *
	 * @param  string $migration
	 * @return boolean
	 */
	public function shouldAddMigration($migration)
	{
		return !$this->migrationIsInStack($migration) &&
			!$this->migrationHasBeenRun($migration);
	}

	public function migrationIsInStack($migration)
	{
		return array_key_exists($migration, $this->runStack);
	}

	public function migrationHasBeenRun($migration)
	{
		return in_array($migration, $this->oldMigrations);
	}

}
