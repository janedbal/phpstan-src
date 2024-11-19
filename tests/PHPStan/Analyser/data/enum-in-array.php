<?php

enum MyEnum: string
{

	case A = 'a';
	case B = 'b';
	case C = 'c';

	const SET1 = [self::A, self::B, self::C];

}


foreach ([MyEnum::A, MyEnum::B, MyEnum::C] as $enum) {

	if (in_array($enum, MyEnum::SET1, true)) {

	} else {
		\PHPStan\Testing\assertType('*NEVER*', $enum);
	}
}
