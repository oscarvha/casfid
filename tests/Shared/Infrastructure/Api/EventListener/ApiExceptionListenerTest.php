<?php

declare(strict_types=1);

namespace App\Tests\Shared\Infrastructure\Api\EventListener;

use App\Shared\Api\Infrastructure\EventListener\ApiExceptionListener;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

final class ApiExceptionListenerTest extends TestCase
{
    public function test_it_returns_500_json_for_runtime_exception_in_api(): void
    {
        $listener = new ApiExceptionListener();

        $request = Request::create(
            '/api/test',
            'GET'
        );

        $exception = new \RuntimeException('Boom');

        $kernel = $this->createStub(HttpKernelInterface::class);

        $event = new ExceptionEvent(
            $kernel,
            $request,
            HttpKernelInterface::MAIN_REQUEST,
            $exception
        );

        $listener($event);

        $response = $event->getResponse();

        $this->assertNotNull($response);
        $this->assertSame(500, $response->getStatusCode());
        $this->assertSame('application/json', $response->headers->get('Content-Type'));

        $data = json_decode($response->getContent(), true);
        $this->assertSame('Internal Server Error', $data['error']);
    }
}
