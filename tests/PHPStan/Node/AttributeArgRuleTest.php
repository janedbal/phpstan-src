<?php declare(strict_types = 1);

namespace PHPStan\Node;

use PhpParser\Node;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<Rule>
 */
class AttributeArgRuleTest extends RuleTestCase
{

	/**
	 * @return Rule<Node\Arg>
	 */
	protected function getRule(): Rule
	{
		return new AttributeArgRule();
	}

	public function dataRule(): iterable
	{
		yield [
			__DIR__ . '/data/attributes.php',
			AttributeArgRule::ERROR_MESSAGE,
			[8, 16, 20, 23, 26, 27, 34, 40],
		];
	}

	/**
	 * @param int[] $lines
	 * @dataProvider dataRule
	 */
	public function testRule(string $file, string $expectedError, array $lines): void
	{
		$errors = [];
		foreach ($lines as $line) {
			$errors[] = [$expectedError, $line];
		}
		$this->analyse([$file], $errors);
	}

}
