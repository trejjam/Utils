<?php
declare(strict_types=1);

namespace Trejjam\Utils;

use Trejjam;

class Utils
{
	public static function unifyZip(string $zip) : string
	{
		if (strlen($zip) === 5) {
			return substr($zip, 0, 3) . ' ' . substr($zip, 3, 2);
		}

		return $zip;
	}

	public static function unifyPhone(string $phone, bool $addPrefix = TRUE, string $prefix = '+420') : string
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

	public static function getValue(array $array, string $key, $keyDelimiter = '.')
	{
		$out = $array;
		$keyArray = explode($keyDelimiter, $key);

		$findKeys = [];
		foreach ($keyArray as $v) {
			if (is_array($out) && array_key_exists($v, $out)) {
				$out = $out[$v];
				$findKeys[] = $v;

				continue;
			}

			if (is_object($out) && $out instanceof \ArrayAccess && isset($out[$v])) {
				$out = $out[$v];
				$findKeys[] = $v;

				continue;
			}

			$magicArrayAccessException = new Trejjam\Utils\MagicArrayAccessException("Key '$v' from '$key' not exist in array.", Exception::UTILS_KEY_NOT_FOUND);
			$magicArrayAccessException->setUsedKeys($findKeys);
			$magicArrayAccessException->setAllKeys($keyArray);
			$magicArrayAccessException->setLastItem($out);

			throw $magicArrayAccessException;
		}

		return $out;
	}

	/**
	 * http://stackoverflow.com/questions/7664121/php-converting-number-to-alphabet-and-vice-versa
	 * @param int    $num
	 * @param string $startLetter
	 *
	 * @return string
	 */
	public static function numberToLetter(int $num, string $startLetter = 'a') : string
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

	public static function letterToNumber(string $str, string $startLetter = 'a') : int
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
}
