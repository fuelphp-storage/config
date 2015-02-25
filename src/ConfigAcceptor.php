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
trait ConfigAcceptor
{
	/**
	 * @var Container
	 */
	protected $config;

	/**
	 * {@inheritdoc}
	 */
	public function getConfig()
	{
		return $this->config;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setConfig(Container $config)
	{
		$this->config = $config;
	}
}
