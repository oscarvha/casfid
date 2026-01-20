<?php

namespace App\Shared\Security\Infrastructure\Api\Symfony;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;

final class ApiAuthenticationEntryPoint implements AuthenticationEntryPointInterface
{
    public function start(Request $request, \Throwable $authException = null): JsonResponse
    {
        return new JsonResponse(
            [
                'error' => 'Unauthorized',
                'message' => 'Authentication required',
            ],
            401
        );
    }
}
