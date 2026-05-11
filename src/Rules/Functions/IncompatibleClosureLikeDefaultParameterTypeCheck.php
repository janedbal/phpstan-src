<?php declare(strict_types = 1);

namespace PHPStan\Rules\Functions;

use PhpParser\Node;
use PhpParser\Node\Param;
use PHPStan\Analyser\Scope;
use PHPStan\DependencyInjection\AutowiredService;
use PHPStan\Rules\IdentifierRuleError;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\ShouldNotHappenException;
use PHPStan\Type\ClosureType;
use PHPStan\Type\Generic\TemplateTypeHelper;
use PHPStan\Type\VerbosityLevel;
use function is_string;
use function sprintf;

#[AutowiredService]
final class IncompatibleClosureLikeDefaultParameterTypeCheck
{

	/**
	 * @param Param[] $params
	 * @return list<IdentifierRuleError>
	 */
	public function check(Scope $scope, ClosureType $closureType, array $params): array
	{
		$parameters = $closureType->getParameters();

		$errors = [];
		foreach ($params as $paramI => $param) {
			if ($param->default === null) {
				continue;
			}
			if (
				$param->var instanceof Node\Expr\Error
				|| !is_string($param->var->name)
			) {
				throw new ShouldNotHappenException();
			}

			$defaultValueType = $scope->getType($param->default);
			$parameterType = $parameters[$paramI]->getType();
			$parameterType = TemplateTypeHelper::resolveToBounds($parameterType);

			$accepts = $parameterType->accepts($defaultValueType, true);
			if ($accepts->yes()) {
				continue;
			}

			$verbosityLevel = VerbosityLevel::getRecommendedLevelByType($parameterType, $defaultValueType);

			$errors[] = RuleErrorBuilder::message(sprintf(
				'Default value of the parameter #%d $%s (%s) of anonymous function is incompatible with type %s.',
				$paramI + 1,
				$param->var->name,
				$defaultValueType->describe($verbosityLevel),
				$parameterType->describe($verbosityLevel),
			))
				->line($param->getStartLine())
				->identifier('parameter.defaultValue')
				->acceptsReasonsTip($accepts->reasons)
				->build();
		}

		return $errors;
	}

}
