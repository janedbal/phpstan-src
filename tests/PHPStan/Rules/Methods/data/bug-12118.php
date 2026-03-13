<?php

namespace Bug12118;

class Foo
{
	public function test(): void
	{
		$closure = static function () {
			self::instanceMethod();
		};
		$closure();
	}

	public function instanceMethod(): void
	{
	}

	public function nonStaticClosure(): void
	{
		$closure = function () {
			self::instanceMethod(); // OK - $this is available
		};
		$closure();
	}

	public static function staticMethod(): void
	{
		$closure = static function () {
			self::instanceMethod();
		};
		$closure();
	}

	public function nestedStaticClosure(): void
	{
		$closure = function () {
			$inner = static function () {
				self::instanceMethod();
			};
			$inner();
		};
		$closure();
	}

	public function arrowFunction(): void
	{
		$fn = static fn () => self::instanceMethod();
	}

	public static function staticInstanceMethod(): void
	{
	}

	public function staticClosureCallsStatic(): void
	{
		$closure = static function () {
			self::staticInstanceMethod(); // OK - static method
		};
		$closure();
	}
}
