<?php

namespace App\NewsHeadlines\Infrastructure\Api\Controller;
use App\NewsHeadlines\Application\DeleteFeed\DeleteFeedAction;
use App\NewsHeadlines\Application\Exception\NewsHeadlineNotFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final readonly class DeleteFeedController
{
    public function __construct(
        private DeleteFeedAction $action
    ) {}

    #[Route('/api/feeds/{id}', name: 'news_headline_delete', methods: ['DELETE'])]
    public function __invoke(string $id): JsonResponse
    {
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
