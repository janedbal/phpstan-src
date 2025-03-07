<?php declare(strict_types = 1);

namespace PHPStan\Rules\Properties;

use PHPStan\Php\PhpVersion;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use const PHP_VERSION_ID;

/**
 * @extends RuleTestCase<PropertiesInInterfaceRule>
 */
class PropertiesInInterfaceRuleTest extends RuleTestCase
{

	protected function getRule(): Rule
	{
		return new PropertiesInInterfaceRule(new PhpVersion(PHP_VERSION_ID));
	}

	public function testPhp83AndPropertiesInInterface(): void
	{
		if (PHP_VERSION_ID >= 80400) {
			$this->markTestSkipped('Test requires PHP 8.3 or earlier.');
		}
		if (PHP_VERSION_ID < 80000) {
			$this->markTestSkipped('Property hooks cause syntax error on PHP 7.4');
		}

		$this->analyse([__DIR__ . '/data/properties-in-interface.php'], [
			[
				'Interfaces can include properties only on PHP 8.4 and later.',
				7,
			],
			[
				'Interfaces can include properties only on PHP 8.4 and later.',
				9,
			],
			[
				'Interfaces can include properties only on PHP 8.4 and later.',
				11,
			],
			[
				'Interfaces can include properties only on PHP 8.4 and later.',
				13,
			],
		]);
	}

	public function testPhp83AndPropertyHooksInInterface(): void
	{
		if (PHP_VERSION_ID >= 80400) {
			$this->markTestSkipped('Test requires PHP 8.3 or earlier.');
		}
		if (PHP_VERSION_ID < 80000) {
			$this->markTestSkipped('Property hooks cause syntax error on PHP 7.4');
		}

		$this->analyse([__DIR__ . '/data/property-hooks-in-interface.php'], [
			[
				'Interfaces can include properties only on PHP 8.4 and later.',
				7,
			],
			[
				'Interfaces can include properties only on PHP 8.4 and later.',
				9,
			],
		]);
	}

	public function testPhp84AndPropertiesInInterface(): void
	{
		if (PHP_VERSION_ID < 80400) {
			$this->markTestSkipped('Test requires PHP 8.4 or later.');
		}

		$this->analyse([__DIR__ . '/data/properties-in-interface.php'], [
			[
				'Interfaces can only include hooked properties.',
				9,
			],
			[
				'Interfaces can only include hooked properties.',
				11,
			],
			[
				'Interfaces can only include hooked properties.',
				13,
			],
		]);
	}

	public function testPhp84AndNonPublicPropertyHooksInInterface(): void
	{
		if (PHP_VERSION_ID < 80400) {
			$this->markTestSkipped('Test requires PHP 8.4 or later.');
		}

		$this->analyse([__DIR__ . '/data/property-hooks-visibility-in-interface.php'], [
			[
				'Interfaces cannot include non-public properties.',
				7,
			],
			[
				'Interfaces cannot include non-public properties.',
				9,
			],
		]);
	}

	public function testPhp84AndPropertyHooksWithBodiesInInterface(): void
	{
		if (PHP_VERSION_ID < 80400) {
			$this->markTestSkipped('Test requires PHP 8.4 or later.');
		}

		$this->analyse([__DIR__ . '/data/property-hooks-bodies-in-interface.php'], [
			[
				'Interfaces cannot include property hooks with bodies.',
				7,
			],
			[
				'Interfaces cannot include property hooks with bodies.',
				13,
			],
		]);
	}

	public function testPhp84AndReadonlyPropertyHooksInInterface(): void
	{
		if (PHP_VERSION_ID < 80400) {
			$this->markTestSkipped('Test requires PHP 8.4 or later.');
		}

		$this->analyse([__DIR__ . '/data/readonly-property-hooks-in-interface.php'], [
			[
				'Interfaces cannot include readonly hooked properties.',
				7,
			],
			[
				'Interfaces cannot include readonly hooked properties.',
				9,
			],
			[
				'Interfaces cannot include readonly hooked properties.',
				11,
			],
		]);
	}

	public function testPhp84AndFinalPropertyHooksInInterface(): void
	{
		if (PHP_VERSION_ID < 80400) {
			$this->markTestSkipped('Test requires PHP 8.4 or later.');
		}

		$this->analyse([__DIR__ . '/data/final-property-hooks-in-interface.php'], [
			[
				'Interfaces cannot include final properties.',
				7,
			],
			[
				'Interfaces cannot include final properties.',
				9,
			],
			[
				'Interfaces cannot include final properties.',
				11,
			],
			[
				'Property hook cannot be both abstract and final.',
				13,
			],
			[
				'Property hook cannot be both abstract and final.',
				17,
			],
		]);
	}

	public function testPhp84AndExplicitAbstractProperty(): void
	{
		if (PHP_VERSION_ID < 80400) {
			$this->markTestSkipped('Test requires PHP 8.4 or later.');
		}

		$this->analyse([__DIR__ . '/data/property-in-interface-explicit-abstract.php'], [
			[
				'Property in interface cannot be explicitly abstract.',
				8,
			],
		]);
	}

	public function testPhp84AndStaticHookedPropertyInInterface(): void
	{
		if (PHP_VERSION_ID < 80400) {
			$this->markTestSkipped('Test requires PHP 8.4 or later.');
		}

		$this->analyse([__DIR__ . '/data/static-hooked-property-in-interface.php'], [
			[
				'Hooked properties cannot be static.',
				7,
			],
			[
				'Hooked properties cannot be static.',
				9,
			],
			[
				'Hooked properties cannot be static.',
				11,
			],
		]);
	}

}
