<?php

namespace App\Tests\NewsHeadlines\Infrastructure\Scrapper;

use App\NewsHeadlines\Domain\Exception\NewsScrapingFailed;
use App\NewsHeadlines\Domain\Model\NewsHeadline;
use App\NewsHeadlines\Domain\Port\NewsHeadlineIdGenerator;
use App\NewsHeadlines\Domain\ValueObject\NewsHeadlineId;
use App\NewsHeadlines\Infrastructure\Scrapper\ElMundoScraper;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

final class ElMundoScraperTest extends TestCase
{
    public function test_it_scrapes_headlines_from_html_fixture(): void
    {
        $path = __DIR__ . '/fixtures/elmundo_home.html';

        if (!file_exists($path)) {
            $this->fail('Fixture elmundo_home.html not found');
        }

        $html = file_get_contents($path);
        $this->assertIsString($html);

        $response = $this->createStub(ResponseInterface::class);
        $response
            ->method('getContent')
            ->willReturn($html);

        $client = $this->createStub(HttpClientInterface::class);
        $client
            ->method('request')
            ->with('GET', 'https://www.elmundo.es/')
            ->willReturn($response);


        $idGenerator = $this->createStub(NewsHeadlineIdGenerator::class);
        $idGenerator
            ->method('generate')
            ->willReturnCallback(
                static fn () => NewsHeadlineId::fromString(uniqid('elmundo-', true))
            );

        $scraper = new ElMundoScraper($client, $idGenerator);

        $collection = $scraper->scrapeTopHeadlines();
        $items = iterator_to_array($collection);

        $this->assertCount(10, $items);
    }

    public function test_it_throws_domain_exception_on_http_failure(): void
    {
        $exception = $this->createStub(TransportExceptionInterface::class);

        $client = $this->createStub(HttpClientInterface::class);
        $client
            ->method('request')
            ->willThrowException($exception);


        $idGenerator = $this->createStub(NewsHeadlineIdGenerator::class);
        $idGenerator
            ->method('generate')
            ->willReturnCallback(
                static fn () => NewsHeadlineId::fromString(uniqid('elmundo-', true))
            );

        $scraper = new ElMundoScraper($client,$idGenerator);

        $this->expectException(NewsScrapingFailed::class);

        $scraper->scrapeTopHeadlines();
    }

}
