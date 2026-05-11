<?php declare(strict_types = 1);

namespace PHPStan\Rules\Comparison;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\DependencyInjection\AutowiredParameter;
use PHPStan\DependencyInjection\RegisteredRule;
use PHPStan\Node\BooleanOrNode;
use PHPStan\Rules\Rule;

/**
 * @implements Rule<BooleanOrNode>
 */
#[RegisteredRule(level: 4)]
final class BooleanOrConstantConditionRule implements Rule
{

	use BooleanConstantConditionRuleTrait;

	public function __construct(
		private ConstantConditionRuleHelper $helper,
		private PossiblyImpureTipHelper $possiblyImpureTipHelper,
		#[AutowiredParameter]
		private bool $treatPhpDocTypesAsCertain,
		#[AutowiredParameter]
		private bool $reportAlwaysTrueInLastCondition,
		#[AutowiredParameter(ref: '%tips.treatPhpDocTypesAsCertain%')]
		private bool $treatPhpDocTypesAsCertainTip,
	)
	{
	}

	public function getNodeType(): string
	{
		return BooleanOrNode::class;
	}

	public function processNode(Node $node, Scope $scope): array
	{
		return $this->processBooleanNode($node, $scope);
	}

}
