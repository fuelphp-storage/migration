<?php

namespace Fuel\Migration;

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
			'Fuel\Migration\MigrationNoDeps'
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
			'Fuel\Migration\MigrationRecursiveB'
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
			'Fuel\Migration\MigrationRecursiveC'
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
			'Fuel\Migration\MigrationRecursiveA'
		);
	}
}

/**
 * Standard migration that always fails
 */
class MigrationBad extends Migration
{

	public function down()
	{
		return Migration::BAD;
	}

	public function up()
	{
		return Migration::BAD;
	}

}
