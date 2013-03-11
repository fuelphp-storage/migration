<?php

namespace FuelPHP\Migration;

/**
 * Standard migration with no dependencies
 */
class MigrationNoDeps extends Migration
{
	
	public function down()
	{
		return Migration::GOOD;
	}

	public function up()
	{
		return Migration::GOOD;
	}
	
}

/**
 * Standard migration with a dependency on the above class
 */
class MigrationWithDeps extends Migration
{
	
	public function down()
	{
		return Migration::GOOD;
	}

	public function up()
	{
		return Migration::GOOD;
	}
	
	public function dependencies()
	{
		return array(
			'FuelPHP\Migration\MigrationNoDeps'
		);
	}
	
}

class MigrationRecursiveA extends Migration
{
	public function down()
	{
		return Migration::GOOD;
	}

	public function up()
	{
		return Migration::GOOD;
	}
	
	public function dependencies()
	{
		return array(
			'FuelPHP\Migration\MigrationRecursiveB'
		);
	}
}

class MigrationRecursiveB extends Migration
{
	public function down()
	{
		return Migration::GOOD;
	}

	public function up()
	{
		return Migration::GOOD;
	}
	
	public function dependencies()
	{
		return array(
			'FuelPHP\Migration\MigrationRecursiveC'
		);
	}
}

class MigrationRecursiveC extends Migration
{
	public function down()
	{
		return Migration::GOOD;
	}

	public function up()
	{
		return Migration::GOOD;
	}
	
	public function dependencies()
	{
		return array(
			'FuelPHP\Migration\MigrationRecursiveA'
		);
	}
}