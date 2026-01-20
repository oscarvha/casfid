<?php

namespace App\Tests\NewsHeadlines\infraestructure\Scrapper;

use App\NewsHeadlines\Domain\Exception\NewsScrapingFailed;
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
            ->willReturnCallback(
                static function () use ($html): string {
                    return $html;
                }
            );

        $client = $this->createStub(HttpClientInterface::class);
        $client
            ->method('request')
            ->with('GET', 'https://www.elmundo.es/')
            ->willReturn($response);

        $scraper = new ElMundoScraper($client);

        $collection = $scraper->scrapeTopHeadlines();

        $items = iterator_to_array($collection);

        $this->assertIsArray($items);
        $this->assertNotEmpty($items);
    }

    public function test_it_throws_domain_exception_on_http_failure(): void
    {
        $exception = $this->createStub(TransportExceptionInterface::class);

        $client = $this->createStub(HttpClientInterface::class);
        $client
            ->method('request')
            ->willThrowException($exception);

        $scraper = new ElMundoScraper($client);

        $this->expectException(NewsScrapingFailed::class);

        $scraper->scrapeTopHeadlines();
    }

}
