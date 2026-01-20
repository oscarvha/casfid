<?php

namespace App\NewsHeadlines\Application\GetFeeds;

use App\NewsHeadlines\Domain\Repository\NewsHeadlineRepository;

final class GetFeedsQuery
{
    public function __construct(
        private readonly NewsHeadlineRepository $repository
    ) {}

    /**
     * @param int $limit
     * @param string|null $cursor
     * @return array
     */
    public function execute(int $limit, ?string $cursor): array
    {
        $collection = $this->repository->findPaginated(
            $limit,
            $cursor
        );

        return iterator_to_array($collection);
    }
}
