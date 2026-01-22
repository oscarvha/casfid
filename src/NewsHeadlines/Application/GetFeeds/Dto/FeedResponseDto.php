<?php

namespace App\NewsHeadlines\Application\GetFeeds\Dto;

final readonly class FeedResponseDto
{
    /**
     * @param list<FeedItemDto> $items
     */
    public function __construct(public array $items, public ?string $nextCursor
    ) {}
}
