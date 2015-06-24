Utils
=====

Library contains 
<ul>
<li>basic Utils</li>
<li>labels</li>
</ul>

Installation
------------

The best way to install Trejjam/Utils is using  [Composer](http://getcomposer.org/):

```sh
$ composer require trejjam/utils
```

Configuration
-------------

.neon
```yml
extensions:
	utils: Trejjam\Utils\DI\UtilsExtension

utils:
	labels:
		table     : utils__labels
		id        : id
		namespace : 
			name    : namespace
			default : default
		name      : name
		value     : value	
	flashes:
		enable : FALSE
	browser:
		enable : FALSE
	labels:
		enable        : FALSE
		componentName : labels
		table         : utils__labels
		keys          :
			id        : id
			namespace : 
				name    : namespace
				default : default
			name      : name
			value     : value
	contents:
		enable                : FALSE,
		configurationDirectory: '%appDir%/config/contents'
		logDirectory          : NULL
		subTypes              : []
```
Config
------

The best way for configuration is using [Kdyby/Console](https://github.com/kdyby/console)

```sh
$ composer require kdyby/console
```

Read how to install [Kdyby/Console](https://github.com/Kdyby/Console/blob/master/docs/en/index.md)

```sh
php index.php
```

After successful installation display:

```
Available commands:
Auth
	Utils:install    Install default tables
	Utils:labels     Edit labels
	help             Displays help for a command
	list             Lists commands
```

Config database
---------------

Create default tables:
```sh
php index.php Utils:install
```

Config labels
-------------

Add/edit label:
```sh
php index.php Utils:labels labelName labelValue [-s namespace]
```

Remove label:
```sh
php index.php Utils:labels -d labelName [-s namespace]
```


Usage
-----

Presenter/Model:

```php
	use \Trejjam\Utils\Layout\BaseLayoutTrait;

	/**
	* @var \Trejjam\Utils\Labels @inject
	*/
	public $labels;
	
	function renderDefault() {
		//-------------utils-------------
		dump($this->labels->page); //print value where namespace==default and name==page
		dump($this->labels->backend->page); //print value where namespace==backend and name==page
		
		$this->labels->backend->page="new value"; //change value
		$this->labels->page="new value"; //change value
		
		Assert::same('1.234,-', Utils::priceCreate(1234));
		Assert::same('1.234,43', Utils::priceCreate(1234.43));
		Assert::same('1.234,68', Utils::priceCreate(1234.689));
		Assert::same('1.234,68', Utils::priceCreate(1234.684));
		Assert::same('21.234,-', Utils::priceCreate(21234));
		Assert::same('21.234,43', Utils::priceCreate(21234.43));
		Assert::same('21.234,68', Utils::priceCreate(21234.689));
		Assert::same('21.234,68', Utils::priceCreate(21234.684));
		Assert::same('321.234,-', Utils::priceCreate(321234));
		Assert::same('321.234,43', Utils::priceCreate(321234.43));
		Assert::same('321.234,68', Utils::priceCreate(321234.689));
		Assert::same('321.234,68', Utils::priceCreate(321234.684));
		Assert::same('4.561.234,-', Utils::priceCreate(4561234));
		Assert::same('4.561.234,43', Utils::priceCreate(4561234.43));
		Assert::same('4.561.234,68', Utils::priceCreate(4561234.689));
		Assert::same('4.561.234,68', Utils::priceCreate(4561234.684));
		Assert::same('45.621.234,-', Utils::priceCreate(45621234));
		Assert::same('45.621.234,43', Utils::priceCreate(45621234.43));
		Assert::same('45.621.234,68', Utils::priceCreate(45621234.689));
		Assert::same('45.621.234,68', Utils::priceCreate(45621234.684));
		Assert::same('456.321.234,-', Utils::priceCreate(456321234));
		Assert::same('456.321.234,43', Utils::priceCreate(456321234.43));
		Assert::same('456.321.234,68', Utils::priceCreate(456321234.689));
		Assert::same('456.321.234,68', Utils::priceCreate(456321234.684));

		Assert::same('1.234,40 Kč', Utils::priceCreate(1234.40, 'Kč'));
		Assert::same('1.234,40 $', Utils::priceCreate(1234.40, '$'));
		Assert::same('-1.234,40 Kč', Utils::priceCreate(-1234.40, 'Kč'));
		
		
		//-------------labels-------------
		
		//save label to database
		$this->labels->setData("key", "value");
		//or
		$this->labels->key = "value";
		
		//read label (use lazy loading)
		dump($this->labels->getData("key"));
		//or
		dump((string)$this->labels->key);
		
		//save label to namespace (to database)
		$this->labels->setData("key", "value", "namespace");
		//or
		$this->labels->namespace->key = "value";
		
		//read label in namespace
		dump($this->labels->getData("key", "namespace"));
		//or
		dump((string)$this->labels->namespace->key);
		
		//delete label
		$this->labels->delete("key", "namespace");
		//or
		$this->labels->namespace->key = NULL;
		
		$this->labels->key = "my value";
		dump((string)$this->labels->key); //print "my value"
		dump((string)$this->labels->backend->key); //print "my value"
		
		$this->labels->backend->key = "my back value";
		dump((string)$this->labels->key); //print "my value"
		dump((string)$this->labels->backend->key); //print "my back value"
		
		$this->labels->key = NULL;
		dump((string)$this->labels->key); //print ""
		dump((string)$this->labels->backend->key); //print "my back value"
	}
```

Latte:

```smarty
	{control label page}
	{control label page, backend}
```