<?php

namespace App\NewsHeadlines\Domain\Collection;

use App\NewsHeadlines\Domain\Model\NewsHeadline;
use ArrayIterator;
use IteratorAggregate;

final class NewsHeadlineCollection implements IteratorAggregate
{
    /** @var NewsHeadline[] */
    private array $items;

    /**
     * @param array $items
     */
    private function __construct(array $items)
    {
        $this->items = $items;
    }

    /**
     * @param int $limit
     * @return self
     */
    public function take(int $limit): self
    {
        return new self(array_slice($this->items, 0, $limit));
    }

    /**
     * @return ArrayIterator
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->items);
    }

    /**
     * @return bool
     */
    public function isEmpty(): bool
    {
        return empty($this->items);
    }

    /**
     * @param NewsHeadlineCollection $other
     * @return self
     */
    public function merge(self $other): self
    {
        return new self(array_merge($this->items, $other->items));
    }

    /**
     * @param array $items
     * @return self
     */
    public static function fromArray(array $items): self
    {
        return new self($items);
    }
}
