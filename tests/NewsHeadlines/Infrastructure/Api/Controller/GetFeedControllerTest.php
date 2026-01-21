<?php

namespace App\Tests\NewsHeadlines\Infrastructure\Api\Controller;

use App\Tests\Shared\Infrastructure\Api\ApiTestCase;
use Exception;

class GetFeedControllerTest extends ApiTestCase
{
    public function test_it_returns_401_without_token(): void
    {
        $this->client->request(
            'GET',
            '/api/feeds/02597efa-e351-41b0-8f29-2efbd1cdeb0c'
        );

        self::assertResponseStatusCodeSame(401);
    }

    public function test_it_returns_400_with_invalid_uuid(): void
    {
        $this->client->request(
            'GET',
            '/api/feeds/not-a-uuid',
            [],
            [],
            [
                'HTTP_Authorization' => 'Bearer ' . $_ENV['API_AUTH_TOKEN'],
            ]
        );

        self::assertResponseStatusCodeSame(400);
    }

    /**
     * @throws Exception
     */
    public function test_it_returns_404_when_feed_does_not_exist(): void
    {
        $this->client->request(
            'GET',
            '/api/feeds/02597efa-e351-41b0-8f29-2efbd1cdeb0c',
            [],
            [],
            [
                'HTTP_Authorization' => 'Bearer ' . $_ENV['API_AUTH_TOKEN'],
            ]
        );

        self::assertResponseStatusCodeSame(404);
    }

    /**
     * @throws Exception
     */
    public function test_it_returns_feed_when_it_exists(): void
    {
        $connection = $this->entityManager->getConnection();

        $connection->executeStatement(
            'INSERT INTO news_headlines (
            id, source, title, url, position, scraped_at, created_at
        ) VALUES (
            :id, :source, :title, :url, :position, :scraped_at, :created_at
        )',
            [
                'id' => '02597efa-e351-41b0-8f29-2efbd1cdeb0c',
                'source' => 'el_pais',
                'title' => 'Test headline',
                'url' => 'https://example.com/test',
                'position' => 1,
                'scraped_at' => '2026-01-20 10:00:00',
                'created_at' => '2026-01-20 10:00:00',
            ]
        );

        $this->client->request(
            'GET',
            '/api/feeds/02597efa-e351-41b0-8f29-2efbd1cdeb0c',
            [],
            [],
            [
                'HTTP_Authorization' => 'Bearer ' . $_ENV['API_AUTH_TOKEN'],
            ]
        );

        self::assertResponseStatusCodeSame(200);
        self::assertResponseHeaderSame('Content-Type', 'application/json');

        $response = json_decode(
            $this->client->getResponse()->getContent(),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        self::assertSame('02597efa-e351-41b0-8f29-2efbd1cdeb0c', $response['id']);
        self::assertSame('el_pais', $response['source']);
        self::assertSame('Test headline', $response['title']);
        self::assertSame('https://example.com/test', $response['url']);
        self::assertSame(1, $response['position']);
        self::assertArrayHasKey('scrapedAt', $response);
        self::assertArrayHasKey('createdAt', $response);
    }
}
