<?php

namespace App\NewsHeadlines\Domain\ValueObject;

use App\NewsHeadlines\Domain\Exception\InvalidNewsHeadlineUrl;

final readonly class NewsHeadlineUrl
{
    /**
     * @var string
     */
    private string $value;

    /**
     * @param string $value
     */
    private function __construct(
        string $value
    ) {
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->value;
    }

    /**
     * @param string $value
     * @return self
     */
    public static function fromString(string $value): self
    {
        if (!filter_var($value, FILTER_VALIDATE_URL)) {
            throw new InvalidNewsHeadlineUrl('HeadlineUrl must be a valid URL');
        }

        return new self($value);
    }
}
