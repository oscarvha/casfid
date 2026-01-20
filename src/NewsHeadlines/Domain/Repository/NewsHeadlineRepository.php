<?php

namespace App\NewsHeadlines\Domain\Repository;

use App\NewsHeadlines\Domain\Collection\NewsHeadlineCollection;

interface NewsHeadlineRepository
{
    public function saveNewOnly(NewsHeadlineCollection $collection): void;
}
