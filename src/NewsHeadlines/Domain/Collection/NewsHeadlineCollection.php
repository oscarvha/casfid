<?php

namespace App\NewsHeadlines\Domain\Collection;

use App\NewsHeadlines\Domain\Model\NewsHeadline;
use ArrayIterator;
use IteratorAggregate;

/**
 * @implements IteratorAggregate<int, NewsHeadline>
 */
final class NewsHeadlineCollection implements IteratorAggregate
{
    /** @var list<NewsHeadline> */
    private array $items;

    /**
     * @param list<NewsHeadline> $items
     */
    private function __construct(array $items)
    {
        $this->items = $items;
    }

    public function take(int $limit): self
    {
        return new self(array_slice($this->items, 0, $limit));
    }

    /**
     * @return ArrayIterator<int, NewsHeadline>
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->items);
    }

    public function isEmpty(): bool
    {
        return $this->items === [];
    }

    public function merge(self $other): self
    {
        return new self(array_merge($this->items, $other->items));
    }

    /**
     * @param list<NewsHeadline> $items
     */
    public static function fromArray(array $items): self
    {
        return new self($items);
    }
}
