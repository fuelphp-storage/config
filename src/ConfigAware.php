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
 * Accepts a Config Container instance
 */
interface ConfigAware
{
	/**
	 * Returns the Config Container
	 *
	 * @return Container
	 */
	public function getConfig();

	/**
	 * Sets the Config Container
	 *
	 * @param Container $config
	 */
	public function setConfig(Container $config);
}
