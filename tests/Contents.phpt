<?php

namespace Test;

use Nette,
	Tester,
	Tester\Assert,
	Trejjam,
	Trejjam\Utils\Contents;

$container = require __DIR__ . '/bootstrap.php';


class ContentsTest extends Tester\TestCase
{
	/**
	 * @var Nette\DI\Container
	 */
	private $container;
	/**
	 * @var Contents\Contents
	 */
	private $contents;

	function __construct(Nette\DI\Container $container)
	{
		$this->container = $container;
	}

	public function setUp()
	{
		$this->contents = $this->container->getService("utils.contents");
	}

	function testContents1()
	{
		$dataObject = $this->contents->getDataObject('testContent', '');

		Assert::same([
			'a' => [
				'a' => '',
				'b' => '',
				'c' => [
					['a' => '', 'b' => ['a' => '']],
					['a' => '', 'b' => ['a' => '']],
				],
			],
		], $dataObject->getContent());
		Assert::equal(Nette\Utils\ArrayHash::from([
			'a' => [
				'a' => '',
				'b' => '',
				'c' => [
					['a' => '', 'b' => ['a' => '']],
					['a' => '', 'b' => ['a' => '']],
				],
			],
		]), $dataObject->getContent(TRUE));

		Assert::throws(function () {
			$this->contents->getDataObject('testContent', 'abcd');
		}, \Trejjam\Utils\LogicException::class, NULL, Trejjam\Utils\Exception::CONTENTS_JSON_DECODE);

		Assert::throws(function () {
			$this->contents->getDataObject('notExistTestContent', 'abcd');
		}, \Trejjam\Utils\InvalidArgumentException::class, NULL, Trejjam\Utils\Exception::CONTENTS_MISSING_CONFIGURATION);
	}
	function testContents2()
	{
		$dataObject = $this->contents->getDataObject('testContent', ['a' => ['a' => 'bcd', 'c' => [['b' => ['a' => 'de', 'b' => 'foo']]]]]);

		Assert::same([
			'a' => [
				'a' => 'bcd',
				'b' => '',
				'c' => [
					['a' => '', 'b' => ['a' => 'de']],
					['a' => '', 'b' => ['a' => '']],
				],
			],
		], $dataObject->getContent());

		Assert::same([
			'a' => [
				'a' => 'bcd',
				'b' => NULL,
				'c' => [
					['a' => NULL, 'b' => ['a' => 'de']],
					['a' => NULL, 'b' => ['a' => NULL]],
				],
			],
		], $dataObject->getRawContent());

		Assert::same([
			'a' => [
				'c' => [
					['b' => ['b' => 'foo']],
				],
			],
		], $dataObject->getRemovedItems());
	}
	function testContents3()
	{
		$dataObject = $this->contents->getDataObject('testContent', ['a' => ['a' => 'bcd', 'c' => [['b' => ['a' => ['a' => 'de'], 'b' => 'foo']]]]]);

		Assert::same([
			'a' => [
				'a' => 'bcd',
				'b' => '',
				'c' => [
					['a' => '', 'b' => ['a' => '']],
					['a' => '', 'b' => ['a' => '']],
				],
			],
		], $dataObject->getContent());

		Assert::same([
			'a' => [
				'a' => 'bcd',
				'b' => NULL,
				'c' => [
					['a' => NULL, 'b' => ['a' => ['a' => 'de']]],
					['a' => NULL, 'b' => ['a' => NULL]],
				],
			],
		], $dataObject->getRawContent());

		Assert::same([
			'a' => [
				'c' => [
					['b' => ['a' => ['a' => 'de'], 'b' => 'foo']],
				],
			],
		], $dataObject->getRemovedItems());
	}

	function testList1()
	{
		/** @var Contents\Items\ListContainer $containerItem */
		$containerItem = Contents\Factory::getItemObject([
			'type'     => 'list',
			'listItem' => [
				'name'    => 'text',
				'content' => 'text',
			],
		], [
			['content' => 'abcd'],
			['content' => 'abcd', 'name' => 'abcdef'],
		]);

		Assert::same([0, 1], array_keys($containerItem->getChild()));
		Assert::same([
			['name' => '', 'content' => 'abcd'],
			['name' => 'abcdef', 'content' => 'abcd'],
		], $containerItem->getContent());

		Assert::throws(function () {
			/** @var Contents\Items\ListContainer $containerItem */
			$containerItem = Contents\Factory::getItemObject([
				'type' => 'list',
			], [['content' => 'abcd']]);
		}, Trejjam\Utils\DomainException::class, NULL, Trejjam\Utils\Exception::CONTENTS_INCOMPLETE_CONFIGURATION);
	}

	function testList2()
	{
		/** @var Contents\Items\ListContainer $containerItem */
		$containerItem = Contents\Factory::getItemObject([
			'type'  => 'list',
			'count' => 3,
			'child' => [
				'name'    => 'text',
				'content' => 'text',
			],
		], [
			['content' => 'abcd'],
			['content' => 'abcd', 'name' => 'abcdef'],
		]);

		Assert::same([0, 1, 2], array_keys($containerItem->getChild()));
		Assert::same([
			['name' => '', 'content' => 'abcd'],
			['name' => 'abcdef', 'content' => 'abcd'],
			['name' => '', 'content' => ''],
		], $containerItem->getContent());

		Assert::throws(function () {
			/** @var Contents\Items\ListContainer $containerItem */
			$containerItem = Contents\Factory::getItemObject([
				'type' => 'list',
			], [['content' => 'abcd']]);
		}, Trejjam\Utils\DomainException::class, NULL, Trejjam\Utils\Exception::CONTENTS_INCOMPLETE_CONFIGURATION);
	}

	function testList3()
	{
		/** @var Contents\Items\ListContainer $containerItem */
		$containerItem = Contents\Factory::getItemObject([
			'type'     => 'list',
			'max'      => 1,
			'listItem' => [
				'name'    => 'text',
				'content' => 'text',
			],
		], [
			['content' => 'abcd'],
			['content' => 'abcd', 'name' => 'abcdef'],
		]);

		Assert::same([0], array_keys($containerItem->getChild()));
		Assert::same([
			['name' => '', 'content' => 'abcd'],
		], $containerItem->getContent());

		Assert::same([
			1 => ['content' => 'abcd', 'name' => 'abcdef'],
		], $containerItem->getRemovedItems());

		Assert::throws(function () {
			/** @var Contents\Items\ListContainer $containerItem */
			$containerItem = Contents\Factory::getItemObject([
				'type'     => 'list',
				'max'      => 2,
				'count'    => 3,
				'listItem' => [

				],
			], [['content' => 'abcd']]);
		}, Trejjam\Utils\DomainException::class, NULL, Trejjam\Utils\Exception::CONTENTS_COLLISION_CONFIGURATION);
	}

	function testContainer1()
	{
		/** @var Contents\Items\Container $containerItem */
		$containerItem = Contents\Factory::getItemObject([
			'type'  => 'container',
			'child' => [
				'name'    => 'text',
				'content' => 'text',
			],
		], ['content' => 'abcd', 'foo' => 'abcd']);

		Assert::same(['name', 'content'], array_keys($containerItem->getChild()));
		Assert::same(['name' => '', 'content' => 'abcd'], $containerItem->getContent());
		Assert::same(['foo' => 'abcd'], $containerItem->getRemovedItems());

		Assert::throws(function () {
			/** @var Contents\Items\Container $containerItem */
			$containerItem = Contents\Factory::getItemObject([
				'type' => 'container',
			], ['content' => 'abcd']);
		}, Trejjam\Utils\DomainException::class, NULL, Trejjam\Utils\Exception::CONTENTS_INCOMPLETE_CONFIGURATION);
	}

	function testContainer2()
	{
		/** @var Contents\Items\Container $containerItem */
		$containerItem = Contents\Factory::getItemObject([
			'type'  => 'container',
			'child' => [
				'name'    => 'text',
				'content' => [
					'type'  => 'container',
					'child' => [
						'foo'  => 'text',
						'text' => 'text',
					],
				],
			],
		], ['content' => ['text' => 'abcd', 'oldFoo' => 'efgh']]);

		Assert::same(['name', 'content'], array_keys($containerItem->getChild()));
		Assert::same(['name' => '', 'content' => ['foo' => '', 'text' => 'abcd']], $containerItem->getContent());
		Assert::same(['content' => ['oldFoo' => 'efgh']], $containerItem->getRemovedItems());
	}

	function testText1()
	{
		/** @var Contents\Items\Text $textItem */
		$textItem = Contents\Factory::getItemObject('text', 'abd');

		Assert::same('abd', $textItem->getContent());

		Assert::throws(function () {
			Contents\Factory::getItemObject('', ['abd']);
		}, Trejjam\Utils\InvalidArgumentException::class, NULL, Trejjam\Utils\Exception::CONTENTS_UNKNOWN_ITEM_TYPE);
	}

	function testText2()
	{
		$textItem = Contents\Factory::getItemObject(['type' => 'text'], ['abd']);
		Assert::same('', $textItem->getContent());
		Assert::same(['abd'], $textItem->getRemovedItems());
	}
}

$test = new ContentsTest($container);
$test->run();
