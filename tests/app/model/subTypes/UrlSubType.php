<?php
/**
 * Created by PhpStorm.
 * User: jam
 * Date: 22.6.15
 * Time: 16:27
 */

namespace Trejjam\Utils\Test;


use Nette,
	Trejjam,
	Trejjam\Utils\Contents\Items;

class UrlSubType extends Items\SubType
{
	/**
	 * @var Nette\Application\LinkGenerator
	 */
	protected $linkGenerator;

	public function __construct(Nette\Application\LinkGenerator $linkGenerator)
	{
		$this->linkGenerator = $linkGenerator;
	}

	/**
	 * Enable usage in items
	 * @param Items\Base $base
	 * @return bool
	 */
	public function applyOn(Items\Base $base)
	{
		$use = FALSE;

		if ($base instanceof Items\Text) {
			$use = TRUE;
		}

		return $use;
	}
	/**
	 * @param mixed $data
	 * @return mixed
	 */
	public function sanitize($data)
	{
		if ($data == '') {
			$data = '#';
		}
		else {
			$presenterString = $this->getUrl($data);

			if ($presenterString !== FALSE) {
				$data = $presenterString;
			}
		}

		return $data;
	}

	public function removedContent($rawData, $data)
	{
		$presenterString = $this->getUrl($rawData);

		if ($presenterString !== FALSE && $presenterString === $data) {
			return FALSE;
		}

		return parent::removedContent($rawData, $data);
	}

	protected function getUrl($presenterString)
	{
		if (preg_match('~^([\w:]+):(\w*+)(#[a-zA-Z][\w:.-]*)?(?:(?:,[ ]+\{)([a-zA-Z=>\{\},: \'"]+)(?:\}))?()\z~', $presenterString, $m)) {
			list(, $presenter, $action, $frag, $rawParameters) = $m;
			if (strlen($frag) > 0 && $frag[0] != '#') {
				$rawParameters = $frag;
				$frag = '';
			}

			try {
				$parameters = Nette\Utils\Json::decode('{' . $rawParameters . '}', Nette\Utils\Json::FORCE_ARRAY);
			}
			catch (Nette\Utils\JsonException $e) {
				$parameters = [];
			}
			if (is_null($parameters)) {
				$parameters = [];
			}

			//dump($m, $rawParameters, $parameters, ['a' => 'c'], Nette\Utils\Json::encode(['a' => 'c']), $this->linkGenerator->link(
			//	(empty($presenter) ? '' : $presenter . ':') . $action . $frag, $parameters
			//));

			return $this->linkGenerator->link(
				(empty($presenter) ? '' : $presenter . ':') . $action . $frag, $parameters
			);
		}

		return FALSE;
	}
}
