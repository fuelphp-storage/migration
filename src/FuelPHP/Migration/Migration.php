<?php

/**
 * Part of the FuelPHP framework.
 *
 * @package   FuelPHP\Fieldset
 * @version   2.0
 * @license   MIT License
 * @copyright 2010 - 2013 Fuel Development Team
 */

namespace FuelPHP\Migration;

/**
 * Defines a common interface migration objects
 *
 * @package FuelPHP\Fieldset
 * @since   2.0.0
 * @author  Fuel Development Team
 */
abstract class Migration
{
	
	/**
	 * Indicates that a migration was run without problem.
	 */
	const GOOD = 0;
	
	/**
	 * Indicates that a migration failed and the running of migrations should be
	 * aborted.
	 */
	const BAD = 1;
	
	/**
	 * Indicates that the migration failed to run but will not abort the running
	 * of other migrations.
	 */
	const UGLY = 2;

	/**
	 * If this is set to true the migration will not be recorded as run and will
	 * never be skipped if already run.
	 */
	protected $alwaysRun = false;
	
	/**
	 * This method should perform things like database setup, file creation,
	 * permission setting and the like.
	 */
	public abstract function up();
	
	/**
	 * This method should perform the oppsite of up. It will be called when the
	 * migration needs to be undone.
	 */
	public abstract function down();
	
	/**
	 * Should return a list of fully namespaced classes for migrations that this
	 * migration depends on.
	 * 
	 * @return array
	 */
	public function dependencies()
	{
		return array();
	}
	
	public function alwaysRun()
	{
		return $this->alwaysRun;
	}
}

