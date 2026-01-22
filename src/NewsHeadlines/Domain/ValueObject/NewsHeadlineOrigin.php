<?php

namespace App\NewsHeadlines\Domain\ValueObject;

use App\NewsHeadlines\Domain\Exception\InvalidOriginHeadlineSource;

final readonly class NewsHeadlineOrigin
{
    private const SCRAPING  = 'scraping';
    private const API = 'api';

    private function __construct(
        private string $value
    ) {}

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->value;
    }

    /**
     * @param NewsHeadlineOrigin $other
     * @return bool
     */
    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    /**
     * @param string $value
     * @return self
     */
    public static function fromString(string $value): self
    {
        $normalized = strtolower(trim($value));

        if (!in_array($normalized, self::allowed(), true)) {
            throw new InvalidOriginHeadlineSource(
                sprintf('Invalid news source "%s"', $value)
            );
        }

        return new self($normalized);
    }

    /**
     * @return self
     */
    public static function scraping(): self
    {
        return new self(self::SCRAPING);
    }

    /**
     * @return self
     */
    public static function api(): self
    {
        return new self(self::API);
    }

    /**
     * @return string[]
     */
    private static function allowed(): array
    {
        return [
            self::SCRAPING,
            self::API
        ];
    }
}
