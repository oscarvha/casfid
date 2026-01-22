<?php

namespace App\Tests\NewsHeadlines\Infrastructure\Api\Controller;

use App\Tests\Shared\Infrastructure\Api\ApiTestCase;
use Doctrine\DBAL\Exception;

final class UpdateFeedControllerTest extends ApiTestCase
{
    /**
     * @throws \JsonException
     * @throws Exception
     */
    public function test_it_updates_a_feed_successfully(): void
    {
        $connection = $this->entityManager->getConnection();

        $connection->executeStatement(
            'INSERT INTO news_headlines (
            id, source, title, url, origin, position, scraped_at, created_at
        ) VALUES (
            :id, :source, :title, :url, :origin, :position, :scraped_at, :created_at
        )',
            [
                'id' => '11111111-1111-1111-1111-111111111111',
                'source' => 'el_pais',
                'title' => 'Old title',
                'url' => 'https://elpais.com/old',
                'origin' => 'api',
                'position' => 1,
                'scraped_at' => '2026-01-20 10:00:00',
                'created_at' => '2026-01-20 10:00:00',
            ]
        );

        $this->client->request(
            'PUT',
            '/api/feeds/11111111-1111-1111-1111-111111111111',
            [],
            [],
            [
                'HTTP_Authorization' => 'Bearer ' . $_ENV['API_AUTH_TOKEN'],
                'CONTENT_TYPE' => 'application/json',
            ],
            json_encode([
                'title' => 'Updated title',
                'url' => 'https://elpais.com/new',
            ], JSON_THROW_ON_ERROR)
        );

        self::assertResponseStatusCodeSame(200);

        $content = $this->client->getResponse()->getContent();

        self::assertNotFalse($content);

        $response = json_decode(
            $content,
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        self::assertSame('Updated title', $response['title']);
        self::assertSame('https://elpais.com/new', $response['url']);
    }

    /**
     * @throws \JsonException
     */
    public function test_it_returns_404_when_feed_does_not_exist(): void
    {
        $this->client->request(
            'PUT',
            '/api/feeds/aaaaaaaa-aaaa-aaaa-aaaa-aaaaaaaaaaaa',
            [],
            [],
            [
                'HTTP_Authorization' => 'Bearer ' . $_ENV['API_AUTH_TOKEN'],
                'CONTENT_TYPE' => 'application/json',
            ],
            json_encode([
                'title' => 'Does not matter',
                'url' => 'https://example.com',
            ], JSON_THROW_ON_ERROR)
        );

        self::assertResponseStatusCodeSame(404);
    }

    /**
     * @throws Exception
     * @throws \JsonException
     */
    public function test_it_returns_409_when_url_already_exists_in_same_source(): void
    {
        $connection = $this->entityManager->getConnection();

        $connection->executeStatement(
            'INSERT INTO news_headlines (
            id, source, title, url, origin, position, scraped_at, created_at
        ) VALUES
        (
            :id1, :source, :title1, :url1, :origin, 1, :scraped_at, :created_at
        ),
        (
            :id2, :source, :title2, :url2, :origin, 2, :scraped_at, :created_at
        )',
            [
                'id1' => '11111111-1111-1111-1111-111111111111',
                'id2' => '22222222-2222-2222-2222-222222222222',
                'source' => 'el_pais',
                'title1' => 'First',
                'title2' => 'Second',
                'url1' => 'https://elpais.com/a',
                'url2' => 'https://elpais.com/b',
                'origin' => 'api',
                'scraped_at' => '2026-01-20 10:00:00',
                'created_at' => '2026-01-20 10:00:00',
            ]
        );

        $this->client->request(
            'PUT',
            '/api/feeds/22222222-2222-2222-2222-222222222222',
            [],
            [],
            [
                'HTTP_Authorization' => 'Bearer ' . $_ENV['API_AUTH_TOKEN'],
                'CONTENT_TYPE' => 'application/json',
            ],
            json_encode([
                'title' => 'Updated',
                'url' => 'https://elpais.com/a',
            ], JSON_THROW_ON_ERROR)
        );

        self::assertResponseStatusCodeSame(409);
    }


    /**
     * @throws \JsonException
     */
    public function test_it_returns_400_when_payload_is_invalid(): void
    {
        $this->client->request(
            'PUT',
            '/api/feeds/11111111-1111-1111-1111-111111111111',
            [],
            [],
            [
                'HTTP_Authorization' => 'Bearer ' . $_ENV['API_AUTH_TOKEN'],
                'CONTENT_TYPE' => 'application/json',
            ],
            json_encode([
                'title' => '',
                'url' => 'not-a-url',
            ], JSON_THROW_ON_ERROR)
        );

        self::assertResponseStatusCodeSame(400);
    }
}
