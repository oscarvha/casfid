<?php

namespace App\Tests\NewsHeadlines\Infrastructure\Api\Controller;

use App\Tests\Shared\Infrastructure\Api\ApiTestCase;
use Doctrine\DBAL\Exception;

final class GetFeedsControllerTest extends ApiTestCase
{
    public function test_it_returns_401_without_token(): void
    {
        $this->client->request('GET', '/api/feeds');

        self::assertResponseStatusCodeSame(401);
        self::assertResponseHeaderSame('Content-Type', 'application/json');
    }

    public function test_it_returns_200_with_valid_token(): void
    {
        $this->client->request(
            'GET',
            '/api/feeds',
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

        self::assertArrayHasKey('items', $response);
        self::assertArrayHasKey('nextCursor', $response);
    }

    /**
     * @throws \JsonException
     * @throws Exception
     */
    public function test_it_returns_items_and_next_cursor(): void
    {
        $connection = $this->entityManager->getConnection();

        $connection->executeStatement(
            'INSERT INTO news_headlines (id, source, title, url, position, scraped_at) VALUES
            (:id1, :source1, :title1, :url1, :pos1, :date1),
            (:id2, :source2, :title2, :url2, :pos2, :date2)',
            [
                'id1' => 'bf6673b6-fc1a-4308-8d68-697e5e379191',
                'source1' => 'el_pais',
                'title1' => 'Title 1',
                'url1' => 'https://example.com/1',
                'pos1' => 1,
                'date1' => '2026-01-20 10:00:00',

                'id2' => 'a3c1c5b2-9f4e-4a1f-8c92-4b4c6c3e2d11',
                'source2' => 'el_mundo',
                'title2' => 'Title 2',
                'url2' => 'https://example.com/2',
                'pos2' => 2,
                'date2' => '2026-01-20 11:00:00',
            ]
        );

        $this->client->request(
            'GET',
            '/api/feeds?limit=2',
            [],
            [],
            [
                'HTTP_Authorization' => 'Bearer ' . $_ENV['API_AUTH_TOKEN'],
            ]
        );

        self::assertResponseStatusCodeSame(200);

        $response = json_decode(
            $this->client->getResponse()->getContent(),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        self::assertCount(2, $response['items']);
        self::assertSame(
            'a3c1c5b2-9f4e-4a1f-8c92-4b4c6c3e2d11',
            $response['nextCursor']
        );
    }

    /**
     * @throws \JsonException
     * @throws Exception
     */
    public function test_it_applies_cursor_pagination(): void
    {
        $connection = $this->entityManager->getConnection();

        $connection->executeStatement(
            'INSERT INTO news_headlines (id, source, title, url, position, scraped_at) VALUES
            (:id1, :source1, :title1, :url1, :pos1, :date1),
            (:id2, :source2, :title2, :url2, :pos2, :date2),
            (:id3, :source3, :title3, :url3, :pos3, :date3)',
            [
                'id1' => 'bf6673b6-fc1a-4308-8d68-697e5e379191',
                'source1' => 'el_mundo',
                'title1' => 'First',
                'url1' => 'https://example.com/1',
                'pos1' => 1,
                'date1' => '2026-01-20 10:00:00',

                'id2' => 'a3c1c5b2-9f4e-4a1f-8c92-4b4c6c3e2d11',
                'source2' => 'el_mundo',
                'title2' => 'Second',
                'url2' => 'https://example.com/2',
                'pos2' => 2,
                'date2' => '2026-01-20 11:00:00',

                'id3' => 'd6e5b9a4-1f2a-4c0b-9a8e-7b6c5d4e3f21',
                'source3' => 'el_mundo',
                'title3' => 'Third',
                'url3' => 'https://example.com/3',
                'pos3' => 3,
                'date3' => '2026-01-20 12:00:00',
            ]
        );

        $this->client->request(
            'GET',
            '/api/feeds?limit=1&cursor=bf6673b6-fc1a-4308-8d68-697e5e379191',
            [],
            [],
            [
                'HTTP_Authorization' => 'Bearer ' . $_ENV['API_AUTH_TOKEN'],
            ]
        );

        self::assertResponseStatusCodeSame(200);

        $response = json_decode(
            $this->client->getResponse()->getContent(),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        self::assertCount(1, $response['items']);
        self::assertSame(
            'a3c1c5b2-9f4e-4a1f-8c92-4b4c6c3e2d11',
            $response['items'][0]['id']
        );
    }

    /**
     * @throws \JsonException
     */
    public function test_it_returns_400_when_limit_is_too_large(): void
    {
        $this->client->request(
            'GET',
            '/api/feeds?limit=999',
            [],
            [],
            [
                'HTTP_Authorization' => 'Bearer ' . $_ENV['API_AUTH_TOKEN'],
            ]
        );

        self::assertResponseStatusCodeSame(400);

        $response = json_decode(
            $this->client->getResponse()->getContent(),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        self::assertSame('Bad Request', $response['error']);
    }

    public function test_it_returns_400_when_cursor_is_invalid(): void
    {
        $this->client->request(
            'GET',
            '/api/feeds?cursor=not-a-uuid',
            [],
            [],
            [
                'HTTP_Authorization' => 'Bearer ' . $_ENV['API_AUTH_TOKEN'],
            ]
        );

        self::assertResponseStatusCodeSame(400);
    }

    /**
     * @throws \JsonException
     */
    public function test_it_returns_empty_items_when_no_feeds_exist(): void
    {
        $this->client->request(
            'GET',
            '/api/feeds',
            [],
            [],
            [
                'HTTP_Authorization' => 'Bearer ' . $_ENV['API_AUTH_TOKEN'],
            ]
        );

        self::assertResponseStatusCodeSame(200);

        $response = json_decode(
            $this->client->getResponse()->getContent(),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        self::assertSame([], $response['items']);
        self::assertNull($response['nextCursor']);
    }


    public function test_cursor_at_end_returns_empty_page(): void
    {
        $connection = $this->entityManager->getConnection();

        $connection->executeStatement(
            'INSERT INTO news_headlines (id, source, title, url, position, scraped_at)
         VALUES (:id, :source, :title, :url, :pos, :date)',
            [
                'id' => 'bf6673b6-fc1a-4308-8d68-697e5e379191',
                'source' => 'el_mundo',
                'title' => 'Only one',
                'url' => 'https://example.com',
                'pos' => 1,
                'date' => '2026-01-20 10:00:00',
            ]
        );

        $this->client->request(
            'GET',
            '/api/feeds?limit=2&cursor=bf6673b6-fc1a-4308-8d68-697e5e379191',
            [],
            [],
            [
                'HTTP_Authorization' => 'Bearer ' . $_ENV['API_AUTH_TOKEN'],
            ]
        );

        self::assertResponseStatusCodeSame(200);

        $response = json_decode(
            $this->client->getResponse()->getContent(),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        self::assertSame([], $response['items']);
        self::assertNull($response['nextCursor']);
    }}
