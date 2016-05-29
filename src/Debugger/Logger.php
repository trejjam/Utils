<?php
/**
 * Created by PhpStorm.
 * User: jam
 * Date: 3.11.15
 * Time: 12:32
 */

namespace Trejjam\Utils\Debugger;

use Nette,
	Tracy;

class Logger extends Tracy\Logger
{
	/**
	 * @var Nette\Mail\IMailer
	 */
	protected $mailerClass;
	/**
	 * @var string
	 */
	protected $host = NULL;
	/**
	 * @var string
	 */
	protected $path = '/devLog/';

	public function __construct($directory, $email = NULL, Tracy\BlueScreen $blueScreen = NULL)
	{
		parent::__construct($directory, $email, $blueScreen);

		$this->mailer = [$this, 'defaultMailer'];
	}

	public function setEmailClass(Nette\Mail\IMailer $mailerClass)
	{
		$this->mailerClass = &$mailerClass;
	}

	public function setEmailSnooze($emailSnooze)
	{
		$this->emailSnooze = $emailSnooze;
	}

	public function setHost($host)
	{
		$this->host = $host;
	}

	public function setPath($path)
	{
		$this->path = $path;
	}

	/**
	 * Logs message or exception to file and sends email notification.
	 *
	 * @param  string|\Exception|\Throwable $message
	 * @param  string                       $priority one of constant ILogger::INFO, WARNING, ERROR (sends email),
	 *                                                EXCEPTION (sends email), CRITICAL
	 *                                                (sends email)
	 *
	 * @return string logged error filename
	 */
	public function log($message, $priority = self::INFO)
	{
		if ( !$this->directory) {
			throw new \LogicException('Directory is not specified.');
		}
		elseif ( !is_dir($this->directory)) {
			throw new \RuntimeException("Directory '$this->directory' is not found or is not directory.");
		}

		$exceptionFile = $message instanceof \Exception || $message instanceof \Throwable
			? $this->getExceptionFile($message)
			: NULL;
		$line = $this->formatLogLine($message, $exceptionFile);
		$file = $this->directory . '/' . strtolower($priority ?: self::INFO) . '.log';

		if ( !@file_put_contents($file, $line . PHP_EOL, FILE_APPEND | LOCK_EX)) { // @ is escalated to exception
			throw new \RuntimeException("Unable to write to log file '$file'. Is directory writable?");
		}

		if ($exceptionFile) {
			$this->logException($message, $exceptionFile);
		}

		if (in_array($priority, [self::ERROR, self::EXCEPTION, self::CRITICAL], TRUE)) {
			$this->sendEmail($message, $exceptionFile, $priority);
		}

		return $exceptionFile;
	}

	/**
	 * @param  string                       $message
	 * @param  string|\Exception|\Throwable $exceptionFile
	 * @param null                          $priority
	 */
	public function sendEmail($message, $exceptionFile = NULL, $priority = NULL)
	{
		$snooze = is_numeric($this->emailSnooze)
			? $this->emailSnooze
			: @strtotime($this->emailSnooze) - time(); // @ timezone may not be set

		$sendMail = FALSE;
		if (is_file($this->directory . '/email-sent')) {
			if (@filectime($this->directory . '/email-sent') + $snooze < time()) {
				$sendMail = TRUE;
			}
			else {
				try {
					$emailSendJson = Nette\Utils\Json::decode(file_get_contents($this->directory . '/email-sent'), Nette\Utils\Json::FORCE_ARRAY);
					if ( !isset($emailSendJson[$exceptionFile])) {
						$sendMail = TRUE;
					}
				}
				catch (Nette\Utils\JsonException $e) {
					$sendMail = TRUE;
				}
			}
		}
		else {
			@file_put_contents($this->directory . '/email-sent', 'sent');
			$sendMail = TRUE;
		}

		if ($this->email && $this->mailer && $sendMail
			//&& @filemtime($this->directory . '/email-sent') + $snooze < time() // @ file may not exist
			//&& @file_put_contents($this->directory . '/email-sent', 'sent') // @ file may not be writable
		) {
			call_user_func($this->mailer, $message, $this->email, $exceptionFile, $priority);
		}
	}

	/**
	 * Default mailer.
	 *
	 * @param $message
	 * @param $email
	 * @param  string|\Exception|\Throwable
	 *
	 * @internal
	 */
	public function defaultMailer($message, $email, $exceptionFile = NULL, $priority = NULL)
	{
		if ( !is_null($this->host)) {
			$host = $this->host;
		}
		else {
			$host = preg_replace('#[^\w.-]+#', '', isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : php_uname('n'));
		}

		try {
			$emailSendJson = Nette\Utils\Json::decode(file_get_contents($this->directory . '/email-sent'), Nette\Utils\Json::FORCE_ARRAY);
		}
		catch (Nette\Utils\JsonException $e) {
			$emailSendJson = [];
		}

		$emailSendJson[$exceptionFile] = TRUE;

		@file_put_contents($this->directory . '/email-sent', Nette\Utils\Json::encode($emailSendJson));

		$mail = new Nette\Mail\Message;
		$mail
			->setFrom(
				$this->fromEmail
					?: (Nette\Utils\Validators::isEmail("noreply@$host")
					? "noreply@$host"
					: "noreply@example.tld"
				)
			)
			->addTo(is_array($email) ? $email[0] : $email)
			->setSubject("PHP: An error (" . $this->getTitle($message, $priority) . ") occurred on the server $host")
			->setHtmlBody($this->formatMessage($message) .
						  "\n\nsource: " . Tracy\Helpers::getSource() .
						  (is_null($exceptionFile)
							  ? ''
							  : "\n\nexception link: " . Nette\Utils\Strings::replace($exceptionFile, [
								  '~^(.*)exception--~' => 'http://' . $host . $this->path . 'exception--',
							  ])
						  ));

		foreach ($email as $k => $v) {
			if ($k == 0) {
				continue;
			}
			$mail->addCc($v);
		}

		$this->mailerClass->send($mail);
	}

	protected function getTitle($message, $priority)
	{
		if ($message instanceof \Exception) {
			return Tracy\Helpers::getClass($message);
		}
		else {
			return $priority;
		}
	}
}