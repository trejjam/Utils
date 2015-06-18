<?php
/**
 * Labels Tests
 * Created by PhpStorm.
 * User: jam
 * Date: 21.12.14
 * Time: 18:24
 */
namespace Test;

use Nette,
	Tester,
	Tester\Assert,
	Trejjam,
	Kdyby,
	Symfony;


$container = require_once __DIR__ . '/bootstrap.php';

class LabelsTest extends Tester\TestCase
{
	/**
	 * @var Nette\DI\Container
	 */
	private $container;
	/**
	 * @var \Trejjam\Utils\Labels\Labels
	 */
	private $labels;

	function __construct(Nette\DI\Container $container)
	{
		$this->container = $container;
	}

	public function setUp()
	{
		/** @var Kdyby\Console\Application $install */
		$install = $this->container->getService('console.application');
		$install->run(new Symfony\Component\Console\Input\ArgvInput(['', 'Utils:install']), new Symfony\Component\Console\Output\NullOutput());

		$this->labels = $this->container->getService("utils.labels");

		foreach ($this->labels->getNamespaces() as $v) {
			foreach ($this->labels->getKeys($v) as $v2) {
				$this->labels->delete($v2, $v);
			}
		}
	}

	public function testLabels()
	{
		$this->labels->setData("key1", "value1");
		$this->labels->setData("key2", "value2");
		$this->labels->setData("key3", "value3");
		$this->labels->setData("key4", "value4");
		$this->labels->setData("key5", "value5");

		$this->labels->setData("key1", "value12", "backend");
		$this->labels->setData("key3", "value32", "backend");
		$this->labels->setData("key5", "value52", "backend");

		Assert::same($this->labels->getData("key1"), "value1");
		Assert::same($this->labels->getData("key2"), "value2");
		Assert::same($this->labels->getData("key3"), "value3");
		Assert::same($this->labels->getData("key4"), "value4");
		Assert::same($this->labels->getData("key5"), "value5");

		Assert::same($this->labels->getData("key1", "backend"), "value12");
		Assert::same($this->labels->getData("key2", "backend"), "value2");
		Assert::same($this->labels->getData("key3", "backend"), "value32");
		Assert::same($this->labels->getData("key4", "backend"), "value4");
		Assert::same($this->labels->getData("key5", "backend"), "value52");

		$this->labels->reInit();

		Assert::same($this->labels->getData("key1"), "value1");
		Assert::same($this->labels->getData("key2"), "value2");
		Assert::same($this->labels->getData("key3"), "value3");
		Assert::same($this->labels->getData("key4"), "value4");
		Assert::same($this->labels->getData("key5"), "value5");

		Assert::same($this->labels->getData("key1", "backend"), "value12");
		Assert::same($this->labels->getData("key2", "backend"), "value2");
		Assert::same($this->labels->getData("key3", "backend"), "value32");
		Assert::same($this->labels->getData("key4", "backend"), "value4");
		Assert::same($this->labels->getData("key5", "backend"), "value52");

		$this->labels->delete("key1");
		$this->labels->delete("key3", "backend");
		$this->labels->delete("key4");

		Assert::exception(function () {
			$this->labels->delete("key4");
		}, "Exception", "Label with name key4 and default namespace not exist.");

		Assert::exception(function () {
			$this->labels->delete("key4", "backend");
		}, "Exception", "Label with name key4 and backend namespace not exist.");

		$this->labels->delete("key5");
		$this->labels->delete("key5", "backend");

		Assert::same($this->labels->getData("key1"), "");
		Assert::same($this->labels->getData("key2"), "value2");
		Assert::same($this->labels->getData("key3"), "value3");
		Assert::same($this->labels->getData("key4"), "");
		Assert::same($this->labels->getData("key5"), "");

		Assert::same($this->labels->getData("key1", "backend"), "value12");
		Assert::same($this->labels->getData("key2", "backend"), "value2");
		Assert::same($this->labels->getData("key3", "backend"), "value3");
		Assert::same($this->labels->getData("key4", "backend"), "");
		Assert::same($this->labels->getData("key5", "backend"), "");

		$this->labels->reInit();

		Assert::same($this->labels->getData("key1"), "");
		Assert::same($this->labels->getData("key2"), "value2");
		Assert::same($this->labels->getData("key3"), "value3");
		Assert::same($this->labels->getData("key4"), "");
		Assert::same($this->labels->getData("key5"), "");

		Assert::same($this->labels->getData("key1", "backend"), "value12");
		Assert::same($this->labels->getData("key2", "backend"), "value2");
		Assert::same($this->labels->getData("key3", "backend"), "value3");
		Assert::same($this->labels->getData("key4", "backend"), "");
		Assert::same($this->labels->getData("key5", "backend"), "");
	}

	public function testLabelsObject()
	{
		$this->labels->setData("key12", "value13");
		$this->labels->setData("key22", "value23");
		$this->labels->setData("key32", "value33");
		$this->labels->setData("key42", "value43");
		$this->labels->setData("key52", "value53");

		$this->labels->setData("key12", "value14", "backend2");
		$this->labels->setData("key32", "value34", "backend2");
		$this->labels->setData("key52", "value54", "backend2");

		Assert::same((string)$this->labels->key12, "value13");
		Assert::same((string)$this->labels->key22, "value23");
		Assert::same((string)$this->labels->key32, "value33");
		Assert::same((string)$this->labels->key42, "value43");
		Assert::same((string)$this->labels->key52, "value53");

		Assert::same((string)$this->labels->backend2->key12, "value14");
		Assert::same((string)$this->labels->backend2->key22, "value23");
		Assert::same((string)$this->labels->backend2->key32, "value34");
		Assert::same((string)$this->labels->backend2->key42, "value43");
		Assert::same((string)$this->labels->backend2->key52, "value54");

		$this->labels->key12 = "value16";
		$this->labels->backend2->key12 = "value17";

		$this->labels->key22 = NULL;
		$this->labels->backend2->key32 = NULL;

		$this->labels->key52 = NULL;
		$this->labels->backend2->key52 = NULL;

		Assert::same((string)$this->labels->key12, "value16");
		Assert::same((string)$this->labels->key22, "");
		Assert::same((string)$this->labels->key32, "value33");
		Assert::same((string)$this->labels->key42, "value43");
		Assert::same((string)$this->labels->key52, "");

		Assert::same((string)$this->labels->backend2->key12, "value17");
		Assert::same((string)$this->labels->backend2->key22, "");
		Assert::same((string)$this->labels->backend2->key32, "value33");
		Assert::same((string)$this->labels->backend2->key42, "value43");
		Assert::same((string)$this->labels->backend2->key52, "");
	}
}

$test = new LabelsTest($container);
$test->run();
