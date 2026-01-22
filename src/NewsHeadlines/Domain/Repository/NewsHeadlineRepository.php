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

    /**
     * @param NewsHeadline $headline
     * @return void
     */
    public function save(NewsHeadline $headline): void;

    /**
     * @param NewsHeadline $headline
     * @return void
     */
    public function update(NewsHeadline $headline): void;

    /**
     * @param string $source
     * @param DateTimeImmutable $createdAt
     * @return int
     */
    public function nextPositionForSourceAndDay(string $source, DateTimeImmutable $createdAt): int;

    /**
     * @param string $url
     * @param string $source
     * @return bool
     */
    public function existByUrlInSource(string $url, string $source): bool;

    /**
     * @param string $id
     * @return void
     */
    public function deleteById(string $id): void;
}
