<?php

namespace Test;

use Nette,
	Tester,
	Tester\Assert,
	Trejjam\Utils\Utils;

$container = require __DIR__ . '/bootstrap.php';


class UtilsTest extends Tester\TestCase
{
	private $container;

	function __construct(Nette\DI\Container $container)
	{
		$this->container = $container;
	}

	function testNumberAt()
	{
		Assert::same(5, Utils::numberAt(12345, 0, 1));
		Assert::same(45, Utils::numberAt(12345, 0, 2));
		Assert::same(234, Utils::numberAt(12345, 1, 3));
		Assert::same(12, Utils::numberAt(12345, 3, 3));
	}

	function testPriceFreeText()
	{
		Assert::same('free', Utils::priceFreeText('free', -1234.40, 'Kč'));
		Assert::same('gratis', Utils::priceFreeText('gratis', -1234.40, 'Kč'));
	}
	function testPriceCreate()
	{
		Assert::same('1.234,-', Utils::priceCreate(1234));
		Assert::same('1.234,43', Utils::priceCreate(1234.43));
		Assert::same('1.234,69', Utils::priceCreate(1234.689));
		Assert::same('1.234,68', Utils::priceCreate(1234.684));
		Assert::same('21.234,-', Utils::priceCreate(21234));
		Assert::same('21.234,43', Utils::priceCreate(21234.43));
		Assert::same('21.234,69', Utils::priceCreate(21234.689));
		Assert::same('21.234,68', Utils::priceCreate(21234.684));
		Assert::same('321.234,-', Utils::priceCreate(321234));
		Assert::same('321.234,43', Utils::priceCreate(321234.43));
		Assert::same('321.234,69', Utils::priceCreate(321234.689));
		Assert::same('321.234,68', Utils::priceCreate(321234.684));
		Assert::same('4.561.234,-', Utils::priceCreate(4561234));
		Assert::same('4.561.234,43', Utils::priceCreate(4561234.43));
		Assert::same('4.561.234,69', Utils::priceCreate(4561234.689));
		Assert::same('4.561.234,68', Utils::priceCreate(4561234.684));
		Assert::same('45.621.234,-', Utils::priceCreate(45621234));
		Assert::same('45.621.234,43', Utils::priceCreate(45621234.43));
		Assert::same('45.621.234,69', Utils::priceCreate(45621234.689));
		Assert::same('45.621.234,68', Utils::priceCreate(45621234.684));
		Assert::same('456.321.234,-', Utils::priceCreate(456321234));
		Assert::same('456.321.234,43', Utils::priceCreate(456321234.43));
		Assert::same('456.321.234,69', Utils::priceCreate(456321234.689));
		Assert::same('456.321.234,68', Utils::priceCreate(456321234.684));

		Assert::same('1.234,40 Kč', Utils::priceCreate(1234.40, 'Kč'));
		Assert::same('1.234,40 $', Utils::priceCreate(1234.40, '$'));
		Assert::same('-1.234,40 Kč', Utils::priceCreate(-1234.40, 'Kč'));

		Assert::same('2.700,-', Utils::priceCreate(2700));
		Assert::same('2.700,-', Utils::priceCreate(2700, NULL, 0));
		Assert::same('2.700,- Kč', Utils::priceCreate(2700, 'Kč', 0));

		Assert::same('2.007,-', Utils::priceCreate(2007));
		Assert::same('2.007,-', Utils::priceCreate(2007, NULL, 0));
		Assert::same('2.007,- Kč', Utils::priceCreate(2007, 'Kč', 0));

		Assert::same('2.007,01', Utils::priceCreate(2007.007700, NULL, 2));
		Assert::same('2.007,008', Utils::priceCreate(2007.007700, NULL, 3));
		Assert::same('2.007,0077', Utils::priceCreate(2007.007700, NULL, 4));
		Assert::same('2.007,00770', Utils::priceCreate(2007.007700, NULL, 5));
		Assert::same('2.007,007700', Utils::priceCreate(2007.007700, NULL, 6));
	}

	function testPriceCreateArray()
	{
		Assert::same([
			'1.234,-',
			'1.234,43',
			'1.234,69',
			'1.234,68',
			'21.234,-',
			'21.234,43',
			'21.234,69',
			'21.234,68',
			'321.234,-',
			'321.234,43',
			'321.234,69',
			'321.234,68',
			'4.561.234,-',
			'4.561.234,43',
			'4.561.234,69',
			'4.561.234,68',
			'45.621.234,-',
			'45.621.234,43',
			'45.621.234,69',
			'45.621.234,68',
			'456.321.234,-',
			'456.321.234,43',
			'456.321.234,69',
			'456.321.234,68',
		], Utils::priceCreate([
			1234,
			1234.43,
			1234.689,
			1234.684,
			21234,
			21234.43,
			21234.689,
			21234.684,
			321234,
			321234.43,
			321234.689,
			321234.684,
			4561234,
			4561234.43,
			4561234.689,
			4561234.684,
			45621234,
			45621234.43,
			45621234.689,
			45621234.684,
			456321234,
			456321234.43,
			456321234.689,
			456321234.684,
		]));

		Assert::same(['1.234,40 Kč'], Utils::priceCreate([1234.40], 'Kč'));
		Assert::same(['1.234,40 $'], Utils::priceCreate([1234.40], '$'));
		Assert::same(['-1.234,40 Kč'], Utils::priceCreate([-1234.40], 'Kč'));
	}

	function testServerInfo()
	{
		Assert::equal(Utils::getServerInfo(), [
			'HTTP_ORIGIN'           => isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '',
			'HTTP_USER_AGENT'       => isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '',
			'REDIRECT_QUERY_STRING' => isset($_SERVER['REDIRECT_QUERY_STRING']) ? $_SERVER['REDIRECT_QUERY_STRING'] : '',
			'QUERY_STRING'          => isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '',
		]);

		Assert::same(Utils::getTextServerInfo(), print_r(Utils::getServerInfo(), TRUE));
	}

	function testUnifyDir()
	{
		Assert::equal('/a/b/', Utils::unifyDir('/a/b'));
		Assert::equal('/a/b/', Utils::unifyDir('/a/b/'));
	}
}

$test = new UtilsTest($container);
$test->run();
