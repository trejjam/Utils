<?php
/**
 * Created by PhpStorm.
 * User: jam
 * Date: 3.11.15
 * Time: 13:16
 */

namespace Trejjam\Utils\Debugger;

use Nette,
	Tracy;

class Debugger extends Tracy\Debugger
{
	/**
	 * @var Logger
	 */
	protected static $logger;
	/**
	 * @var BlueScreen
	 */
	protected static $blueScreen;

	/**
	 * @return Logger
	 */
	public static function getLogger()
	{
		if ( !static::$logger) {
			static::$logger = new Logger(static::$logDirectory, static::$email, static::getBlueScreen());
			static::$logger->directory = &Tracy\Debugger::$logDirectory; // back compatiblity
			static::$logger->email = &Tracy\Debugger::$email;

			Tracy\Debugger::setLogger(static::$logger);
		}

		return Tracy\Debugger::getLogger();
	}

	/**
	 * @return BlueScreen
	 */
	public static function getBlueScreen()
	{
		if ( !self::$blueScreen || !(parent::getBlueScreen() instanceof BlueScreen)) {
			if ( !self::$blueScreen) {
				self::$blueScreen = new BlueScreen;
				self::$blueScreen->info = [
					'PHP ' . PHP_VERSION,
					isset($_SERVER['SERVER_SOFTWARE']) ? $_SERVER['SERVER_SOFTWARE'] : NULL,
					'Tracy ' . self::VERSION,
				];
			}

			$reflection = new \ReflectionClass(static::class);
			$tracyDebugger = $reflection->getParentClass();
			$tracyDebuggerBlueScreen = $tracyDebugger->getProperty('blueScreen');
			$tracyDebuggerBlueScreen->setAccessible(TRUE);
			$tracyDebuggerBlueScreen->setValue(NULL, self::$blueScreen);
		}

		return self::$blueScreen;
	}
}
