<?php

namespace App\Tests\Shared\Security\Infrastructure\Api;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class CheckAuthControllerTest extends WebTestCase
{
    /**
     * @throws \JsonException
     */
    public function test_it_returns_401_when_no_token_is_provided(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/auth/check');

        self::assertResponseStatusCodeSame(401);
        self::assertResponseFormatSame('json');

        $content = $client->getResponse()->getContent();

        self::assertNotFalse($content);

        $response = json_decode($content, true, 512, JSON_THROW_ON_ERROR);

        self::assertSame('Unauthorized', $response['error']);
    }

    /**
     * @throws \JsonException
     */
    public function test_it_returns_401_with_invalid_token(): void
    {
        $client = static::createClient();

        $client->request(
            'GET',
            '/api/auth/check',
            server: [
                'HTTP_AUTHORIZATION' => 'Bearer invalid-token',
            ]
        );

        self::assertResponseStatusCodeSame(401);
        self::assertResponseFormatSame('json');

        $content = $client->getResponse()->getContent();

        self::assertNotFalse($content);

        $response = json_decode($content, true, 512, JSON_THROW_ON_ERROR);

        self::assertSame('Unauthorized', $response['error']);
    }

    /**
     * @throws \JsonException
     */
    public function test_it_returns_200_with_valid_token(): void
    {
        $client = static::createClient();

        $client->request(
            'GET',
            '/api/auth/check',
            server: [
                'HTTP_AUTHORIZATION' => 'Bearer ' . $_ENV['API_AUTH_TOKEN'],
            ]
        );

        self::assertResponseIsSuccessful();
        self::assertResponseFormatSame('json');

        $content = $client->getResponse()->getContent();

        self::assertNotFalse($content);

        $response = json_decode($content, true, 512, JSON_THROW_ON_ERROR);

        self::assertTrue($response['authenticated']);
    }
}
