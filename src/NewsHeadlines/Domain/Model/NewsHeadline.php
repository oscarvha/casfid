<?php

namespace App\NewsHeadlines\Domain\Model;

use App\NewsHeadlines\Domain\ValueObject\NewsHeadlineId;
use App\NewsHeadlines\Domain\ValueObject\NewsHeadlineOrigin;
use App\NewsHeadlines\Domain\ValueObject\NewsHeadlineSource;
use App\NewsHeadlines\Domain\ValueObject\NewsHeadlineTitle;
use App\NewsHeadlines\Domain\ValueObject\NewsHeadlineUrl;
use DateTimeImmutable;

/**
 *
 */
final class NewsHeadline
{
    /**
     * @param NewsHeadlineId $id
     * @param NewsHeadlineSource $source
     * @param NewsHeadlineTitle $title
     * @param NewsHeadlineUrl $url
     * @param NewsHeadlineOrigin $origin
     * @param int $position
     * @param DateTimeImmutable $scrapedAt
     * @param DateTimeImmutable $createdAt
     */
    private function __construct(
        private readonly NewsHeadlineId     $id,
        private readonly NewsHeadlineSource $source,
        private NewsHeadlineTitle           $title,
        private NewsHeadlineUrl             $url,
        private readonly NewsHeadlineOrigin $origin,
        private readonly int                $position,
        private readonly DateTimeImmutable  $scrapedAt,
        private readonly DateTimeImmutable $createdAt
    ) {}

    /**
     * @return string
     */
    public function id(): string
    {
        return $this->id->__toString();
    }

    /**
     * @return string
     */
    public function source(): string
    {
        return $this->source->__toString();
    }

    /**
     * @return string
     */
    public function origin(): string
    {
        return $this->origin->__toString();
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return $this->title->__toString();
    }

    /**
     * @return string
     */
    public function url(): string
    {
        return $this->url->__toString();
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
     * @return DateTimeImmutable
     */
    public function createdAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function update(NewsHeadlineTitle $title, NewsHeadlineUrl $headLineUrl): void
    {
        $this->title = $title;
        $this->url = $headLineUrl;
    }

    /**
     * @param NewsHeadlineId $id
     * @param NewsHeadlineSource $source
     * @param NewsHeadlineTitle $title
     * @param NewsHeadlineUrl $url
     * @param NewsHeadlineOrigin $origin
     * @param int $position
     * @param DateTimeImmutable $scrapedAt
     * @param DateTimeImmutable|null $createdAt
     * @return self
     */
    public static function create(
        NewsHeadlineId     $id,
        NewsHeadlineSource $source,
        NewsHeadlineTitle  $title,
        NewsHeadlineUrl    $url,
        NewsHeadlineOrigin $origin,
        int                $position,
        DateTimeImmutable  $scrapedAt,
        ?DateTimeImmutable  $createdAt = null
    ): self {
        return new self($id, $source, $title, $url,$origin, $position, $scrapedAt, $createdAt ?? new DateTimeImmutable())    ;
    }


}
