<?php

namespace App\NewsHeadlines\Infrastructure\Api\Controller;

use App\NewsHeadlines\Application\Exception\NewsHeadlineApplicationException;
use App\NewsHeadlines\Application\Exception\NewsHeadlineNotFoundException;
use App\NewsHeadlines\Application\GetFeeds\Dto\FeedItemDto;
use App\NewsHeadlines\Application\UpdateFeed\Exception\NewsHeadlineUrlAlreadyExistsException;
use App\NewsHeadlines\Application\UpdateFeed\UpdateFeedAction;
use App\NewsHeadlines\Infrastructure\Api\Request\UpdateFeedRequest;
use JsonException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final readonly class UpdateFeedController
{

    public function __construct(
        private UpdateFeedAction $action
    ) {}

    /**
     * @throws JsonException
     */
    #[Route('/api/feeds/{id}', name: 'news_headline_update', methods: ['PUT'])]
    public function __invoke(string $id, Request $request, ValidatorInterface $validator): JsonResponse
    {
        $payload = json_decode(
            $request->getContent(),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        $dto = UpdateFeedRequest::fromRequest($payload);
        $errors = $validator->validate($dto);

        if (count($errors) > 0) {
            $messages = [];

            foreach ($errors as $error) {
                $messages[] = $error->getPropertyPath() . ': ' . $error->getMessage();
            }

            return new JsonResponse(
                ['error' => 'Bad Request', 'details' => $messages],
                400
            );
        }

        try {
            $headline = $this->action->execute(
                $id,
                $dto->title,
                $dto->url
            );
        } catch (NewsHeadlineNotFoundException $e) {
            return new JsonResponse(['error' => $e->getMessage()], 404);
        } catch (NewsHeadlineUrlAlreadyExistsException $e) {
            return new JsonResponse(['error' => $e->getMessage()], 409);
        }

        return new JsonResponse(
            FeedItemDto::fromDomain($headline)
        );
    }
}
