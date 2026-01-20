<?php

namespace App\NewsHeadlines\Application;

use App\NewsHeadlines\Domain\Collection\NewsHeadlineCollection;
use App\NewsHeadlines\Domain\Repository\NewsHeadlineRepository;
use App\NewsHeadlines\Domain\Scrapper\NewsScraper;

final class FetchTopHeadlines
{
    /**
     * @param iterable<NewsScraper> $scrapers
     */
    public function __construct(
        private iterable $scrapers,
        private NewsHeadlineRepository $repository
    ) {}

    public function execute(): NewsHeadlineCollection
    {
        $allHeadlines = [];

        foreach ($this->scrapers as $scraper) {
            $collection = $scraper->scrapeTopHeadlines()->take(5);

            foreach ($collection as $headline) {
                $allHeadlines[] = $headline;
            }
        }

        $result = NewsHeadlineCollection::fromArray($allHeadlines);

        $this->repository->saveNewOnly($result);

        return $result;
    }
}
