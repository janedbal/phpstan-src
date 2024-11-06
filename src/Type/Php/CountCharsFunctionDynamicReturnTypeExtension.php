<?php declare(strict_types = 1);

namespace PHPStan\Type\Php;

use PhpParser\Node\Expr\FuncCall;
use PHPStan\Analyser\Scope;
use PHPStan\Php\PhpVersion;
use PHPStan\Reflection\FunctionReflection;
use PHPStan\Type\ArrayType;
use PHPStan\Type\Constant\ConstantBooleanType;
use PHPStan\Type\DynamicFunctionReturnTypeExtension;
use PHPStan\Type\IntegerRangeType;
use PHPStan\Type\IntegerType;
use PHPStan\Type\StringType;
use PHPStan\Type\Type;
use PHPStan\Type\TypeUtils;
use PHPStan\Type\UnionType;
use function count;

final class CountCharsFunctionDynamicReturnTypeExtension implements DynamicFunctionReturnTypeExtension
{

	public function __construct(private PhpVersion $phpVersion)
	{
	}

	public function isFunctionSupported(FunctionReflection $functionReflection): bool
	{
		return $functionReflection->getName() === 'count_chars';
	}

	public function getTypeFromFunctionCall(
		FunctionReflection $functionReflection,
		FuncCall $functionCall,
		Scope $scope,
	): ?Type
	{
		if (count($functionCall->getArgs()) < 1) {
			return null;
		}

		$modeType = $scope->getType($functionCall->getArgs()[1]->value);

		if (IntegerRangeType::fromInterval(0, 2)->isSuperTypeOf($modeType)->yes()) {
			$arrayType = new ArrayType(new IntegerType(), new IntegerType());

			return $this->phpVersion->throwsValueErrorForInternalFunctions()
				? $arrayType
				: TypeUtils::toBenevolentUnion(new UnionType([$arrayType, new ConstantBooleanType(false)]));
		}

		$stringType = new StringType();

		return $this->phpVersion->throwsValueErrorForInternalFunctions()
			? $stringType
			: TypeUtils::toBenevolentUnion(new UnionType([$stringType, new ConstantBooleanType(false)]));
	}

}