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
	App,
	Trejjam;

class DateTimeFields
{
	const
		DATE = 'date',
		TIME = 'time';

	public static function addDateTime(Nette\Forms\Container $container, $name, $label, \DateTime $dateTime = NULL)
	{
		$subContainer = $container->addContainer($name);
		$date = static::addDate($subContainer, static::DATE, $label);
		$time = static::addTime($subContainer, static::TIME);
		if (!is_null($dateTime)) {
			$dateValue = $dateTime->format('Y-m-d');
			$date->setDefaultValue($dateValue[0] == '-' ? NULL : $dateValue);
			$time->setDefaultValue($dateValue[0] == '-' ? NULL : $dateTime->format('H:i'));
		}

		$date->addConditionOn($time, UI\Form::FILLED)
			 ->addRule(UI\Form::FILLED, __('Please, fill date otherwise you lose data.'));
	}
	/**
	 * Get DateTime from ::addDateTime() container
	 * @param $dateTime
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
			return (is_null($date) || $date == '' ? '0000-00-00' : $date) . ' ' . (is_null($time) || $time == '' ? '00:00:00' : $time);
		}
	}

	public static function addDate(Nette\Forms\Container $container, $name, $label = NULL, $cols = NULL, $maxLength = NULL)
	{
		$input = $container->addText($name, $label, $cols, $maxLength);
		$input->setType('date');
		$input->addCondition(UI\Form::FILLED)
			  ->addRule(UI\Form::PATTERN, __('Date must be in format YYYY-MM-DD'), '([0-9]{4}-[0-9]{2}-[0-9]{2})|(\d{1,2}[/.]{1}[ ]{0,1}\d{1,2}[/.]{1}[ ]{0,1}\d{4})');

		return $input;
	}
	/**
	 * Get Date from ::addDate() field
	 * @param $value
	 * @return string
	 */
	public static function getDateValue($value)
	{
		if (preg_match('~^\d{1,2}[/.]{1}[ ]{0,1}\d{1,2}[/.]{1}[ ]{0,1}\d{4}$~', $value, $arr)) {
			$dateArr = preg_split('/[.\/]{1}[ ]{0,1}/s', $arr[0]);
			$date = new Nette\Utils\DateTime($dateArr[2] . '-' . $dateArr[1] . '-' . $dateArr[0]);

			return $date->format('Y-m-d');
		}

		return $value;
	}

	public static function addTime(Nette\Forms\Container $container, $name, $label = NULL, $cols = NULL, $maxLength = NULL)
	{
		$input = $container->addText($name, $label, $cols, $maxLength);
		$input->setType('time');
		$input->addCondition(UI\Form::FILLED)
			  ->addRule(UI\Form::PATTERN, __('Time must be in format HH:MM'), '([01]?[0-9]{1}|2[0-3]{1}):[0-5]{1}[0-9]{1}');

		return $input;
	}
	/**
	 * Get Time from ::addTime() field
	 * @param $value
	 * @return mixed
	 */
	public static function getTimeValue($value)
	{
		return $value . ':00';
	}
}
