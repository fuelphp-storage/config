<?php
/**
 * @package    Fuel\Config
 * @version    2.0
 * @author     Fuel Development Team
 * @license    MIT License
 * @copyright  2010 - 2015 Fuel Development Team
 * @link       http://fuelphp.com
 */

namespace Fuel\Config\Providers;

use Fuel\Config\Container;
use League\Container\ServiceProvider;

/**
 * Fuel ServiceProvider class for this package
 *
 * @package Fuel\Config
 *
 * @since 2.0
 */
class FuelServiceProvider extends ServiceProvider
{
	/**
	 * @var array
	 */
	protected $provides = ['config'];

	/**
	 * {@inheritdoc}
	 */
	public function register()
	{
		$this->container->add('config', function ($environment = null, $finder = null, $defaultFormat = 'php')
		{
			return new Container($environment, $finder, $defaultFormat);
		});
	}
}
