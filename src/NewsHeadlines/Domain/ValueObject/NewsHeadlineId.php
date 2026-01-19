<?php

namespace App\NewsHeadlines\Domain\ValueObject;

class NewsHeadlineId
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
            throw new \InvalidArgumentException('NewsHeadlineId cannot be empty');
        }

        return new self($value);
    }

}
