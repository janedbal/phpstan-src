<?php declare(strict_types = 1);

namespace PHPStan\Rules\Functions;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\DependencyInjection\RegisteredRule;
use PHPStan\Node\InArrowFunctionNode;
use PHPStan\Rules\Rule;

/**
 * @implements Rule<InArrowFunctionNode>
 */
#[RegisteredRule(level: 2)]
final class IncompatibleArrowFunctionDefaultParameterTypeRule implements Rule
{

	public function __construct(private IncompatibleClosureLikeDefaultParameterTypeCheck $check)
	{
	}

	public function getNodeType(): string
	{
		return InArrowFunctionNode::class;
	}

	public function processNode(Node $node, Scope $scope): array
	{
		return $this->check->check($scope, $node->getClosureType(), $node->getOriginalNode()->getParams());
	}

}
