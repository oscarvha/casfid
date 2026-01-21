<?php

namespace App\Tests\NewsHeadlines\Domain\Model;

use App\NewsHeadlines\Domain\Model\NewsHeadline;
use App\NewsHeadlines\Domain\ValueObject\NewsHeadlineId;
use App\NewsHeadlines\Domain\ValueObject\NewsHeadlineOrigin;
use App\NewsHeadlines\Domain\ValueObject\NewsHeadlineSource;
use App\NewsHeadlines\Domain\ValueObject\NewsHeadlineTitle;
use App\NewsHeadlines\Domain\ValueObject\NewsHeadlineUrl;
use PHPUnit\Framework\TestCase;

final class NewsHeadlineTest extends TestCase
{
    public function test_it_can_be_created(): void
    {
        $headline = NewsHeadline::create(
            NewsHeadlineId::fromString('123e4567-e89b-12d3-a456-426614174000'),
            NewsHeadlineSource::fromString('el_pais'),
            NewsHeadlineTitle::fromString('Example title'),
            NewsHeadlineUrl::fromString('https://elpais.com/example'),
            NewsHeadlineOrigin::api(),
            1,
            new \DateTimeImmutable()
        );

        $this->assertInstanceOf(NewsHeadline::class, $headline);
    }
}
