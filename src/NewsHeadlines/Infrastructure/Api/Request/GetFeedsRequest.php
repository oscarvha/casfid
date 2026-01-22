<?php

namespace App\NewsHeadlines\Infrastructure\Api\Request;

use Symfony\Component\Validator\Constraints as Assert;

final class GetFeedsRequest
{
    #[Assert\Positive]
    #[Assert\LessThanOrEqual(50)]
    public int $limit = 2;

    public ?string $cursor = null;

    /**
     * @param array<string, mixed> $query
     */
    public static function fromRequest(array $query): self
    {
        $self = new self();

        if (isset($query['limit'])) {
            $self->limit = (int) $query['limit'];
        }

        if (isset($query['cursor']) && $query['cursor'] !== '') {
            $self->cursor = $query['cursor'];
        }

        return $self;
    }
}
