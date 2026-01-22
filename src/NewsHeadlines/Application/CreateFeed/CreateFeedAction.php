<?php

namespace App\NewsHeadlines\Application\CreateFeed;

use App\NewsHeadlines\Application\CreateFeed\Exception\NewsHeadlineCreateFailedException;
use App\NewsHeadlines\Application\CreateFeed\Exception\NewsHeadlineExistInSourceException;
use App\NewsHeadlines\Domain\Exception\NewsDomainException;
use App\NewsHeadlines\Domain\Model\NewsHeadline;
use App\NewsHeadlines\Domain\Port\NewsHeadlineIdGenerator;
use App\NewsHeadlines\Domain\Repository\NewsHeadlineRepository;
use App\NewsHeadlines\Domain\ValueObject\NewsHeadlineOrigin;
use App\NewsHeadlines\Domain\ValueObject\NewsHeadlineSource;
use App\NewsHeadlines\Domain\ValueObject\NewsHeadlineTitle;
use App\NewsHeadlines\Domain\ValueObject\NewsHeadlineUrl;
use DateTimeImmutable;

final readonly class CreateFeedAction
{
    public function __construct(
        private NewsHeadlineRepository  $repository,
        private NewsHeadlineIdGenerator $idGenerator
    ) {}

    /**
     * @throws NewsHeadlineCreateFailedException
     * @throws NewsHeadlineExistInSourceException
     */
    public function execute(string $source, string $title, string $url): NewsHeadline
    {

        if ($this->repository->existByUrlInSource($url, $source)) {
            throw new NewsHeadlineExistInSourceException('News headline with this URL '.$url.' already exists.');
        }


        $createdAt = new DateTimeImmutable();

        $position = $this->repository->nextPositionForSourceAndDay(
            $source,
            $createdAt
        );

        try {
            $headline = NewsHeadline::create(
                $this->idGenerator->generate(),
                NewsHeadlineSource::fromString($source),
                NewsHeadlineTitle::fromString($title),
                NewsHeadlineUrl::fromString($url),
                NewsHeadlineOrigin::fromString('api'),
                $position,
                new DateTimeImmutable(),
                $createdAt
            );

        } catch (NewsDomainException $e) {
            throw new NewsHeadlineCreateFailedException('Failed to create news headline: ' . $e->getMessage(), 0, $e);
        }

        $this->repository->save($headline);

        return $headline;
    }
}
