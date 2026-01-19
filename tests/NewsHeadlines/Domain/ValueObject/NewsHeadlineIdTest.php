<?php

namespace App\Tests\NewsHeadlines\Domain\ValueObject;

use App\NewsHeadlines\Domain\ValueObject\NewsHeadlineId;
use PHPUnit\Framework\TestCase;

class NewsHeadlineIdTest extends TestCase
{
    public function test_it_can_be_created_with_valid_value(): void
    {
        $id = NewsHeadlineId::fromString('123e4567-e89b-12d3-a456-426614174000');

        $this->assertSame(
            '123e4567-e89b-12d3-a456-426614174000',
            $id->toString()
        );
    }

    public function test_it_throws_exception_when_value_is_empty(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        NewsHeadlineId::fromString('');
    }

    public function test_it_can_compare_two_ids(): void
    {
        $id1 = NewsHeadlineId::fromString('a');
        $id2 = NewsHeadlineId::fromString('a');
        $id3 = NewsHeadlineId::fromString('b');

        $this->assertTrue($id1->equals($id2));
        $this->assertFalse($id1->equals($id3));
    }
}
