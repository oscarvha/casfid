<?php

namespace App\NewsHeadlines\Domain\Repository;

use App\NewsHeadlines\Domain\Collection\NewsHeadlineCollection;
use App\NewsHeadlines\Domain\Model\NewsHeadline;

interface NewsHeadlineRepository
{
    /**
     * @param NewsHeadlineCollection $collection
     * @return void
     */
    public function saveNewOnly(NewsHeadlineCollection $collection): void;

    /**
     * @param int $limit
     * @param string|null $cursor
     * @return NewsHeadlineCollection
     */
    public function findPaginated(int $limit, ?string $cursor): NewsHeadlineCollection;

    /**
     * @param string $id
     * @return NewsHeadline|null
     */
    public function findById(string $id): ?NewsHeadline;
}
