<?php

declare(strict_types=1);

namespace App\NewsHeadlines\Infrastructure\Persistence\Doctrine;

use App\NewsHeadlines\Domain\Collection\NewsHeadlineCollection;
use App\NewsHeadlines\Domain\Model\NewsHeadline;
use App\NewsHeadlines\Domain\Repository\NewsHeadlineRepository;
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

    public function save(NewsHeadline $headline): void
    {
        $this->entityManager->persist($headline);
        $this->entityManager->flush();
    }

    /**
     * @param NewsHeadlineCollection $headlines
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

    /**
     * @return NewsHeadline[]
     */
    public function searchAll(): array
    {
        return $this->repository->findAll();
    }

    public function existsByUrl(string $url): bool
    {
        return $this->repository->count(['url' => $url]) > 0;
    }
}
