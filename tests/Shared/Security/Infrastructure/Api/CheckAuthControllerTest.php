<?php

namespace App\Tests\Shared\Security\Infrastructure\Api;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class CheckAuthControllerTest extends WebTestCase
{
    public function test_it_returns_401_when_no_token_is_provided(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/auth/check');

        self::assertResponseStatusCodeSame(401);
        self::assertResponseFormatSame('json');

        $response = json_decode($client->getResponse()->getContent(), true);

        self::assertSame('Unauthorized', $response['error']);
    }

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

        $response = json_decode($client->getResponse()->getContent(), true);

        self::assertSame('Unauthorized', $response['error']);
    }

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

        $response = json_decode($client->getResponse()->getContent(), true);

        self::assertTrue($response['authenticated']);
    }
}
