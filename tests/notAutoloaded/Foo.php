<?php declare(strict_types = 1);

namespace SomeOtherNamespace\Tests\NonAutoloaded;

class Foo
{

	const FOO_CONST = 'foo';

	/** @var string */
	private $fooProperty;

	public function doFoo(): string
	{
		$this->fooProperty = 'test';

		return $this->fooProperty;
	}

}
