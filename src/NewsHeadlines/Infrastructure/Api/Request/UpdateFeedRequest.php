<?php

namespace App\NewsHeadlines\Infrastructure\Api\Request;

use Symfony\Component\Validator\Constraints as Assert;

final class UpdateFeedRequest
{
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    public string $title;

    #[Assert\NotBlank]
    #[Assert\Url(requireTld: false)]
    public string $url;

    public static function fromRequest(array $data): self
    {
        $self = new self();
        $self->title = $data['title'] ?? '';
        $self->url = $data['url'] ?? '';

        return $self;
    }
}
