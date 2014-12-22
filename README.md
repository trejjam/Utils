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
	utils: Trejjam\DI\UtilsExtension

utils:
	labels:
		table     : utils__labels
		id        : id
		namespace : 
			name    : namespace
			default : default
		name      : name
		value     : value	
	pageInfo : 
		table        : page_info
		id           : id
		parentId     : parent_id
		page         : page
		# parentId=>NULL: [[module:]presenter:]action
		# parentId=>int:  value for parent subAttribute
		subAttribute : sub_attribute
		title        : title
		description  : description
		keywords     : keywords
		img          : img
		rootPage     : 1
		cache:
			"use"     : TRUE
			"name"    : "page_info"
			"timeout" : "60 minutes"
	layout: 
		fileVersion   : 1
		reformatFlash : TRUE    
	debugger: false #not implemented yet
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
		dump($this->labels->page); //print value where namespace==default and name==page
		dump($this->labels->backend->page); //print value where namespace==backend and name==page
		
		$this->labels->backend->page="new value"; //change value
		$this->labels->page="new value"; //change value
		
		dump(\Trejjam\Utils\Utils::priceCreate(1234.4, "Kč")); //print 1.235 Kč
		dump(\Trejjam\Utils\Utils::priceCreate(1234.4, "$")); //print 1.235 $
		dump(\Trejjam\Utils\Utils::priceCreate(-1234.4, "Kč")); //print free
		dump(\Trejjam\Utils\Utils::priceCreate(-1234.4, "Kč", "gratis")); //print gratis
		dump(\Trejjam\Utils\Utils::priceCreate(-1234.4, "Kč", FALSE)); //print -1.235
		
		dump(\Trejjam\Utils\Utils::isJson("foo")); //print FALSE
		dump(\Trejjam\Utils\Utils::isJson(json_encode(["foo"]))); //print TRUE
		
		dump(\Trejjam\Utils\Utils::getServerInfo());
		
		/*		
			"HTTP_ORIGIN"          => ...
			"HTTP_USER_AGENT"      => ...
			"REDIRECT_QUERY_STRING"=> ...
			"QUERY_STRING"         => ...
		*/
		
		dump(\Trejjam\Utils\Utils::getTextServerInfo());
				
		/*		
			Array ( [HTTP_ORIGIN] => ... [HTTP_USER_AGENT] => ... [REDIRECT_QUERY_STRING] => ... [QUERY_STRING] => ... ) 
		*/
		
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
<!DOCTYPE html>
<html n:class="$browser, ($IEversion!=-1)?'ie-'.$IEversion" lang="cs">
	<head>
		<meta name="robots" content="index, follow" />
		<meta charset="UTF-8">
		<link href="{$basePath}/img/favicon.ico" type="image/x-icon" rel="shortcut icon"/>
		<link href="{$basePath}/img/favicon.ico" type="image/x-icon" rel="icon"/>

		<meta name="description" content="{$pageInfo->description}" />
		<meta name="keywords" content="{$pageInfo->keywords}" />

		<meta property="og:url" content="http://{!$server}{$uri}"/>
		<meta property="og:image" content="http://{!$server}{$basePath}/img2/{$pageInfo->fbImg}"/>
		<meta property="og:title" content="{$pageInfo->title}"/>
		<meta property="og:site_name" content="IMA config"/>
		<meta property="og:description" content="{$pageInfo->description}"/>

		<meta name="viewport" content="width=device-width, initial-scale=0.9">

		<title>{$pageInfo->title}</title>
	</head>
	<body>
		{include body}
		<div id="fb-root"></div>
		<script type="text/javascript" src="{$basePath}/js/jquery-1.11.1.min.js?{$fv}"></script>
		<script type="text/javascript" src="{$basePath}/js/netteForms.js?{$fv}"></script>
		<script type="text/javascript" src="{$basePath}/js/nette.ajax.js?{$fv}"></script>
	</body>
</html>

{define body}
	{control label page}
	{control label page, backend}
	
	User is {$userLogged?"":"not"} logged.
{/define}
```