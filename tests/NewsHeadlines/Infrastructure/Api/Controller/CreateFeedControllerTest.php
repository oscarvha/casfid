<?php

namespace App\Tests\NewsHeadlines\Infrastructure\Api\Controller;

use App\Tests\Shared\Infrastructure\Api\ApiTestCase;
use Doctrine\DBAL\Exception;

final class CreateFeedControllerTest extends ApiTestCase
{
    public function test_it_creates_a_feed_successfully(): void
    {
        $this->client->request(
            'POST',
            '/api/feeds',
            [],
            [],
            [
                'HTTP_Authorization' => 'Bearer ' . $_ENV['API_AUTH_TOKEN'],
                'CONTENT_TYPE' => 'application/json',
            ],
            json_encode([
                'source' => 'el_mundo',
                'title'  => 'Headline created via API',
                'url'    => 'https://www.elmundo.es/api/test-headline.html',
            ], JSON_THROW_ON_ERROR)
        );

        self::assertResponseStatusCodeSame(201);

        $response = json_decode(
            $this->client->getResponse()->getContent(),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        self::assertArrayHasKey('id', $response);
        self::assertSame('el_mundo', $response['source']);
        self::assertSame('Headline created via API', $response['title']);
        self::assertSame('https://www.elmundo.es/api/test-headline.html', $response['url']);
        self::assertSame('api', $response['origin']);
        self::assertArrayHasKey('scrapedAt', $response);
        self::assertArrayHasKey('createdAt', $response);
        self::assertArrayHasKey('position', $response);
    }


    /**
     * @throws \JsonException
     */
    public function test_it_returns_400_when_payload_is_invalid(): void
    {
        $this->client->request(
            'POST',
            '/api/feeds',
            [],
            [],
            [
                'HTTP_Authorization' => 'Bearer ' . $_ENV['API_AUTH_TOKEN'],
                'CONTENT_TYPE' => 'application/json',
            ],
            json_encode([
                'source' => '',
                'title'  => '',
                'url'    => '',
            ], JSON_THROW_ON_ERROR)
        );

        self::assertResponseStatusCodeSame(400);

        $response = json_decode(
            $this->client->getResponse()->getContent(),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        self::assertSame('Bad Request', $response['error']);
        self::assertArrayHasKey('details', $response);
    }

    /**
     * @throws \JsonException
     * @throws Exception
     */
    public function test_it_returns_409_when_feed_already_exists_in_source(): void
    {
        $connection = $this->entityManager->getConnection();

        $connection->executeStatement(
            'INSERT INTO news_headlines (
            id, source, title, url, origin, position, scraped_at, created_at
        ) VALUES (
            :id, :source, :title, :url, :origin, :position, :scraped_at, :created_at
        )',
            [
                'id'         => '11111111-1111-1111-1111-111111111111',
                'source'     => 'el_mundo',
                'title'      => 'Existing headline',
                'url'        => 'https://www.elmundo.es/api/duplicated.html',
                'origin'     => 'scraping',
                'position'   => 1,
                'scraped_at' => '2026-01-21 10:00:00',
                'created_at' => '2026-01-21 10:00:00',
            ]
        );

        $this->client->request(
            'POST',
            '/api/feeds',
            [],
            [],
            [
                'HTTP_Authorization' => 'Bearer ' . $_ENV['API_AUTH_TOKEN'],
                'CONTENT_TYPE' => 'application/json',
            ],
            json_encode([
                'source' => 'el_mundo',
                'title'  => 'Duplicate headline',
                'url'    => 'https://www.elmundo.es/api/duplicated.html',
            ], JSON_THROW_ON_ERROR)
        );

        self::assertResponseStatusCodeSame(409);

        $response = json_decode(
            $this->client->getResponse()->getContent(),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        self::assertArrayHasKey('error', $response);
    }

    /**
     * @throws \JsonException
     */
    public function test_it_returns_401_without_token(): void
    {
        $this->client->request(
            'POST',
            '/api/feeds',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'source' => 'el_pais',
                'title'  => 'Unauthorized headline',
                'url'    => 'https://elpais.com/api/test.html',
            ], JSON_THROW_ON_ERROR)
        );

        self::assertResponseStatusCodeSame(401);
    }
}
