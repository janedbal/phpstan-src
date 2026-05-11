<?php declare(strict_types = 1);

namespace PHPStan\Rules\Functions;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\DependencyInjection\RegisteredRule;
use PHPStan\Node\InClosureNode;
use PHPStan\Rules\Rule;

/**
 * @implements Rule<InClosureNode>
 */
#[RegisteredRule(level: 2)]
final class IncompatibleClosureDefaultParameterTypeRule implements Rule
{

	public function __construct(private IncompatibleClosureLikeDefaultParameterTypeCheck $check)
	{
	}

	public function getNodeType(): string
	{
		return InClosureNode::class;
	}

	public function processNode(Node $node, Scope $scope): array
	{
		return $this->check->check($scope, $node->getClosureType(), $node->getOriginalNode()->getParams());
	}

}
