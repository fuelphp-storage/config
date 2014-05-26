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

use Fuel\FileSystem\Finder;
use Fuel\Common\DataContainer;
use Exception;

class Container extends DataContainer
{
	/**
	 * @var  string  $environment  environment name
	 */
	protected $environment;

	/**
	 * @var  Fuel\FileSystem\Finder  $finder  config finder
	 */
	protected $finder;

	/**
	 * @var  array  $handlers  array of config file handlers
	 */
	protected $handlers;

	/**
	 * @var  string  config folder name prefix
	 */
	protected $configFolder = 'config';

	/**
	 * @var  string  $defaultFormat  default config format
	 */
	protected $defaultFormat = 'php';

	/**
	 * Constructor
	 *
	 * @param string $environment environment dir
	 * @param Finder $finder      finder instance
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
	 * Set the default format
	 *
	 * @param   string  $format  default format
	 * @return  $this
	 */
	public function setDefaultFormat($format)
	{
		$this->defaultFormat = $format;

		return $this;
	}

	/**
	 * Get the default format
	 *
	 * @return  string  the default format
	 */
	public function getDefaultFormat()
	{
		return $this->defaultFormat;
	}

	/**
	 * Ensure a default config format.
	 *
	 * @param  string $file config file name
	 * @return string file name with ensured extension
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
	 * Set the  environment.
	 *
	 * @param   string  $enviroment  environment
	 * @return  $this
	 */
	public function setEnvironment($environment)
	{
		if ($environment)
		{
			$environment = trim($environment, DIRECTORY_SEPARATOR);
		}

		$this->environment = $environment;

		return $this;
	}

	/**
	 * Get the environment
	 *
	 * @return  string  environment
	 */
	public function getEnvironment()
	{
		return $this->environment;
	}

	/**
	 * Unload a config group
	 *
	 * @param   string  $group  group name
	 * @return  $this
	 */
	public function unload($group)
	{
		$this->delete($group);

		return $this;
	}

	/**
	 * Reload a group.
	 *
	 * @param   string       $name  group name
	 * @param   string|true  $group  true for same as $name or group name
	 * @return  array|null   config array or null when not found
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
	 * Load a config file
	 *
	 * @param   string       $name  group name
	 * @param   null|string|true  $group  true for same as $name or group name, null for no group
	 * @return  array|null   config array or null when not found
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
	 * Store a config file
	 *
	 * @param   string     $group         group name
	 * @param   string     $desctination  destination, null for same as $group
	 * @throws  Exception
	 */
	public function save($group, $destination = null)
	{
		if ($destination === null)
		{
			$destination = $group;
		}

		if ( ! $this->has($group))
		{
			throw new Exception('Unable to save unexistig config group: '.$group);
		}

		$destination = $this->ensureDefaultFormat($destination);
		$format = pathinfo($destination, PATHINFO_EXTENSION);
		$handler = $this->getHandler($format);
		$data = $this->get($group);
		$output = $handler->format($data);
		$path = $this->findDestination($destination);

		if ( ! $path)
		{
			throw new Exception('Could not save group "'.$group.'" as "'.$destination.'".');
		}

		return file_put_contents($path, $output);
	}

	/**
	 * Find a config file.
	 *
	 * @param   string  $destination  destination path
	 * @return  string  resolved destination
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
	 * Retrieve the handler for a file type
	 *
	 * @param   string   $extension  extension
	 * @return  Handler  file handler
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
			throw new Exception('Could not find config handler for extension: '.$extension);
		}

		$handler = new $class;
		$this->handlers[$extension] = $handler;

		return $handler;
	}

	/**
	 * Handler injection method.
	 *
	 * @param   string  $extension  entension
	 * @param   Loader  $loader     config loader
	 * @return  $this
	 */
	public function setHandler($extension, Handler $loader)
	{
		$this->handlers[$extension] = $loader;

		return $this;
	}

	/**
	 * Set the config folder
	 *
	 * @param   string  $folder  folder path
	 * @return  $this
	 */
	public function setConfigFolder($folder)
	{
		$this->configFolder = rtrim($folder, DIRECTORY_SEPARATOR);

		return $this;
	}

	/**
	 * Add a path.
	 *
	 * @param   array  $path  paths
	 * @return  $this
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
	 * Adds paths to look in.
	 *
	 * @param array $paths paths
	 * @return  $this
	 */
	public function addPaths(array $paths)
	{
		array_map(array($this, 'addPath'), $paths);

		return $this;
	}

	/**
	 * Remove paths.
	 *
	 * @param   array  $path  path
	 * @return  $this
	 */
	public function removePaths(array $paths)
	{
		array_map(array($this, 'removePath'), $paths);

		return $this;
	}

	/**
	 * Remove a path.
	 *
	 * @param   array  $paths  paths
	 * @return  $this
	 */
	public function removePath($path)
	{
		$path = rtrim($path, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
		$this->finder->removePath($path);

		if ($this->environment)
		{
			$this->finder->removePath($path.$this->environment);
		}

		return $this;
	}
}
