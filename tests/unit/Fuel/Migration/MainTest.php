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
use org\bovigo\vfs\vfsStream;

/**
 * Tests for Main
 *
 * @package Fuel\Migration
 * @author  Fuel Development Team
 *
 * @coversDefaultClass \Fuel\Migration\Main
 */
class MainTest extends Test
{

	/**
	 * @var Main
	 */
	protected $object;

	protected function _before()
	{
		vfsStream::setup('fuelphpMigration');
		$storageFile = vfsStream::url('fuelphpMigration/MainTest.tmp');

		$storage = new Storage\File(['location' => $storageFile]);

		$this->object = new Main($storage);
	}

	/**
	 * @covers ::up
	 * @covers ::runMigration
	 * @group  Migration
	 */
	public function testUp()
	{
		$migration = 'Fuel\Migration\MigrationNoDeps';

		$this->assertTrue(
			$this->object->up($migration)
		);

		$this->assertEquals(
			array($migration => $migration),
			$this->object->getStorage()->get()
		);
	}

	/**
	 * @covers ::up
	 * @group  Migration
	 */
	public function testUpThatFails()
	{
		$migration = 'Fuel\Migration\MigrationBad';

		$this->assertFalse(
			$this->object->up($migration)
		);

		$this->assertEquals(
			[],
			$this->object->getStorage()->get()
		);
	}

}
