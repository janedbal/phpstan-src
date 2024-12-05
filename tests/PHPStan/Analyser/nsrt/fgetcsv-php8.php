<?php // lint >= 8.0

declare(strict_types = 1);

namespace TestFGetCsv;

use function PHPStan\Testing\assertType;

function test($resource): void
{
	assertType('list<string|null>|false', fgetcsv($resource));
}
