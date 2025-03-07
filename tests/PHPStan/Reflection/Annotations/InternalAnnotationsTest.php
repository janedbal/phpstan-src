<?php declare(strict_types = 1);

namespace PHPStan\Reflection\Annotations;

use InternalAnnotations\Foo;
use InternalAnnotations\FooInterface;
use InternalAnnotations\FooTrait;
use InternalAnnotations\InternalFoo;
use InternalAnnotations\InternalFooInterface;
use InternalAnnotations\InternalFooTrait;
use PhpParser\Node\Name;
use PHPStan\Analyser\Scope;
use PHPStan\Testing\PHPStanTestCase;

class InternalAnnotationsTest extends PHPStanTestCase
{

	public function dataInternalAnnotations(): array
	{
		return [
			[
				false,
				Foo::class,
				[
					'constant' => [
						'FOO',
					],
					'method' => [
						'foo',
						'staticFoo',
					],
					'property' => [
						'foo',
						'staticFoo',
					],
				],
			],
			[
				true,
				InternalFoo::class,
				[
					'constant' => [
						'INTERNAL_FOO',
					],
					'method' => [
						'internalFoo',
						'internalStaticFoo',
					],
					'property' => [
						'internalFoo',
						'internalStaticFoo',
					],
				],
			],
			[
				false,
				FooInterface::class,
				[
					'constant' => [
						'FOO',
					],
					'method' => [
						'foo',
						'staticFoo',
					],
				],
			],
			[
				true,
				InternalFooInterface::class,
				[
					'constant' => [
						'INTERNAL_FOO',
					],
					'method' => [
						'internalFoo',
						'internalStaticFoo',
					],
				],
			],
			[
				false,
				FooTrait::class,
				[
					'method' => [
						'foo',
						'staticFoo',
					],
					'property' => [
						'foo',
						'staticFoo',
					],
				],
			],
			[
				true,
				InternalFooTrait::class,
				[
					'method' => [
						'internalFoo',
						'internalStaticFoo',
					],
					'property' => [
						'internalFoo',
						'internalStaticFoo',
					],
				],
			],
		];
	}

	/**
	 * @dataProvider dataInternalAnnotations
	 * @param array<string, mixed> $internalAnnotations
	 */
	public function testInternalAnnotations(bool $internal, string $className, array $internalAnnotations): void
	{
		$reflectionProvider = $this->createReflectionProvider();
		$class = $reflectionProvider->getClass($className);
		$scope = $this->createMock(Scope::class);
		$scope->method('isInClass')->willReturn(true);
		$scope->method('getClassReflection')->willReturn($class);
		$scope->method('canAccessProperty')->willReturn(true);
		$scope->method('canReadProperty')->willReturn(true);
		$scope->method('canWriteProperty')->willReturn(true);

		$this->assertSame($internal, $class->isInternal());

		foreach ($internalAnnotations['method'] ?? [] as $methodName) {
			$methodAnnotation = $class->getMethod($methodName, $scope);
			$this->assertSame($internal, $methodAnnotation->isInternal()->yes());
		}

		foreach ($internalAnnotations['property'] ?? [] as $propertyName) {
			$propertyAnnotation = $class->getProperty($propertyName, $scope);
			$this->assertSame($internal, $propertyAnnotation->isInternal()->yes());
		}

		foreach ($internalAnnotations['constant'] ?? [] as $constantName) {
			$constantAnnotation = $class->getConstant($constantName);
			$this->assertSame($internal, $constantAnnotation->isInternal()->yes());
		}
	}

	public function testInternalUserFunctions(): void
	{
		require_once __DIR__ . '/data/annotations-internal.php';

		$reflectionProvider = $this->createReflectionProvider();

		$this->assertFalse($reflectionProvider->getFunction(new Name\FullyQualified('InternalAnnotations\foo'), null)->isInternal()->yes());
		$this->assertTrue($reflectionProvider->getFunction(new Name\FullyQualified('InternalAnnotations\internalFoo'), null)->isInternal()->yes());
	}

}
