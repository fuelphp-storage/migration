<?php

namespace Fuel\Migration\Storage;

class StorageTest extends \PHPUnit_Framework_TestCase
{

	/**
	 * @var Storage
	 */
	protected $object;

	protected function setUp()
	{
		$this->object = \Mockery::mock('Fuel\Migration\Storage\Storage[save, getPast]');
		$this->object->shouldReceive('save')->andReturn(true);
		$this->object->shouldReceive('getPast')->andReturn(true);
	}

	/**
	 * @covers Fuel\Migration\Storage\Storage::get
	 * @covers Fuel\Migration\Storage\Storage::add
	 * @group  Migration
	 */
	public function testAdd()
	{
		$string = 'foobar';

		$this->object->add($string);

		$this->assertEquals(
			array($string => $string),
			$this->object->get()
		);
	}

	/**
	 * @covers Fuel\Migration\Storage\Storage::add
	 * @covers Fuel\Migration\Storage\Storage::get
	 * @covers Fuel\Migration\Storage\Storage::remove
	 * @group  Migration
	 */
	public function testRemove()
	{
		$string1 = 'foo';
		$string2 = 'bar';

		$this->object->add($string1)
			->add($string2)
			->remove($string1);

		$this->assertEquals(
			array($string2 => $string2),
			$this->object->get()
		);
	}

	/**
	 * @covers Fuel\Migration\Storage\Storage::get
	 * @covers Fuel\Migration\Storage\Storage::add
	 * @covers Fuel\Migration\Storage\Storage::reset
	 * @group  Migration
	 */
	public function testReset()
	{
		$this->object->add('abc')
			->add('def')
			->reset();

		$this->assertEquals(array(), $this->object->get());
	}

}
