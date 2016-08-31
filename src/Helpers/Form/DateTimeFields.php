<?php
/**
 * Created by PhpStorm.
 * User: jam
 * Date: 4.10.15
 * Time: 17:07
 */

namespace Trejjam\Utils\Helpers\Form;

use Nette,
	Nette\Application\UI,
	Trejjam;

class DateTimeFields
{
	const
		DATE = 'date',
		TIME = 'time';

	static public $useTranslatorRule   = TRUE;
	static public $translatorRuleClass = 'rule';

	public static function addDateTimeLocal(Nette\Forms\Container $container, $name, $label = NULL, \DateTime $dateTime = NULL, $onNullSetNow = TRUE)
	{
		$input = $container->addText($name, $label);
		$input->setType('datetime-local');

		if ($onNullSetNow && is_null($dateTime)) {
			$dateTime = new Nette\Utils\DateTime;
		}

		if ( !is_null($dateTime)) {
			$dateValue = $dateTime->format('Y-m-d\TH:i:s');
			$input->setDefaultValue($dateValue[0] == '-' ? NULL : $dateValue);
		}

		$input->addCondition(UI\Form::FILLED)
			  ->addRule(UI\Form::PATTERN, static::$useTranslatorRule ? __('Datetime must be in format YYYY-MM-DD HH:mm') : $name . '.' . static::$translatorRuleClass . '.filled', '(([0-9]{4}-[0-9]{2}-[0-9]{2})|([0-9]{1,2}[/.]{1}[ ]{0,1}[0-9]{1,2}[/.]{1}[ ]{0,1}[0-9]{4}))[T ]{1}([0-9]{1,2}(:[0-9]{1,2}){1,2})')
			  ->addRule(
				  [DateTimeFields::class, 'validateUnixDateTimeLocal'],
				  static::$useTranslatorRule ? __('Date must be older than 1970-01-01 00:00') : $name . '.' . static::$translatorRuleClass . '.invalid',
				  new Nette\Utils\DateTime('1970-01-01 00:00')
			  );

		return $input;
	}

	public static function validateUnixDateTimeLocal(Nette\Forms\Controls\TextInput $control, $restriction, callable $getValue = NULL)
	{
		$rawValue = $control->getValue();

		if ( !empty($rawValue)) {
			$value = $getValue ? $getValue($rawValue) : static::getDateTimeLocalValue($rawValue);

			return $value >= $restriction;
		}

		return TRUE;
	}

	/**
	 * Get DateTime from ::addDateTimeLocal() field
	 *
	 * @param Nette\Forms\Controls\TextInput|string $input
	 *
	 * @return string
	 */
	public static function getDateTimeLocalValue($input)
	{
		if ($input instanceof Nette\Forms\Controls\TextInput) {
			$value = $input->getValue();
		}
		else {
			$value = $input;
		}

		if (preg_match('~^(\d{1,2}?)[/.]{1}[ ]{0,1}(\d{1,2}?)[/.]{1}[ ]{0,1}(\d{4}?) (\d{1,2}?)[:]{1}(\d{1,2}?)([:]{1}(\d{1,2}?)){0,1}$~', $value, $arr)) {
			$date = new Nette\Utils\DateTime($arr[1] . '-' . $arr[2] . '-' . $arr[3] . ' ' . $arr[4] . ':' . $arr[5] . (isset($arr[7]) ? ':' . $arr[7] : ''));

			return $date->getTimestamp() < 10 ? NULL : $date->format('Y-m-d H:i:s');
		}
		else if (preg_match('~^(\d{4}?)[-]{1}(\d{2}?)[-]{1}(\d{2}?)T(\d{1,2}?)[:]{1}(\d{1,2}?)([:]{1}(\d{1,2}?)){0,1}$~', $value, $arr)) {
			$date = new Nette\Utils\DateTime($arr[3] . '-' . $arr[2] . '-' . $arr[1] . ' ' . $arr[4] . ':' . $arr[5] . (isset($arr[7]) ? ':' . $arr[7] : ''));

			return $date->getTimestamp() < 10 ? NULL : $date->format('Y-m-d H:i:s');
		}

		return empty($value) || $value == '0000-00-00' ? NULL : $value;
	}

	public static function addDateTime(Nette\Forms\Container $container, $name, $label = NULL, \DateTime $dateTime = NULL, $onNullSetNow = TRUE)
	{
		$subContainer = $container->addContainer($name);
		$date = static::addDate($subContainer, static::DATE, $label);
		$time = static::addTime($subContainer, static::TIME);

		if ($onNullSetNow && is_null($dateTime)) {
			$dateTime = new Nette\Utils\DateTime;
		}

		if ( !is_null($dateTime)) {
			$dateValue = $dateTime->format('Y-m-d');
			$date->setDefaultValue($dateValue[0] == '-' ? NULL : $dateValue);
			$time->setDefaultValue($dateValue[0] == '-' ? NULL : $dateTime->format('H:i:s'));
		}

		$date->addConditionOn($time, UI\Form::FILLED)
			 ->addRule(UI\Form::FILLED, static::$useTranslatorRule ? __('Please, fill date otherwise you lose data.') : $name . '.' . static::$translatorRuleClass . '.filled')
			 ->addRule(
				 [DateTimeFields::class, 'validateUnixDateTime'],
				 static::$useTranslatorRule ? __('Date must be older than 1970-01-01 00:00') : $name . '.' . static::$translatorRuleClass . '.invalid',
				 new Nette\Utils\DateTime('1970-01-01 00:00')
			 );
	}

	public static function validateUnixDateTime(Nette\Forms\Controls\TextInput $control, $restriction)
	{
		return static::validateUnixDateTimeLocal($control, $restriction, [DateTimeFields::class, 'getDateTimeValue']);
	}

	/**
	 * Get DateTime from ::addDateTime() container
	 *
	 * @param Nette\Forms\Controls\TextInput[]|string[] $dateTime
	 *
	 * @return string
	 */
	public static function getDateTimeValue($dateTime)
	{
		$date = static::getDateValue($dateTime[static::DATE]);
		$time = static::getTimeValue($dateTime[static::TIME]);

		if (!is_null($date) && $date != '' && !is_null($time) && $time != '') {
			return $date . ' ' . $time;
		}
		else {
			return (empty($date) || $date == '' ? '0000-00-00' : $date) . ' ' . (is_null($time) || $time == '' ? '00:00:00' : $time);
		}
	}

	public static function addDate(Nette\Forms\Container $container, $name, $label = NULL, $cols = NULL, $maxLength = NULL, \DateTime $dateTime = NULL, $onNullSetNow = TRUE)
	{
		$input = $container->addText($name, $label, $cols, $maxLength);
		$input->setType('date');
		$input->addCondition(UI\Form::FILLED)
			  ->addRule(UI\Form::PATTERN, static::$useTranslatorRule ? __('Date must be in format YYYY-MM-DD') : $name . '.' . static::$translatorRuleClass . '.filled', '([0-9]{4}-[0-9]{2}-[0-9]{2})|(\d{1,2}[/.]{1}[ ]{0,1}\d{1,2}[/.]{1}[ ]{0,1}\d{4})')
			  ->addRule(
				  [DateTimeFields::class, 'validateUnixDate'],
				  static::$useTranslatorRule ? __('Date must be older than 1970-01-01 00:00') : $name . '.' . static::$translatorRuleClass . '.invalid',
				  new Nette\Utils\DateTime('1970-01-01 00:00')
			  );

		if ($onNullSetNow && is_null($dateTime)) {
			$dateTime = new Nette\Utils\DateTime;
		}

		if (!is_null($dateTime)) {
			$input->setDefaultValue($dateTime->format('Y-m-d'));
		}

		return $input;
	}

	public static function validateUnixDate(Nette\Forms\Controls\TextInput $control, $restriction)
	{
		return static::validateUnixDateTimeLocal($control, $restriction, [DateTimeFields::class, 'getDateValue']);
	}

	/**
	 * Get Date from ::addDate() field
	 *
	 * @param Nette\Forms\Controls\TextInput|string $input
	 *
	 * @return string
	 */
	public static function getDateValue($input)
	{
		if ($input instanceof Nette\Forms\Controls\TextInput) {
			$value = $input->getValue();
		}
		else {
			$value = $input;
		}

		if (preg_match('~^\d{1,2}[/.]{1}[ ]{0,1}\d{1,2}[/.]{1}[ ]{0,1}\d{4}$~', $value, $arr)) {
			$dateArr = preg_split('/[.\/]{1}[ ]{0,1}/s', $arr[0]);
			$date = new Nette\Utils\DateTime($dateArr[2] . '-' . $dateArr[1] . '-' . $dateArr[0]);

			return $date->getTimestamp() < 10 ? NULL : $date->format('Y-m-d');
		}

		return empty($value) || $value == '0000-00-00' ? NULL : $value;
	}

	public static function addTime(Nette\Forms\Container $container, $name, $label = NULL, $cols = NULL, $maxLength = NULL, \DateTime $dateTime = NULL, $onNullSetNow = TRUE)
	{
		$input = $container->addText($name, $label, $cols, $maxLength);
		$input->setType('time');
		$input->addCondition(UI\Form::FILLED)
			  ->addRule(UI\Form::PATTERN, static::$useTranslatorRule ? __('Time must be in format HH:MM') : $name . '.' . static::$translatorRuleClass . '.filled', '([01]?[0-9]{1}|2[0-3]{1})(:[0-5]{1}[0-9]{1}){1,2}');

		if ($onNullSetNow && is_null($dateTime)) {
			$dateTime = new Nette\Utils\DateTime;
		}

		if ( !is_null($dateTime)) {
			$input->setDefaultValue($dateTime->format('H:i:s'));
		}

		return $input;
	}

	/**
	 * Get Time from ::addTime() field
	 *
	 * @param Nette\Forms\Controls\TextInput $input
	 *
	 * @return mixed
	 */
	public static function getTimeValue($input)
	{
		if ($input instanceof Nette\Forms\Controls\TextInput) {
			$value = $input->getValue();
		}
		else {
			$value = $input;
		}

		return empty($value) ? NULL : $value . ':00';
	}
}
