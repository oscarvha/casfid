<?php

namespace App\Tests\NewsHeadlines\Infrastructure\Persistence\Doctrine;

use App\NewsHeadlines\Domain\Collection\NewsHeadlineCollection;
use App\NewsHeadlines\Domain\Model\NewsHeadline;
use App\NewsHeadlines\Domain\Repository\NewsHeadlineRepository;
use App\NewsHeadlines\Domain\ValueObject\NewsHeadlineId;
use App\NewsHeadlines\Domain\ValueObject\NewsHeadlineSource;
use App\NewsHeadlines\Domain\ValueObject\NewsHeadlineTitle;
use App\NewsHeadlines\Domain\ValueObject\NewsHeadlineUrl;
use DateTimeImmutable;

final class DoctrineNewsHeadlineRepositoryTest extends DoctrineRepositoryTestCase
{
    public function test_it_persists_new_headlines(): void
    {
        $repository = self::getContainer()
            ->get(NewsHeadlineRepository::class);

        $collection = NewsHeadlineCollection::fromArray([
            $this->headline('https://elpais.com/a'),
            $this->headline('https://elmundo.es/b'),
        ]);

        $repository->saveNewOnly($collection);

        $count = $this->entityManager
            ->getConnection()
            ->fetchOne('SELECT COUNT(*) FROM news_headlines');

        $this->assertSame(2, (int) $count);
    }

    public function test_it_checks_if_url_exists_in_source(): void
    {
        $repository = self::getContainer()->get(NewsHeadlineRepository::class);

        $headline = $this->headline(
            'https://elpais.com/a',
        );

        $repository->save($headline);

        self::assertTrue(
            $repository->existByUrlInSource(
                'https://elpais.com/a',
                'el_pais'
            )
        );

        self::assertFalse(
            $repository->existByUrlInSource(
                'https://elpais.com/a',
                'el_mundo'
            )
        );
    }

    public function test_it_finds_headline_by_id(): void
    {
        $repository = self::getContainer()->get(NewsHeadlineRepository::class);

        $headline = $this->headline('https://elpais.com/a');
        $repository->save($headline);

        $found = $repository->findById($headline->id());

        self::assertNotNull($found);
        self::assertSame($headline->id(), $found->id());
    }


    public function test_it_returns_null_when_headline_does_not_exist(): void
    {
        $repository = self::getContainer()->get(NewsHeadlineRepository::class);

        self::assertNull(
            $repository->findById('11111111-1111-1111-1111-111111111111')
        );
    }

    public function test_it_calculates_next_position_per_source_and_day(): void
    {
        $repository = self::getContainer()->get(NewsHeadlineRepository::class);

        $date = new \DateTimeImmutable('2026-01-21 10:00:00');

        $repository->save(
            $this->headline(
                'https://elpais.com/a',
                position: 1,
                createdAt: $date
            )
        );

        $repository->save(
            $this->headline(
                'https://elpais.com/b',
                position: 2,
                createdAt: $date
            )
        );

        $next = $repository->nextPositionForSourceAndDay('el_pais', $date);

        self::assertSame(3, $next);
    }

    public function test_it_returns_paginated_results_using_cursor(): void
    {
        $repository = self::getContainer()->get(NewsHeadlineRepository::class);

        $createdAt1 = new \DateTimeImmutable('2026-01-21 10:00:00');
        $createdAt2 = new \DateTimeImmutable('2026-01-21 11:00:00');

        $h1 = $this->headline(
            'https://elpais.com/a',
            position: 1,
            createdAt: $createdAt1
        );

        $h2 = $this->headline(
            'https://elpais.com/b',
            position: 2,
            createdAt: $createdAt2
        );

        $repository->save($h1);
        $repository->save($h2);

        $collection = $repository->findPaginated(
            1,
            $createdAt2,
            $h2->id()
        );

        $items = iterator_to_array($collection);

        self::assertCount(1, $items);
        self::assertSame($h1->id(), $items[0]->id());
    }
}
