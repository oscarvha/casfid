<?php

namespace App\Tests\NewsHeadlines\Domain\Collection;

use App\NewsHeadlines\Domain\Collection\NewsHeadlineCollection;
use App\NewsHeadlines\Domain\Model\NewsHeadline;
use App\NewsHeadlines\Domain\ValueObject\NewsHeadlineId;
use App\NewsHeadlines\Domain\ValueObject\NewsHeadlineSource;
use App\NewsHeadlines\Domain\ValueObject\NewsHeadlineTitle;
use App\NewsHeadlines\Domain\ValueObject\NewsHeadlineUrl;
use PHPUnit\Framework\TestCase;

class NewsHeadlineCollectionTest extends TestCase
{
    private function headline(int $position = 1): NewsHeadline
    {
        return NewsHeadline::create(
            NewsHeadlineId::fromString(bin2hex(random_bytes(16))),
            NewsHeadlineSource::elPais(),
            NewsHeadlineTitle::fromString('Some title'),
            NewsHeadlineUrl::fromString('https://example.com/news'),
            $position,
            new \DateTimeImmutable()
        );
    }

    public function test_it_can_be_created_from_array(): void
    {
        $collection = NewsHeadlineCollection::fromArray([
            $this->headline(),
            $this->headline(),
        ]);

        $this->assertCount(2, iterator_to_array($collection));
    }

    public function test_it_is_iterable(): void
    {
        $collection = NewsHeadlineCollection::fromArray([
            $this->headline(),
            $this->headline(),
            $this->headline(),
        ]);

        $count = 0;
        foreach ($collection as $item) {
            $this->assertInstanceOf(NewsHeadline::class, $item);
            $count++;
        }

        $this->assertSame(3, $count);
    }

    public function test_it_can_take_a_limited_number_of_items(): void
    {
        $collection = NewsHeadlineCollection::fromArray([
            $this->headline(),
            $this->headline(),
            $this->headline(),
        ]);

        $limited = $collection->take(2);

        $this->assertCount(2, iterator_to_array($limited));
    }

    public function test_take_returns_a_new_instance(): void
    {
        $collection = NewsHeadlineCollection::fromArray([
            $this->headline(),
            $this->headline(),
        ]);

        $limited = $collection->take(1);

        $this->assertNotSame($collection, $limited);
    }

    public function test_take_does_not_modify_original_collection(): void
    {
        $collection = NewsHeadlineCollection::fromArray([
            $this->headline(),
            $this->headline(),
            $this->headline(),
        ]);

        $collection->take(1);

        $this->assertCount(3, iterator_to_array($collection));
    }

    public function test_it_can_merge_two_collections(): void
    {
        $collection1 = NewsHeadlineCollection::fromArray([
            $this->headline(),
            $this->headline(),
        ]);

        $collection2 = NewsHeadlineCollection::fromArray([
            $this->headline(),
            $this->headline(),
            $this->headline(),
        ]);

        $merged = $collection1->merge($collection2);

        $this->assertCount(5, iterator_to_array($merged));
    }
}
