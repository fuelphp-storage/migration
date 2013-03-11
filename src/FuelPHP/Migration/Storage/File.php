<?php

/**
 * Part of the FuelPHP framework.
 *
 * @package   FuelPHP\Migration\Storage
 * @version   2.0
 * @license   MIT License
 * @copyright 2010 - 2013 Fuel Development Team
 */

namespace FuelPHP\Migration\Storage;

/**
 * Defines a common interface for storing run migrations.
 *
 * @package FuelPHP\Migration\Storage
 * @since   2.0.0
 * @author  Fuel Development Team
 */
class File extends Storage
{

	protected $fileLocation = '';

	public function __construct($config)
	{
		$this->fileLocation = $config['location'];
	}

	public function getPast()
	{
		$content = file_get_contents($this->fileLocation);
		
		return explode("\n", $content);
	}

	public function save()
	{
		$result = file_put_contents(
			$this->fileLocation, implode("\n", $this->get()), FILE_APPEND
		);

		if ( $result === false )
		{
			throw new Exception('Unable to save the list to ' . $this->fileLocation . '. This could be due to a permissions error.');
		}

		return $this;
	}

}
