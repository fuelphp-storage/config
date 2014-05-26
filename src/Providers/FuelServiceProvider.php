<?php
/**
 * @package    Fuel\Config
 * @version    2.0
 * @author     Fuel Development Team
 * @license    MIT License
 * @copyright  2010 - 2013 Fuel Development Team
 * @link       http://fuelphp.com
 */

namespace Fuel\Config\Providers;

use Fuel\Dependency\ServiceProvider;

/**
 * FuelPHP ServiceProvider class for this package
 *
 * @package  Fuel\Config
 *
 * @since  1.0.0
 */
class FuelServiceProvider extends ServiceProvider
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
			return $dic->resolve('Fuel\Config\Container', array($environment, $finder, $defaultFormat));
		});
	}
}
