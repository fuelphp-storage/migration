<?php

namespace FuelPHP\Migration;

use org\bovigo\vfs\vfsStream;

require_once __DIR__.'/../../test_migration_classes.php';

class MainTest extends \PHPUnit_Framework_TestCase
{
	
	/**
	 * @var Main
	 */
	protected $object;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp()
	{
		vfsStream::setup('fuelphpMigration');
		$storageFile = vfsStream::url('fuelphpMigration/MainTest.tmp');
		
		$this->object = new Main(array(
			'storage' => array(
				'type' => 'FuelPHP\Migration\Storage\File',
				'location' => $storageFile,
			),
		));
	}
	
	public function testUp()
	{
		$migration = 'FuelPHP\Migration\MigrationNoDeps';
		
		$this->object->up($migration);
		
		$this->assertEquals(
			array($migration => $migration),
			$this->object->getStorage()->get()
		);
	}
	
}
