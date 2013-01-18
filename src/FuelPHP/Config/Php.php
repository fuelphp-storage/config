<?php

namespace FuelPHP\Config;

class Php implements Handler
{
	/**
	 * Load a config file
	 *
	 * @param  string $file file path
	 * @return array  config contents
	 */
	public function load($file)
	{
		return include $file;
	}

	/**
	 * Format a config file for saving.
	 *
	 * @param  array  $data config data
	 * @return string data export
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
