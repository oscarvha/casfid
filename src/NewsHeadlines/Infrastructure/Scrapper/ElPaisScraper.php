<?php

namespace App\NewsHeadlines\Infrastructure\Scrapper;

use App\NewsHeadlines\Domain\Collection\NewsHeadlineCollection;
use App\NewsHeadlines\Domain\Exception\NewsScrapingFailed;
use App\NewsHeadlines\Domain\Model\NewsHeadline;
use App\NewsHeadlines\Domain\Port\NewsHeadlineIdGenerator;
use App\NewsHeadlines\Domain\Scrapper\NewsScraper;
use App\NewsHeadlines\Domain\ValueObject\NewsHeadlineId;
use App\NewsHeadlines\Domain\ValueObject\NewsHeadlineOrigin;
use App\NewsHeadlines\Domain\ValueObject\NewsHeadlineSource;
use App\NewsHeadlines\Domain\ValueObject\NewsHeadlineTitle;
use App\NewsHeadlines\Domain\ValueObject\NewsHeadlineUrl;
use DOMElement;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class ElPaisScraper implements NewsScraper
{

    private const HEADLINE_SELECTOR = 'article h2 a';

    public function __construct(
        private readonly HttpClientInterface $client,
        private NewsHeadlineIdGenerator $idGenerator
    ) {}

    /**
     * @return NewsHeadlineSource
     */
    public function source(): NewsHeadlineSource
    {
        return NewsHeadlineSource::fromString('el_pais');
    }

    /**
     * @return NewsHeadlineCollection
     */
    public function scrapeTopHeadlines(): NewsHeadlineCollection
    {
        try {
            $response = $this->client->request('GET', 'https://elpais.com/');
            $html = $response->getContent();

            $crawler = new Crawler($html);
            $headlines = [];

            foreach ($crawler->filter(self::HEADLINE_SELECTOR) as $a) {
                if (count($headlines) === 10) {
                    break;
                }

                $title = trim($a->textContent);

                if (!$a instanceof DOMElement) {
                    continue;
                }

                $url = $a->getAttribute('href');

                if ($title === '' || $url === '') {
                    continue;
                }

                if (str_starts_with($url, '/')) {
                    $url = 'https://elpais.com' . $url;
                }

                $headlines[] = NewsHeadline::create(
                    $this->idGenerator->generate(),
                    $this->source(),
                    NewsHeadlineTitle::fromString($title),
                    NewsHeadlineUrl::fromString($url),
                    NewsHeadlineOrigin::scraping(),
                    count($headlines) + 1,
                    new \DateTimeImmutable()
                );
            }

            return NewsHeadlineCollection::fromArray($headlines);

        } catch (\Throwable $e) {
            throw new NewsScrapingFailed(
                'Failed scraping headlines from El País',
                previous: $e
            );
        }
    }
}
