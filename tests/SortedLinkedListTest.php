<?php
declare(strict_types=1);

namespace Tests;

use Generator;
use http\Exception\InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use SMT\SortedLinkedList\DataType;
use SMT\SortedLinkedList\Sort;
use SMT\SortedLinkedList\SortedLinkedList;

class SortedLinkedListTest extends TestCase
{
    /**
     * @dataProvider addAndRemoveProvider
     */
    public function testAddAndRemove(
        DataType $dataType,
        array $items,
        int|string|null $itemToRemove,
        array $expectedResult,
        ?string $throwError,
        Sort $sort = Sort::ASC
    ): void {
        try {
            $list = SortedLinkedList::fromArray($dataType, $items);
            $list->removeItem($itemToRemove);
        } catch (InvalidArgumentException $exception) {
            self::assertSame($throwError, $exception->getMessage());

            return;
        }

        self::assertSame($expectedResult, $list->getItems($sort));
        self::assertSame(count($expectedResult), $list->getSize());
        self::assertFalse($list->isEmpty());
    }

    public static function addAndRemoveProvider(): Generator
    {
        yield 'add both error' => [
            'dataType' => DataType::INTEGER,
            'items' => [1, 2, 'a'],
            'itemToRemove' => null,
            'expectedResult' => [],
            'throwError' => 'Invalid input data type: string',
        ];
        yield 'add INT remove STRING' => [
            'dataType' => DataType::INTEGER,
            'items' => [1, 2, 3],
            'itemToRemove' => 'a',
            'expectedResult' => [1, 2 ,3],
            'throwError' => 'Invalid input data type: string',
        ];
        yield 'add STRING remove INT' => [
            'dataType' => DataType::STRING,
            'items' => ['a', 'b', 'c'],
            'itemToRemove' => 1,
            'expectedResult' => ['a', 'b', 'c'],
            'throwError' => 'Invalid input data type: integer',
        ];
        yield 'add invalid input data type integer' => [
            'dataType' => DataType::STRING,
            'items' => [1],
            'itemToRemove' => null,
            'expectedResult' => [],
            'throwError' => 'Invalid input data type: integer',
        ];
        yield 'add invalid input data type string' => [
            'dataType' => DataType::INTEGER,
            'items' => ['a'],
            'itemToRemove' => null,
            'expectedResult' => [],
            'throwError' => 'Invalid input data type: string',
        ];
        yield 'add unsorted, result sorted' => [
            'dataType' => DataType::INTEGER,
            'items' => [1, 3, 2],
            'itemToRemove' => null,
            'expectedResult' => [1, 2, 3],
            'throwError' => null,
        ];
        yield 'add unsorted, result sorted DESC' => [
            'dataType' => DataType::INTEGER,
            'items' => [1, 3, 2],
            'itemToRemove' => null,
            'expectedResult' => [3, 2, 1],
            'throwError' => null,
            'sort' => Sort::DESC,
        ];
        yield 'add unsorted string, result sorted' => [
            'dataType' => DataType::STRING,
            'items' => ['b', 'a', 'c'],
            'itemToRemove' => null,
            'expectedResult' => ['a', 'b', 'c'],
            'throwError' => null,
        ];
        yield 'remove first' => [
            'dataType' => DataType::STRING,
            'items' => ['a', 'b', 'c'],
            'itemToRemove' => 'a',
            'expectedResult' => ['b', 'c'],
            'throwError' => null,
        ];
        yield 'remove middle' => [
            'dataType' => DataType::STRING,
            'items' => ['a', 'b', 'c'],
            'itemToRemove' => 'b',
            'expectedResult' => ['a', 'c'],
            'throwError' => null,
        ];
        yield 'remove last' => [
            'dataType' => DataType::STRING,
            'items' => ['a', 'b'],
            'itemToRemove' => 'c',
            'expectedResult' => ['a', 'b'],
            'throwError' => null,
        ];
    }

    public function testFirst(): void
    {
        $list = SortedLinkedList::fromArray(DataType::INTEGER, [1, 2, 3]);

        self::assertSame(1, $list->first());
    }

    public function testLast(): void
    {
        $list = SortedLinkedList::fromArray(DataType::INTEGER, [1, 2, 3]);

        self::assertSame(3, $list->last());
    }

    public function testContains(): void
    {
        $list = SortedLinkedList::fromArray(DataType::INTEGER, [1, 2, 3]);

        self::assertTrue($list->contains(1));
        self::assertTrue($list->contains(2));
        self::assertTrue($list->contains(3));
        self::assertFalse($list->contains(4));
    }

    public function testAllOnEmptyList(): void
    {
        $list = SortedLinkedList::fromArray(DataType::INTEGER, []);

        self::assertSame([], $list->getItems());
        self::assertSame(0, $list->getSize());
        self::assertSame(null, $list->first());
        self::assertSame(null, $list->last());
        self::assertSame(false, $list->contains(0));
    }

    public function testFunctionWithNullOrOnEmptyList(): void
    {
        $list = SortedLinkedList::fromArray(DataType::STRING, []);
        $exception = false;

        try {
            $list->addItemAndSort(null);
            $list->removeItem(null);
            $list->removeItem('a');
            $list->contains(null);
            $list->contains('a');
        } catch (\Throwable) {
            $exception = true;
        }

        self::assertFalse($exception);
    }
}