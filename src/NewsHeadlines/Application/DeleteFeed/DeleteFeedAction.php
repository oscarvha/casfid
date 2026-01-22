<?php

namespace App\NewsHeadlines\Application\DeleteFeed;

use App\NewsHeadlines\Application\Exception\NewsHeadlineNotFoundException;
use App\NewsHeadlines\Domain\Repository\NewsHeadlineRepository;

final class DeleteFeedAction
{
    public function __construct(
        private readonly NewsHeadlineRepository $repository
    ) {}
    public function execute(string $id): void
    {
        $headline = $this->repository->findById($id);

        if ($headline === null) {
            throw new NewsHeadlineNotFoundException('Feed not found');
        }

        $this->repository->deleteById($headline->id());
    }
}
