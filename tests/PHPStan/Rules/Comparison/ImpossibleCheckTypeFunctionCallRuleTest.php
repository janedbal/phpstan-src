<?php declare(strict_types = 1);

namespace PHPStan\Rules\Comparison;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use stdClass;
use function array_filter;
use function array_map;
use function array_values;
use function count;
use const PHP_VERSION_ID;

/**
 * @extends RuleTestCase<ImpossibleCheckTypeFunctionCallRule>
 */
class ImpossibleCheckTypeFunctionCallRuleTest extends RuleTestCase
{

	private bool $treatPhpDocTypesAsCertain;

	private bool $reportAlwaysTrueInLastCondition = false;

	protected function getRule(): Rule
	{
		return new ImpossibleCheckTypeFunctionCallRule(
			new ImpossibleCheckTypeHelper(
				$this->createReflectionProvider(),
				$this->getTypeSpecifier(),
				[stdClass::class],
				$this->treatPhpDocTypesAsCertain,
			),
			$this->treatPhpDocTypesAsCertain,
			$this->reportAlwaysTrueInLastCondition,
			true,
		);
	}

	protected function shouldTreatPhpDocTypesAsCertain(): bool
	{
		return $this->treatPhpDocTypesAsCertain;
	}

	public function testImpossibleCheckTypeFunctionCall(): void
	{
		if (PHP_VERSION_ID < 80000) {
			self::markTestSkipped('Test requires PHP 8.0.');
		}

		$this->treatPhpDocTypesAsCertain = true;
		$this->analyse(
			[__DIR__ . '/data/check-type-function-call.php'],
			[
				[
					'Call to function is_int() with int will always evaluate to true.',
					25,
				],
				[
					'Call to function is_int() with string will always evaluate to false.',
					31,
				],
				[
					'Call to function is_callable() with array<int> will always evaluate to false.',
					44,
					'Because the type is coming from a PHPDoc, you can turn off this check by setting <fg=cyan>treatPhpDocTypesAsCertain: false</> in your <fg=cyan>%configurationFile%</>.',
				],
				[
					'Call to function assert() with false will always evaluate to false.',
					48,
				],
				[
					'Call to function is_callable() with \'date\' will always evaluate to true.',
					84,
				],
				[
					'Call to function is_callable() with \'nonexistentFunction\' will always evaluate to false.',
					87,
				],
				[
					'Call to function is_numeric() with \'123\' will always evaluate to true.',
					102,
				],
				[
					'Call to function is_numeric() with \'blabla\' will always evaluate to false.',
					105,
				],
				[
					'Call to function is_numeric() with 123|float will always evaluate to true.',
					118,
				],
				[
					'Call to function is_string() with string will always evaluate to true.',
					140,
				],
				[
					'Call to function method_exists() with CheckTypeFunctionCall\Foo and \'doFoo\' will always evaluate to true.',
					179,
				],
				[
					'Call to function method_exists() with CheckTypeFunctionCall\Foo and \'doFoo\' will always evaluate to true.',
					189,
				],
				[
					'Call to function method_exists() with $this(CheckTypeFunctionCall\FinalClassWithMethodExists) and \'doFoo\' will always evaluate to true.',
					201,
				],
				[
					'Call to function method_exists() with $this(CheckTypeFunctionCall\FinalClassWithMethodExists) and \'doBar\' will always evaluate to false.',
					204,
				],
				[
					'Call to function property_exists() with $this(CheckTypeFunctionCall\FinalClassWithPropertyExists) and \'fooProperty\' will always evaluate to true.',
					220,
				],
				[
					'Call to function in_array() with arguments int, array{\'foo\', \'bar\'} and true will always evaluate to false.',
					246,
				],
				[
					'Call to function in_array() with arguments \'bar\'|\'foo\', array{\'baz\', \'lorem\'} and true will always evaluate to false.',
					255,
				],
				[
					'Call to function in_array() with arguments \'foo\', array{\'foo\'} and true will always evaluate to true.',
					263,
				],
				[
					'Call to function in_array() with arguments \'foo\', array{\'foo\', \'bar\'} and true will always evaluate to true.',
					267,
				],
				[
					'Call to function in_array() with arguments \'bar\', array{}|array{\'foo\'} and true will always evaluate to false.',
					331,
				],
				[
					'Call to function in_array() with arguments \'baz\', array{0: \'bar\', 1?: \'foo\'} and true will always evaluate to false.',
					347,
				],
				[
					'Call to function in_array() with arguments \'foo\', array{} and true will always evaluate to false.',
					354,
				],
				[
					'Call to function array_key_exists() with \'a\' and array{a: 1, b?: 2} will always evaluate to true.',
					371,
				],
				[
					'Call to function array_key_exists() with \'c\' and array{a: 1, b?: 2} will always evaluate to false.',
					377,
				],
				[
					'Call to function is_string() with mixed will always evaluate to false.',
					571,
				],
				[
					'Call to function is_callable() with mixed will always evaluate to false.',
					582,
				],
				[
					'Call to function method_exists() with \'CheckTypeFunctionCall\\\\MethodExists\' and \'testWithStringFirst…\' will always evaluate to true.',
					596,
				],
				[
					'Call to function method_exists() with \'UndefinedClass\' and string will always evaluate to false.',
					605,
				],
				[
					'Call to function method_exists() with \'UndefinedClass\' and \'test\' will always evaluate to false.',
					608,
				],
				[
					'Call to function method_exists() with CheckTypeFunctionCall\MethodExists and \'testWithNewObjectIn…\' will always evaluate to true.',
					620,
				],
				[
					'Call to function method_exists() with CheckTypeFunctionCall\MethodExists and \'testWithNewObjectIn…\' will always evaluate to true.',
					635,
				],
				[
					'Call to function method_exists() with $this(CheckTypeFunctionCall\MethodExistsWithTrait) and \'method\' will always evaluate to true.',
					650,
				],
				[
					'Call to function method_exists() with $this(CheckTypeFunctionCall\MethodExistsWithTrait) and \'someAnother\' will always evaluate to true.',
					653,
				],
				[
					'Call to function method_exists() with $this(CheckTypeFunctionCall\MethodExistsWithTrait) and \'unknown\' will always evaluate to false.',
					656,
				],
				[
					'Call to function method_exists() with \'CheckTypeFunctionCall\\\\MethodExistsWithTrait\' and \'method\' will always evaluate to true.',
					659,
					'Because the type is coming from a PHPDoc, you can turn off this check by setting <fg=cyan>treatPhpDocTypesAsCertain: false</> in your <fg=cyan>%configurationFile%</>.',
				],
				[
					'Call to function method_exists() with \'CheckTypeFunctionCall\\\\MethodExistsWithTrait\' and \'someAnother\' will always evaluate to true.',
					662,
					'Because the type is coming from a PHPDoc, you can turn off this check by setting <fg=cyan>treatPhpDocTypesAsCertain: false</> in your <fg=cyan>%configurationFile%</>.',
				],
				[
					'Call to function method_exists() with \'CheckTypeFunctionCall\\\\MethodExistsWithTrait\' and \'unknown\' will always evaluate to false.',
					665,
					'Because the type is coming from a PHPDoc, you can turn off this check by setting <fg=cyan>treatPhpDocTypesAsCertain: false</> in your <fg=cyan>%configurationFile%</>.',
				],
				[
					'Call to function method_exists() with \'CheckTypeFunctionCall\\\\MethodExistsWithTrait\' and \'method\' will always evaluate to true.',
					668,
				],
				[
					'Call to function method_exists() with \'CheckTypeFunctionCall\\\\MethodExistsWithTrait\' and \'someAnother\' will always evaluate to true.',
					671,
				],
				[
					'Call to function method_exists() with \'CheckTypeFunctionCall\\\\MethodExistsWithTrait\' and \'unknown\' will always evaluate to false.',
					674,
				],
				[
					'Call to function is_string() with string will always evaluate to true.',
					703,
					'Because the type is coming from a PHPDoc, you can turn off this check by setting <fg=cyan>treatPhpDocTypesAsCertain: false</> in your <fg=cyan>%configurationFile%</>.',
				],
				[
					'Call to function assert() with true will always evaluate to true.',
					718,
					'Because the type is coming from a PHPDoc, you can turn off this check by setting <fg=cyan>treatPhpDocTypesAsCertain: false</> in your <fg=cyan>%configurationFile%</>.',
				],
				[
					'Call to function is_numeric() with \'123\' will always evaluate to true.',
					718,
				],
				[
					'Call to function assert() with false will always evaluate to false.',
					719,
					'Because the type is coming from a PHPDoc, you can turn off this check by setting <fg=cyan>treatPhpDocTypesAsCertain: false</> in your <fg=cyan>%configurationFile%</>.',
				],
				[
					'Call to function is_numeric() with \'blabla\' will always evaluate to false.',
					719,
				],
				[
					'Call to function assert() with true will always evaluate to true.',
					726,
					'Because the type is coming from a PHPDoc, you can turn off this check by setting <fg=cyan>treatPhpDocTypesAsCertain: false</> in your <fg=cyan>%configurationFile%</>.',
				],
				[
					'Call to function is_numeric() with 123|float will always evaluate to true.',
					726,
				],
				[
					'Call to function property_exists() with CheckTypeFunctionCall\Bug2221 and \'foo\' will always evaluate to true.',
					809,
				],
				[
					'Call to function property_exists() with CheckTypeFunctionCall\Bug2221 and \'foo\' will always evaluate to true.',
					813,
				],
				[
					'Call to function testIsInt() with int will always evaluate to true.',
					900,
				],
				[
					'Call to function is_int() with int will always evaluate to true.',
					914,
					'Remove remaining cases below this one and this error will disappear too.',
				],
				[
					'Call to function in_array() with arguments 1, array<string> and true will always evaluate to false.',
					952,
					'Because the type is coming from a PHPDoc, you can turn off this check by setting <fg=cyan>treatPhpDocTypesAsCertain: false</> in your <fg=cyan>%configurationFile%</>.',
				],
			],
		);
	}

	public function testBug7898(): void
	{
		$this->treatPhpDocTypesAsCertain = true;
		$this->analyse([__DIR__ . '/data/bug-7898.php'], []);
	}

	public function testDoNotReportTypesFromPhpDocs(): void
	{
		$this->treatPhpDocTypesAsCertain = false;
		$this->analyse([__DIR__ . '/data/check-type-function-call-not-phpdoc.php'], [
			[
				'Call to function is_int() with int will always evaluate to true.',
				16,
			],
		]);
	}

	public function testReportTypesFromPhpDocs(): void
	{
		$this->treatPhpDocTypesAsCertain = true;
		$this->analyse([__DIR__ . '/data/check-type-function-call-not-phpdoc.php'], [
			[
				'Call to function is_int() with int will always evaluate to true.',
				16,
			],
			[
				'Call to function is_int() with int will always evaluate to true.',
				19,
				'Because the type is coming from a PHPDoc, you can turn off this check by setting <fg=cyan>treatPhpDocTypesAsCertain: false</> in your <fg=cyan>%configurationFile%</>.',
			],
			[
				'Call to function in_array() with arguments int, array<string> and true will always evaluate to false.',
				27,
				'Because the type is coming from a PHPDoc, you can turn off this check by setting <fg=cyan>treatPhpDocTypesAsCertain: false</> in your <fg=cyan>%configurationFile%</>.',
			],
			[
				'Call to function in_array() with arguments 1, array<string> and true will always evaluate to false.',
				30,
				'Because the type is coming from a PHPDoc, you can turn off this check by setting <fg=cyan>treatPhpDocTypesAsCertain: false</> in your <fg=cyan>%configurationFile%</>.',
			],
		]);
	}

	public function testBug2550(): void
	{
		$this->treatPhpDocTypesAsCertain = true;
		$this->analyse([__DIR__ . '/data/bug-2550.php'], []);
	}

	public function testBug3994(): void
	{
		$this->treatPhpDocTypesAsCertain = true;
		$this->analyse([__DIR__ . '/data/bug-3994.php'], []);
	}

	public function testBug1613(): void
	{
		$this->treatPhpDocTypesAsCertain = true;
		$this->analyse([__DIR__ . '/data/bug-1613.php'], []);
	}

	public function testBug2714(): void
	{
		$this->treatPhpDocTypesAsCertain = true;
		$this->analyse([__DIR__ . '/data/bug-2714.php'], []);
	}

	public function testBug4657(): void
	{
		$this->treatPhpDocTypesAsCertain = false;
		$this->analyse([__DIR__ . '/data/bug-4657.php'], []);
	}

	public function testBug4999(): void
	{
		$this->treatPhpDocTypesAsCertain = false;
		$this->analyse([__DIR__ . '/data/bug-4999.php'], []);
	}

	public function testArrayIsList(): void
	{
		if (PHP_VERSION_ID < 80100) {
			$this->markTestSkipped('Test requires PHP 8.1.');
		}

		$this->treatPhpDocTypesAsCertain = true;
		$this->analyse([__DIR__ . '/data/array-is-list.php'], [
			[
				'Call to function array_is_list() with array<string, int> will always evaluate to false.',
				13,
				'Because the type is coming from a PHPDoc, you can turn off this check by setting <fg=cyan>treatPhpDocTypesAsCertain: false</> in your <fg=cyan>%configurationFile%</>.',
			],
			[
				'Call to function array_is_list() with array{foo: \'bar\', bar: \'baz\'} will always evaluate to false.',
				40,
			],
			[
				'Call to function array_is_list() with array{0: \'foo\', foo: \'bar\', bar: \'baz\'} will always evaluate to false.',
				44,
			],
		]);
	}

	public function testBug3766(): void
	{
		$this->treatPhpDocTypesAsCertain = true;
		$this->analyse([__DIR__ . '/data/bug-3766.php'], []);
	}

	public function testBug6305(): void
	{
		$this->treatPhpDocTypesAsCertain = true;
		$this->analyse([__DIR__ . '/data/bug-6305.php'], [
			[
				'Call to function is_subclass_of() with Bug6305\B and \'Bug6305\\\A\' will always evaluate to true.',
				11,
			],
			[
				'Call to function is_subclass_of() with Bug6305\B and \'Bug6305\\\B\' will always evaluate to false.',
				14,
			],
		]);
	}

	public function testBug6698(): void
	{
		$this->treatPhpDocTypesAsCertain = true;
		$this->analyse([__DIR__ . '/data/bug-6698.php'], []);
	}

	public function testBug5369(): void
	{
		$this->treatPhpDocTypesAsCertain = true;
		$this->analyse([__DIR__ . '/data/bug-5369.php'], []);
	}

	public function testBugInArrayDateFormat(): void
	{
		$this->treatPhpDocTypesAsCertain = true;
		$this->analyse([__DIR__ . '/data/in-array-date-format.php'], [
			[
				'Call to function in_array() with arguments \'a\', non-empty-array<int, \'a\'> and true will always evaluate to true.',
				39,
				'Because the type is coming from a PHPDoc, you can turn off this check by setting <fg=cyan>treatPhpDocTypesAsCertain: false</> in your <fg=cyan>%configurationFile%</>.',
			],
			[
				'Call to function in_array() with arguments \'b\', non-empty-array<int, \'a\'> and true will always evaluate to false.',
				43,
				//'Because the type is coming from a PHPDoc, you can turn off this check by setting <fg=cyan>treatPhpDocTypesAsCertain: false</> in your <fg=cyan>%configurationFile%</>.',
			],
			[
				'Call to function in_array() with arguments int, array{} and true will always evaluate to false.',
				47,
			],
			[
				'Call to function in_array() with arguments int, array<int, string> and true will always evaluate to false.',
				61,
				'Because the type is coming from a PHPDoc, you can turn off this check by setting <fg=cyan>treatPhpDocTypesAsCertain: false</> in your <fg=cyan>%configurationFile%</>.',
			],
		]);
	}

	public function testBug5496(): void
	{
		$this->treatPhpDocTypesAsCertain = true;
		$this->analyse([__DIR__ . '/data/bug-5496.php'], []);
	}

	public function testBug3892(): void
	{
		$this->treatPhpDocTypesAsCertain = true;
		$this->analyse([__DIR__ . '/data/bug-3892.php'], []);
	}

	public function testBug3314(): void
	{
		$this->treatPhpDocTypesAsCertain = true;
		$this->analyse([__DIR__ . '/data/bug-3314.php'], []);
	}

	public function testBug2870(): void
	{
		$this->treatPhpDocTypesAsCertain = true;
		$this->analyse([__DIR__ . '/data/bug-2870.php'], []);
	}

	public function testBug5354(): void
	{
		$this->treatPhpDocTypesAsCertain = true;
		$this->analyse([__DIR__ . '/data/bug-5354.php'], []);
	}

	public function testSlevomatCsInArrayBug(): void
	{
		$this->treatPhpDocTypesAsCertain = true;
		$this->analyse([__DIR__ . '/data/slevomat-cs-in-array.php'], []);
	}

	public function testNonEmptySpecifiedString(): void
	{
		$this->treatPhpDocTypesAsCertain = true;
		$this->analyse([__DIR__ . '/data/non-empty-string-impossible-type.php'], []);
	}

	public function testBug2755(): void
	{
		$this->treatPhpDocTypesAsCertain = true;
		$this->analyse([__DIR__ . '/data/bug-2755.php'], []);
	}

	public function testBug7079(): void
	{
		$this->treatPhpDocTypesAsCertain = true;
		$this->analyse([__DIR__ . '/data/bug-7079.php'], []);
	}

	public function testConditionalTypesInference(): void
	{
		if (PHP_VERSION_ID < 80000) {
			self::markTestSkipped('Test requires PHP 8.0.');
		}

		$this->treatPhpDocTypesAsCertain = true;
		$this->analyse([__DIR__ . '/../../Analyser/nsrt/conditional-types-inference.php'], [
			[
				'Call to function testIsInt() with string will always evaluate to false.',
				49,
			],
			[
				'Call to function testIsNotInt() with string will always evaluate to true.',
				55,
			],
			[
				'Call to function testIsInt() with int will always evaluate to true.',
				66,
			],
			[
				'Call to function testIsNotInt() with int will always evaluate to false.',
				72,
			],
			[
				'Call to function assertIsInt() with int will always evaluate to true.',
				78,
			],
		]);
	}

	public function testBug6697(): void
	{
		$this->treatPhpDocTypesAsCertain = true;
		$this->analyse([__DIR__ . '/data/bug-6697.php'], []);
	}

	public function testBug6443(): void
	{
		if (PHP_VERSION_ID < 80000) {
			self::markTestSkipped('Test requires PHP 8.0.');
		}

		$this->treatPhpDocTypesAsCertain = true;
		$this->analyse([__DIR__ . '/data/bug-6443.php'], []);
	}

	public function testBug7684(): void
	{
		$this->treatPhpDocTypesAsCertain = true;
		$this->analyse([__DIR__ . '/data/bug-7684.php'], []);
	}

	public function testBug7224(): void
	{
		$this->treatPhpDocTypesAsCertain = true;
		$this->analyse([__DIR__ . '/../../Analyser/nsrt/bug-7224.php'], []);
	}

	public function testBug4708(): void
	{
		$this->treatPhpDocTypesAsCertain = true;
		$this->analyse([__DIR__ . '/data/bug-4708.php'], []);
	}

	public function testBug3821(): void
	{
		$this->treatPhpDocTypesAsCertain = true;
		$this->analyse([__DIR__ . '/data/bug-3821.php'], []);
	}

	public function testBug6599(): void
	{
		$this->treatPhpDocTypesAsCertain = true;
		$this->analyse([__DIR__ . '/data/bug-6599.php'], []);
	}

	public function testBug7914(): void
	{
		$this->treatPhpDocTypesAsCertain = true;
		$this->analyse([__DIR__ . '/data/bug-7914.php'], []);
	}

	public function testDocblockAssertEquality(): void
	{
		$this->treatPhpDocTypesAsCertain = true;
		$this->analyse([__DIR__ . '/data/docblock-assert-equality.php'], [
			[
				'Call to function isAnInteger() with int will always evaluate to true.',
				42,
			],
		]);
	}

	public function testBug8076(): void
	{
		$this->treatPhpDocTypesAsCertain = true;
		$this->analyse([__DIR__ . '/data/bug-8076.php'], []);
	}

	public function testBug8562(): void
	{
		$this->treatPhpDocTypesAsCertain = true;
		$this->analyse([__DIR__ . '/data/bug-8562.php'], []);
	}

	public function testBug6938(): void
	{
		$this->treatPhpDocTypesAsCertain = false;
		$this->analyse([__DIR__ . '/data/bug-6938.php'], []);
	}

	public function testBug8727(): void
	{
		$this->treatPhpDocTypesAsCertain = true;
		$this->analyse([__DIR__ . '/data/bug-8727.php'], []);
	}

	public function testBug8474(): void
	{
		$this->treatPhpDocTypesAsCertain = true;
		$this->analyse([__DIR__ . '/data/bug-8474.php'], []);
	}

	public function testBug5695(): void
	{
		$this->treatPhpDocTypesAsCertain = false;
		$this->analyse([__DIR__ . '/data/bug-5695.php'], []);
	}

	public function testBug8752(): void
	{
		$this->treatPhpDocTypesAsCertain = true;
		$this->analyse([__DIR__ . '/../../Analyser/nsrt/bug-8752.php'], []);
	}

	public function testDiscussion9134(): void
	{
		$this->treatPhpDocTypesAsCertain = true;
		$this->analyse([__DIR__ . '/../../Analyser/nsrt/discussion-9134.php'], []);
	}

	public function testImpossibleMethodExistOnGenericClassString(): void
	{
		$this->treatPhpDocTypesAsCertain = true;

		$tipText = 'Because the type is coming from a PHPDoc, you can turn off this check by setting <fg=cyan>treatPhpDocTypesAsCertain: false</> in your <fg=cyan>%configurationFile%</>.';
		$this->analyse([__DIR__ . '/data/impossible-method-exists-on-generic-class-string.php'], [
			[
				"Call to function method_exists() with class-string<ImpossibleMethodExistsOnGenericClassString\S>&literal-string and 'staticAbc' will always evaluate to true.",
				18,
				$tipText,
			],
			[
				"Call to function method_exists() with class-string<ImpossibleMethodExistsOnGenericClassString\S>&literal-string and 'nonStaticAbc' will always evaluate to true.",
				23,
				$tipText,
			],
			[
				"Call to function method_exists() with class-string<ImpossibleMethodExistsOnGenericClassString\FinalS>&literal-string and 'nonExistent' will always evaluate to false.",
				34,
				$tipText,
			],
			[
				"Call to function method_exists() with class-string<ImpossibleMethodExistsOnGenericClassString\FinalS>&literal-string and 'staticAbc' will always evaluate to true.",
				39,
				$tipText,
			],
			[
				"Call to function method_exists() with class-string<ImpossibleMethodExistsOnGenericClassString\FinalS>&literal-string and 'nonStaticAbc' will always evaluate to true.",
				44,
				$tipText,
			],

		]);
	}

	public function dataReportAlwaysTrueInLastCondition(): iterable
	{
		yield [false, [
			[
				'Call to function is_int() with int will always evaluate to true.',
				21,
				'Remove remaining cases below this one and this error will disappear too.',
			],
		]];
		yield [true, [
			[
				'Call to function is_int() with int will always evaluate to true.',
				12,
			],
			[
				'Call to function is_int() with int will always evaluate to true.',
				21,
			],
		]];
	}

	/**
	 * @dataProvider dataReportAlwaysTrueInLastCondition
	 * @param list<array{0: string, 1: int, 2?: string}> $expectedErrors
	 */
	public function testReportAlwaysTrueInLastCondition(bool $reportAlwaysTrueInLastCondition, array $expectedErrors): void
	{
		$this->treatPhpDocTypesAsCertain = true;
		$this->reportAlwaysTrueInLastCondition = $reportAlwaysTrueInLastCondition;
		$this->analyse([__DIR__ . '/data/impossible-function-report-always-true-last-condition.php'], $expectedErrors);
	}

	public function testObjectShapes(): void
	{
		$this->treatPhpDocTypesAsCertain = true;
		$this->analyse([__DIR__ . '/data/property-exists-object-shapes.php'], [
			[
				'Call to function property_exists() with object{foo: int, bar?: string} and \'baz\' will always evaluate to false.',
				24,
				'Because the type is coming from a PHPDoc, you can turn off this check by setting <fg=cyan>treatPhpDocTypesAsCertain: false</> in your <fg=cyan>%configurationFile%</>.',
			],
		]);
	}

	/** @return list<array{0: string, 1: int, 2?: string}> */
	private static function getLooseComparisonAgainsEnumsIssues(): array
	{
		$tipText = 'Because the type is coming from a PHPDoc, you can turn off this check by setting <fg=cyan>treatPhpDocTypesAsCertain: false</> in your <fg=cyan>%configurationFile%</>.';
		return [
			[
				'Call to function in_array() with LooseComparisonAgainstEnums\\FooUnitEnum and array{\'A\'} will always evaluate to false.',
				21,
			],
			[
				'Call to function in_array() with arguments LooseComparisonAgainstEnums\\FooUnitEnum, array{\'A\'} and false will always evaluate to false.',
				24,
			],
			[
				'Call to function in_array() with LooseComparisonAgainstEnums\\FooBackedEnum and array{\'A\'} will always evaluate to false.',
				27,
			],
			[
				'Call to function in_array() with arguments LooseComparisonAgainstEnums\\FooBackedEnum, array{\'A\'} and false will always evaluate to false.',
				30,
			],
			[
				'Call to function in_array() with arguments LooseComparisonAgainstEnums\\FooBackedEnum|LooseComparisonAgainstEnums\\FooUnitEnum, array{\'A\'} and false will always evaluate to false.',
				33,
			],
			[
				'Call to function in_array() with \'A\' and array{LooseComparisonAgainstEnums\\FooUnitEnum} will always evaluate to false.',
				39,
			],
			[
				'Call to function in_array() with arguments \'A\', array{LooseComparisonAgainstEnums\\FooUnitEnum} and false will always evaluate to false.',
				42,
			],
			[
				'Call to function in_array() with \'A\' and array{LooseComparisonAgainstEnums\\FooBackedEnum} will always evaluate to false.',
				45,
			],
			[
				'Call to function in_array() with arguments \'A\', array{LooseComparisonAgainstEnums\\FooBackedEnum} and false will always evaluate to false.',
				48,
			],
			[
				'Call to function in_array() with arguments \'A\', array{LooseComparisonAgainstEnums\\FooBackedEnum|LooseComparisonAgainstEnums\\FooUnitEnum} and false will always evaluate to false.',
				51,
			],
			[
				'Call to function in_array() with LooseComparisonAgainstEnums\FooUnitEnum and array{bool} will always evaluate to false.',
				57,
			],
			[
				'Call to function in_array() with arguments LooseComparisonAgainstEnums\FooUnitEnum, array{bool} and false will always evaluate to false.',
				60,
			],
			[
				'Call to function in_array() with LooseComparisonAgainstEnums\FooBackedEnum and array{bool} will always evaluate to false.',
				63,
			],
			[
				'Call to function in_array() with arguments LooseComparisonAgainstEnums\FooBackedEnum, array{bool} and false will always evaluate to false.',
				66,
			],
			[
				'Call to function in_array() with arguments LooseComparisonAgainstEnums\FooBackedEnum|LooseComparisonAgainstEnums\FooUnitEnum, array{bool} and false will always evaluate to false.',
				69,
			],
			[
				'Call to function in_array() with bool and array{LooseComparisonAgainstEnums\FooUnitEnum} will always evaluate to false.',
				75,
			],
			[
				'Call to function in_array() with arguments bool, array{LooseComparisonAgainstEnums\FooUnitEnum} and false will always evaluate to false.',
				78,
			],
			[
				'Call to function in_array() with bool and array{LooseComparisonAgainstEnums\FooBackedEnum} will always evaluate to false.',
				81,
			],
			[
				'Call to function in_array() with arguments bool, array{LooseComparisonAgainstEnums\FooBackedEnum} and false will always evaluate to false.',
				84,
			],
			[
				'Call to function in_array() with arguments bool, array{LooseComparisonAgainstEnums\FooBackedEnum|LooseComparisonAgainstEnums\FooUnitEnum} and false will always evaluate to false.',
				87,
			],
			[
				'Call to function in_array() with LooseComparisonAgainstEnums\FooUnitEnum and array{null} will always evaluate to false.',
				93,
			],
			[
				'Call to function in_array() with null and array{LooseComparisonAgainstEnums\FooBackedEnum} will always evaluate to false.',
				96,
			],
			[
				'Call to function in_array() with LooseComparisonAgainstEnums\FooUnitEnum and array<string> will always evaluate to false.',
				125,
				$tipText,
			],
			[
				'Call to function in_array() with arguments LooseComparisonAgainstEnums\FooUnitEnum, array<string> and false will always evaluate to false.',
				128,
				$tipText,
			],
			[
				'Call to function in_array() with arguments LooseComparisonAgainstEnums\FooUnitEnum, array<string> and true will always evaluate to false.',
				131,
				$tipText,
			],
			[
				'Call to function in_array() with string and array<LooseComparisonAgainstEnums\FooUnitEnum> will always evaluate to false.',
				143,
				$tipText,
			],
			[
				'Call to function in_array() with arguments string, array<LooseComparisonAgainstEnums\FooUnitEnum> and false will always evaluate to false.',
				146,
				$tipText,
			],
			[
				'Call to function in_array() with arguments string, array<LooseComparisonAgainstEnums\FooUnitEnum> and true will always evaluate to false.',
				149,
				$tipText,
			],
			[
				'Call to function in_array() with LooseComparisonAgainstEnums\FooUnitEnum::B and non-empty-array<LooseComparisonAgainstEnums\FooUnitEnum::A> will always evaluate to false.',
				159,
				$tipText,
			],
			[
				'Call to function in_array() with LooseComparisonAgainstEnums\FooUnitEnum::A and non-empty-array<LooseComparisonAgainstEnums\FooUnitEnum::A> will always evaluate to true.',
				162,
				$tipText,
			],
			[
				'Call to function in_array() with arguments LooseComparisonAgainstEnums\FooUnitEnum::A, non-empty-array<LooseComparisonAgainstEnums\FooUnitEnum::A> and false will always evaluate to true.',
				165,
				'BUG',
				//$tipText,
			],
			[
				'Call to function in_array() with arguments LooseComparisonAgainstEnums\FooUnitEnum::A, non-empty-array<LooseComparisonAgainstEnums\FooUnitEnum::A> and true will always evaluate to true.',
				168,
				'BUG',
				//$tipText,
			],
			[
				'Call to function in_array() with arguments LooseComparisonAgainstEnums\FooUnitEnum::B, non-empty-array<LooseComparisonAgainstEnums\FooUnitEnum::A> and false will always evaluate to false.',
				171,
				'BUG',
				//$tipText,
			],
			[
				'Call to function in_array() with arguments LooseComparisonAgainstEnums\FooUnitEnum::B, non-empty-array<LooseComparisonAgainstEnums\FooUnitEnum::A> and true will always evaluate to false.',
				174,
				'BUG',
				//$tipText,
			],
		];
	}

	public function testLooseComparisonAgainstEnums(): void
	{
		if (PHP_VERSION_ID < 80100) {
			$this->markTestSkipped('Test requires PHP 8.1.');
		}

		$this->treatPhpDocTypesAsCertain = true;
		$issues = array_map(
			static function (array $i): array {
				if (($i[2] ?? null) === 'BUG') {
					unset($i[2]);
				}

				return $i;
			},
			self::getLooseComparisonAgainsEnumsIssues(),
		);
		$this->analyse([__DIR__ . '/data/loose-comparison-against-enums.php'], $issues);
	}

	public function testNonStrictInArray(): void
	{
		$this->treatPhpDocTypesAsCertain = true;
		$this->analyse([__DIR__ . '/../../Analyser/nsrt/bug-9662.php'], []);
	}

	public function testNonStrictInArrayEnums(): void
	{
		if (PHP_VERSION_ID < 80100) {
			$this->markTestSkipped('Test requires PHP 8.1.');
		}

		$tipText = 'Because the type is coming from a PHPDoc, you can turn off this check by setting <fg=cyan>treatPhpDocTypesAsCertain: false</> in your <fg=cyan>%configurationFile%</>.';

		$this->treatPhpDocTypesAsCertain = true;
		$this->analyse([__DIR__ . '/../../Analyser/nsrt/bug-9662-enums.php'], [
			[
				"Call to function in_array() with 'NotAnEnumCase' and array<Bug9662Enums\Suit> will always evaluate to false.",
				19,
				$tipText,
			],
			[
				"Call to function in_array() with 'NotAnEnumCase' and array<Bug9662Enums\StringBackedSuit> will always evaluate to false.",
				62,
				$tipText,
			],
			[
				'Call to function in_array() with string and array<Bug9662Enums\StringBackedSuit> will always evaluate to false.',
				77,
			],
			[
				'Call to function in_array() with int and array<Bug9662Enums\StringBackedSuit> will always evaluate to false.',
				84,
			],
		]);
	}

	public function testLooseComparisonAgainstEnumsNoPhpdoc(): void
	{
		if (PHP_VERSION_ID < 80100) {
			$this->markTestSkipped('Test requires PHP 8.1.');
		}

		$this->treatPhpDocTypesAsCertain = false;
		$issues = self::getLooseComparisonAgainsEnumsIssues();
		$issues = array_values(array_filter($issues, static fn (array $i) => count($i) === 2));
		$this->analyse([__DIR__ . '/data/loose-comparison-against-enums.php'], $issues);
	}

	public function testBug10502(): void
	{
		$tipText = 'Because the type is coming from a PHPDoc, you can turn off this check by setting <fg=cyan>treatPhpDocTypesAsCertain: false</> in your <fg=cyan>%configurationFile%</>.';

		$this->treatPhpDocTypesAsCertain = true;
		$this->analyse([__DIR__ . '/data/bug-10502.php'], [
			[
				"Call to function is_callable() with array{ArrayObject<int, int>, 'count'} will always evaluate to true.",
				23,
			],
			[
				"Call to function is_callable() with array{1: 'count', 0: ArrayObject<int, int>} will always evaluate to true.",
				24,
				$tipText,
			],
		]);
	}

	public function testAlwaysTruePregMatch(): void
	{
		$this->treatPhpDocTypesAsCertain = true;
		$this->analyse([__DIR__ . '/data/always-true-preg-match.php'], []);
	}

}
