<?php

declare(strict_types=1);

namespace App\Pagination;

use ApiPlatform\State\Pagination\PartialPaginatorInterface;

class PartialApiPaginator implements \IteratorAggregate, PartialPaginatorInterface
{
    public function __construct(private readonly \Traversable $traversable, private readonly float $currentPage, private readonly float $itemsPerPage)
    {
    }

    public function getIterator(): \Traversable
    {
        return $this->traversable;
    }

    public function count(): int
    {
        return iterator_count($this->getIterator());
    }

    public function getCurrentPage(): float
    {
        return $this->currentPage;
    }

    public function getItemsPerPage(): float
    {
        return $this->itemsPerPage;
    }
}
