<?php

namespace FuelPHP\Migration;

require __DIR__.'/../../test_migration_classes.php';

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.0 on 2013-03-11 at 15:53:47.
 */
class DependencyCompillerTest extends \PHPUnit_Framework_TestCase
{

	/**
	 * @var DependencyCompiller
	 */
	protected $object;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp()
	{
		$storage = \Mockery::mock('FuelPHP\Migration\Storage\Storage[save, getPast]');
		$storage->shouldReceive('save')->andReturn(true);
		$storage->shouldReceive('getPast')->andReturn(array());
		
		$this->object = new DependencyCompiller($storage);
	}

	/**
	 * @covers FuelPHP\Migration\DependencyCompiller::addMigration
	 * @covers FuelPHP\Migration\DependencyCompiller::getList
	 * @group  Migration
	 */
	public function testAddMigration()
	{
		$this->object->addMigration('FuelPHP\Migration\MigrationNoDeps');
		
		$this->assertTrue(key_exists(
			'FuelPHP\Migration\MigrationNoDeps', $this->object->getList()
		));
	}
	
	/**
	 * @covers FuelPHP\Migration\DependencyCompiller::addMigration
	 * @covers FuelPHP\Migration\DependencyCompiller::getList
	 * @group  Migration
	 */
	public function testAddMigrationWithDeps()
	{
		$this->object->addMigration('FuelPHP\Migration\MigrationWithDeps');
		
		
		$this->assertTrue(key_exists(
			'FuelPHP\Migration\MigrationNoDeps', $this->object->getList()
		));
		$this->assertTrue(key_exists(
			'FuelPHP\Migration\MigrationWithDeps', $this->object->getList()
		));
	}

	/**
	 * @covers FuelPHP\Migration\DependencyCompiller::addMigration
	 * @covers FuelPHP\Migration\DependencyCompiller::getList
	 * @group  Migration
	 */
	public function testAddMigrationRecursive()
	{
		$this->object->addMigration('FuelPHP\Migration\MigrationRecursiveA');
		
		
		$this->assertTrue(key_exists(
			'FuelPHP\Migration\MigrationNoDeps', $this->object->getList()
		));
		$this->assertTrue(key_exists(
			'FuelPHP\Migration\MigrationWithDeps', $this->object->getList()
		));
	}
}
