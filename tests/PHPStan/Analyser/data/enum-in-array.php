<?php

use function PHPStan\Testing\assertType;

enum MyEnum: string
{

	case A = 'a';
	case B = 'b';
	case C = 'c';

	const SET_AB = [self::A, self::B];
	const SET_C = [self::C];
	const SET_ABC = [self::A, self::B, self::C];

	public function test1(): void
	{
		foreach (self::cases() as $enum) {
			if (in_array($enum, MyEnum::SET_AB, true)) {
				assertType('MyEnum::A|MyEnum::B', $enum);
			} elseif (in_array($enum, MyEnum::SET_C, true)) {
				assertType('MyEnum::C', $enum);
			} else {
				assertType('*NEVER*', $enum);
			}
		}
	}

	public function test2(): void
	{
		foreach (self::cases() as $enum) {
			if (in_array($enum, MyEnum::SET_ABC, true)) {
				assertType('MyEnum::A|MyEnum::B|MyEnum::C', $enum);
			} else {
				assertType('*NEVER*', $enum);
			}
		}
	}

	public function test3(): void
	{
		foreach (self::cases() as $enum) {
			if (in_array($enum, MyEnum::SET_C, true)) {
				assertType('MyEnum::C', $enum);
			} else {
				assertType('MyEnum::A|MyEnum::B', $enum);
			}
		}
	}
	public function test4(): void
	{
		foreach ([MyEnum::C] as $enum) {
			if (in_array($enum, MyEnum::SET_C, true)) {
				assertType('MyEnum::C', $enum);
			} else {
				assertType('*NEVER*', $enum);
			}
		}
	}

}

class InArrayEnum
{

	/** @var list<MyEnum> */
	private array $list = [];

	public function doFoo(MyEnum $enum): void
	{
		if (in_array($enum, $this->list, true)) {
			return;
		}

		assertType(MyEnum::class, $enum);
	}

}


class InArrayOtherFiniteType {

	const SET_AB = ['a', 'b'];
	const SET_C = ['c'];
	const SET_ABC = ['a', 'b', 'c'];

	public function test1(): void
	{
		foreach (['a', 'b', 'c'] as $item) {
			if (in_array($item, self::SET_AB, true)) {
				assertType("'a'|'b'", $item);
			} elseif (in_array($item, self::SET_C, true)) {
				assertType("'c'", $item);
			} else {
				assertType('*NEVER*', $item);
			}
		}
	}

	public function test2(): void
	{
		foreach (['a', 'b', 'c'] as $item) {
			if (in_array($item, self::SET_ABC, true)) {
				assertType("'a'|'b'|'c'", $item);
			} else {
				assertType('*NEVER*', $item);
			}
		}
	}

	public function test3(): void
	{
		foreach (['a', 'b', 'c'] as $item) {
			if (in_array($item, self::SET_C, true)) {
				assertType("'c'", $item);
			} else {
				assertType("'a'|'b'", $item);
			}
		}
	}
	public function test4(): void
	{
		foreach (['c'] as $item) {
			if (in_array($item, self::SET_C, true)) {
				assertType("'c'", $item);
			} else {
				assertType('*NEVER*', $item);
			}
		}
	}
}
