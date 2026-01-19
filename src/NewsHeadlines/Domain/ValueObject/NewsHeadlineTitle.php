<?php

namespace App\NewsHeadlines\Domain\ValueObject;

use App\NewsHeadlines\Domain\Exception\InvalidNewsHeadlineTitle;

final readonly class NewsHeadlineTitle
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
     * @param NewsHeadlineTitle $t2
     * @return bool
     */
    public function equals(NewsHeadlineTitle $t2): bool
    {
        return $this->toString() === $t2->toString();
    }

    /**
     * @param string $value
     * @return self
     */
    public static function fromString(string $value): self
    {
        if ($value === '') {
            throw new InvalidNewsHeadlineTitle('HeadlineTitle cannot be empty');
        }

        if (mb_strlen($value) > 255) {
            throw new InvalidNewsHeadlineTitle('HeadlineTitle cannot be longer than 255 characters');
        }

        return new self($value);
    }
}
