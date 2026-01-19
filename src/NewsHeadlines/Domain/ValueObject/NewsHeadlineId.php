<?php

namespace App\NewsHeadlines\Domain\ValueObject;

use App\NewsHeadlines\Domain\Exception\InvalidNewsHeadlineId;

final readonly class NewsHeadlineId
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
    public function toString(): string
    {
        return $this->value;
    }

    /**
     * @param NewsHeadlineId $other
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
        if ($value === '') {
            throw new InvalidNewsHeadlineId('NewsHeadlineId cannot be empty');
        }

        return new self($value);
    }

}
