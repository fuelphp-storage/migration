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

use Codeception\TestCase\Test;

require_once __DIR__ . '/../../test_migration_classes.php';

/**
 * Tests for DependencyCompiler
 *
 * @package Fuel\Migration
 * @author  Fuel Development Team
 *
 * @coversDefaultClass \Fuel\Migration\DependencyCompiler
 */
class DependencyCompilerTest extends Test
{

	/**
	 * @var DependencyCompiler
	 */
	protected $object;

	protected function _before()
	{
		$storage = \Mockery::mock('Fuel\Migration\Storage\Storage[save, getPast]');
		$storage->shouldReceive('save')->andReturn(true);
		$storage->shouldReceive('getPast')->andReturn(array());

		$this->object = new DependencyCompiler($storage);
	}

	/**
	 * @covers ::addMigration
	 * @covers ::getList
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
	 * @covers ::addMigration
	 * @covers ::getList
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
	 * @covers            ::addMigration
	 * @expectedException \Fuel\Migration\RecursiveDependency
	 * @group             Migration
	 */
	public function testAddMigrationRecursive()
	{
		$this->object->addMigration('Fuel\Migration\MigrationRecursiveA');
	}

	/**
	 * @covers ::addMigration
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