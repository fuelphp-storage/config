<?php

namespace FuelPHP\Config;

class Json implements Handler
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

        return json_decode($contents, true);
    }

    /**
     * Format a config file for saving.
     *
     * @param  array  $data config data
     * @return string data export
     */
    public function format($data)
    {
        return json_encode($data);
    }
}
