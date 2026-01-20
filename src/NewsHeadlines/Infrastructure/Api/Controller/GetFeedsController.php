<?php

namespace App\NewsHeadlines\Infrastructure\Api\Controller;

use App\NewsHeadlines\Application\GetFeeds\Dto\FeedItemDto;
use App\NewsHeadlines\Application\GetFeeds\Dto\FeedResponseDto;
use App\NewsHeadlines\Application\GetFeeds\GetFeedsQuery;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final readonly class GetFeedsController
{
    public function __construct(
        private GetFeedsQuery $feedsQuery,
    ) {
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    #[Route('/api/feeds', name: 'news_headline_get', methods: ['GET'])]
    public function __invoke(Request $request): JsonResponse
    {
        $limit = (int) $request->query->get('limit', 2);
        $cursor = $request->query->get('cursor');

        $items = $this->feedsQuery->execute($limit, $cursor);

        $dtos = array_map(
            static fn ($headline) => FeedItemDto::fromDomain($headline),
            $items
        );

        $nextCursor = empty($dtos)
            ? null
            : end($dtos)->id;

        return new JsonResponse(
            new FeedResponseDto($dtos, $nextCursor)
        );
    }

}
