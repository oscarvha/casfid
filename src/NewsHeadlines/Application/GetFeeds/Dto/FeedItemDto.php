<?php

namespace App\NewsHeadlines\Application\GetFeeds\Dto;

use App\NewsHeadlines\Domain\Model\NewsHeadline;

final readonly class FeedItemDto
{
    public function __construct(
        public string $id,
        public string $source,
        public string $title,
        public string $url,
        public string $origin,
        public int    $position,
        public string $scrapedAt,
        public string $createdAt
    ) {}

    public static function fromDomain(NewsHeadline $headline): self
    {
        return new self(
            $headline->id(),
            $headline->source(),
            $headline->title(),
            $headline->url(),
            $headline->origin(),
            $headline->position(),
            $headline->scrapedAt()->format(DATE_ATOM),
            $headline->createdAt()->format(DATE_ATOM)
        );
    }
}
