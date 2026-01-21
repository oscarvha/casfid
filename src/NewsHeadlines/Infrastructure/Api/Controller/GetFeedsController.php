<?php

namespace App\NewsHeadlines\Infrastructure\Api\Controller;

use App\NewsHeadlines\Application\GetFeeds\Dto\FeedItemDto;
use App\NewsHeadlines\Application\GetFeeds\Dto\FeedResponseDto;
use App\NewsHeadlines\Application\GetFeeds\GetFeedsQuery;
use App\NewsHeadlines\Infrastructure\Api\Request\GetFeedsRequest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final readonly class GetFeedsController
{
    public function __construct(
        private GetFeedsQuery $feedsQuery,
    ) {
    }

    /**
     * @param Request $request
     * @param ValidatorInterface $validator
     * @return JsonResponse
     */
    #[Route('/api/feeds', name: 'news_headline_get', methods: ['GET'])]
    public function __invoke(Request $request, ValidatorInterface $validator): JsonResponse
    {
        $feedsRequest = GetFeedsRequest::fromRequest($request->query->all());

        $errors = $validator->validate($feedsRequest);

        if (count($errors) > 0) {
            throw new BadRequestHttpException((string) $errors);
        }

        $items = $this->feedsQuery->execute(
            $feedsRequest->limit,
            $feedsRequest->cursor
        );

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
