<?php
namespace App\NewsHeadlines\Infrastructure\Persistence\Doctrine;

use App\NewsHeadlines\Domain\Collection\NewsHeadlineCollection;
use App\NewsHeadlines\Domain\Model\NewsHeadline;
use App\NewsHeadlines\Domain\Repository\NewsHeadlineRepository;
use App\NewsHeadlines\Domain\ValueObject\NewsHeadlineId;
use App\NewsHeadlines\Domain\ValueObject\NewsHeadlineOrigin;
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
     * @param DateTimeImmutable|null $cursorCreatedAt
     * @param string|null $cursorId
     * @return NewsHeadlineCollection
     */
    public function findPaginated(int $limit, ?DateTimeImmutable $cursorCreatedAt, ?string $cursorId): NewsHeadlineCollection
    {
        $qb = $this->entityManager
            ->createQueryBuilder()
            ->select('n')
            ->from(NewsHeadline::class, 'n')
            ->orderBy('n.createdAt', 'DESC')
            ->addOrderBy('n.id', 'DESC')
            ->setMaxResults($limit);

        if ($cursorCreatedAt !== null && $cursorId !== null) {
            $qb->andWhere(
                '(n.createdAt < :createdAt)
                 OR (n.createdAt = :createdAt AND n.id < :id)'
            )
                ->setParameter('createdAt', $cursorCreatedAt)
                ->setParameter('id', $cursorId);
        }

        $rows = $qb->getQuery()->getArrayResult();

        foreach ($rows as &$row) {

            $row = NewsHeadline::create(
                NewsHeadlineId::fromString($row['id']),
                NewsHeadlineSource::fromString($row['source']),
                NewsHeadlineTitle::fromString($row['title']),
                NewsHeadlineUrl::fromString($row['url']),
                NewsHeadlineOrigin::fromString($row['origin']),
                (int) $row['position'],
                $row['scrapedAt'],
                $row['createdAt']
            );
        }


        return NewsHeadlineCollection::fromArray($rows);
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
            NewsHeadlineOrigin::fromString($row['origin']),
            (int) $row['position'],
            $row['scrapedAt'],
            $row['createdAt']
        );
    }

    public function save(NewsHeadline $headline): void
    {
        $this->entityManager->persist($headline);
        $this->entityManager->flush();
    }

    /**
     * @param string $source
     * @param DateTimeImmutable $createdAt
     * @return int
     */
    public function nextPositionForSourceAndDay(string $source, DateTimeImmutable $createdAt): int
    {
        $startOfDay = $createdAt->setTime(0, 0, 0);
        $endOfDay = $createdAt->setTime(23, 59, 59);

        $qb = $this->entityManager
            ->createQueryBuilder()
            ->select('MAX(n.position) as maxPosition')
            ->from(NewsHeadline::class, 'n')
            ->where('n.source = :source')
            ->andWhere('n.createdAt BETWEEN :startOfDay AND :endOfDay')
            ->setParameter('source', $source)
            ->setParameter('startOfDay', $startOfDay)
            ->setParameter('endOfDay', $endOfDay);

        $result = $qb->getQuery()->getSingleScalarResult();

        return ($result !== null ? (int)$result : 0) + 1;
    }

    public function existByUrlInSource(string $url, string $source): bool
    {
        $count = $this->repository->count([
            'url' => $url,
            'source' => $source,
        ]);

        return $count > 0;
    }
}
