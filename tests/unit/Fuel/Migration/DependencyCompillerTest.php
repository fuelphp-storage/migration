<?php

namespace Fuel\Migration;

require_once __DIR__ . '/../../test_migration_classes.php';

class DependencyCompillerTest extends \PHPUnit_Framework_TestCase
{

	/**
	 * @var DependencyCompiller
	 */
	protected $object;

	protected function setUp()
	{
		$storage = \Mockery::mock('Fuel\Migration\Storage\Storage[save, getPast]');
		$storage->shouldReceive('save')->andReturn(true);
		$storage->shouldReceive('getPast')->andReturn(array());

		$this->object = new DependencyCompiller($storage);
	}

	/**
	 * @covers Fuel\Migration\DependencyCompiller::addMigration
	 * @covers Fuel\Migration\DependencyCompiller::getList
	 * @group  Migration
	 */
	public function testAddMigration()
	{
		$this->object->addMigration('Fuel\Migration\MigrationNoDeps');

		$this->assertTrue(array_key_exists(
			'Fuel\Migration\MigrationNoDeps', $this->object->getList()
		));
	}

	/**
	 * @covers Fuel\Migration\DependencyCompiller::addMigration
	 * @covers Fuel\Migration\DependencyCompiller::getList
	 * @group  Migration
	 */
	public function testAddMigrationWithDeps()
	{
		$this->object->addMigration('Fuel\Migration\MigrationWithDeps');

		$this->assertTrue(array_key_exists(
			'Fuel\Migration\MigrationNoDeps', $this->object->getList()
		));
		$this->assertTrue(array_key_exists(
			'Fuel\Migration\MigrationWithDeps', $this->object->getList()
		));
	}

	/**
	 * @covers Fuel\Migration\DependencyCompiller::addMigration
	 * @expectedException \Fuel\Migration\RecursiveDependency
	 * @group  Migration
	 */
	public function testAddMigrationRecursive()
	{
		$this->object->addMigration('Fuel\Migration\MigrationRecursiveA');
	}

	/**
	 * @covers FuelPHP\Migration\DependencyCompiller::addMigration
	 * @group  Migration
	 */
	public function testAddMigrationRecursiveStack()
	{
		$stack = array();

		try
		{
			$this->object->addMigration('Fuel\Migration\MigrationRecursiveA');
		}
		catch ( RecursiveDependency $exc )
		{
			$stack = $exc->getStack();
		}

		$expected = array(
			'Fuel\Migration\MigrationRecursiveA',
			'Fuel\Migration\MigrationRecursiveB',
			'Fuel\Migration\MigrationRecursiveC',
		);

		$this->assertEquals($expected, $stack);
	}

}
