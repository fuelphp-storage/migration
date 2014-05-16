<?php
/**
 * Part of the FuelPHP framework.
 *
 * @package   FuelPHP\Migration
 * @version   2.0
 * @license   MIT License
 * @copyright 2010 - 2014 Fuel Development Team
 */

namespace Fuel\Migration\Storage;

use Codeception\TestCase\Test;
use org\bovigo\vfs\vfsStream;

/**
 * Tests for File
 *
 * @package Fuel\Migration\Storage
 * @author  Fuel Development Team
 *
 * @coversDefaultClass \Fuel\Migration\Storage\File
 */
class FileTest extends Test
{

	/**
	 * @var File
	 */
	protected $object;

	/**
	 * @var string
	 */
	protected $fileLocation = '';

	protected function _before()
	{
		vfsStream::setup('fuelphpMigration');
		$this->fileLocation = vfsStream::url('fuelphpMigration/FileTest.tmp');

		$this->object = new File(array('location' => $this->fileLocation));
	}

	/**
	 * @covers ::getPast
	 * @group  Migration
	 */
	public function testGetPast()
	{
		$this->object->add('one')
			->add('two')
			->add('three')
			->save();

		$this->assertEquals(
			array('one', 'two', 'three'), $this->object->getPast()
		);
	}

	/**
	 * @covers ::save
	 * @group  Migration
	 */
	public function testSave()
	{
		$this->object->add('one')
			->add('two')
			->add('three')
			->save();

		$actual = file_get_contents($this->fileLocation);

		$this->assertEquals("one\ntwo\nthree", $actual);
	}

}
