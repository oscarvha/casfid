<?php

namespace App\NewsHeadlines\Infrastructure\Identity;

use App\NewsHeadlines\Domain\Port\NewsHeadlineIdGenerator;
use App\NewsHeadlines\Domain\ValueObject\NewsHeadlineId;
use Symfony\Component\Uid\Uuid;

class SymfonyNewsHeadlineIdGenerator implements NewsHeadlineIdGenerator
{
    /**
     * @return NewsHeadlineId
     */
    public function generate(): NewsHeadlineId
    {
        return NewsHeadlineId::fromString(
            Uuid::v4()->toRfc4122()
        );
    }
}
