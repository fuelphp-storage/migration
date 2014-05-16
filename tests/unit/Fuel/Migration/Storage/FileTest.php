<?php

namespace Fuel\Migration\Storage;

use org\bovigo\vfs\vfsStream;

class FileTest extends \PHPUnit_Framework_TestCase
{

	/**
	 * @var File
	 */
	protected $object;
	protected $fileLocation = '';

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp()
	{
		vfsStream::setup('fuelphpMigration');
		$this->fileLocation = vfsStream::url('fuelphpMigration/FileTest.tmp');

		$this->object = new File(array('location' => $this->fileLocation));
	}

	/**
	 * @covers Fuel\Migration\Storage\File::getPast
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
	 * @covers Fuel\Migration\Storage\File::save
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
