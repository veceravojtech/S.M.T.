<?php

namespace SMT\SortedLinkedList;

interface SortedLinkedListInterface
{
    /** add item to list and sort it, when is it possible */
    public function addItemAndSort(string|int|null $item): void;

    /** remove item from list */
    public function removeItem(string|int|null $item): void;

    /** get all items from list */
    public function getItems(Sort $sort = Sort::ASC): array;

    /** does list contains item? */
    public function contains(string|int|null $item): bool;

    /** get size of list */
    public function getSize(): int;

    /** is list empty? */
    public function isEmpty(): bool;

    /** get first item from list */
    public function first(): string|int|null;

    /** get last item from list */
    public function last(): string|int|null;

    /** build list from array */
    public static function fromArray(DataType $dataType, array $items): SortedLinkedListInterface;
}