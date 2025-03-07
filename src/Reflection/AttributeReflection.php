<?php declare(strict_types = 1);

namespace PHPStan\Reflection;

use PHPStan\Type\Type;

/**
 * @api
 */
final class AttributeReflection
{

	/**
	 * @param array<string, Type> $argumentTypes
	 */
	public function __construct(private string $name, private array $argumentTypes)
	{
	}

	public function getName(): string
	{
		return $this->name;
	}

	/**
	 * @return array<string, Type>
	 */
	public function getArgumentTypes(): array
	{
		return $this->argumentTypes;
	}

}
