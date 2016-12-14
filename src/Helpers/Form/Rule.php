<?php

namespace Trejjam\Utils\Helpers\Form;

use Trejjam;
use Nextras;

class Rule
{
	const IPV4 = '(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)';
	const IPV6 = '(?:[a-fA-F0-9]{1,4}:){7}[a-fA-F0-9]{1,4}';
	const MAC  = '([a-fA-F0-9]{2}[:.-]?){7}[[a-fA-F0-9]{2}';

	const DateTimeCompare = 'Trejjam\Utils\Helpers\Form\Rule::dateTimeCompare';
	const DateCompare     = 'Trejjam\Utils\Helpers\Form\Rule::dateCompare';

	static function dateTimeCompare(Nextras\Forms\Controls\DateTimePicker $item, array $arg)
	{
		if (count($arg) > 1) {
			if ($arg[0] === $item) {
				trigger_error('Use only second argument, for compare against source control', E_USER_DEPRECATED);
			}

			return $arg[0] <= $arg[1];
		}

		return $item->getValue() <= $arg[0];
	}

	static function dateCompare(Nextras\Forms\Controls\DatePicker $item, array $arg)
	{
		if (count($arg) > 1) {
			if ($arg[0] === $item) {
				trigger_error('Use only second argument, for compare against source control', E_USER_DEPRECATED);
			}

			return $arg[0] <= $arg[1];
		}

		return $item->getValue() <= $arg[0];
	}
}
