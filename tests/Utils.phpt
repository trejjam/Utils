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

	function testToNumber()
	{
		Assert::equal('a', Utils::numberToLetter(0));
		Assert::equal('b', Utils::numberToLetter(1));
		Assert::equal('n', Utils::numberToLetter(13));
		Assert::equal('y', Utils::numberToLetter(24));
		Assert::equal('z', Utils::numberToLetter(25));
		Assert::equal('aa', Utils::numberToLetter(26));
		$main = 'a';
		for ($i = 1; $i <= 26; $i++ && $main++) {
			Assert::equal($main . 'b', Utils::numberToLetter(($i * 26) + 1));
		}
		Assert::equal('ab', Utils::numberToLetter(27));
		Assert::equal('ac', Utils::numberToLetter(28));
		Assert::equal('af', Utils::numberToLetter(31));
		Assert::equal('ay', Utils::numberToLetter(50));
		Assert::equal('az', Utils::numberToLetter(51));
		Assert::equal('ba', Utils::numberToLetter(52));
		Assert::equal('bb', Utils::numberToLetter(53));
		Assert::equal('bc', Utils::numberToLetter(54));
		Assert::equal('yz', Utils::numberToLetter(675));
		Assert::equal('za', Utils::numberToLetter(676));
		Assert::equal('zb', Utils::numberToLetter(677));
		Assert::equal('zc', Utils::numberToLetter(678));
		Assert::equal('zy', Utils::numberToLetter(700));
		Assert::equal('zz', Utils::numberToLetter(701));
		Assert::equal('aaa', Utils::numberToLetter(702));
		Assert::equal('aab', Utils::numberToLetter(703));
		Assert::equal('aba', Utils::numberToLetter(728));
		Assert::equal('abb', Utils::numberToLetter(729));
		Assert::equal('abc', Utils::numberToLetter(730));
		Assert::equal('abd', Utils::numberToLetter(731));
	}

	function testToNumberCapitals()
	{
		Assert::equal('A', Utils::numberToLetter(0, 'A'));
		Assert::equal('B', Utils::numberToLetter(1, 'A'));
		Assert::equal('N', Utils::numberToLetter(13, 'A'));
		Assert::equal('Y', Utils::numberToLetter(24, 'A'));
		Assert::equal('Z', Utils::numberToLetter(25, 'A'));
		Assert::equal('AA', Utils::numberToLetter(26, 'A'));
		$main = 'A';
		for ($i = 1; $i <= 26; $i++ && $main++) {
			Assert::equal($main . 'B', Utils::numberToLetter(($i * 26) + 1, 'A'));
		}
		Assert::equal('AB', Utils::numberToLetter(27, 'A'));
		Assert::equal('AC', Utils::numberToLetter(28, 'A'));
		Assert::equal('AF', Utils::numberToLetter(31, 'A'));
		Assert::equal('AY', Utils::numberToLetter(50, 'A'));
		Assert::equal('AZ', Utils::numberToLetter(51, 'A'));
		Assert::equal('BA', Utils::numberToLetter(52, 'A'));
		Assert::equal('BB', Utils::numberToLetter(53, 'A'));
		Assert::equal('BC', Utils::numberToLetter(54, 'A'));
		Assert::equal('YZ', Utils::numberToLetter(675, 'A'));
		Assert::equal('ZA', Utils::numberToLetter(676, 'A'));
		Assert::equal('ZB', Utils::numberToLetter(677, 'A'));
		Assert::equal('ZC', Utils::numberToLetter(678, 'A'));
		Assert::equal('ZY', Utils::numberToLetter(700, 'A'));
		Assert::equal('ZZ', Utils::numberToLetter(701, 'A'));
		Assert::equal('AAA', Utils::numberToLetter(702, 'A'));
		Assert::equal('AAB', Utils::numberToLetter(703, 'A'));
		Assert::equal('ABA', Utils::numberToLetter(728, 'A'));
		Assert::equal('ABB', Utils::numberToLetter(729, 'A'));
		Assert::equal('ABC', Utils::numberToLetter(730, 'A'));
		Assert::equal('ABD', Utils::numberToLetter(731, 'A'));
	}

	function testToLetters()
	{
		Assert::equal(0, Utils::letterToNumber('a'));
		Assert::equal(1, Utils::letterToNumber('b'));
		Assert::equal(13, Utils::letterToNumber('n'));
		Assert::equal(24, Utils::letterToNumber('y'));
		Assert::equal(25, Utils::letterToNumber('z'));
		Assert::equal(26, Utils::letterToNumber('aa'));
		$main = 'a';
		for ($i = 1; $i <= 26; $i++ && $main++) {
			Assert::equal(($i * 26) + 1, Utils::letterToNumber($main . 'b'));
		}
		Assert::equal(27, Utils::letterToNumber('ab'));
		Assert::equal(28, Utils::letterToNumber('ac'));
		Assert::equal(31, Utils::letterToNumber('af'));
		Assert::equal(50, Utils::letterToNumber('ay'));
		Assert::equal(51, Utils::letterToNumber('az'));
		Assert::equal(52, Utils::letterToNumber('ba'));
		Assert::equal(53, Utils::letterToNumber('bb'));
		Assert::equal(54, Utils::letterToNumber('bc'));
		Assert::equal(675, Utils::letterToNumber('yz'));
		Assert::equal(676, Utils::letterToNumber('za'));
		Assert::equal(677, Utils::letterToNumber('zb'));
		Assert::equal(678, Utils::letterToNumber('zc'));
		Assert::equal(700, Utils::letterToNumber('zy'));
		Assert::equal(701, Utils::letterToNumber('zz'));
		Assert::equal(702, Utils::letterToNumber('aaa'));
		Assert::equal(703, Utils::letterToNumber('aab'));
		Assert::equal(728, Utils::letterToNumber('aba'));
		Assert::equal(729, Utils::letterToNumber('abb'));
		Assert::equal(730, Utils::letterToNumber('abc'));
		Assert::equal(731, Utils::letterToNumber('abd'));
	}

	function testToLettersCapitals()
	{
		Assert::equal(0, Utils::letterToNumber('A', 'A'));
		Assert::equal(1, Utils::letterToNumber('B', 'A'));
		Assert::equal(13, Utils::letterToNumber('N', 'A'));
		Assert::equal(24, Utils::letterToNumber('Y', 'A'));
		Assert::equal(25, Utils::letterToNumber('Z', 'A'));
		Assert::equal(26, Utils::letterToNumber('AA', 'A'));
		$main = 'A';
		for ($i = 1; $i <= 26; $i++ && $main++) {
			Assert::equal(($i * 26) + 1, Utils::letterToNumber($main . 'B', 'A'));
		}
		Assert::equal(27, Utils::letterToNumber('AB', 'A'));
		Assert::equal(28, Utils::letterToNumber('AC', 'A'));
		Assert::equal(31, Utils::letterToNumber('AF', 'A'));
		Assert::equal(50, Utils::letterToNumber('AY', 'A'));
		Assert::equal(51, Utils::letterToNumber('AZ', 'A'));
		Assert::equal(52, Utils::letterToNumber('BA', 'A'));
		Assert::equal(53, Utils::letterToNumber('BB', 'A'));
		Assert::equal(54, Utils::letterToNumber('BC', 'A'));
		Assert::equal(675, Utils::letterToNumber('YZ', 'A'));
		Assert::equal(676, Utils::letterToNumber('ZA', 'A'));
		Assert::equal(677, Utils::letterToNumber('ZB', 'A'));
		Assert::equal(678, Utils::letterToNumber('ZC', 'A'));
		Assert::equal(700, Utils::letterToNumber('ZY', 'A'));
		Assert::equal(701, Utils::letterToNumber('ZZ', 'A'));
		Assert::equal(702, Utils::letterToNumber('AAA', 'A'));
		Assert::equal(703, Utils::letterToNumber('AAB', 'A'));
		Assert::equal(728, Utils::letterToNumber('ABA', 'A'));
		Assert::equal(729, Utils::letterToNumber('ABB', 'A'));
		Assert::equal(730, Utils::letterToNumber('ABC', 'A'));
		Assert::equal(731, Utils::letterToNumber('ABD', 'A'));
	}
}

$test = new UtilsTest($container);
$test->run();
