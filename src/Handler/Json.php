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
 * JSON configuration loading and formatting logic
 */
class Json implements Handler
{
	/**
	 * {@inheritdoc}
	 */
    public function load($file)
    {
        $contents = file_get_contents($file);

        return json_decode($contents, true);
    }

	/**
	 * {@inheritdoc}
	 */
    public function format($data)
    {
        return json_encode($data);
    }
}
