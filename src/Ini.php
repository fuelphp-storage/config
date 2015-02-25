<?php
/**
 * @package    Fuel\Config
 * @version    2.0
 * @author     Fuel Development Team
 * @license    MIT License
 * @copyright  2010 - 2015 Fuel Development Team
 * @link       http://fuelphp.com
 */

namespace Fuel\Config;

/**
 * INI configuration loading and formatting (not implemented) logic
 */
class Ini implements Handler
{
	/**
	 * {@inheritdoc}
	 */
    public function load($file)
    {
        $contents = file_get_contents($file);

        return parse_ini_string($contents, true);
    }

	/**
	 * {@inheritdoc}
	 */
    public function format($data)
    {
        throw new \LogicException('Ini export is not available');
    }
}
