<?php

namespace App\Tests\Shared\Infrastructure\Api;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

abstract class ApiTestCase extends WebTestCase
{
    protected ?EntityManagerInterface $entityManager = null;
    protected ?KernelBrowser $client = null;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->client = static::createClient();

        $this->entityManager = static::getContainer()
            ->get(EntityManagerInterface::class);

        $this->createSchema();
    }

    /**
     * @return void
     */
    protected function tearDown(): void
    {
        $this->entityManager?->close();

        parent::tearDown();
    }

    /**
     * @return void
     */
    private function createSchema(): void
    {
        if ($this->entityManager === null) {
            return;
        }

        $metadata = $this->entityManager
            ->getMetadataFactory()
            ->getAllMetadata();

        if (!$metadata) {
            return;
        }

        $schemaTool = new SchemaTool($this->entityManager);
        $schemaTool->dropSchema($metadata);
        $schemaTool->createSchema($metadata);
    }
}
