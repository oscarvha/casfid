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
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

final class DoctrineNewsHeadlineRepository implements NewsHeadlineRepository
{
    private EntityRepository $repository;

    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {
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
            ->orderBy('n.scrapedAt', 'DESC')
            ->addOrderBy('n.id', 'DESC')
            ->setMaxResults($limit);

        if ($cursor !== null) {
            $qb
                ->andWhere('n.id < :cursor')
                ->setParameter('cursor', $cursor);
        }

        $rows = $qb->getQuery()->getArrayResult();


        $headlines = array_map(
            fn (array $row) => NewsHeadline::create(
                NewsHeadlineId::fromString($row['id']),
                NewsHeadlineSource::fromString($row['source']),
                NewsHeadlineTitle::fromString($row['title']),
                NewsHeadlineUrl::fromString($row['url']),
                (int) $row['position'],
                $row['scrapedAt']
            ),
            $rows
        );

        return NewsHeadlineCollection::fromArray($headlines);


    }
}
