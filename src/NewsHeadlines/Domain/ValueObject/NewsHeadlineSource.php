<?php

namespace App\NewsHeadlines\Domain\ValueObject;

use App\NewsHeadlines\Domain\Exception\InvalidNewsHeadlineSource;

final readonly class NewsHeadlineSource
{
    private const EL_PAIS  = 'el_pais';
    private const EL_MUNDO = 'el_mundo';

    private function __construct(
        private string $value
    ) {}

    /**
     * @return self
     */
    public static function elPais(): self
    {
        return new self(self::EL_PAIS);
    }

    /**
     * @return self
     */
    public static function elMundo(): self
    {
        return new self(self::EL_MUNDO);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->value;
    }

    /**
     * @param NewsHeadlineSource $other
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
            throw new InvalidNewsHeadlineSource(
                sprintf('Invalid news source "%s" allowed values "%s" ', $value, implode(', ', self::allowed()))
            );
        }

        return new self($normalized);
    }

    /**
     * @return string[]
     */
    private static function allowed(): array
    {
        return [
            self::EL_PAIS,
            self::EL_MUNDO,
        ];
    }
}
