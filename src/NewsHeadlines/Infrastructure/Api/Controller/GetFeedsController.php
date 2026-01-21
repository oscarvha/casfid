<?php

namespace App\NewsHeadlines\Infrastructure\Api\Controller;

use App\NewsHeadlines\Application\GetFeeds\Dto\FeedItemDto;
use App\NewsHeadlines\Application\GetFeeds\Dto\FeedResponseDto;
use App\NewsHeadlines\Application\GetFeeds\Exception\InvalidCursorException;
use App\NewsHeadlines\Application\GetFeeds\GetFeedsQuery;
use App\NewsHeadlines\Infrastructure\Api\Request\GetFeedsRequest;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final readonly class GetFeedsController
{
    public function __construct(
        private GetFeedsQuery $feedsQuery,
    ) {
    }

    /**
     * @param Request $request
     * @param ValidatorInterface $validator
     * @return JsonResponse
     * @throws Exception
     */
    #[Route('/api/feeds', name: 'news_headline_get', methods: ['GET'])]
    public function __invoke(Request $request, ValidatorInterface $validator): JsonResponse
    {
        $dto = GetFeedsRequest::fromRequest($request->query->all());

        $errors = $validator->validate($dto);

        if (count($errors) > 0) {
            $messages = [];

            foreach ($errors as $error) {
                $messages[] = $error->getPropertyPath() . ': ' . $error->getMessage();
            }

            return new JsonResponse(
                [
                    'error' => 'Bad Request',
                    'details' => $messages,
                ],
                400
            );
        }

        try {
            $responseDto = $this->feedsQuery->execute(
                $dto->limit,
                $dto->cursor
            );
        } catch (\InvalidArgumentException|InvalidCursorException $e) {
            return new JsonResponse(
                ['error' => 'Bad Request'],
                400
            );
        }

        return new JsonResponse($responseDto, Response::HTTP_OK);
    }

}
