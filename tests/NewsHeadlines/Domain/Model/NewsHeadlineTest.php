<?php

namespace App\Tests\NewsHeadlines\Domain\Model;

use App\NewsHeadlines\Domain\Exception\NewsDomainException;
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

    public function test_it_updates_title_and_url(): void
    {
        $headline = NewsHeadline::create(
            NewsHeadlineId::fromString('11111111-1111-1111-1111-111111111111'),
            NewsHeadlineSource::fromString('el_pais'),
            NewsHeadlineTitle::fromString('Old title'),
            NewsHeadlineUrl::fromString('https://elpais.com/old'),
            NewsHeadlineOrigin::fromString('api'),
            1,
            new \DateTimeImmutable('2026-01-20 10:00:00'),
            new \DateTimeImmutable('2026-01-20 10:00:00')
        );

        $headline->update(
            NewsHeadlineTitle::fromString('New title'),
            NewsHeadlineUrl::fromString('https://elpais.com/new')
        );

        self::assertSame('New title', $headline->title());
        self::assertSame('https://elpais.com/new', $headline->url());
    }


    public function test_update_throws_exception_with_invalid_title(): void
    {
        $this->expectException(NewsDomainException::class);

        $headline = NewsHeadline::create(
            NewsHeadlineId::fromString('11111111-1111-1111-1111-111111111111'),
            NewsHeadlineSource::fromString('el_pais'),
            NewsHeadlineTitle::fromString('Valid title'),
            NewsHeadlineUrl::fromString('https://elpais.com/old'),
            NewsHeadlineOrigin::fromString('api'),
            1,
            new \DateTimeImmutable(),
            new \DateTimeImmutable()
        );

        $headline->update(
            NewsHeadlineTitle::fromString(''),
            NewsHeadlineUrl::fromString('https://elpais.com/new')
        );
    }

    public function test_update_throws_exception_with_invalid_url(): void
    {
        $this->expectException(NewsDomainException::class);

        $headline = NewsHeadline::create(
            NewsHeadlineId::fromString('11111111-1111-1111-1111-111111111111'),
            NewsHeadlineSource::fromString('el_pais'),
            NewsHeadlineTitle::fromString('Valid title'),
            NewsHeadlineUrl::fromString('https://elpais.com/old'),
            NewsHeadlineOrigin::fromString('api'),
            1,
            new \DateTimeImmutable(),
            new \DateTimeImmutable()
        );

        $headline->update(
            NewsHeadlineTitle::fromString('New title'),
            NewsHeadlineUrl::fromString('not-a-url')
        );
    }
}
