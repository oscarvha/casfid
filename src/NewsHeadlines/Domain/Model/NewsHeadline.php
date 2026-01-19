<?php

namespace App\NewsHeadlines\Domain\Model;

use App\NewsHeadlines\Domain\ValueObject\NewsHeadlineId;
use App\NewsHeadlines\Domain\ValueObject\NewsHeadlineSource;
use App\NewsHeadlines\Domain\ValueObject\NewsHeadlineTitle;
use App\NewsHeadlines\Domain\ValueObject\NewsHeadlineUrl;
use DateTimeImmutable;

final class NewsHeadline
{
    private function __construct(
        private NewsHeadlineId     $id,
        private NewsHeadlineSource $source,
        private NewsHeadlineTitle  $title,
        private NewsHeadlineUrl    $url,
        private int                $position,
        private DateTimeImmutable  $scrapedAt
    ) {}

    public static function create(
        NewsHeadlineId     $id,
        NewsHeadlineSource $source,
        NewsHeadlineTitle  $title,
        NewsHeadlineUrl    $url,
        int                $position,
        DateTimeImmutable  $scrapedAt
    ): self {
        return new self($id, $source, $title, $url, $position, $scrapedAt);
    }
}
