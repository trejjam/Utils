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
	/**
	 * @param double      $price
	 * @param string      $append
	 * @param bool|string $freeText
	 * @return string
	 */
	public static function priceCreate($price, $append, $freeText = TRUE) {
		if ($price <= 0 && $freeText !== FALSE) {
			return $freeText === TRUE ? "free" : $freeText;
		}

		$workPrice = ceil(max(-$price, $price));

		$length = strlen($workPrice);
		$ternary = ceil($length / 3);

		$tempPrice = "";
		for ($i = 0; $i < $ternary - 1; $i++) {
			if ($tempPrice != "") $tempPrice = "." . $tempPrice;
			$tempPrice = self::numI($workPrice, $i * 3 + 2) . self::numI($workPrice, $i * 3 + 1) . self::numI($workPrice, $i * 3) . $tempPrice;
		}

		$tempPrice2 = ceil(self::numI($workPrice, ($ternary - 1) * 3 + 2) * 100 + self::numI($workPrice, ($ternary - 1) * 3 + 1) * 10 + self::numI($workPrice, ($ternary - 1) * 3));

		if ($tempPrice != "" && $tempPrice2 > 0) $tempPrice = $tempPrice2 . "." . $tempPrice;
		else $tempPrice = $tempPrice2;

		return ($price < 0 ? "-" : "") . $tempPrice . ",-" . (strlen($append) ? " " . $append : "");
	}
	/**
	 * @param $num
	 * @param $i
	 * @return int
	 */
	private static function numI($num, $i) {
		return (floor($num / pow(10, $i))) % 10;
	}
	/**
	 * @param $string
	 * @return bool
	 */
	public static function isJson($string) {
		json_decode($string);

		return (json_last_error() == JSON_ERROR_NONE);
	}

	/**
	 * @return array
	 */
	public static function getServerInfo() {
		$info = [
			"HTTP_ORIGIN"           => isset($_SERVER["HTTP_ORIGIN"]) ? $_SERVER["HTTP_ORIGIN"] : "",
			"HTTP_USER_AGENT"       => isset($_SERVER["HTTP_USER_AGENT"]) ? $_SERVER["HTTP_USER_AGENT"] : "",
			"REDIRECT_QUERY_STRING" => isset($_SERVER["REDIRECT_QUERY_STRING"]) ? $_SERVER["REDIRECT_QUERY_STRING"] : "",
			"QUERY_STRING"          => isset($_SERVER["QUERY_STRING"]) ? $_SERVER["QUERY_STRING"] : "",
		];

		return $info;
	}
	public static function getTextServerInfo() {
		return print_r(self::getServerInfo(), TRUE);
	}
}