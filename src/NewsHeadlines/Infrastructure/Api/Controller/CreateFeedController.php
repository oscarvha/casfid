<?php

namespace App\NewsHeadlines\Infrastructure\Api\Controller;

use App\NewsHeadlines\Application\CreateFeed\CreateFeedAction;
use App\NewsHeadlines\Application\Exception\NewsHeadlineApplicationException;
use App\NewsHeadlines\Application\GetFeeds\Dto\FeedItemDto;
use App\NewsHeadlines\Infrastructure\Api\Request\CreateFeedRequest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final readonly class CreateFeedController
{
    public function __construct(
        private CreateFeedAction $action
    ) {}

    /**
     * @throws \JsonException
     */
    #[Route('/api/feeds', name: 'news_headline_create', methods: ['POST'])]
    public function __invoke(Request $request, ValidatorInterface $validator): JsonResponse
    {
        $payload = json_decode(
            $request->getContent(),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        $dto = CreateFeedRequest::fromRequest($payload);
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

        try {
            $headline = $this->action->execute(
                $dto->source,
                $dto->title,
                $dto->url,
            );

        } catch (NewsHeadlineApplicationException $e) {
            return new JsonResponse(
                ['error' => $e->getMessage()],
                409
            );
        }

        return new JsonResponse(
            FeedItemDto::fromDomain($headline),
            201
        );
    }
}
