<?php

namespace App\Shared\Security\Infrastructure\Api\Symfony;



use JsonException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final class ApiTokenAuthenticator extends AbstractAuthenticator
{
    public function __construct(
        private readonly string $apiToken
    ) {}

    public function supports(Request $request): bool
    {
        return $request->headers->has('Authorization');
    }

    public function authenticate(Request $request): Passport
    {
        $header = $request->headers->get('Authorization');

        if (!$header || !str_starts_with($header, 'Bearer ')) {
            throw new AuthenticationException('Missing Bearer token');
        }

        $token = substr($header, 7);

        if ($token !== $this->apiToken) {
            throw new AuthenticationException('Invalid API token');
        }


        return new SelfValidatingPassport(
            new UserBadge(
                'api-token',
                static fn () => new class implements UserInterface {
                    public function getUserIdentifier(): string
                    {
                        return 'api-token';
                    }

                    public function getRoles(): array
                    {
                        return ['ROLE_API'];
                    }

                    #[\Deprecated]
                    public function eraseCredentials(): void {}
                }
            )
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;
    }

    /**
     * @throws JsonException
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response
    {
        return new Response(
            json_encode([
                'error' => 'Unauthorized',
                'message' => $exception->getMessage(),
            ], JSON_THROW_ON_ERROR),
            Response::HTTP_UNAUTHORIZED,
            ['Content-Type' => 'application/json']
        );
    }
}
