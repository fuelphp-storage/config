<?php
/**
 * @package    Fuel\Config
 * @version    2.0
 * @author     Fuel Development Team
 * @license    MIT License
 * @copyright  2010 - 2013 Fuel Development Team
 * @link       http://fuelphp.com
 */

namespace Fuel\Config;

use Exception;

class Ini implements Handler
{
    /**
     * Load a config file
     *
     * @param  string $file file path
     * @return array  config contents
     */
    public function load($file)
    {
        $contents = file_get_contents($file);

        return parse_ini_string($contents, true);
    }

    /**
     * Format a config file for saving. [NOT IMPLEMENTED]
     *
     * @param  array     $data config data
     * @throws Exception
     */
    public function format($data)
    {
        throw new Exception('Ini export is not available');
    }
}
