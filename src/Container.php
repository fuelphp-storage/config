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

use Fuel\FileSystem\Finder;
use Fuel\Common\DataContainer;

/**
 * Holds configuration data
 */
class Container extends DataContainer
{
	/**
	 * @var string
	 */
	protected $environment;

	/**
	 * @var Finder
	 */
	protected $finder;

	/**
	 * @var array
	 */
	protected $handlers;

	/**
	 * @var string
	 */
	protected $configFolder = 'config';

	/**
	 * @var string
	 */
	protected $defaultFormat = 'php';

	/**
	 * @param string $environment
	 * @param Finder $finder
	 * @param string $defaultFormat
	 */
	public function __construct($environment = null, $finder = null, $defaultFormat = 'php')
	{
		if ($environment instanceof Finder)
		{
			$finder = $environment;
			$environment = null;
		}

		if ( ! $finder)
		{
			$finder = new Finder();
		}

		if ($environment)
		{
			$this->setEnvironment($environment);
		}

		$this->defaultFormat = $defaultFormat;
		$this->finder = $finder;
	}

	/**
	 * Returns the default format
	 *
	 * @return string
	 */
	public function getDefaultFormat()
	{
		return $this->defaultFormat;
	}

	/**
	 * Sets the default format
	 *
	 * @param string $format
	 */
	public function setDefaultFormat($format)
	{
		$this->defaultFormat = $format;
	}

	/**
	 * Ensures a default config format
	 *
	 * @param string $file
	 *
	 * @return string
	 */
	public function ensureDefaultFormat($file)
	{
		if ( ! pathinfo($file, PATHINFO_EXTENSION))
		{
			$file .= '.'.$this->defaultFormat;
		}

		return empty($this->configFolder) ? $file : $this->configFolder.DIRECTORY_SEPARATOR.$file;
	}

	/**
	 * Returns the environment
	 *
	 * @return string
	 */
	public function getEnvironment()
	{
		return $this->environment;
	}

	/**
	 * Sets the environment
	 *
	 * @param string $enviroment
	 */
	public function setEnvironment($environment)
	{
		if ($environment)
		{
			$environment = trim($environment, DIRECTORY_SEPARATOR);
		}

		$this->environment = $environment;
	}

	/**
	 * Unloads a config group
	 *
	 * @param string $group
	 */
	public function unload($group)
	{
		$this->delete($group);
	}

	/**
	 * Reloads a group
	 *
	 * @param string         $name
	 * @param string|boolean $group
	 *
	 * @return array|null
	 */
	public function reload($name, $group = true)
	{
		if ($group === true)
		{
			$group = pathinfo($name, PATHINFO_FILENAME);
		}

		$this->delete($group);

		return $this->load($name, $group);
	}

	/**
	 * Loads a config file
	 *
	 * @param string              $name
	 * @param null|string|boolean $group
	 *
	 * @return array|null
	 */
	public function load($name, $group = null)
	{
		if ($group === true)
		{
			$group = pathinfo($name, PATHINFO_FILENAME);
		}

		if ($group and $cached = $this->get($group))
		{
			return $cached;
		}

		$name = $this->ensureDefaultFormat($name);
		$paths = $this->finder->findAllFiles($name);

		if (empty($paths))
		{
			return false;
		}

		$config = array();

		foreach ($paths as $path)
		{
			$extension = pathinfo($path, PATHINFO_EXTENSION);
			$handler = $this->getHandler($extension);
			$config = \Arr::merge($config, $handler->load($path));
		}

		if ($group)
		{
			$this->set($group, $config);
		}
		elseif ($group === null)
		{
			$this->merge($config);
		}

		return $config;
	}

	/**
	 * Stores a config file
	 *
	 * @param string      $group
	 * @param string|null $destination
	 *
	 * @throws \RuntimeException
	 */
	public function save($group, $destination = null)
	{
		if ($destination === null)
		{
			$destination = $group;
		}

		if ( ! $this->has($group))
		{
			throw new \RuntimeException('Unable to save non-existig config group: '.$group);
		}

		$destination = $this->ensureDefaultFormat($destination);
		$format = pathinfo($destination, PATHINFO_EXTENSION);
		$handler = $this->getHandler($format);
		$data = $this->get($group);
		$output = $handler->format($data);
		$path = $this->findDestination($destination);

		if ( ! $path)
		{
			throw new \RuntimeException(sprintf('Could not save group "%" as "%s".', $group, $destination));
		}

		return file_put_contents($path, $output);
	}

	/**
	 * Finds a config file
	 *
	 * @param string $destination
	 *
	 * @return string
	 */
	public function findDestination($destination)
	{
		if (is_file($destination))
		{
			return $destination;
		}

		if ($location = $this->finder->findFileReversed($destination))
		{
			return $location;
		}

		$paths = $this->finder->getPaths();

		if (empty($paths))
		{
			return false;
		}

		$last = end($paths);

		return $last.ltrim($destination, DIRECTORY_SEPARATOR);
	}

	/**
	 * Retrieves the handler for a file type
	 *
	 * @param string $extension
	 *
	 * @return Handler
	 *
	 * @throws \RuntimeException
	 */
	public function getHandler($extension)
	{
		if (isset($this->handlers[$extension]))
		{
			return $this->handlers[$extension];
		}

		$class = 'Fuel\Config\\'.ucfirst($extension);

		if ( ! class_exists($class, true))
		{
			throw new \RuntimeException('Could not find config handler for extension: '.$extension);
		}

		$handler = new $class;
		$this->handlers[$extension] = $handler;

		return $handler;
	}

	/**
	 * Sets a handler for an extension
	 *
	 * @param string  $extension
	 * @param Handler $handler
	 */
	public function setHandler($extension, Handler $loader)
	{
		$this->handlers[$extension] = $loader;
	}

	/**
	 * Sets the config folder
	 *
	 * @param string $folder
	 */
	public function setConfigFolder($folder)
	{
		$this->configFolder = rtrim($folder, DIRECTORY_SEPARATOR);
	}

	/**
	 * Adds a path
	 *
	 * @param string $path
	 *
	 * @return $this
	 */
	public function addPath($path)
	{
		$path = rtrim($path, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
		$this->finder->addPath($path);

		if ($this->environment)
		{
			$this->finder->addPath($path.$this->environment);
		}

		return $this;
	}

	/**
	 * Adds paths to look in
	 *
	 * @param array $paths
	 */
	public function addPaths(array $paths)
	{
		array_map(array($this, 'addPath'), $paths);
	}

	/**
	 * Removes a path
	 *
	 * @param string $path
	 */
	public function removePath($path)
	{
		$path = rtrim($path, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
		$this->finder->removePath($path);

		if ($this->environment)
		{
			$this->finder->removePath($path.$this->environment);
		}
	}

	/**
	 * Removes paths
	 *
	 * @param  array $path
	 */
	public function removePaths(array $paths)
	{
		array_map(array($this, 'removePath'), $paths);
	}
}
