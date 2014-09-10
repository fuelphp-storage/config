<?php

namespace Fuel\Config;

use Codeception\TestCase\Test;

class DataContainerTest extends Test
{
	protected $base;

	public function setUp()
	{
		parent::setUp();
		$this->base = realpath(__DIR__.'/../resources');
	}

	public function testLoad()
	{
		$config = new Container(new \Fuel\FileSystem\Finder);
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
		$config->addPath($this->base);
		$expected = array('some' => 'setting');
		$this->assertEquals($expected, $config->load('conf', true));
		$this->assertEquals($expected, $config->get('conf'));
	}

	public function testLoadEnv()
	{
		$config = new Container('develop');
		$config->setConfigFolder('');
		$config->addPath($this->base);
		$config->load('conf', true);
		$result = $config->get('conf.some');
		$this->assertEquals('develop', $result);
	}

	public function testSave()
	{
		$c = new Container();
		$c->setConfigFolder('');
		$c->addPath($this->base);
		$c->load('conf', true);
		$c->save('conf', 'new');

		$this->assertFileExists($this->base.'/new.php');
		$this->assertEquals(
			include $this->base.'/conf.php',
			include $this->base.'/new.php'
		);

		unlink($this->base.'/new.php');
	}

	public function testIni()
	{
		$c = new Container();
		$c->addPath($this->base);
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
	 * @expectedException \Exception
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
		$c->addPath($this->base);
		$c->save('j', 'data.json');
		$this->assertTrue(file_exists($this->base.'/config/data.json'));
		$c->load('data.json', 'new');
		$this->assertEquals($data, $c->get('new'));
		unlink($this->base.'/config/data.json');
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
		$c->addPath($this->base);
		$c->save('j', 'data.yml');
		$this->assertTrue(file_exists($this->base.'/data.yml'));
		$c->load('data.yml', 'new');
		$this->assertEquals($data, $c->get('new'));
		unlink($this->base.'/data.yml');
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
	 * @expectedException \Exception
	 */
	public function testSaveNoGroup()
	{
		$c = new Container;
		$c->save('nope');
	}

	/**
	 * @expectedException \Exception
	 */
	public function testNoHandler()
	{
		$c = new Container;
		$c->getHandler('nope');
	}

	public function testSetHandler()
	{
		$c = new Container;
		$c->setHandler('woo', new \Fuel\Config\Php);
		$this->assertInstanceOf('Fuel\Config\Php', $c->getHandler('woo'));
	}

	public function testPaths()
	{
		$c = new Container('env');
		$this->assertFalse($c->findDestination('this'));
		$found = realpath($this->base.'/conf.php');
		$this->assertEquals($found, $c->findDestination($found));
		$c->addPaths(array($this->base));
		$this->assertEquals($found, $c->findDestination('conf'));
		$c->removePaths(array($this->base));
		$this->assertFalse($c->findDestination('ini.ini'));
	}

	/**
	 * @expectedException \Exception
	 */
	public function testInvalidSavePath()
	{
		$c = new Container;
		$c->set('data.data', true);
		$c->save('data');
	}
}
