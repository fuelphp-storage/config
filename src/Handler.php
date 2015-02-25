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
 * Configuration loading and formatting logic
 */
interface Handler
{
    /**
     * Loads a config file
     *
     * @param string $file
     *
     * @return array
     */
    public function load($file);

    /**
     * Formats a config file for saving
     *
     * @param array $data
     *
     * @return string
     */
    public function format($data);
}
