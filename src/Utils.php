<?php
/**
 * Created by PhpStorm.
 * User: Jan
 * Date: 17. 11. 2014
 * Time: 2:06
 */

namespace Trejjam\Utils;

use Nette;

class Utils
{
	public static function priceFreeText($freeText, $price, $units = NULL, $decimalLength = 2)
	{
		return $price <= 0 ? $freeText : self::priceCreate($price, $units, $decimalLength);
	}

	public static function priceCreate($price, $units = NULL, $decimalLength = 2)
	{
		$workPrice = floor(abs($price * pow(10, $decimalLength)));
		$integerPrice = floor($workPrice / pow(10, $decimalLength));
		$integerLength = strlen($integerPrice);
		$decimalPrice = self::numberAt($workPrice, 0, $decimalLength);

		$integerTernary = ceil($integerLength / 3);
		$decimalTernary = ceil($decimalLength / 3);

		$outPrice = '';
		for ($i = $integerTernary - 1; $i >= 0; $i--) {
			if ($outPrice != "") $outPrice .= '.';
			$outPrice .= self::numberAt($integerPrice, $i * 3, 3);
		}

		$outDecimalPrice = '';
		for ($i = $decimalTernary - 1; $i >= 0; $i--) {
			if ($outDecimalPrice != "") $outPrice .= '.';
			$decimalPosition = ($decimalLength - ($i + 1) * 3);
			$decimalPosition = $decimalPosition < 0 ? 0 : $decimalPosition;
			$outDecimalPrice .= self::numberAt($decimalPrice, $decimalPosition, 3);
		}

		return ($price < 0 ? '-' : '') . $outPrice . ',' . (in_array($outDecimalPrice, ['', '0']) ? '-' : $outDecimalPrice) . (is_null($units) ? '' : ' ' . $units);
	}

	public static function numberAt($number, $positionStart, $numberLength = 1)
	{
		return (int)(floor($number / pow(10, $positionStart))) % pow(10, $numberLength);
	}
	/**
	 * @param $zip
	 * @return string
	 */
	public static function unifyZip($zip)
	{
		if (strlen($zip) == 5) {
			return substr($zip, 0, 3) . " " . substr($zip, 3, 2);
		}

		return $zip;
	}
	/**
	 * @param string $phone
	 * @param bool   $addPrefix
	 * @param string $prefix
	 * @return string
	 */
	public static function unifyPhone($phone, $addPrefix = TRUE, $prefix = "+420")
	{
		$trimPhone = str_replace(' ', '', $phone);

		while (strlen($trimPhone) > 9 && $trimPhone[0] == '0') {
			$trimPhone = substr($trimPhone, 1);
		}

		$out = '';
		for ($i = strlen($trimPhone); $i >= 3; $i -= 3) {
			if ($out != '') $out = ' ' . $out;
			$out = substr($trimPhone, $i - 3, 3) . $out;

			if ($i < 6) {
				$out = substr($trimPhone, 0, $i - 3) . $out;
			}
		}

		if ($addPrefix && strlen($out) < 12 && $out != '') {
			$out = $prefix . ' ' . $out;
		}

		return $out;
	}

	/**
	 * @return array
	 */
	public static function getServerInfo()
	{
		$info = [
			"HTTP_ORIGIN"           => isset($_SERVER["HTTP_ORIGIN"]) ? $_SERVER["HTTP_ORIGIN"] : "",
			"HTTP_USER_AGENT"       => isset($_SERVER["HTTP_USER_AGENT"]) ? $_SERVER["HTTP_USER_AGENT"] : "",
			"REDIRECT_QUERY_STRING" => isset($_SERVER["REDIRECT_QUERY_STRING"]) ? $_SERVER["REDIRECT_QUERY_STRING"] : "",
			"QUERY_STRING"          => isset($_SERVER["QUERY_STRING"]) ? $_SERVER["QUERY_STRING"] : "",
		];

		return $info;
	}
	public static function getTextServerInfo()
	{
		return print_r(self::getServerInfo(), TRUE);
	}
}

/*
echo Utils::numberAt(12345, 0, 1) . "\n";
echo Utils::numberAt(12345, 0, 2) . "\n";
echo Utils::numberAt(12345, 0, 3) . "\n";


echo Utils::priceCreate(1234) . "\n";
echo Utils::priceCreate(1234.43) . "\n";
echo Utils::priceCreate(1234.689) . "\n";
echo Utils::priceCreate(1234.684) . "\n";

echo Utils::priceCreate(21234) . "\n";
echo Utils::priceCreate(21234.43) . "\n";
echo Utils::priceCreate(21234.689) . "\n";
echo Utils::priceCreate(21234.684) . "\n";

echo Utils::priceCreate(321234) . "\n";
echo Utils::priceCreate(321234.43) . "\n";
echo Utils::priceCreate(321234.689) . "\n";
echo Utils::priceCreate(321234.684) . "\n";

echo Utils::priceCreate(4561234) . "\n";
echo Utils::priceCreate(4561234.43) . "\n";
echo Utils::priceCreate(4561234.689) . "\n";
echo Utils::priceCreate(4561234.684) . "\n";

echo Utils::priceCreate(45621234) . "\n";
echo Utils::priceCreate(45621234.43) . "\n";
echo Utils::priceCreate(45621234.689) . "\n";
echo Utils::priceCreate(45621234.684) . "\n";

echo Utils::priceCreate(456321234) . "\n";
echo Utils::priceCreate(456321234.43) . "\n";
echo Utils::priceCreate(456321234.689) . "\n";
echo Utils::priceCreate(456321234.684) . "\n";
*/
