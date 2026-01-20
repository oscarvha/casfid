<?php

namespace App\NewsHeadlines\Application\GetFeeds\Dto;

final class FeedResponseDto
{
    public function __construct(
        public readonly array $items,
        public readonly ?string $nextCursor
    ) {}
}
