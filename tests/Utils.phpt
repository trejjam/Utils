<?php

namespace Test;

use Nette,
	Tester,
	Tester\Assert;

$container = require __DIR__ . '/bootstrap.php';


class UtilsTest extends Tester\TestCase
{
	private $container;

	function __construct(Nette\DI\Container $container) {
		$this->container = $container;
	}

	function testPriceCreate() {
		Assert::same(\Trejjam\Utils\Utils::priceCreate(1234.4, "Kč"), "1.235,- Kč");
		Assert::same(\Trejjam\Utils\Utils::priceCreate(1234.4, "$"), "1.235,- $");
		Assert::same(\Trejjam\Utils\Utils::priceCreate(-1234.4, "Kč"), "free");
		Assert::same(\Trejjam\Utils\Utils::priceCreate(-1234.4, "Kč", "gratis"), "gratis");
		Assert::same(\Trejjam\Utils\Utils::priceCreate(-1234.4, "Kč", FALSE), "-1.235,- Kč");
	}

	function testIsJson() {
		Assert::true(\Trejjam\Utils\Utils::isJson(\json_encode(["foo"])));
		Assert::false(\Trejjam\Utils\Utils::isJson(\json_encode(["foo"]) . "foo"));
		Assert::false(\Trejjam\Utils\Utils::isJson("foo"));
		Assert::error(function () {
			\Trejjam\Utils\Utils::isJson(["foo"]);
		}, E_WARNING);
	}

	function testServerInfo() {
		Assert::equal(\Trejjam\Utils\Utils::getServerInfo(), [
			"HTTP_ORIGIN"           => isset($_SERVER["HTTP_ORIGIN"]) ? $_SERVER["HTTP_ORIGIN"] : "",
			"HTTP_USER_AGENT"       => isset($_SERVER["HTTP_USER_AGENT"]) ? $_SERVER["HTTP_USER_AGENT"] : "",
			"REDIRECT_QUERY_STRING" => isset($_SERVER["REDIRECT_QUERY_STRING"]) ? $_SERVER["REDIRECT_QUERY_STRING"] : "",
			"QUERY_STRING"          => isset($_SERVER["QUERY_STRING"]) ? $_SERVER["QUERY_STRING"] : "",
		]);

		Assert::same(\Trejjam\Utils\Utils::getTextServerInfo(), print_r(\Trejjam\Utils\Utils::getServerInfo(), true));
	}
}

$test = new UtilsTest($container);
$test->run();