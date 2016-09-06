<?php

namespace Trejjam\Utils\Helpers\Form;

use Trejjam;

class Rule
{
	const IPV4      = '(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)';
	const IPV6      = '(?:[a-fA-F0-9]{1,4}:){7}[a-fA-F0-9]{1,4}';
	const MAC       = '([a-fA-F0-9]{2}[:.-]?){7}[[a-fA-F0-9]{2}';
}
