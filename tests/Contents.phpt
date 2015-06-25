<?php

namespace Test;

use Nette,
	Tester,
	Tester\Assert,
	Trejjam,
	Trejjam\Utils\Contents,
	PresenterTester\PresenterTester;

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
		/** @var Contents\Items\Container $dataObject */
		$dataObject = $this->contents->getDataObject('testContent', NULL);

		Assert::same([
			'a' => [
				'a' => '#',
				'b' => '',
				'c' => [
					['a' => '', 'b' => ['a' => '']],
					['a' => '', 'b' => ['a' => '']],
				],
			],
			'b' => [],
			'c' => FALSE,
		], $dataObject->getContent());

		Assert::equal(Nette\Utils\ArrayHash::from([
			'a' => [
				'a' => '#',
				'b' => '',
				'c' => [
					['a' => '', 'b' => ['a' => '']],
					['a' => '', 'b' => ['a' => '']],
				],
			],
			'b' => [],
			'c' => FALSE,
		]), $dataObject->getContent(TRUE));

		Assert::throws(function () {
			$this->contents->getDataObject('testContent', 'abcd');
		}, \Trejjam\Utils\LogicException::class, NULL, Trejjam\Utils\Exception::CONTENTS_JSON_DECODE);

		Assert::throws(function () {
			$this->contents->getDataObject('notExistTestContent', 'abcd');
		}, \Trejjam\Utils\InvalidArgumentException::class, NULL, Trejjam\Utils\Exception::CONTENTS_MISSING_CONFIGURATION);

		/**
		 *
		 * @var $tester        PresenterTester
		 * @var $presenterPost Nette\Application\UI\Presenter
		 */
		list($tester, $presenterPost) = $this->getPresenter();
		$form = $this->contents->createForm($dataObject, [
			'child' => [
				'a' => [
					'child' => [
						'a' => [
							'class' => 'foo',
						],
					],
				],
			],
		], 'testContent.update');
		$presenterPost->addComponent($form, 'contentsForm');

		$tester->run();

		ob_start();
		$form->render();
		$formHtml = ob_get_clean();

		$formDom = Tester\DomQuery::fromHtml($formHtml);

		Assert::true($formDom->has('form input.foo[name=\'root[a][a]\']'));

		Assert::same('{"a":{"a":null,"b":null,"c":[{"a":null,"b":{"a":null}},{"a":null,"b":{"a":null}}]},"b":[],"c":null}', $dataObjectJson = (string)$dataObject);

		/** @var Contents\Items\Container $dataObject */
		$dataObject2 = $this->contents->getDataObject('testContent', $dataObjectJson);

		Assert::same($dataObjectJson, (string)$dataObject2);
	}
	function testContents2()
	{
		/** @var Contents\Items\Container $dataObject */
		$dataObject = $this->contents->getDataObject('testContent', Nette\Utils\Json::encode([
			'a' => ['a' => 'bcd', 'c' => ['abc' => 'foo', 'foo2', ['b' => ['a' => 'de', 'b' => 'foo']]]],
			'c' => 'foo',
		]));

		Assert::same([
			'a' => [
				'a' => 'bcd',
				'b' => '',
				'c' => [
					['a' => '', 'b' => ['a' => '']],
					['a' => '', 'b' => ['a' => 'de']],
				],
			],
			'b' => [],
			'c' => FALSE,
		], $dataObject->getContent());

		Assert::same([
			'a' => [
				'a' => 'bcd',
				'b' => NULL,
				'c' => [
					['a' => NULL, 'b' => ['a' => NULL]],
					['a' => NULL, 'b' => ['a' => 'de']],
				],
			],
			'b' => [],
			'c' => 'foo',
		], $dataObject->getRawContent());

		Assert::same([
			'a' => [
				'c' => [
					'foo2',
					['b' => ['b' => 'foo']],
					'abc' => 'foo', //non numeric key removed directly!
				],
			],
			'c' => 'foo',
		], $dataObject->getRemovedItems());
	}
	function testContents3()
	{
		/** @var Contents\Items\Container $dataObject */
		$dataObject = $this->contents->getDataObject('testContent', [
			'a' => ['a' => 'Homepage:default#abcd, {"a":"b"}', 'c' => [['b' => ['a' => ['a' => 'de'], 'b' => 'foo']], 'abcd']],
			'b' => [
				['a' => '#'],
				['a' => ''],
				['a' => 'Homepage:default#abcd'],
				['a' => 'Homepage:default, {"a":"b"}'],
				['a' => 'Homepage:default'],
				['a' => 'Homepage:'],
			],
			'c' => TRUE,
		]);

		Assert::same([
			'a' => [
				'a' => 'http://localhost.tld/?a=b#abcd',
				'b' => '',
				'c' => [
					['a' => '', 'b' => ['a' => '']],
					['a' => '', 'b' => ['a' => '']],
				],
			],
			'b' => [
				['a' => '#'],
				['a' => '#'],
				['a' => 'http://localhost.tld/#abcd'],
				['a' => 'http://localhost.tld/?a=b'],
				['a' => 'http://localhost.tld/'],
				['a' => 'http://localhost.tld/'],
			],
			'c' => TRUE,
		], $dataObject->getContent());

		Assert::same([
			'a' => [
				'a' => 'Homepage:default#abcd, {"a":"b"}',
				'b' => NULL,
				'c' => [
					['a' => NULL, 'b' => ['a' => ['a' => 'de']]],
					['a' => NULL, 'b' => ['a' => NULL]],
				],
			],
			'b' => [
				['a' => '#'],
				['a' => ''],
				['a' => 'Homepage:default#abcd'],
				['a' => 'Homepage:default, {"a":"b"}'],
				['a' => 'Homepage:default'],
				['a' => 'Homepage:'],
			],
			'c' => TRUE,
		], $dataObject->getRawContent());

		Assert::same([
			'a' => [
				'c' => [
					[
						'b' => [
							'a' => ['a' => 'de'], 'b' => 'foo'
						],
					],
					'abcd',
				],
			],
			'b' => [
				1 => ['a' => ''],
			],
		], $dataObject->getRemovedItems());

		Assert::same('{"a":{"a":"Homepage:default#abcd, {\"a\":\"b\"}","b":null,"c":[{"a":null,"b":{"a":{"a":"de"}}},{"a":null,"b":{"a":null}}]},"b":[{"a":"#"},{"a":""},{"a":"Homepage:default#abcd"},{"a":"Homepage:default, {\"a\":\"b\"}"},{"a":"Homepage:default"},{"a":"Homepage:"}],"c":true}', $dataObjectJson = (string)$dataObject);

		/** @var Contents\Items\Container $dataObject */
		$dataObject2 = $this->contents->getDataObject('testContent', $dataObjectJson);

		Assert::same($dataObjectJson, (string)$dataObject2);
	}
	function testContents4()
	{
		/** @var Contents\Items\Container $dataObject */
		$dataObject = $this->contents->getDataObject('testContent', [
			'a' => [
				'a' => 'Homepage:default#abcd, {"a":"b"}',
				'c' => [
					['b' => ['a' => ['a' => 'de'], 'b' => 'foo']],
					'abcd',
				],
			],
			'b' => [
				['a' => '#'],
				['a' => ''],
				['a' => 'Homepage:default#abcd'],
				['a' => 'Homepage:default, {"a":"b"}'],
				['a' => 'Homepage:default'],
				['a' => 'Homepage:'],
			],
			'c' => TRUE,
		]);

		Assert::same([
			'a' => [
				'a' => 'http://localhost.tld/?a=b#abcd',
				'b' => '',
				'c' => [
					['a' => '', 'b' => ['a' => '']],
					['a' => '', 'b' => ['a' => '']],
				],
			],
			'b' => [
				['a' => '#'],
				['a' => '#'],
				['a' => 'http://localhost.tld/#abcd'],
				['a' => 'http://localhost.tld/?a=b'],
				['a' => 'http://localhost.tld/'],
				['a' => 'http://localhost.tld/'],
			],
			'c' => TRUE,
		], $dataObject->getContent());

		Assert::same([
			'a' => [
				'a' => 'Homepage:default#abcd, {"a":"b"}',
				'b' => NULL,
				'c' => [
					['a' => NULL, 'b' => ['a' => ['a' => 'de']]],
					['a' => NULL, 'b' => ['a' => NULL]],
				],
			],
			'b' => [
				['a' => '#'],
				['a' => ''],
				['a' => 'Homepage:default#abcd'],
				['a' => 'Homepage:default, {"a":"b"}'],
				['a' => 'Homepage:default'],
				['a' => 'Homepage:'],
			],
			'c' => TRUE,
		], $dataObject->getRawContent());

		Assert::same([
			'a' => [
				'c' => [
					[
						'b' => [
							'a' => ['a' => 'de'], 'b' => 'foo'
						],
					],
					'abcd',
				],
			],
			'b' => [
				1 => ['a' => ''],
			],
		], $dataObject->getRemovedItems());

		Assert::same('{"a":{"a":"Homepage:default#abcd, {\"a\":\"b\"}","b":null,"c":[{"a":null,"b":{"a":{"a":"de"}}},{"a":null,"b":{"a":null}}]},"b":[{"a":"#"},{"a":""},{"a":"Homepage:default#abcd"},{"a":"Homepage:default, {\"a\":\"b\"}"},{"a":"Homepage:default"},{"a":"Homepage:"}],"c":true}', $dataObjectJson = (string)$dataObject);

		/** @var Contents\Items\Container $dataObject */
		$dataObject2 = $this->contents->getDataObject('testContent', $dataObjectJson);

		Assert::same($dataObjectJson, (string)$dataObject2);

		/**
		 *
		 * @var $tester        PresenterTester
		 * @var $presenterPost Nette\Application\UI\Presenter
		 */
		list($tester, $presenterPost) = $this->getPresenter();
		$form = $this->contents->createForm($dataObject, [
			'fields' => [
				'a' => [
					'child' => [
						'a' => [
							'class' => 'foo',
						],
					],
				],
			],
		], 'testContent.update', ['a', 'c']);
		$presenterPost->addComponent($form, 'contentsForm');

		$tester->run();

		ob_start();
		$form->render();
		$formHtml = ob_get_clean();

		$formDom = Tester\DomQuery::fromHtml($formHtml);

		Assert::true($formDom->has('form input.foo[name=\'a[a]\']'));
		Assert::false($formDom->has('form input[name=\'b\']'));
		Assert::true($formDom->has('form input[name=\'c\']'));
	}

	function testList1()
	{
		/** @var Contents\Items\ListContainer $listItem */
		$listItem = Contents\Factory::getItemObject([
			'type'     => 'list',
			'listHead' => 'name',
			'listItem' => [
				'name'    => 'text',
				'content' => 'text',
			],
		], [
			['content' => 'abcd'],
			['content' => 'abcd', 'name' => 'abcdef'],
		]);

		Assert::same([0, 1], array_keys($listItem->getChild()));
		Assert::same([
			['name' => '', 'content' => 'abcd'],
			['name' => 'abcdef', 'content' => 'abcd'],
		], $listItem->getContent());

		Assert::throws(function () {
			/** @var Contents\Items\ListContainer $containerItem */
			$containerItem = Contents\Factory::getItemObject([
				'type' => 'list',
			], [['content' => 'abcd']]);
		}, Trejjam\Utils\DomainException::class, NULL, Trejjam\Utils\Exception::CONTENTS_INCOMPLETE_CONFIGURATION);


		/**
		 * @var $tester        PresenterTester
		 * @var $presenterPost Nette\Application\UI\Presenter
		 */
		list($tester, $presenterPost) = $this->getPresenter();
		$form = $this->contents->createForm($listItem, [], 'testContent.update');
		$presenterPost->addComponent($form, 'contentsForm');

		$tester->run();

		$form->setValues([
			'root' => [
				['name' => 'new_value', 'content' => 'new_value2']
			],
		]);
		$form->onSuccess($form);

		ob_start();
		$form->render();
		$formHtml = ob_get_clean();

		$formDom = Tester\DomQuery::fromHtml($formHtml);

		Assert::true($formDom->has('form'));
		Assert::true($formDom->has('form input[name=\'root[' . Contents\Items\ListContainer::NEW_ITEM . ']\']'));
		Assert::true($formDom->has('form select[name=\'root[' . Contents\Items\ListContainer::LIST_BOX . ']\'] option[value=\'0\']'));
		Assert::true($formDom->has('form select[name=\'root[' . Contents\Items\ListContainer::LIST_BOX . ']\'] option[value=\'abcdef\']'));
		Assert::true($formDom->has('form input[id=__root__new__]'));

		Assert::same([
			['name' => 'new_value', 'content' => 'new_value2',],
			['name' => 'abcdef', 'content' => 'abcd'],
		], $listItem->getRawContent());
		Assert::same([
			['name' => 'new_value', 'content' => 'new_value2',],
			['name' => 'abcdef', 'content' => 'abcd'],
		], $listItem->getContent());

		Assert::same([
			['name' => Contents\Items\Base::EMPTY_VALUE, 'content' => 'abcd'],
		], $listItem->getUpdated());


		$form2 = $this->contents->createForm($listItem, [], 'testContent.update');
		$presenterPost->addComponent($form2, 'contentsForm2');

		ob_start();
		$form2->render();
		$form2Html = ob_get_clean();

		$form2Dom = Tester\DomQuery::fromHtml($form2Html);

		Assert::true($form2Dom->has('form'));
		Assert::true($form2Dom->has('form input[name=\'root[' . Contents\Items\ListContainer::NEW_ITEM . ']\']'));
		Assert::true($form2Dom->has('form select[name=\'root[' . Contents\Items\ListContainer::LIST_BOX . ']\'] option[value=\'new_value\']'));
		Assert::true($form2Dom->has('form select[name=\'root[' . Contents\Items\ListContainer::LIST_BOX . ']\'] option[value=\'abcdef\']'));


		$form3 = $this->contents->createForm($listItem, [
			'listHead' => 'not.exist.key',
		], 'testContent.update');
		$presenterPost->addComponent($form3, 'contentsForm3');

		ob_start();
		$form3->render();
		$form3Html = ob_get_clean();

		$form3Dom = Tester\DomQuery::fromHtml($form3Html);

		Assert::true($form3Dom->has('form'));
		Assert::true($form3Dom->has('form input[name=\'root[' . Contents\Items\ListContainer::NEW_ITEM . ']\']'));
		Assert::true($form3Dom->has('form select[name=\'root[' . Contents\Items\ListContainer::LIST_BOX . ']\'] option[value=\'0\']'));
		Assert::true($form3Dom->has('form select[name=\'root[' . Contents\Items\ListContainer::LIST_BOX . ']\'] option[value=\'1\']'));

		Assert::throws(function () use ($listItem) {
			$this->contents->createForm($listItem, [], 'testContent.update', [
				'notExistKey'
			]);
		}, Trejjam\Utils\LogicException::class, NULL, Trejjam\Utils\Exception::CONTENTS_CHILD_NOT_EXIST);
	}
	function testList2()
	{
		/** @var Contents\Items\ListContainer $listItem */
		$listItem = Contents\Factory::getItemObject([
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

		Assert::same([0, 1, 2], array_keys($listItem->getChild()));
		Assert::same([
			['name' => '', 'content' => 'abcd'],
			['name' => 'abcdef', 'content' => 'abcd'],
			['name' => '', 'content' => ''],
		], $listItem->getContent());

		Assert::throws(function () {
			/** @var Contents\Items\ListContainer $containerItem */
			$containerItem = Contents\Factory::getItemObject([
				'type' => 'list',
			], [['content' => 'abcd']]);
		}, Trejjam\Utils\DomainException::class, NULL, Trejjam\Utils\Exception::CONTENTS_INCOMPLETE_CONFIGURATION);
	}
	function testList3()
	{
		/** @var Contents\Items\ListContainer $listItem */
		$listItem = Contents\Factory::getItemObject([
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

		Assert::same([0], array_keys($listItem->getChild()));
		Assert::same([
			['name' => '', 'content' => 'abcd'],
		], $listItem->getContent());

		Assert::same([
			1 => ['content' => 'abcd', 'name' => 'abcdef'],
		], $listItem->getRemovedItems());

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

		/**
		 * @var $tester        PresenterTester
		 * @var $presenterPost Nette\Application\UI\Presenter
		 */
		list($tester, $presenterPost) = $this->getPresenter();
		$form = $this->contents->createForm($listItem, [
			'listHead' => 'name',
		], 'testContent.update');
		$presenterPost->addComponent($form, 'contentsForm');

		$tester->run();

		ob_start();
		$form->render();
		$formHtml = ob_get_clean();

		$formDom = Tester\DomQuery::fromHtml($formHtml);

		Assert::true($formDom->has('form'));
		Assert::false($formDom->has('form input[name=\'root[' . Contents\Items\ListContainer::NEW_ITEM . ']\']'));
		Assert::true($formDom->has('form select[name=\'root[' . Contents\Items\ListContainer::LIST_BOX . ']\'] option[value=\'0\']'));
		Assert::false($formDom->has('form select[name=\'root[' . Contents\Items\ListContainer::LIST_BOX . ']\'] option[value=\'abcd\']'));

		$form->setValues([
			'root' => [
				['name' => 'new_value', 'content' => 'new_value2']
			],
		]);
		$form->onSuccess($form);

		Assert::same([
			['name' => 'new_value', 'content' => 'new_value2',]
		], $listItem->getRawContent());
		Assert::same([
			['name' => 'new_value', 'content' => 'new_value2',]
		], $listItem->getContent());

		Assert::same('[{"name":"new_value","content":"new_value2"}]', (string)$listItem);
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

		/**
		 * @var $tester        PresenterTester
		 * @var $presenterPost Nette\Application\UI\Presenter
		 */
		list($tester, $presenterPost) = $this->getPresenter();
		$form = $this->contents->createForm($containerItem, [], 'testContent.update');
		$presenterPost->addComponent($form, 'contentsForm');

		$tester->run();

		$form->setValues([
			'root' => [
				'name'    => 'new_value',
				'content' => [
					'text' => 'new_value_3',
				],
			],
		]);
		$form->onSuccess($form);

		Assert::same([
			'name'    => 'new_value',
			'content' => [
				'foo'  => '',
				'text' => 'new_value_3',
			],
		], $containerItem->getRawContent());
		Assert::same([
			'name'    => 'new_value',
			'content' => [
				'foo'  => '',
				'text' => 'new_value_3',
			],
		], $containerItem->getContent());

		Assert::same('{"name":"new_value","content":{"foo":"","text":"new_value_3"}}', (string)$containerItem);
	}

	function testText1()
	{
		/** @var Contents\Items\Text $textItem */
		$textItem = Contents\Factory::getItemObject('text', 'abd');

		Assert::same('abd', $textItem->getContent());

		Assert::throws(function () {
			Contents\Factory::getItemObject('', ['abd']);
		}, Trejjam\Utils\InvalidArgumentException::class, NULL, Trejjam\Utils\Exception::CONTENTS_UNKNOWN_ITEM_TYPE);

		$tester = new PresenterTester($this->container->getByType('\Nette\Application\IPresenterFactory'));
		$tester->setPresenter('Homepage');
		/** @var  $presenter */
		$presenter = $tester->getPresenterComponent();
		$form = $this->contents->createForm($textItem, [], 'testContent.update');

		Assert::true($form instanceof Nette\Application\UI\Form);
		$presenter->addComponent($form, 'contentsForm');
		$tester->run();

		ob_start();
		$form->render();
		$formHtml = ob_get_clean();

		$formDom = Tester\DomQuery::fromHtml($formHtml);

		Assert::true($formDom->has('form'));
		Assert::true($formDom->has('form input[name="root"]'));


		$form = $this->contents->createForm($textItem, [], 'testContent.update');
		$form->addSubmit('send', 'send');
		Assert::true($form instanceof Nette\Application\UI\Form);

		/**
		 * @var $tester        PresenterTester
		 * @var $presenterPost Nette\Application\UI\Presenter
		 */
		list($tester, $presenterPost) = $this->getPresenter();
		$tester->setHandle('contentsForm-submit');
		$tester->setParams(['submit' => 'send']);
		$tester->setPost([
			'do'     => 'contentsForm-submit',
			'root'   => 'new_value',
			'send'   => 'send',
			'submit' => '',
			//Nette\Application\UI\Form::PROTECTOR_ID => '',//$protector->getControl()->attrs['value'],
		]);
		$presenterPost->addComponent($form, 'contentsForm');

		$tester->run();

		//Assert::truthy($form->isSubmitted());

		$form->setValues([
			'root' => 'new_value',
			//Nette\Application\UI\Form::PROTECTOR_ID => $protector->getControl()->attrs['value'],
			'send' => 'send',
		]);
		$form->onSuccess($form);

		Assert::same('new_value', $textItem->getRawContent());
		Assert::same('new_value', $textItem->getContent());

		Assert::same('new_value', (string)$textItem);
	}
	function testText2()
	{
		$textItem = Contents\Factory::getItemObject(['type' => 'text'], ['abd']);
		Assert::same('', $textItem->getContent());
		Assert::same(['abd'], $textItem->getRemovedItems());
	}

	/**
	 * @param string $presenterName
	 * @return array(PresenterTester, Nette\Application\UI\Presenter)
	 */
	function getPresenter($presenterName = 'Homepage')
	{
		$tester = new PresenterTester($this->container->getByType('\Nette\Application\IPresenterFactory'));
		$tester->clean();
		$tester->setPresenter($presenterName);

		$presenter = $tester->getPresenterComponent();

		//$presenter->invalidLinkMode = 0;

		return [$tester, $presenter];
	}
}

$test = new ContentsTest($container);
$test->run();
