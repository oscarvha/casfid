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
}
