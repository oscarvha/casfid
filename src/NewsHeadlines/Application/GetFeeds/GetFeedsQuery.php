<?php

namespace App\NewsHeadlines\Application\GetFeeds;

use App\NewsHeadlines\Application\GetFeeds\Dto\FeedItemDto;
use App\NewsHeadlines\Application\GetFeeds\Dto\FeedResponseDto;
use App\NewsHeadlines\Application\GetFeeds\Exception\InvalidCursorException;
use App\NewsHeadlines\Domain\Repository\NewsHeadlineRepository;
use DateTimeImmutable;
use Exception;
use JsonException;

final class GetFeedsQuery
{
    private const MAX_LIMIT = 50;

    public function __construct(
        private readonly NewsHeadlineRepository $repository
    ) {}

    /**
     * @param int $limit
     * @param string|null $cursor
     * @return FeedResponseDto
     * @throws JsonException
     * @throws Exception
     */
    public function execute(int $limit, ?string $cursor) : FeedResponseDto
    {
        $limit = min($limit, self::MAX_LIMIT);

        $cursorCreatedAt = null;
        $cursorId = null;

        if ($cursor !== null) {
            $decodedRaw = base64_decode($cursor, true);

            if ($decodedRaw === false) {
                throw new InvalidCursorException('Invalid base64 encoding.');
            }

            $decoded = json_decode($decodedRaw, true, 512, JSON_THROW_ON_ERROR);

            if (!isset($decoded['createdAt'], $decoded['id'])) {
                throw new InvalidCursorException('Cursor is missing required fields.');
            }

           $cursorCreatedAt = new DateTimeImmutable($decoded['createdAt']);
            $cursorId = $decoded['id'];
        }

        $items = $this->repository->findPaginated(
            $limit + 1,
            $cursorCreatedAt,
            $cursorId
        );

        $itemsArray = iterator_to_array($items);

        $hasMore = count($itemsArray) > $limit;

        $itemsArray = array_slice($itemsArray, 0, $limit);

        $dtos = array_map(
            static fn ($item) => FeedItemDto::fromDomain($item),
            $itemsArray
        );

        $nextCursor = null;

        if ($hasMore) {
            $last = end($dtos);

            $nextCursor = base64_encode(json_encode([
                'createdAt' => $last->createdAt,
                'id' => $last->id,
            ], JSON_THROW_ON_ERROR));
        }

        return new FeedResponseDto($dtos, $nextCursor);

    }
}
