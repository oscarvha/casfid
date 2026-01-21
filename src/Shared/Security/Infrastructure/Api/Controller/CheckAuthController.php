<?php

namespace App\Shared\Security\Infrastructure\Api\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class CheckAuthController extends AbstractController
{
    /**
     * @return JsonResponse
     */
    #[Route('/api/auth/check', name: 'api_auth_check', methods: ['GET'])]
    public function __invoke(): JsonResponse
    {
        return new JsonResponse([
            'authenticated' => true,
            'message' => 'The API token is valid.',

        ]);
    }
}
