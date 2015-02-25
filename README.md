# Fuel Config

[![Build Status](https://img.shields.io/travis/fuelphp/config.svg?style=flat-square)](https://travis-ci.org/fuelphp/config)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/fuelphp/config.svg?style=flat-square)](https://scrutinizer-ci.com/g/fuelphp/config)
[![Quality Score](https://img.shields.io/scrutinizer/g/fuelphp/config.svg?style=flat-square)](https://scrutinizer-ci.com/g/fuelphp/config)
[![HHVM Status](https://img.shields.io/hhvm/fuelphp/config.svg?style=flat-square)](http://hhvm.h4cc.de/package/fuelphp/config)


This library handles config files. It's responsible for loading, saving and accessing config settings.

There are multiple formats in which config files can be handled:

- php
- json
- yaml
- ini

The only odd one is `ini`. It's the only filetype that can't be automatically formatted for saving. Symfony\Yaml is needed in order to parse and format `.yml` files.


## Loading

Get a new container

```
use Fuel\Config\Container;

$config = new Container;
```

We'll need to add a path to load the files from:

```
$config->addPath(__DIR__.'app/config');
```

Now we're able to load config files.

```
$config->load('name');
// Load app/config/name.php into the name group


$other = $config->load('other', false);
// load it, but don't store it

$config->load('data.json');
// Load json data
```


## Default format

It's also possible to set a default config format. By default this is `php`.

```
$config->setDefaultFormat('json');

$data = $config->load('data');
// this will load data.json
```


## Environment settings

An environment will be used to load a secondary config file and will overwrite the default settings.

```
$config->setEnvironment('develop');
```


## Saving

It's possible to write all the types (except for ini) to disk.

```
$container->save('data');

// or use an alternate location
$container->save('data', 'other/file');
```

The container is aware of overwrites so it'll always save the config file in the place last loaded, therefor overwriting all that came before.


## Accessing data

The config Container extends the FuelPHP\Common\DataContainer class. Therefor it's possible to retrieve the data in two ways: through ->get and the ArrayAccess way.

```
$setting = $config->get('setting');

// is the same as

$setting = $config['setting'];
```

The first way does allow you to supply a default
