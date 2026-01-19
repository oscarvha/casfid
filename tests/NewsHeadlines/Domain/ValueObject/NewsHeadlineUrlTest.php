<?php

namespace App\Tests\NewsHeadlines\Domain\ValueObject;

use App\NewsHeadlines\Domain\Exception\InvalidNewsHeadlineUrl;
use App\NewsHeadlines\Domain\ValueObject\NewsHeadlineUrl;
use PHPUnit\Framework\TestCase;

final class NewsHeadlineUrlTest extends TestCase
{
    public function test_it_can_be_created_with_valid_url(): void
    {
        $url = 'https://elpais.com/internacional/2026/01/15/example.html';

        $headlineUrl = NewsHeadlineUrl::fromString($url);

        $this->assertSame($url, $headlineUrl->toString());
    }

    public function test_it_throws_exception_for_invalid_url(): void
    {
        $this->expectException(InvalidNewsHeadlineUrl::class);

        NewsHeadlineUrl::fromString('not-a-valid-url');
    }

    public function test_it_throws_exception_for_empty_string(): void
    {
        $this->expectException(InvalidNewsHeadlineUrl::class);

        NewsHeadlineUrl::fromString('');
    }

    public function test_it_throws_exception_for_non_url_text(): void
    {
        $this->expectException(InvalidNewsHeadlineUrl::class);

        NewsHeadlineUrl::fromString('elpais.com/noticia');
    }
}
