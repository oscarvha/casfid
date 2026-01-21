<?php

namespace App\NewsHeadlines\Application\GetFeeds;

use App\NewsHeadlines\Domain\Repository\NewsHeadlineRepository;

final class GetFeedsQuery
{
    private const MAX_LIMIT = 50;

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
        $limit = min($limit, self::MAX_LIMIT);

        $collection = $this->repository->findPaginated(
            $limit,
            $cursor
        );

        return iterator_to_array($collection);
    }
}
