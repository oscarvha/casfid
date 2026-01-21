<?php

namespace App\NewsHeadlines\Infrastructure\Api\Controller;

use App\NewsHeadlines\Application\GetFeed\GetFeedQuery;
use App\NewsHeadlines\Application\GetFeeds\Dto\FeedItemDto;
use App\NewsHeadlines\Infrastructure\Api\Request\GetFeedByIdRequest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final readonly class GetFeedController
{
    public function __construct(
        private GetFeedQuery $getFeedQuery
    ) {
    }
    #[Route('/api/feeds/{id}', name: 'news_headline_get_one', methods: ['GET'])]
    public function __invoke(string $id, ValidatorInterface $validator): JsonResponse
    {
        $dto = GetFeedByIdRequest::fromRoute($id);

        $errors = $validator->validate($dto);

        if (count($errors) > 0) {
            $messages = [];

            foreach ($errors as $error) {
                $messages[] = $error->getPropertyPath() . ': ' . $error->getMessage();
            }

            return new JsonResponse(
                [
                    'error' => 'Bad Request',
                    'details' => $messages,
                ],
                400
            );
        }

        $headline = $this->getFeedQuery->execute($id);

        if ($headline === null) {
            return new JsonResponse(
                ['error' => 'Not Found'],
                404
            );
        }

        return new JsonResponse(
            FeedItemDto::fromDomain($headline)
        );
    }
}
