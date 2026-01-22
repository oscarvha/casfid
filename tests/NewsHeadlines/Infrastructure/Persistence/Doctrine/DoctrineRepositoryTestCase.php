<?php

namespace App\Tests\NewsHeadlines\Infrastructure\Persistence\Doctrine;

use App\NewsHeadlines\Domain\Model\NewsHeadline;
use App\NewsHeadlines\Domain\ValueObject\NewsHeadlineId;
use App\NewsHeadlines\Domain\ValueObject\NewsHeadlineOrigin;
use App\NewsHeadlines\Domain\ValueObject\NewsHeadlineSource;
use App\NewsHeadlines\Domain\ValueObject\NewsHeadlineTitle;
use App\NewsHeadlines\Domain\ValueObject\NewsHeadlineUrl;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class DoctrineRepositoryTestCase extends KernelTestCase
{
    protected EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        self::bootKernel();

        $this->entityManager = self::getContainer()->get(EntityManagerInterface::class);

        $schemaTool = new SchemaTool($this->entityManager);
        $metadata = $this->entityManager->getMetadataFactory()->getAllMetadata();

        $schemaTool->createSchema($metadata);
    }

    protected function tearDown(): void
    {
        $this->entityManager->close();
        parent::tearDown();
    }

    protected function headline(string $url,int $position = 1, ?DateTimeImmutable $createdAt = null): NewsHeadline
    {
        return NewsHeadline::create(
            NewsHeadlineId::fromString(uniqid('test-', true)),
            NewsHeadlineSource::fromString('el_pais'),
            NewsHeadlineTitle::fromString('Test headline'),
            NewsHeadlineUrl::fromString($url),
            NewsHeadlineOrigin::fromString('scraping'),
            $position,
            new DateTimeImmutable(),
            $createdAt ?? new DateTimeImmutable()
        );
    }
}
