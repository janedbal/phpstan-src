<?php

namespace Bug7068;

use function PHPStan\Testing\assertType;

class Foo
{

	/**
	 * @template T
	 * @param array<T> ...$arrays
	 * @return array<T>
	 */
	function merge(array ...$arrays): array {
		return array_merge(...$arrays);
	}

	public function doFoo(): void
	{
		assertType('array<int>', $this->merge([1, 2], [3, 4], [5]));
		assertType('array<int|string>', $this->merge([1, 2], ['foo', 'bar']));
	}

}
