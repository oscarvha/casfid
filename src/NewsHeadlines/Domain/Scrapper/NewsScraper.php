<?php

namespace App\NewsHeadlines\Domain\Scrapper;

use App\NewsHeadlines\Domain\Collection\NewsHeadlineCollection;
use App\NewsHeadlines\Domain\ValueObject\NewsHeadlineSource;

interface NewsScraper
{
    public function source(): NewsHeadlineSource;

    public function scrapeTopHeadlines(): NewsHeadlineCollection;
}
