<?php

namespace App\NewsHeadlines\Infrastructure\Api\Request;

use Symfony\Component\Validator\Constraints as Assert;
final class CreateFeedRequest
{
    #[Assert\NotBlank]
    public string $source;

    #[Assert\NotBlank]
    public string $title;

    #[Assert\NotBlank]
    #[Assert\Url(requireTld: true)]

    public string $url;

    public static function fromRequest(array $data): self
    {
        $self = new self();
        $self->source = $data['source'] ?? '';
        $self->title = $data['title'] ?? '';
        $self->url = $data['url'] ?? '';

        return $self;
    }
}
