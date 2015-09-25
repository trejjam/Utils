<?php
/**
 * Created by PhpStorm.
 * User: Jan
 * Date: 3.5.14
 * Time: 19:06
 */

namespace Trejjam\Utils\Helpers;

use Nette,
	Trejjam;

/**
 * Manage shopping cart
 * @package App\Model
 */
abstract class AShopCart extends Nette\Object
{
	const
		COMMENT = '_comment-',
		INFORMATION = '_information-';

	/**
	 * @var Nette\Http\SessionSection
	 */
	protected $shopCart;
	/**
	 * @var Trejjam\Utils\Labels\Labels
	 */
	protected $labels;
	/**
	 * @var Nette\Security\User
	 */
	protected $user;

	public function __construct(Nette\Http\SessionSection $shopCart, Trejjam\Utils\Labels\Labels $labels, Nette\Security\User $user)
	{
		$this->shopCart = $shopCart;
		$this->shopCart->setExpiration('14 days');

		$this->labels = $labels;
		$this->user = $user;

		if (isset($this->shopCart->_userId) && $this->shopCart->_userId != $this->user->getId()) {
			$this->cartClear();
		}
		if (!isset($this->shopCart->_userId)) {
			$this->shopCart->_userId = $this->user->getId();
		}
	}

	/**
	 * @param string $id
	 * @param int    $count
	 */
	public function addItem($id, $count)
	{
		$this->shopCart->$id = $count;
	}
	/**
	 * @param string $id
	 */
	public function removeItem($id)
	{
		if (isset($this->shopCart->$id)) {
			unset($this->shopCart->$id);
		}
	}
	/**
	 * @param string $id
	 * @return int
	 */
	public function getItemCount($id)
	{
		if (isset($this->shopCart->$id) && $this->shopCart->$id > 0) {
			return $this->shopCart->$id;
		}

		return 0;
	}
	/**
	 * @return array
	 */
	public function getCart()
	{
		$out = array();
		foreach ($this->shopCart as $k => $v) {
			if (!Nette\Utils\Strings::startsWith($k, '_') && $v > 0) {
				$out[$k] = $v;
			}
		}

		ksort($out);

		return $out;
	}
	/**
	 * @return int
	 */
	public function getCount()
	{
		return count($this->getCart());
	}

	public function getItemsCount()
	{
		$out = 0;

		foreach ($this->getCart() as $v) {
			$out += $v;
		}

		return $out;
	}
	/**
	 * @return int
	 */
	public abstract function getSum();

	/**
	 *
	 */
	public function cartClear()
	{
		$this->shopCart->remove();
	}

	/**
	 * @param $id
	 * @param $text
	 */
	public function editComment($id, $text)
	{
		$this->shopCart->{static::COMMENT . $id} = $text;
	}
	/**
	 * @param $id
	 * @return string
	 */
	public function getComment($id)
	{
		return isset($this->shopCart->{static::COMMENT . $id}) ? $this->shopCart->{static::COMMENT . $id} : '';
	}

	public function getInformation($id)
	{
		return isset($this->shopCart->{static::INFORMATION . $id}) ? $this->shopCart->{static::INFORMATION . $id} : NULL;
	}
	public function setInformation($id, $information)
	{
		$this->shopCart->{static::INFORMATION . $id} = $information;
	}
}
