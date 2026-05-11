<?php declare(strict_types = 1);

namespace PHPStan\Rules\Comparison;

use PhpParser\Node;
use PhpParser\Node\Expr\BinaryOp\BooleanAnd;
use PhpParser\Node\Expr\BinaryOp\BooleanOr;
use PhpParser\Node\Expr\BinaryOp\LogicalAnd;
use PHPStan\Analyser\Scope;
use PHPStan\Node\BooleanAndNode;
use PHPStan\Node\BooleanOrNode;
use PHPStan\Parser\LastConditionVisitor;
use PHPStan\Rules\IdentifierRuleError;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\Type\Constant\ConstantBooleanType;
use function count;
use function sprintf;

trait BooleanConstantConditionRuleTrait
{

	/**
	 * @return list<IdentifierRuleError>
	 */
	private function processBooleanNode(BooleanAndNode|BooleanOrNode $node, Scope $scope): array
	{
		$errors = [];
		$originalNode = $node->getOriginalNode();
		$nodeText = $originalNode->getOperatorSigil();
		$leftType = $this->helper->getBooleanType($scope, $originalNode->left);
		$identifierType = match (true) {
			$originalNode instanceof BooleanAnd => 'booleanAnd',
			$originalNode instanceof LogicalAnd => 'logicalAnd',
			$originalNode instanceof BooleanOr => 'booleanOr',
			default => 'logicalOr',
		};
		if ($leftType instanceof ConstantBooleanType) {
			$addTipLeft = function (RuleErrorBuilder $ruleErrorBuilder) use ($scope, $originalNode): RuleErrorBuilder {
				if (!$this->treatPhpDocTypesAsCertain) {
					return $this->possiblyImpureTipHelper->addTip($scope, $originalNode->left, $ruleErrorBuilder);
				}

				$booleanNativeType = $this->helper->getNativeBooleanType($scope, $originalNode->left);
				if ($booleanNativeType instanceof ConstantBooleanType) {
					return $this->possiblyImpureTipHelper->addTip($scope, $originalNode->left, $ruleErrorBuilder);
				}
				if (!$this->treatPhpDocTypesAsCertainTip) {
					return $this->possiblyImpureTipHelper->addTip($scope, $originalNode->left, $ruleErrorBuilder);
				}

				$ruleErrorBuilder = $ruleErrorBuilder->treatPhpDocTypesAsCertainTip();

				return $this->possiblyImpureTipHelper->addTip($scope, $originalNode->left, $ruleErrorBuilder);
			};

			$isLast = $node->getAttribute(LastConditionVisitor::ATTRIBUTE_NAME);
			if (!$leftType->getValue() || $isLast !== true || $this->reportAlwaysTrueInLastCondition) {
				$errorBuilder = $addTipLeft(RuleErrorBuilder::message(sprintf(
					'Left side of %s is always %s.',
					$nodeText,
					$leftType->getValue() ? 'true' : 'false',
				)))
					->identifier(sprintf('%s.leftAlways%s', $identifierType, $leftType->getValue() ? 'True' : 'False'))
					->line($originalNode->left->getStartLine());
				if ($leftType->getValue() && $isLast === false && !$this->reportAlwaysTrueInLastCondition) {
					$errorBuilder->tip('Remove remaining cases below this one and this error will disappear too.');
				}
				$errors[] = $errorBuilder->build();
			}
		}

		$rightScope = $node->getRightScope();
		$rightType = $this->helper->getBooleanType(
			$rightScope,
			$originalNode->right,
		);
		if ($rightType instanceof ConstantBooleanType && !$scope->isInFirstLevelStatement()) {
			$addTipRight = function (RuleErrorBuilder $ruleErrorBuilder) use ($rightScope, $originalNode): RuleErrorBuilder {
				if (!$this->treatPhpDocTypesAsCertain) {
					return $this->possiblyImpureTipHelper->addTip($rightScope, $originalNode->right, $ruleErrorBuilder);
				}

				$booleanNativeType = $this->helper->getNativeBooleanType(
					$rightScope,
					$originalNode->right,
				);
				if ($booleanNativeType instanceof ConstantBooleanType) {
					return $this->possiblyImpureTipHelper->addTip($rightScope, $originalNode->right, $ruleErrorBuilder);
				}
				if (!$this->treatPhpDocTypesAsCertainTip) {
					return $this->possiblyImpureTipHelper->addTip($rightScope, $originalNode->right, $ruleErrorBuilder);
				}

				$ruleErrorBuilder = $ruleErrorBuilder->treatPhpDocTypesAsCertainTip();

				return $this->possiblyImpureTipHelper->addTip($rightScope, $originalNode->right, $ruleErrorBuilder);
			};

			$isLast = $node->getAttribute(LastConditionVisitor::ATTRIBUTE_NAME);
			if (!$rightType->getValue() || $isLast !== true || $this->reportAlwaysTrueInLastCondition) {
				$errorBuilder = $addTipRight(RuleErrorBuilder::message(sprintf(
					'Right side of %s is always %s.',
					$nodeText,
					$rightType->getValue() ? 'true' : 'false',
				)))
					->identifier(sprintf('%s.rightAlways%s', $identifierType, $rightType->getValue() ? 'True' : 'False'))
					->line($originalNode->right->getStartLine());
				if ($rightType->getValue() && $isLast === false && !$this->reportAlwaysTrueInLastCondition) {
					$errorBuilder->tip('Remove remaining cases below this one and this error will disappear too.');
				}
				$errors[] = $errorBuilder->build();
			}
		}

		if (count($errors) === 0 && !$scope->isInFirstLevelStatement()) {
			$nodeType = $this->treatPhpDocTypesAsCertain ? $scope->getType($originalNode) : $scope->getNativeType($originalNode);
			if ($nodeType instanceof ConstantBooleanType) {
				$addTip = function (RuleErrorBuilder $ruleErrorBuilder) use ($scope, $originalNode): RuleErrorBuilder {
					if (!$this->treatPhpDocTypesAsCertain) {
						return $this->possiblyImpureTipHelper->addTip($scope, $originalNode, $ruleErrorBuilder);
					}

					$booleanNativeType = $scope->getNativeType($originalNode);
					if ($booleanNativeType instanceof ConstantBooleanType) {
						return $this->possiblyImpureTipHelper->addTip($scope, $originalNode, $ruleErrorBuilder);
					}
					if (!$this->treatPhpDocTypesAsCertainTip) {
						return $this->possiblyImpureTipHelper->addTip($scope, $originalNode, $ruleErrorBuilder);
					}

					$ruleErrorBuilder = $ruleErrorBuilder->treatPhpDocTypesAsCertainTip();

					return $this->possiblyImpureTipHelper->addTip($scope, $originalNode, $ruleErrorBuilder);
				};

				$isLast = $node->getAttribute(LastConditionVisitor::ATTRIBUTE_NAME);
				if (!$nodeType->getValue() || $isLast !== true || $this->reportAlwaysTrueInLastCondition) {
					$errorBuilder = $addTip(RuleErrorBuilder::message(sprintf(
						'Result of %s is always %s.',
						$nodeText,
						$nodeType->getValue() ? 'true' : 'false',
					)));
					if ($nodeType->getValue() && $isLast === false && !$this->reportAlwaysTrueInLastCondition) {
						$errorBuilder->tip('Remove remaining cases below this one and this error will disappear too.');
					}

					$errorBuilder->identifier(sprintf('%s.always%s', $identifierType, $nodeType->getValue() ? 'True' : 'False'));

					$errors[] = $errorBuilder->build();
				}
			}
		}

		return $errors;
	}

}
