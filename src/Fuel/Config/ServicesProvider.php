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

use Fuel\Dependency\ServiceProvider;

/**
 * ServicesProvider class
 *
 * Defines the services published by this namespace to the DiC
 *
 * @package  Fuel\Config
 *
 * @since  1.0.0
 */
class ServicesProvider extends ServiceProvider
{
	/**
	 * @var  array  list of service names provided by this provider
	 */
	public $provides = array('config');

	/**
	 * Service provider definitions
	 */
	public function provide()
	{
		// \Fuel\Config\Container
		$this->register('config', function ($dic, $environment = null, $finder = null, $defaultFormat = 'php')
		{
			return new Container($environment, $finder, $defaultFormat);
		});
	}
}
