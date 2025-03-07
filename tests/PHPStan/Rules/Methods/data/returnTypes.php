<?php

namespace ReturnTypes;

class Foo extends FooParent implements FooInterface
{

	public function returnNothing()
	{
		return;
	}

	public function returnInteger(): int
	{
		if (rand(0, 1)) {
			return 1;
		}

		if (rand(0, 1)) {
			return 'foo';
		}
		$foo = function () {
			return 'bar';
		};
	}

	public function returnObject(): Bar
	{
		if (rand(0, 1)) {
			return 1;
		}

		if (rand(0, 1)) {
			return new self();
		}

		if (rand(0, 1)) {
			return new Bar();
		}
	}

	public function returnChild(): self
	{
		if (rand(0, 1)) {
			return new self();
		}

		if (rand(0, 1)) {
			return new FooChild();
		}

		if (rand(0, 1)) {
			return new OtherInterfaceImpl();
		}
	}

	/**
	 * @return string|null
	 */
	public function returnNullable()
	{
		if (rand(0, 1)) {
			return 'foo';
		}

		if (rand(0, 1)) {
			return null;
		}
	}

	public function returnInterface(): FooInterface
	{
		return new self();
	}

	/**
	 * @return void
	 */
	public function returnVoid()
	{
		if (rand(0, 1)) {
			return;
		}

		if (rand(0, 1)) {
			return null;
		}

		if (rand(0, 1)) {
			return 1;
		}
	}

	/**
	 * @return static
	 */
	public function returnStatic(): FooParent
	{
		if (rand(0, 1)) {
			return parent::returnStatic();
		}

		$parent = new FooParent();

		if (rand(0, 1)) {
			return $parent->returnStatic(); // the only case with wrong static base class
		}

		if (rand(0, 1)) {
			return $this->returnStatic();
		}
	}

	public function returnAlias(): Foo
	{
		return new FooAlias();
	}

	public function returnAnotherAlias(): FooAlias
	{
		return new Foo();
	}

	/**
	 * @param self[]|Collection $collection
	 * @return self[]|Collection|array
	 */
	public function returnUnionIterableType($collection)
	{
		if (rand(0, 1)) {
			return $collection;
		}

		if (rand(0, 1)) {
			return new Collection();
		}

		if (rand(0, 1)) {
			return new self();
		}

		if (rand(0, 1)) {
			return [new self()];
		}

		if (rand(0, 1)) {
			return new Bar();
		}

		if (rand(0, 1)) {
			return [new Bar()];
		}

		if (rand(0, 1)) {
			return 1;
		}

		if (rand(0, 1)) {
			return;
		}

		/** @var Bar[]|Collection $barListOrCollection */
		$barListOrCollection = doFoo();

		if (rand(0, 1)) {
			return $barListOrCollection;
		}

		/** @var self[]|AnotherCollection $selfListOrAnotherCollection */
		$selfListOrAnotherCollection = doFoo();

		if (rand(0, 1)) {
			return $selfListOrAnotherCollection;
		}

		/** @var self[]|Collection|AnotherCollection $selfListOrCollectionorAnotherCollection */
		$selfListOrCollectionorAnotherCollection = doFoo();

		if (rand(0, 1)) {
			return $selfListOrCollectionorAnotherCollection;
		}

		/** @var Bar[]|AnotherCollection $completelyDiffernetUnionIterable */
		$completelyDiffernetUnionIterable = doFoo();

		if (rand(0, 1)) {
			return $completelyDiffernetUnionIterable;
		}

		if (rand(0, 1)) {
			return null;
		}
	}

	/**
	 * @param self[]|Collection $collection
	 * @return self[]|Collection|AnotherCollection|null
	 */
	public function returnUnionIterableLooserReturnType($collection)
	{
		if (rand(0, 1)) {
			return $collection;
		}

		if (rand(0, 1)) {
			return null;
		}
	}

	/**
	 * @return $this
	 */
	public function returnThis(): self
	{
		if (rand(0, 1)) {
			return $this;
		}
		if (rand(0, 1)) {
			return new self();
		}
		if (rand(0, 1)) {
			return 1;
		}
		if (rand(0, 1)) {
			return new static();
		}

		if (rand(0, 1)) {
			return null;
		}

		if (rand(0, 1)) {
			$that = $this;
			return $that;
		}
	}

	/**
	 * @return $this|null
	 */
	public function returnThisOrNull()
	{
		if (rand(0, 1)) {
			return $this;
		}
		if (rand(0, 1)) {
			return new self();
		}
		if (rand(0, 1)) {
			return 1;
		}
		if (rand(0, 1)) {
			return null;
		}
		if (rand(0, 1)) {
			return $this->returnThis();
		}
		if (rand(0, 1)) {
			return $this->returnStaticThatReturnsNewStatic();
		}
	}

	/**
	 * @return static
	 */
	public function returnStaticThatReturnsNewStatic(): self
	{
		if (rand(0, 1)) {
			return new static();
		}
		if (rand(0, 1)) {
			return $this;
		}
	}

	public function returnsParent(): parent
	{
		if (rand(0, 1)) {
			return new FooParent();
		}
		if (rand(0, 1)) {
			return 1;
		}
		if (rand(0, 1)) {
			return null;
		}
	}

	/**
	 * @return parent
	 */
	public function returnsPhpDocParent()
	{
		if (rand(0, 1)) {
			return new FooParent();
		}
		if (rand(0, 1)) {
			return 1;
		}
		if (rand(0, 1)) {
			return null;
		}
	}

	/**
	 * @return scalar
	 */
	public function returnScalar()
	{
		if (rand(0, 1)) {
			return 1;
		}
		if (rand(0, 1)) {
			return 10.1;
		}
		if (rand(0, 1)) {
			return 'a';
		}
		if (rand(0, 1)) {
			return false;
		}
		if (rand(0, 1)) {
			return new \stdClass();
		}
	}

	/**
	 * @return int
	 */
	public function containsYield()
	{
		yield 1;
		return;
	}

	public function returnsNullInTernary(): int
	{
		/** @var int|null $intOrNull */
		$intOrNull = doFoo();

		if (rand(0, 1)) {
			return $intOrNull;
		}
		if (rand(0, 1)) {
			return $intOrNull !== null ? $intOrNull : 5;
		}
		if (rand(0, 1)) {
			return $intOrNull !== null ? $intOrNull : null;
		}
	}

	public function misleadingBoolReturnType(): \ReturnTypes\boolean
	{
		if (rand(0, 1)) {
			return true;
		}
		if (rand(0, 1)) {
			return 1;
		}
		if (rand(0, 1)) {
			return new boolean();
		}
	}

	public function misleadingIntReturnType(): \ReturnTypes\integer
	{
		if (rand(0, 1)) {
			return 1;
		}
		if (rand(0, 1)) {
			return true;
		}
		if (rand(0, 1)) {
			return new integer();
		}
	}

	/*public function misleadingMixedReturnType(): mixed
	{
		if (rand(0, 1)) {
			return 1;
		}
		if (rand(0, 1)) {
			return true;
		}
		if (rand(0, 1)) {
			return new mixed();
		}
	}*/
}

class FooChild extends Foo
{

}

class Stock
{

	/** @var self */
	private $stock;

	/** @var self|null */
	private $nullableStock;

	public function getActualStock(): self
	{
		if (is_null($this->stock))
		{
			$this->stock = $this->findStock();
			if (is_null($this->stock)) {
				throw new \Exception();
			}
			return $this->stock;
		}
		return $this->stock;
	}

	/**
	 * @return self|null
	 */
	public function findStock()
	{
		return new self();
	}

	public function getAnotherStock(): self
	{
		return $this->findStock();
	}

	public function returnSelf(): self
	{
		$stock = $this->findStock();
		if ($stock === null) {
			$stock = new self();
		}

		return $stock;
	}

	public function returnSelfAgain(): self
	{
		$stock = $this->findStock();
		if ($stock === null) {
			$stock = new self();
		} elseif (test()) {
			doFoo();
		}

		return $stock;
	}

	public function returnSelfYetAgain(): self
	{
		$stock = $this->findStock();
		if ($stock === null) {
			$stock = new self();
		} elseif (test()) {
			doFoo();
		} else {
			doBar();
		}

		return $stock;
	}

	public function returnSelfYetYetAgain(): self
	{
		if ($this->nullableStock === null) {
			$this->nullableStock = new self();
		}

		return $this->nullableStock;
	}

	public function returnSelfAgainError(): self
	{
		$stock = $this->findStock();
		if (doFoo()) {
			$stock = new self();
		}

		return $stock; // still possible null
	}

	public function returnsSelfAgainAgain(): self
	{
		while (true) {
			try {
				if ($this->getActualStock() === null) {
					continue;
				}
			} catch (\Exception $ex) {
				continue;
			}
			return $this->getActualStock();
		}
	}

	public function returnYetSelfAgainError(): self
	{
		$stock = $this->findStock();
		if ($stock === false) {
			$stock = new self();
		}

		return $stock; // still possible null
	}

}

class Issue105
{
	/**
	 * @param string $type
	 *
	 * @return array|float|int|null|string
	 */
	public function manyTypes(string $type)
	{

	}

	/**
	 * @return array
	 */
	public function returnArray(): array
	{
		$result = $this->manyTypes('array');
		$result = is_array($result) ? $result : [];

		return $result;
	}

	public function returnAnotherArray(): array
	{
		$result = $this->manyTypes('array');
		if (!is_array($result)) {
			$result = [];
		}

		return $result;
	}
}

class ReturningSomethingFromConstructor
{

	public function __construct()
	{
		return new Foo();
	}

}

class WeirdReturnFormat
{

	/**
	 * @return \PHPStan\Foo\Bar |
	 *         \PHPStan\Foo\Baz
	 */
	public function test()
	{
		return 1;
	}

}

class Collection implements \IteratorAggregate
{

	#[\ReturnTypeWillChange]
	public function getIterator()
	{
		return new \ArrayIterator([]);
	}

}

class AnotherCollection implements \IteratorAggregate
{

	#[\ReturnTypeWillChange]
	public function getIterator()
	{
		return new \ArrayIterator([]);
	}

}

class GeneratorMethod
{

	public function doFoo(): \Generator
	{
		return false;
		yield "foo";
	}

}

class ReturnTernary
{

	/**
	 * @param Foo|false $fooOrFalse
	 * @return Foo
	 */
	public function returnTernary($fooOrFalse): Foo
	{
		if (rand(0, 1)) {
			return $fooOrFalse ?: new Foo();
		}
		if (rand(0, 1)) {
			return $fooOrFalse !== false ? $fooOrFalse : new Foo();
		}

		if (rand(0, 1)) {
			$fooOrFalse ? ($fooResult = $fooOrFalse) : new Foo();
			return $fooResult;
		}

		if (rand(0, 1)) {
			$fooOrFalse ? false : ($falseResult = $fooOrFalse);
			return $falseResult;
		}
	}

	/**
	 * @return static|null
	 */
	public function returnStatic()
	{
		$out = doFoo();

		return is_a($out, static::class, false) ? $out : null;
	}

}

class TrickyVoid
{

	/**
	 * @return int|void
	 */
	public function returnVoidOrInt()
	{
		if (rand(0, 1)) {
			return;
		}
		if (rand(0, 1)) {
			return  1;
		}
		if (rand(0, 1)) {
			return 'str';
		}
	}

}

class TernaryWithJsonEncode
{

	public function toJsonOrNull(array $arr, string $s): ?string
	{
		if (rand(0, 1)) {
			return json_encode($arr) ?: null;
		}
		if (rand(0, 1)) {
			return json_encode($arr) ? json_encode($arr): null;
		}
		if (rand(0, 1)) {
			return (rand(0, 1) ? $s : false) ?: null;
		}
	}

	public function toJson(array $arr): string
	{
		if (rand(0, 1)) {
			return json_encode($arr) ?: '';
		}
		if (rand(0, 1)) {
			return json_encode($arr) ? json_encode($arr) : '';
		}
		if (rand(0, 1)) {
			return json_encode($arr) ?: json_encode($arr);
		}
	}

}

class AppendedArrayReturnType
{

	/** @return int[] */
	public function foo() : array {
		$arr = [];
		$arr[] = new \stdClass();
		return $arr;
	}

	/**
	 * @param int[] $arr
	 * @return int[]
	 */
	public function bar(array $arr): array
	{
		$arr[] = new \stdClass();
		return $arr;
	}

}

class WrongMagicMethods
{

	public function __toString()
	{
		return true;
	}

	public function __isset($name)
	{
		return 42;
	}

	public function __destruct()
	{
		return 1;
	}

	public function __unset($name)
	{
		return 1;
	}

	public function __sleep()
	{
		return [
			new \stdClass(),
		];
	}

	public function __wakeup()
	{
		return 1;
	}

	public static function __set_state(array $properties)
	{
		return ['foo' => 'bar'];
	}

	public function __clone()
	{
		return 1;
	}

}

class ReturnSpecifiedMethodCall
{

	/**
	 * @return string|false
	 */
	public function stringOrFalse()
	{

	}

	public function doFoo(): string
	{
		if ($this->stringOrFalse()) {
			return $this->stringOrFalse();
		}

		if (is_string($this->stringOrFalse())) {
			return $this->stringOrFalse();
		}

		return '';
	}

}

class ArrayFillKeysIssue
{
	/**
	 * @param string[] $stringIds
	 *
	 * @return array<string, Foo[]>
	 */
	public function getIPs(array $stringIds)
	{
		$paired = array_fill_keys($stringIds, []);
		foreach ($stringIds as $id) {
			$paired[$id][] = new Foo();
		}
		return $paired;
	}

	/**
	 * @param string[] $stringIds
	 *
	 * @return array<string, Foo[]>
	 */
	public function getIPs2(array $stringIds)
	{
		$paired = array_fill_keys($stringIds, []);
		foreach ($stringIds as $id) {
			$paired[$id][] = new Bar();
		}
		return $paired;
	}
}

class AssertThisInstanceOf
{

	/**
	 * @return $this
	 */
	public function doFoo()
	{
		assert($this instanceof FooInterface);
		return $this;
	}

	/**
	 * @return $this
	 */
	public function doBar(self $otherInstance)
	{
		assert($otherInstance instanceof FooInterface);
		return $otherInstance;
	}

}

class NestedArrayCheck
{

	/**
	 * @param mixed[] $rows
	 * @return array<string,bool>
	 */
	public function doFoo(array $rows)
	{
		$entities = [];

		foreach ($rows as $row) {
			$entities['string'][] = 'string';
		}

		return $entities;
	}

	/**
	 * @param mixed[] $rows
	 * @return array<string,bool>
	 */
	public function doBar(array $rows)
	{
		$entities = [];

		foreach ($rows as $row) {
			$entities['string']['foo'] = 'string';
		}

		return $entities;
	}

}

class CheckNullWithConstantType
{

	const SOME_NULL_CONST = null;

	public function doFoo(?array $nullableArray): array
	{
		if ($nullableArray === self::SOME_NULL_CONST) {
			$nullableArray = [];
		}

		return $nullableArray;
	}

}

class NullConditionInDoWhile
{

	public function doFoo(): string
	{
		do {
			$string = $this->doBar();
		} while ($string === null);

		return $string;
	}

	public function doBar(): ?string
	{

	}

}

class RecursiveStaticResolving
{
	/**
	 * @return $this
	 */
	public function f2(): self
	{
		return $this;
	}

	/**
	 * @return $this
	 */
	public function f3(): self
	{
		return $this;
	}

	/**
	 * @return $this
	 */
	public function f1(): self
	{
		return $this->f2()->f3();
	}
}

class Foo2 extends FooParent implements FooInterface
{
	public function returnIntFromParent()
	{
		if (rand(0, 1)) {
			return 1;
		}
		if (rand(0, 1)) {
			return '1';
		}
		if (rand(0, 1)) {
			return new integer();
		}
	}

	public function returnsVoid(): self
	{
		return $this;
	}
}

class HelloWorld
{
	/**
	 * @param string $column Columna
	 * @return string
	 */
	public function columnToField(string $column) : string
	{
		$idx = strrpos($column, '.');
		$field = str_replace('.', '_', $column);
		$field[$idx] = '.';

		return $field;
	}
}

class AssertInIf
{

	/** @var string|null */
	private $foo;

	public function doFoo(): string
	{
		if ($this->foo === null) {
			$foo = getenv('FOO');
			assert($foo !== false);
			assert(is_string($foo));

			$this->foo = $foo;
		}

		return $this->foo;
	}

}

class VariableOverwrittenInForeach
{

	public function doFoo(): int
	{
		$x = 0;
		$y = 1;
		foreach ([0, 1, 2] as $i) {
			$x = $y;
			$y = "hello";
		}
		return $x;
	}

	/**
	 * @param int[] $arrayOfIntegers
	 * @return int
	 */
	public function doBar(array $arrayOfIntegers): int
	{
		$x = 0;
		$y = 1;
		foreach ($arrayOfIntegers as $i) {
			$x = $y;
			$y = "hello";
		}
		return $x;
	}

}

class ReturnStaticGeneric
{

	/**
	 * @template T of self
	 * @param T $foo
	 * @return T
	 */
	public function doFoo($foo)
	{
		return $foo::returnsStatic();
	}

	/**
	 * @template T of self
	 * @param T $foo
	 * @return T
	 */
	public function doBar($foo)
	{
		return $foo->instanceReturnsStatic();
	}

	/** @return static */
	public static function returnsStatic()
	{
		return new static();
	}

	/** @return static */
	public function instanceReturnsStatic() {
		if (rand(0, 1)) {
			return new static();
		}
		if (rand(0, 1)) {
			return new self();
		}
		if (rand(0, 1)) {
			return $this;
		}
	}
}

interface InterfaceThatWillBeDocInherited
{

	/**
	 * @return $this
	 */
	public function setTableSchema(): self;

	/**
	 * @return $this
	 */
	public function setTableSchema2(): self;

	/**
	 * @return $this
	 */
	public function setTableSchema3(): self;

	/**
	 * @return static
	 */
	public function setTableSchema4(): self;

	/**
	 * @return static
	 */
	public function setTableSchema5(): self;

}

class ClassThatImplementsInterfaceAndInheritDocsIt implements InterfaceThatWillBeDocInherited
{

	public function setTableSchema(): InterfaceThatWillBeDocInherited
	{
		return $this;
	}

	/**
	 * {@inheritDoc}
	 */
	public function setTableSchema2(): InterfaceThatWillBeDocInherited
	{
		return $this;
	}

	/**
	 * @inheritDoc
	 */
	public function setTableSchema3(): InterfaceThatWillBeDocInherited
	{
		return $this;
	}

	/**
	 * @inheritDoc
	 */
	public function setTableSchema4(): InterfaceThatWillBeDocInherited
	{
		return $this;
	}

	/**
	 * @inheritDoc
	 */
	public function setTableSchema5(): InterfaceThatWillBeDocInherited
	{
		return $this;
	}

}

class WithIntegerConst
{
	public const INTEGER_CONST = 1;

	public function returnIntegerBySumWithStaticConst(): int
	{
		return static::INTEGER_CONST + 1;
	}

	public function returnIntegerBySumWithStaticConst1(): int
	{
		return static::INTEGER_CONST + 1 + 1;
	}

	public function returnIntegerBySumWithStaticConst2(): int
	{
		return 1 + static::INTEGER_CONST + 1;
	}

	public function returnIntegerBySumWithStaticConst3(): int
	{
		return 1 + 1 + static::INTEGER_CONST;
	}
}

class VarAnnotationAboveStmtReturn
{

	public function doFoo(\DateTimeInterface $date): \DateTimeImmutable
	{
		/** @var \DateTimeImmutable $date */
		return $date;
	}

	public function doBar(\DateTimeInterface $date): \DateTimeImmutable
	{
		/** @var \DateTimeImmutable */
		return $date;
	}

}

/**
 * @template CollectionKey of array-key
 * @template CollectionValue
 * @implements \Iterator<CollectionKey, CollectionValue>
 */
abstract class CollectionWithArrayKey implements \Iterator
{

	/** @var array<CollectionKey, CollectionValue> */
	private $data = [];

	/**
	 * @param CollectionValue $value
	 * @param CollectionKey
	 */
	public function add($value, $key = null): void
	{
		$this->data[$key] = $value;
	}

	/**
	 * @return CollectionKey|null
	 */
	#[\ReturnTypeWillChange]
	public function key()
	{
		return key($this->data);
	}

}

class Bug3072
{

	/**
	 * @template T
	 * @return iterable<T>
	 */
	public function getIterable(): iterable
	{
		return [];
	}

}

class NeverReturn
{

	/**
	 * @return never
	 */
	public function doFoo(): void
	{
		return;
	}

	/**
	 * @return never
	 */
	public function doBaz3(): string
	{
		try {
			throw new \Exception('try');
		} catch (\Exception $e) {
			throw new \Exception('catch');
		} finally {
			return 'finally';
		}
	}

}

interface MySQLiAffectedRowsReturnTypeInterface
{
    /**
     * @return int|numeric-string
     */
    function exec(\mysqli $connection, string $sql);
}

final class MySQLiAffectedRowsReturnType implements MySQLiAffectedRowsReturnTypeInterface
{
	/**
     * @return int<0, max>|numeric-string
     */
    function exec(\mysqli $mysqli, string $sql)
    {
	    $result = $mysqli->query($sql);

	    if ($result === false || 0 > $mysqli->affected_rows) {
		    throw new \RuntimeException();
	    }

		return $mysqli->affected_rows;
    }
}
