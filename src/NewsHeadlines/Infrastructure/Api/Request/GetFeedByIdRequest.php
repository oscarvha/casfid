<?php

namespace App\NewsHeadlines\Infrastructure\Api\Request;

use Symfony\Component\Validator\Constraints as Assert;


class GetFeedByIdRequest
{
    #[Assert\NotBlank]
    #[Assert\Uuid(versions: Assert\Uuid::V4_RANDOM)]
    public string $id;

    public static function fromRoute(string $id): self
    {
        $self = new self();
        $self->id = $id;

        return $self;
    }

}
