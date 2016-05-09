<?php
/**
 * easyProperty.com
 *
 * @link      www.easyproperty.com
 * @copyright Copyright (c) 2016 easyproperty.com
 * @license   Proprietary
 */
namespace Fuel\Config;

/**
 * Holds configuration data
 */
interface ContainerInterface
{
	/**
	 * Returns the environment
	 *
	 * @return string
	 */
	public function getEnvironment();

	/**
	 * Sets the environment
	 *
	 * @param string $enviroment
	 */
	public function setEnvironment($environment);

	/**
	 * Unloads a config group
	 *
	 * @param string $group
	 */
	public function unload($group);

	/**
	 * Reloads a group
	 *
	 * @param string         $name
	 * @param string|boolean $group
	 *
	 * @return array|null
	 */
	public function reload($name, $group = true);

	/**
	 * Loads a config file
	 *
	 * @param string              $name
	 * @param null|string|boolean $group
	 *
	 * @return array|null
	 */
	public function load($name, $group = null);

	/**
	 * Stores a config file
	 *
	 * @param string      $group
	 * @param string|null $destination
	 *
	 * @throws \RuntimeException
	 */
	public function save($group, $destination = null);

	/**
	 * Adds a path
	 *
	 * @param string $path
	 *
	 * @return $this
	 */
	public function addPath($path);

	/**
	 * Adds paths to look in
	 *
	 * @param array $paths
	 */
	public function addPaths(array $paths);

	/**
	 * Removes a path
	 *
	 * @param string $path
	 */
	public function removePath($path);

	/**
	 * Removes paths
	 *
	 * @param  array $paths
	 */
	public function removePaths(array $paths);

	/**
	 * Delete data from the container
	 *
	 * @param   string $key key to delete
	 *
	 * @return  boolean  delete success boolean
	 * @since   2.0.0
	 */
	public function delete($key);
}
