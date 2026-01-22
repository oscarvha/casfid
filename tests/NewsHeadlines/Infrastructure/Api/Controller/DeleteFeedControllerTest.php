<?php

namespace App\Tests\NewsHeadlines\Infrastructure\Api\Controller;

use App\Tests\Shared\Infrastructure\Api\ApiTestCase;

final class DeleteFeedControllerTest extends ApiTestCase
{
    public function test_it_deletes_a_feed(): void
    {
        $connection = $this->entityManager->getConnection();

        $connection->executeStatement(
            'INSERT INTO news_headlines (
            id, source, title, url, origin, position, scraped_at, created_at
        ) VALUES (
            :id, :source, :title, :url, :origin, :position, :scraped_at, :created_at
        )',
            [
                'id' => '92dd32ad-95c9-4da1-a5f8-3b6363bd4404',
                'source' => 'el_mundo',
                'title' => 'To be deleted',
                'url' => 'https://elmundo.es/delete',
                'origin' => 'api',
                'position' => 1,
                'scraped_at' => '2026-01-21 10:00:00',
                'created_at' => '2026-01-21 10:00:00',
            ]
        );

        $this->client->request(
            'DELETE',
            '/api/feeds/92dd32ad-95c9-4da1-a5f8-3b6363bd4404',
            [],
            [],
            ['HTTP_Authorization' => 'Bearer ' . $_ENV['API_AUTH_TOKEN']]
        );

        self::assertResponseStatusCodeSame(204);

        $count = $connection->fetchOne(
            'SELECT COUNT(*) FROM news_headlines WHERE id = :id',
            ['id' => '92dd32ad-95c9-4da1-a5f8-3b6363bd4404']
        );

        self::assertSame(0, (int) $count);
    }

    public function test_delete_returns_404_when_feed_does_not_exist(): void
    {
        $this->client->request(
            'DELETE',
            '/api/feeds/aaaaaaaa-aaaa-aaaa-aaaa-aaaaaaaaaaaa',
            [],
            [],
            ['HTTP_Authorization' => 'Bearer ' . $_ENV['API_AUTH_TOKEN']]
        );

        self::assertResponseStatusCodeSame(404);
    }
}
