<?php

namespace App\NewsHeadlines\Domain\Repository;

use App\NewsHeadlines\Domain\Collection\NewsHeadlineCollection;
use App\NewsHeadlines\Domain\Model\NewsHeadline;
use DateTimeImmutable;

interface NewsHeadlineRepository
{
    /**
     * @param NewsHeadlineCollection $collection
     * @return void
     */
    public function saveNewOnly(NewsHeadlineCollection $collection): void;

    /**
     * @param int $limit
     * @param DateTimeImmutable|null $cursorCreatedAt
     * @param string|null $cursorId
     * @return NewsHeadlineCollection
     */
    public function findPaginated(int $limit, ?DateTimeImmutable $cursorCreatedAt, ?string $cursorId): NewsHeadlineCollection;

    /**
     * @param string $id
     * @return NewsHeadline|null
     */
    public function findById(string $id): ?NewsHeadline;
}
