<?php

namespace App\NewsHeadlines\Application\GetFeed;

use App\NewsHeadlines\Domain\Model\NewsHeadline;
use App\NewsHeadlines\Domain\Repository\NewsHeadlineRepository;

final readonly class GetFeedQuery
{
    public function __construct(
        private NewsHeadlineRepository $repository
    ) {}

    public function execute(string $id): ?NewsHeadline
    {
        return $this->repository->findById($id);
    }
}
