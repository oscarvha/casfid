<?php
namespace App\NewsHeadlines\Infrastructure\Persistence\Doctrine;

use App\NewsHeadlines\Domain\Collection\NewsHeadlineCollection;
use App\NewsHeadlines\Domain\Model\NewsHeadline;
use App\NewsHeadlines\Domain\Repository\NewsHeadlineRepository;
use App\NewsHeadlines\Domain\ValueObject\NewsHeadlineId;
use App\NewsHeadlines\Domain\ValueObject\NewsHeadlineSource;
use App\NewsHeadlines\Domain\ValueObject\NewsHeadlineTitle;
use App\NewsHeadlines\Domain\ValueObject\NewsHeadlineUrl;
use DateTimeImmutable;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

final class DoctrineNewsHeadlineRepository implements NewsHeadlineRepository
{
    private EntityRepository $repository;

    public function __construct(
        private readonly EntityManagerInterface $entityManager
    )
    {
        $this->repository = $this->entityManager->getRepository(NewsHeadline::class);
    }

    /**
     * @param NewsHeadlineCollection $collection
     */
    public function saveNewOnly(NewsHeadlineCollection $collection): void
    {
        foreach ($collection as $headline) {

            if ($this->existsByUrl($headline->url())) {
                continue;
            }

            $this->entityManager->persist($headline);
        }

        $this->entityManager->flush();
    }

    private function existsByUrl(string $url): bool
    {
        return $this->repository->count(['url' => $url]) > 0;
    }

    /**
     * @param int $limit
     * @param string|null $cursor
     * @return NewsHeadlineCollection
     * @throws \Exception
     */
    public function findPaginated(int $limit, ?string $cursor): NewsHeadlineCollection
    {
        $qb = $this->entityManager
            ->createQueryBuilder()
            ->select('n')
            ->from(NewsHeadline::class, 'n')
            ->orderBy('n.scrapedAt', 'ASC')
            ->addOrderBy('n.id', 'ASC')
            ->setMaxResults($limit);

        if ($cursor !== null) {
            $qb->andWhere(
                '(n.scrapedAt > (
                SELECT c.scrapedAt
                FROM App\NewsHeadlines\Domain\Model\NewsHeadline c
                WHERE c.id = :cursor
            ))
            OR (
                n.scrapedAt = (
                    SELECT c2.scrapedAt
                    FROM App\NewsHeadlines\Domain\Model\NewsHeadline c2
                    WHERE c2.id = :cursor
                )
                AND n.id > :cursor
            )'
            )
                ->setParameter('cursor', $cursor);
        }

        $rows = $qb->getQuery()->getArrayResult();

        $headlines = [];
        foreach ($rows as $row) {
            $headlines[] = NewsHeadline::create(
                NewsHeadlineId::fromString($row['id']),
                NewsHeadlineSource::fromString($row['source']),
                NewsHeadlineTitle::fromString($row['title']),
                NewsHeadlineUrl::fromString($row['url']),
                (int) $row['position'],
                $row['scrapedAt']
            );
        }

        return NewsHeadlineCollection::fromArray($headlines);
    }

    /**
     * @param string $id
     * @return NewsHeadline|null
     */
    public function findById(string $id): ?NewsHeadline
    {
        $row = $this->entityManager
            ->createQueryBuilder()
            ->select('n')
            ->from(NewsHeadline::class, 'n')
            ->where('n.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult(AbstractQuery::HYDRATE_ARRAY);

        if ($row === null) {
            return null;
        }

        return NewsHeadline::create(
            NewsHeadlineId::fromString($row['id']),
            NewsHeadlineSource::fromString($row['source']),
            NewsHeadlineTitle::fromString($row['title']),
            NewsHeadlineUrl::fromString($row['url']),
            (int) $row['position'],
            $row['scrapedAt']
        );
    }
}
