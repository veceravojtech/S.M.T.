<?php
declare(strict_types=1);

namespace SMT\SortedLinkedList;

final class Item
{
    public function __construct(
        private readonly int|string $value,
        private ?Item               $nextItem = null
    ) {}

    public function getValue(): int|string
    {
        return $this->value;
    }

    public function getNextItem(): ?Item
    {
        return $this->nextItem;
    }

    public function setNextItem(?Item $nextItem): void
    {
        $this->nextItem = $nextItem;
    }
}