<?php

namespace App\NewsHeadlines\Infrastructure\Scrapper;

use App\NewsHeadlines\Domain\Collection\NewsHeadlineCollection;
use App\NewsHeadlines\Domain\Exception\NewsScrapingFailed;
use App\NewsHeadlines\Domain\Model\NewsHeadline;
use App\NewsHeadlines\Domain\Port\NewsHeadlineIdGenerator;
use App\NewsHeadlines\Domain\Scrapper\NewsScraper;
use App\NewsHeadlines\Domain\ValueObject\NewsHeadlineOrigin;
use App\NewsHeadlines\Domain\ValueObject\NewsHeadlineSource;
use App\NewsHeadlines\Domain\ValueObject\NewsHeadlineTitle;
use App\NewsHeadlines\Domain\ValueObject\NewsHeadlineUrl;
use DOMElement;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class ElMundoScraper implements NewsScraper
{
    private const HEADLINE_SELECTOR = 'div.ue-l-cg__body';

    public function __construct(
        private readonly HttpClientInterface $client,
        private readonly NewsHeadlineIdGenerator $idGenerator
    ) {}

    /**
     * @return NewsHeadlineSource
     */
    public function source(): NewsHeadlineSource
    {
        return NewsHeadlineSource::fromString('el_mundo');
    }

    /**
     * @return NewsHeadlineCollection
     */
    public function scrapeTopHeadlines(): NewsHeadlineCollection
    {
        try {
            $response = $this->client->request('GET', 'https://www.elmundo.es/');
            $html = $response->getContent();

            $crawler = new Crawler($html);
            $blocks = $crawler->filter(self::HEADLINE_SELECTOR);

            if ($blocks->count() < 2) {
                return NewsHeadlineCollection::fromArray([]);
            }

            $headlines = [];

            for ($i = 2; $i < $blocks->count(); $i++) {
                $block = $blocks->eq($i);

                foreach ($block->filter('article a > h2.ue-c-cover-content__headline') as $headlineNode) {
                    if (count($headlines) === 10) {
                        break 2;
                    }

                    $title = trim($headlineNode->textContent);
                    $a = $headlineNode->parentNode;

                    if (!$a instanceof DOMElement) {
                        continue;
                    }

                    $url = $a->getAttribute('href');

                    if ($title === '' || $url === '') {
                        continue;
                    }

                    if (str_starts_with($url, '/')) {
                        $url = 'https://www.elmundo.es' . $url;
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
            }

            return NewsHeadlineCollection::fromArray($headlines);

        }catch(\Throwable $e) {
            throw new NewsScrapingFailed(
                'Failed scraping headlines from El Mundo',
                previous: $e
            );
        }

    }
}
