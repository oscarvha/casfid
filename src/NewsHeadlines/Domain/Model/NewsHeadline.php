<?php

namespace App\NewsHeadlines\Domain\Model;

use App\NewsHeadlines\Domain\ValueObject\NewsHeadlineId;
use App\NewsHeadlines\Domain\ValueObject\NewsHeadlineSource;
use App\NewsHeadlines\Domain\ValueObject\NewsHeadlineTitle;
use App\NewsHeadlines\Domain\ValueObject\NewsHeadlineUrl;
use DateTimeImmutable;

/**
 *
 */
final readonly class NewsHeadline
{
    /**
     * @param NewsHeadlineId $id
     * @param NewsHeadlineSource $source
     * @param NewsHeadlineTitle $title
     * @param NewsHeadlineUrl $url
     * @param int $position
     * @param DateTimeImmutable $scrapedAt
     */
    private function __construct(
        private NewsHeadlineId             $id,
        private NewsHeadlineSource         $source,
        private NewsHeadlineTitle          $title,
        private NewsHeadlineUrl            $url,
        private int                        $position,
        private DateTimeImmutable          $scrapedAt
    ) {}
    public function id(): string
    {
        return $this->id->__toString();
    }

    public function source(): string
    {
        return $this->source->__toString();
    }

    public function title(): string
    {
        return $this->title->__toString();
    }

    public function url(): string
    {
        return $this->url->__toString();
    }

    /**
     * @param NewsHeadlineId $id
     * @param NewsHeadlineSource $source
     * @param NewsHeadlineTitle $title
     * @param NewsHeadlineUrl $url
     * @param int $position
     * @param DateTimeImmutable $scrapedAt
     * @return self
     */
    public static function create(
        NewsHeadlineId     $id,
        NewsHeadlineSource $source,
        NewsHeadlineTitle  $title,
        NewsHeadlineUrl    $url,
        int                $position,
        DateTimeImmutable  $scrapedAt
    ): self {
        return new self($id, $source, $title, $url, $position, $scrapedAt);
    }


}
