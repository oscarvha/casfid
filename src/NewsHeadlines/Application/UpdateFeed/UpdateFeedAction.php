<?php

namespace App\NewsHeadlines\Application\UpdateFeed;

use App\NewsHeadlines\Application\Exception\NewsHeadlineNotFoundException;
use App\NewsHeadlines\Application\UpdateFeed\Exception\NewsHeadlineUrlAlreadyExistsException;
use App\NewsHeadlines\Domain\Model\NewsHeadline;
use App\NewsHeadlines\Domain\Repository\NewsHeadlineRepository;
use App\NewsHeadlines\Domain\ValueObject\NewsHeadlineTitle;
use App\NewsHeadlines\Domain\ValueObject\NewsHeadlineUrl;

final readonly class UpdateFeedAction
{
    public function __construct(
        private NewsHeadlineRepository $repository
    ) {}

    /**
     * @param string $id
     * @param string $title
     * @param string $url
     * @return NewsHeadline
     */
    public function execute(string $id, string $title, string $url): NewsHeadline
    {
        $headline = $this->repository->findById($id);

        if ($headline === null) {
            throw new NewsHeadlineNotFoundException('Feed not found');
        }

        if($headline->url() !== $url && $this->repository->existByUrlInSource($url, $headline->source()))  {

            throw new NewsHeadlineUrlAlreadyExistsException('The URL already exists in another news from the same source');
        }

        $headline->update(
            NewsHeadlineTitle::fromString($title),
            NewsHeadlineUrl::fromString($url)
        );

        $this->repository->update($headline);

        return $headline;
    }
}
