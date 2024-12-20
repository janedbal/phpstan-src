<?php declare(strict_types = 1);

namespace PHPStan\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\Variable;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\ShouldNotHappenException;
use PHPStan\Type\Constant\ConstantStringType;
use function array_combine;
use function array_map;
use function array_merge;
use function is_array;
use function is_string;
use function sprintf;

final class UnusedFunctionParametersCheck
{

	public function __construct(private ReflectionProvider $reflectionProvider)
	{
	}

	/**
	 * @param Variable[] $parameterVars
	 * @param Node[] $statements
	 * @param 'constructor.unusedParameter'|'closure.unusedUse' $identifier
	 * @return list<IdentifierRuleError>
	 */
	public function getUnusedParameters(
		Scope $scope,
		array $parameterVars,
		array $statements,
		string $unusedParameterMessage,
		string $identifier,
	): array
	{
		$parameterNames = array_map(static function (Variable $variable): string {
			if (!is_string($variable->name)) {
				throw new ShouldNotHappenException();
			}
			return $variable->name;
		}, $parameterVars);
		$unusedParameters = array_combine($parameterNames, $parameterVars);
		foreach ($this->getUsedVariables($scope, $statements) as $variableName) {
			if (!isset($unusedParameters[$variableName])) {
				continue;
			}

			unset($unusedParameters[$variableName]);
		}
		$errors = [];
		foreach ($unusedParameters as $name => $variable) {
			$errors[] = RuleErrorBuilder::message(
				sprintf($unusedParameterMessage, $name),
			)->identifier($identifier)->line($variable->getStartLine())->build();
		}

		return $errors;
	}

	/**
	 * @param Node[]|Node|scalar|null $node
	 * @return string[]
	 */
	private function getUsedVariables(Scope $scope, $node): array
	{
		$variableNames = [];
		if ($node instanceof Node) {
			if ($node instanceof Node\Expr\FuncCall && $node->name instanceof Node\Name) {
				$functionName = $this->reflectionProvider->resolveFunctionName($node->name, $scope);
				if ($functionName === 'func_get_args' || $functionName === 'get_defined_vars') {
					return $scope->getDefinedVariables();
				}
			}
			if ($node instanceof Variable && is_string($node->name) && $node->name !== 'this') {
				return [$node->name];
			}
			if ($node instanceof Node\ClosureUse && is_string($node->var->name)) {
				return [$node->var->name];
			}
			if (
				$node instanceof Node\Expr\FuncCall
				&& $node->name instanceof Node\Name
				&& (string) $node->name === 'compact'
			) {
				foreach ($node->getArgs() as $arg) {
					$argType = $scope->getType($arg->value);
					if (!($argType instanceof ConstantStringType)) {
						continue;
					}

					$variableNames[] = $argType->getValue();
				}
			}
			foreach ($node->getSubNodeNames() as $subNodeName) {
				if ($node instanceof Node\Expr\Closure && $subNodeName !== 'uses') {
					continue;
				}
				$subNode = $node->{$subNodeName};
				$variableNames = array_merge($variableNames, $this->getUsedVariables($scope, $subNode));
			}
		} elseif (is_array($node)) {
			foreach ($node as $subNode) {
				$variableNames = array_merge($variableNames, $this->getUsedVariables($scope, $subNode));
			}
		}

		return $variableNames;
	}

}
