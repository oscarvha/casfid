<?php

namespace App\NewsHeadlines\Application\GetFeeds\Dto;

use App\NewsHeadlines\Domain\Model\NewsHeadline;

final class FeedItemDto
{
    public function __construct(
        public readonly string $id,
        public readonly string $source,
        public readonly string $title,
        public readonly string $url,
        public readonly int $position,
        public readonly string $scrapedAt
    ) {}

    public static function fromDomain(NewsHeadline $headline): self
    {
        return new self(
            $headline->id(),
            $headline->source(),
            $headline->title(),
            $headline->url(),
            $headline->position(),
            $headline->scrapedAt()->format(DATE_ATOM)
        );
    }
}
