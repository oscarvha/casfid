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

    /**
     * @return NewsHeadlineSource
     */
    public function source(): NewsHeadlineSource
    {
        return $this->source;
    }

    /**
     * @return NewsHeadlineTitle
     */
    public function title(): NewsHeadlineTitle
    {
        return $this->title;
    }

    /**
     * @return NewsHeadlineUrl
     */
    public function url(): NewsHeadlineUrl
    {
        return $this->url;
    }

    /**
     * @return int
     */
    public function position(): int
    {
        return $this->position;
    }

    /**
     * @return DateTimeImmutable
     */
    public function scrapedAt(): DateTimeImmutable
    {
        return $this->scrapedAt;
    }

    /**
     * @return NewsHeadlineId
     */
    public function id(): NewsHeadlineId
    {
        return $this->id;
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
