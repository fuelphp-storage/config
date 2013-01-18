<?php

namespace FuelPHP\Config;

interface Handler
{
    /**
     * Load a config file
     *
     * @param  string $file file path
     * @return array  config contents
     */
    public function load($file);

    /**
     * Format a config file for saving.
     *
     * @param  array  $data config data
     * @return string data export
     */
    public function format($data);
}
