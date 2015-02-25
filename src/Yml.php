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

use Symfony\Component\Yaml\Yaml;

/**
 * YAML configuration loading and formatting logic
 *
 * @package Fuel\Config
 *
 * @since 2.0
 */
class Yml implements Handler
{
	/**
	 * {@inheritdoc}
	 */
    public function load($file)
    {
        $contents = file_get_contents($file);

        return Yaml::parse($contents);
    }

	/**
	 * {@inheritdoc}
	 */
    public function format($data)
    {
        return Yaml::dump($data);
    }
}
