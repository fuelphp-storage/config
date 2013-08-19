<?php

use Fuel\Config\Container;

class DataContainerTests extends PHPUnit_Framework_TestCase
{
	protected $base;

	public function setUp()
	{
		$this->base = realpath(__DIR__.'/../resources');
	}
	public function testLoad()
	{
		$config = new Container(new Fuel\FileSystem\Finder);
		$config->setConfigFolder('');
		$config->addPath($this->base);
		$expected = array('some' => 'setting');
		$this->assertEquals($expected, $config->load('conf', true));
		$this->assertEquals($expected, $config->load('conf', true));
		$this->assertEquals($expected, $config->reload('conf', true));
		$this->assertEquals($expected, $config->get('conf'));
		$config->unload('conf');
		$this->assertNull($config->get('conf'));
	}

	public function testLoadGroup()
	{
		$config = new Container;
		$config->setConfigFolder('');
		$config->addPath(__DIR__.'/../resources');
		$expected = array('some' => 'setting');
		$this->assertEquals($expected, $config->load('conf', true));
		$this->assertEquals($expected, $config->get('conf'));
	}

	public function testLoadEnv()
	{
		$config = new Container('develop');
		$config->setConfigFolder('');
		$config->addPath(__DIR__.'/../resources');
		$config->load('conf', true);
		$result = $config->get('conf.some');
		$this->assertEquals('develop', $result);
	}

	public function testSave()
	{
		$c = new Container();
		$c->setConfigFolder('');
		$c->addPath(__DIR__.'/../resources');
		$c->load('conf', true);
		$c->save('conf', 'new');
		$this->assertTrue(file_exists(__DIR__.'/../resources/new.php'));
		$this->assertEquals(
			file_get_contents(__DIR__.'/../resources/new.php'),
			file_get_contents(__DIR__.'/../resources/conf.php')
		);

		unlink(__DIR__.'/../resources/new.php');
	}

	public function testIni()
	{
		$c = new Container();
		$c->addPath(__DIR__.'/../resources');
		$c->load('ini.ini', true);
		$expected = array(
			'key' => array(
				'one' => '1',
				'two' => '3',
				'four' => 'string',
			),
			'yeah' => array(
				'right' => array(1, 2, 3),
			),
		);
		$this->assertEquals($expected, $c->get('ini'));
	}

	/**
	 * @expectedException Exception
	 */
	public function testIniSave()
	{
		$c = new Container;
		$c->set('some.thing', true);
		$c->save('some', '/some.ini');
	}

	public function testJson()
	{
		$data = array(
			'some' => 'value',
			'arr' => array(1, 2, 3),
		);

		$c = new Container;
		$c->set('j', $data);
		$c->addPath(__DIR__.'/../resources');
		$c->save('j', 'data.json');
		$this->assertTrue(file_exists(__DIR__.'/../resources/config/data.json'));
		$c->load('data.json', 'new');
		$this->assertEquals($data, $c->get('new'));
		unlink(__DIR__.'/../resources/config/data.json');
	}

	public function testYaml()
	{
		$data = array(
			'some' => 'value',
			'arr' => array(1, 2, 3),
		);

		$c = new Container;
		$c->setConfigFolder('');
		$c->set('j', $data);
		$c->addPath(__DIR__.'/../resources');
		$c->save('j', 'data.yml');
		$this->assertTrue(file_exists(__DIR__.'/../resources/data.yml'));
		$c->load('data.yml', 'new');
		$this->assertEquals($data, $c->get('new'));
		unlink(__DIR__.'/../resources/data.yml');
	}

	public function testDefaultFormat()
	{
		$c = new Container;
		$c->setConfigFolder('');
		$c->setDefaultFormat('json');
		$this->assertFalse($c->load('conf', true));
		$this->assertEquals('thing.json', $c->ensureDefaultFormat('thing'));
		$this->assertEquals('json', $c->getDefaultFormat());
	}

	public function testSetGetEnv()
	{
		$c = new Container('env');
		$this->assertEquals('env', $c->getEnvironment());
		$c->setEnvironment('production');
		$this->assertEquals('production', $c->getEnvironment());
	}

	/**
	 * @expectedException Exception
	 */
	public function testSaveNoGroup()
	{
		$c = new Container;
		$c->save('nope');
	}

	/**
	 * @expectedException Exception
	 */
	public function testNoHandler()
	{
		$c = new Container;
		$c->getHandler('nope');
	}

	public function testSetHandler()
	{
		$c = new Container;
		$c->setHandler('woo', new Fuel\Config\Php);
		$this->assertInstanceOf('Fuel\Config\Php', $c->getHandler('woo'));
	}

	public function testPaths()
	{
		$c = new Container('env');
		$this->assertFalse($c->findDestination('this'));
		$found = realpath(__DIR__.'/../resources/conf.php');
		$this->assertEquals($found, $c->findDestination($found));
		$c->addPaths(array(__DIR__.'/../resources'));
		$this->assertEquals($found, $c->findDestination('conf'));
		$c->removePaths(array(__DIR__.'/../resources'));
		$this->assertFalse($c->findDestination('ini.ini'));
	}

	/**
	 * @expectedException Exception
	 */
	public function testInvalidSavePath()
	{
		$c = new Container;
		$c->set('data.data', true);
		$c->save('data');
	}
}
