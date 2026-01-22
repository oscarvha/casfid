<?php

namespace App\NewsHeadlines\Infrastructure\Api\Controller;
use App\NewsHeadlines\Application\DeleteFeed\DeleteFeedAction;
use App\NewsHeadlines\Application\Exception\NewsHeadlineNotFoundException;
use App\NewsHeadlines\Infrastructure\Api\Request\DeleteFeedRequest;
use App\NewsHeadlines\Infrastructure\Api\Request\GetFeedByIdRequest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final readonly class DeleteFeedController
{
    public function __construct(
        private DeleteFeedAction $action
    ) {}

    #[Route('/api/feeds/{id}', name: 'news_headline_delete', methods: ['DELETE'])]
    public function __invoke(string $id, ValidatorInterface $validator): JsonResponse
    {
        $dto = DeleteFeedRequest::fromRoute($id);

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
            $this->action->execute($id);
        } catch (NewsHeadlineNotFoundException $e) {
            return new JsonResponse(
                ['error' => $e->getMessage()],
                404
            );
        }

        return new JsonResponse(null, 204);
    }
}
