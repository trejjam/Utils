<?php
/**
 * Created by PhpStorm.
 * User: Jan
 * Date: 17. 11. 2014
 * Time: 2:06
 */

namespace Trejjam\Utils;

use Nette,
	Trejjam;

class Utils
{
	/**
	 * @param string          $freeText
	 * @param double|double[] $price
	 * @param null|string     $units
	 * @param int             $decimalLength
	 *
	 * @return string|string[]
	 */
	public static function priceFreeText($freeText, $price, $units = NULL, $decimalLength = 2)
	{
		if (is_array($price)) {
			$out = [];
			foreach ($price as $v) {
				$out[] = static::priceFreeText($freeText, $v, $units, $decimalLength);
			}

			return $out;
		}
		else {
			return $price <= 0 ? $freeText : static::priceCreate($price, $units, $decimalLength);
		}
	}

	/**
	 * @param double|double[] $price
	 * @param null|string     $units
	 * @param int             $decimalLength
	 *
	 * @return string|string[]
	 */
	public static function priceCreate($price, $units = NULL, $decimalLength = 2)
	{
		if (is_array($price)) {
			$out = [];
			foreach ($price as $v) {
				$out[] = static::priceCreate($v, $units, $decimalLength);
			}

			return $out;
		}
		else {
			$workPrice = floor(abs($price * pow(10, $decimalLength)));
			$workDecimalPrice = floor(abs($price * pow(10, $decimalLength + 1)));
			$integerPrice = floor($workPrice / pow(10, $decimalLength));
			$integerLength = strlen($integerPrice);
			$decimalPrice = Nette\Utils\Strings::padLeft(
				round(static::numberAt($workDecimalPrice, 0, $decimalLength + 1) / 10),
				$decimalLength,
				'0'
			);

			$integerTernary = ceil($integerLength / 3);

			$outPrice = '';
			for ($i = $integerTernary - 1; $i >= 0; $i--) {
				if ($outPrice != "") $outPrice .= '.';
				$outPrice .=
					Nette\Utils\Strings::padLeft(
						static::numberAt($integerPrice, $i * 3, 3),
						3,
						'0'
					);
			}
			$outPrice = Nette\Utils\Strings::replace($outPrice, [
				'~^[0]*~' => '',
			]);

			return ($price < 0 ? '-' : '') . $outPrice . ',' . (in_array($decimalPrice, ['', '0']) ? '-' : $decimalPrice) . (is_null($units) ? '' : ' ' . $units);
		}
	}

	/**
	 * @param int $number
	 * @param int $positionStart
	 * @param int $numberLength
	 *
	 * @return int
	 */
	public static function numberAt($number, $positionStart, $numberLength = 1)
	{
		return (int)(floor($number / pow(10, $positionStart))) % pow(10, $numberLength);
	}

	/**
	 * @param $zip
	 *
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
	 *
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
		return print_r(static::getServerInfo(), TRUE);
	}


	public static function getValue(array $array, $key, $keyDelimiter = '.')
	{
		$out = $array;
		$keyArray = explode($keyDelimiter, $key);

		$findKeys = [];
		foreach ($keyArray as $v) {
			if (array_key_exists($v, $out)) {
				$out = $out[$v];
				$findKeys[] = $v;
			}
			else {
				$magicArrayAccessException = new Trejjam\Utils\MagicArrayAccessException("Key '$v' from '$key' not exist in array.", Exception::UTILS_KEY_NOT_FOUND);
				$magicArrayAccessException->setUsedKeys($findKeys);
				$magicArrayAccessException->setAllKeys($keyArray);
				$magicArrayAccessException->setLastItem($out);

				throw $magicArrayAccessException;
			}
		}

		return $out;
	}

	public static function getModuleFromRequest(Nette\Application\Request $request, $outputModuleDelimiter = ':')
	{
		$presenterArr = explode(':', $request->getPresenterName());
		array_pop($presenterArr);
		foreach ($presenterArr as $k => $v) {
			$presenterArr[$k] = Nette\Utils\Strings::firstLower($v);
		}

		$module = implode($outputModuleDelimiter, $presenterArr);

		return $module;
	}

	public static function getPresenterFromRequest(Nette\Application\Request $request)
	{
		$presenterArr = explode(':', $request->getPresenterName());

		return array_pop($presenterArr);
	}

	public static function unifyDir($dir)
	{
		$dirEnds = $dir[Nette\Utils\Strings::length($dir) - 1];
		$dir .= in_array($dirEnds, ['\\', '/']) ? '' : '/';

		return $dir;
	}

	/**
	 * http://stackoverflow.com/questions/7664121/php-converting-number-to-alphabet-and-vice-versa
	 * @param int    $num
	 * @param string $startLetter
	 *
	 * @return string
	 */
	public static function numberToLetter($num, $startLetter = 'a')
	{
		$startAscii = ord($startLetter);
		$searchingNum = $num;
		$help = 26;

		$chars = [$num % 26];
		while ($searchingNum >= $help) {
			$help *= 26;

			$num = (int)($num / 26);
			$num -= 1;
			if ($num >= 0) {
				$chars[] = $num % 26;
			}
		}

		$str = '';
		for ($i = count($chars) - 1; $i >= 0; $i--) {
			$str .= chr($chars[$i] + $startAscii);
		}

		return $str;
	}

	/**
	 * @param string $str
	 * @param string $startLetter
	 *
	 * @return int
	 */
	public static function letterToNumber($str, $startLetter = 'a')
	{
		$startAscii = ord($startLetter);

		$num = 0;
		for ($i = 0; $i < strlen($str); $i++) {
			$num *= 26;
			$num += ord($str[$i]) - $startAscii;
			$num += $i < (strlen($str) - 1) ? 1 : 0;
		}

		return $num;
	}

	public static function extractFieldFromArray($array, $key, $skipEmpty = FALSE, $keyDelimiter = '.', callable $getArrayCallback=null)
	{
		$out = [];

		foreach ($array as $v) {
			if (is_callable($getArrayCallback)) {
				$value = self::getValue($getArrayCallback($v), $key, $keyDelimiter);
			}
			else {
				$value = self::getValue($v, $key, $keyDelimiter);
			}

			if ($skipEmpty && empty($value)) {
				continue;
			}

			$out[] = $value;
		}

		return $out;
	}
}
