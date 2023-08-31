<?php
declare(strict_types=1);

namespace SMT\SortedLinkedList;

use http\Exception\InvalidArgumentException;

final class SortedLinkedList implements SortedLinkedListInterface
{
    public function __construct(
        private readonly DataType $dataType,
        private ?Item $currentItem = null
    ) {}

    public function addItemAndSort(string|int|null $item): void
    {
        if (null === $item) {
            return;
        }

        $this->checkActualDataType($item);

        $newItem = new Item($item);

        if (!$this->currentItem || $item <= $this->currentItem->getValue()) {
            $newItem->setNextItem($this->currentItem);
            $this->currentItem = $newItem;

            return;
        }

        $currentItem = $this->currentItem;
        while ($currentItem->getNextItem() && $item > $currentItem->getNextItem()->getValue()) {
            $currentItem = $currentItem->getNextItem();
        }
        $newItem->setNextItem($currentItem->getNextItem());
        $currentItem->setNextItem($newItem);
    }

    public function removeItem(string|int|null $item): void
    {
        if (null === $item) {
            return;
        }

        $this->checkActualDataType($item);

        //nothing to remove
        if (!$this->currentItem) {
            return;
        }

        //remove first item
        if ($this->currentItem->getValue() === $item) {
            $this->currentItem = $this->currentItem->getNextItem();

            return;
        }

        //remove any other item
        $currentItem = $this->currentItem;
        while ($itemNext = $currentItem->getNextItem()) {
            if ($itemNext->getValue() === $item) {
                $currentItem->setNextItem($itemNext->getNextItem());

                return;
            }
            $currentItem = $itemNext;
        }
    }

    public function contains(string|int|null $item): bool
    {
        if (null === $item) {
            return false;
        }

        $this->checkActualDataType($item);

        $currentItem = $this->currentItem;
        while ($currentItem) {
            if ($currentItem->getValue() === $item) {
                return true;
            }
            $currentItem = $currentItem->getNextItem();
        }

        return false;
    }

    public function getItems(Sort $sort = Sort::ASC): array
    {
        if (!$this->currentItem) {
            return [];
        }

        $items = [];
        $currentItem = $this->currentItem;
        while ($currentItem) {
            $items[] = $currentItem->getValue();
            $currentItem = $currentItem->getNextItem();
        }

        if ($sort === Sort::DESC) {
            return array_reverse($items);
        }

        return $items;
    }

    public function getSize(): int
    {
        $items = 0;

        if (!$this->currentItem) {
            return $items;
        }

        $currentItem = $this->currentItem;
        while ($currentItem) {
            $items++;
            $currentItem = $currentItem->getNextItem();
        }

        return $items;
    }

    public function isEmpty(): bool
    {
        return $this?->currentItem === null;
    }

    public function first(): string|int|null
    {
        return $this?->currentItem?->getValue();
    }

    public function last(): string|int|null
    {
        $item = null;
        $currentItem = $this->currentItem;
        while ($currentItem) {
            $item = $currentItem->getValue();
            $currentItem = $currentItem->getNextItem();
        }

        return $item;
    }

    public static function fromArray(DataType $dataType, array $items): SortedLinkedListInterface
    {
        $list = new self($dataType);
        array_walk($items, static fn(string|int|null $item) => $list->addItemAndSort($item));

        return $list;
    }

    private function checkActualDataType(int|string $item): void
    {
        $itemType = gettype($item);

        $dataType = match ($itemType) {
            'integer' => DataType::INTEGER,
            'string' => DataType::STRING,
            default => throw new InvalidArgumentException("Unsupported data type: $itemType"),
        };

        if ($dataType !== $this->dataType) {
            throw new InvalidArgumentException("Invalid input data type: $itemType");
        }
    }
}