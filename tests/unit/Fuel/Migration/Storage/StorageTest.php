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

/**
 * Tests for Storage
 *
 * @package Fuel\Migration\Storage
 * @author  Fuel Development Team
 *
 * @coversDefaultClass \Fuel\Migration\Storage\Storage
 */
class StorageTest extends Test
{

	/**
	 * @var Storage
	 */
	protected $object;

	protected function _before()
	{
		$this->object = \Mockery::mock('Fuel\Migration\Storage\Storage[save, getPast]');
		$this->object->shouldReceive('save')->andReturn(true);
		$this->object->shouldReceive('getPast')->andReturn(true);
	}

	/**
	 * @covers ::get
	 * @covers ::add
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
	 * @covers ::add
	 * @covers ::get
	 * @covers ::remove
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
	 * @covers ::get
	 * @covers ::add
	 * @covers ::reset
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
