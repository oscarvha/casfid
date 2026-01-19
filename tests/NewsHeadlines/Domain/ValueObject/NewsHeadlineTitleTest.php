<?php

namespace App\Tests\NewsHeadlines\Domain\ValueObject;

use App\NewsHeadlines\Domain\Exception\InvalidNewsHeadlineTitle;
use App\NewsHeadlines\Domain\ValueObject\NewsHeadlineTitle;
use PHPUnit\Framework\TestCase;

final class NewsHeadlineTitleTest extends TestCase
{
    public function test_it_creates_valid_title(): void
    {
        $title = NewsHeadlineTitle::fromString('This is a valid headline');

        $this->assertSame('This is a valid headline', $title->toString());
    }

    public function test_it_throws_exception_when_title_is_empty(): void
    {
        $this->expectException(InvalidNewsHeadlineTitle::class);

        NewsHeadlineTitle::fromString('');
    }

    public function test_it_throws_exception_when_title_is_too_long(): void
    {
        $this->expectException(InvalidNewsHeadlineTitle::class);

        NewsHeadlineTitle::fromString(str_repeat('a', 256));
    }

    public function test_it_compares_two_titles(): void
    {
        $t1 = NewsHeadlineTitle::fromString('Same title');
        $t2 = NewsHeadlineTitle::fromString('Same title');
        $t3 = NewsHeadlineTitle::fromString('Different title');

        $this->assertTrue($t1->equals($t2));
        $this->assertFalse($t1->equals($t3));
    }
}
