<?php declare(strict_types = 1);

namespace PHPStan\Rules\Variables;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Php\PhpVersion;
use PHPStan\Rules\IdentifierRuleError;
use PHPStan\Rules\Properties\PropertyReflectionFinder;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\Type\VerbosityLevel;
use function is_string;
use function sprintf;

/**
 * @implements Rule<Node\Stmt\Unset_>
 */
final class UnsetRule implements Rule
{

	public function __construct(
		private PropertyReflectionFinder $propertyReflectionFinder,
		private PhpVersion $phpVersion,
	)
	{
	}

	public function getNodeType(): string
	{
		return Node\Stmt\Unset_::class;
	}

	public function processNode(Node $node, Scope $scope): array
	{
		$functionArguments = $node->vars;
		$errors = [];

		foreach ($functionArguments as $argument) {
			$error = $this->canBeUnset($argument, $scope);
			if ($error === null) {
				continue;
			}

			$errors[] = $error;
		}

		return $errors;
	}

	private function canBeUnset(Node $node, Scope $scope): ?IdentifierRuleError
	{
		if ($node instanceof Node\Expr\Variable && is_string($node->name)) {
			$hasVariable = $scope->hasVariableType($node->name);
			if ($hasVariable->no()) {
				return RuleErrorBuilder::message(
					sprintf('Call to function unset() contains undefined variable $%s.', $node->name),
				)
					->line($node->getStartLine())
					->identifier('unset.variable')
					->build();
			}
		} elseif ($node instanceof Node\Expr\ArrayDimFetch && $node->dim !== null) {
			$type = $scope->getType($node->var);
			$dimType = $scope->getType($node->dim);

			if ($type->isOffsetAccessible()->no() || $type->hasOffsetValueType($dimType)->no()) {
				return RuleErrorBuilder::message(
					sprintf(
						'Cannot unset offset %s on %s.',
						$dimType->describe(VerbosityLevel::value()),
						$type->describe(VerbosityLevel::value()),
					),
				)
					->line($node->getStartLine())
					->identifier('unset.offset')
					->build();
			}

			return $this->canBeUnset($node->var, $scope);
		} elseif (
			$node instanceof Node\Expr\PropertyFetch
			&& $node->name instanceof Node\Identifier
		) {
			$foundPropertyReflection = $this->propertyReflectionFinder->findPropertyReflectionFromNode($node, $scope);
			if ($foundPropertyReflection === null) {
				return null;
			}

			$propertyReflection = $foundPropertyReflection->getNativeReflection();
			if ($propertyReflection === null) {
				return null;
			}

			if ($propertyReflection->isReadOnly() || $propertyReflection->isReadOnlyByPhpDoc()) {
				return RuleErrorBuilder::message(
					sprintf(
						'Cannot unset %s %s::$%s property.',
						$propertyReflection->isReadOnly() ? 'readonly' : '@readonly',
						$propertyReflection->getDeclaringClass()->getDisplayName(),
						$foundPropertyReflection->getName(),
					),
				)
					->line($node->getStartLine())
					->identifier($propertyReflection->isReadOnly() ? 'unset.readOnlyProperty' : 'unset.readOnlyPropertyByPhpDoc')
					->build();
			}

			if ($propertyReflection->isHooked()) {
				return RuleErrorBuilder::message(
					sprintf(
						'Cannot unset hooked %s::$%s property.',
						$propertyReflection->getDeclaringClass()->getDisplayName(),
						$foundPropertyReflection->getName(),
					),
				)
					->line($node->getStartLine())
					->identifier('unset.hookedProperty')
					->build();
			} elseif ($this->phpVersion->supportsPropertyHooks()) {
				if (
					!$propertyReflection->isPrivate()
					&& !$propertyReflection->isFinal()->yes()
					&& !$propertyReflection->getDeclaringClass()->isFinal()
				) {
					return RuleErrorBuilder::message(
						sprintf(
							'Cannot unset property %s::$%s because it might have hooks in a subclass.',
							$propertyReflection->getDeclaringClass()->getDisplayName(),
							$foundPropertyReflection->getName(),
						),
					)
						->line($node->getStartLine())
						->identifier('unset.possiblyHookedProperty')
						->build();
				}
			}
		}

		return null;
	}

}
