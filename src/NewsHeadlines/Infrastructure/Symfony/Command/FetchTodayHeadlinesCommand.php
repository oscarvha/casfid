<?php

namespace App\NewsHeadlines\Infrastructure\Symfony\Command;

use App\NewsHeadlines\Application\FetchTopHeadlines;
use App\NewsHeadlines\Domain\Exception\NewsScrapingFailed;
use App\NewsHeadlines\Infrastructure\Scrapper\ElMundoScraper;
use App\NewsHeadlines\Infrastructure\Scrapper\ElPaisScraper;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;


#[AsCommand(
    name: 'app:news-headlines:fetch-today',
    description: 'Fetch today\'s news headlines from external sources'
)]
final class FetchTodayHeadlinesCommand extends Command
{

    /**
     * @param FetchTopHeadlines $fetchTopHeadlines
     */
    public function __construct(
        private readonly FetchTopHeadlines $fetchTopHeadlines,

    ) {
        parent::__construct();
    }


    protected function configure(): void
    {
        $this
            ->setDescription('Fetch today\'s news headlines from external sources.')
            ->setHelp('This command allows you to fetch and store today\'s news headlines...');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Fetching today\'s news headlines...');

        try {

            $headlines = $this->fetchTopHeadlines->execute();

        } catch (NewsScrapingFailed $e) {

            $output->writeln('Error fetching headlines: ' . $e->getMessage());
            return Command::FAILURE;
        }

        if ($headlines->isEmpty()) {

            $output->writeln('No headlines found.');

            return Command::FAILURE;
        }

        foreach ($headlines as $headline) {
            $output->writeln('- ' . $headline->title() . ' (' . $headline->url() . ') '.' from ' . $headline->source());
        }

        return Command::SUCCESS;
    }
}
