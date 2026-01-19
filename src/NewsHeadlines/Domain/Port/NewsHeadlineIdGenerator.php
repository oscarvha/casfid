<?php

namespace App\NewsHeadlines\Domain\Port;

use App\NewsHeadlines\Domain\ValueObject\NewsHeadlineId;

interface NewsHeadlineIdGenerator
{
    /**
     * @return NewsHeadlineId
     */
    public function generate(): NewsHeadlineId;
}
