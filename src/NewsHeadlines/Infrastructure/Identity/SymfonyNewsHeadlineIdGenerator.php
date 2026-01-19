<?php

namespace App\NewsHeadlines\Infrastructure\Identity;

use App\NewsHeadlines\Domain\ValueObject\NewsHeadlineId;
use Symfony\Component\Uid\Uuid;

class SymfonyNewsHeadlineIdGenerator
{
    /**
     * @return string
     */
    public function generate(): NewsHeadlineId
    {
        return NewsHeadlineId::fromString(
            Uuid::v4()->toRfc4122()
        );
    }
}
