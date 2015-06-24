<?php
/**
 * Created by PhpStorm.
 * User: jam
 * Date: 19.6.15
 * Time: 5:30
 */

namespace Trejjam\Utils\Contents\Items;


use Nette,
	Trejjam;

class Text extends Base
{
	protected function sanitizeData($data)
	{
		return is_scalar($data) ? $data : '';
	}

	/**
	 * @param bool|FALSE $forceObject
	 * @return string
	 */
	public function getContent($forceObject = FALSE)
	{
		return $this->data;
	}

	/**
	 * @param bool|FALSE $forceObject
	 * @return mixed
	 */
	public function getRawContent($forceObject = FALSE)
	{
		return $this->rawData;
	}

	public function getRemovedItems()
	{
		$out = $this->rawData !== $this->data ? $this->rawData : NULL;

		list(, , $out) = $this->useSubType(function (SubType $subtype, array $inData) {
			list($data, $rawData, $previous) = $inData;

			$out = [
				$data,
				$rawData,
				$previous,
			];

			if ($previous !== FALSE) {
				$subRemoved = $subtype->removedContent($rawData, $data);

				if (!is_null($subRemoved)) {
					$out[2] = $subRemoved;
				}
			}

			return $out;
		}, [
			$this->data,
			$this->rawData,
			$out,
		]);

		return $out === FALSE ? NULL : $out;
	}

	public function generateForm(Base $item, Nette\Forms\Container &$formContainer, $name, $parentName, array &$ids, array $userOptions = [])
	{
		$addFormItem = $this->useSubType(function (SubType $subType, $addFormItem) use ($formContainer, $name, $parentName, $ids, $userOptions) {
			if ($subType instanceof IEditItem) {
				$subType->generateForm($this, $formContainer, $name, $parentName, $ids, $userOptions);

				return FALSE;
			}

			return $addFormItem;
		}, TRUE);

		if ($addFormItem) {
			$input = $formContainer->addText($name, $name);
			$input->setOption('id', $ids[] = $parentName . '__' . $name);
			$input->setValue($item->getRawContent());

			$item->applyUserOptions($input, $userOptions);
		}
	}

	public function update($data)
	{
		$oldData = $this->rawData;

		parent::update($data);

		if ($this->isUpdated) {
			$this->updated = is_null($oldData) ? self::EMPTY_VALUE : $oldData;
		}
	}
}
