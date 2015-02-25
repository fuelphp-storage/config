<?php
/**
 * @package    Fuel\Config
 * @version    2.0
 * @author     Fuel Development Team
 * @license    MIT License
 * @copyright  2010 - 2015 Fuel Development Team
 * @link       http://fuelphp.com
 */

namespace Fuel\Config\Handler;

use Fuel\Config\Handler;

/**
 * PHP configuration loading and formatting logic
 */
class Php implements Handler
{
	/**
	 * {@inheritdoc}
	 */
	public function load($file)
	{
		return include $file;
	}

	/**
	 * {@inheritdoc}
	 */
	public function format($data)
	{
		$data = var_export($data, true);

		$formatted = str_replace(
			array('  ', 'array ('),
			array("\t", 'array('),
			$data
		);

		$output = <<<CONF
<?php

return {$formatted};
CONF;

		return $output;
	}
}
