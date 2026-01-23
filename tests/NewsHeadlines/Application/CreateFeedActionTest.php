<?php

declare(strict_types=1);

namespace App\Tests\NewsHeadlines\Application;

use App\NewsHeadlines\Application\CreateFeed\CreateFeedAction;
use App\NewsHeadlines\Application\CreateFeed\Exception\NewsHeadlineExistInSourceException;
use App\NewsHeadlines\Domain\Model\NewsHeadline;
use App\NewsHeadlines\Domain\Port\NewsHeadlineIdGenerator;
use App\NewsHeadlines\Domain\Repository\NewsHeadlineRepository;
use App\NewsHeadlines\Domain\ValueObject\NewsHeadlineId;
use PHPUnit\Framework\TestCase;

final class CreateFeedActionTest extends TestCase
{
    public function test_it_creates_a_news_headline(): void
    {
        $repository = $this->createMock(NewsHeadlineRepository::class);
        $idGenerator = $this->createMock(NewsHeadlineIdGenerator::class);

        $idGenerator
            ->expects($this->once())
            ->method('generate')
            ->willReturn(
                NewsHeadlineId::fromString('headline-id')
            );

        $repository
            ->method('existByUrlInSource')
            ->willReturn(false);

        $repository
            ->method('nextPositionForSourceAndDay')
            ->willReturn(1);

        $repository
            ->expects($this->once())
            ->method('save')
            ->with($this->isInstanceOf(NewsHeadline::class));

        $action = new CreateFeedAction($repository, $idGenerator);

        $headline = $action->execute(
            'el_pais',
            'Some news title',
            'https://example.com/news'
        );

        $this->assertSame('el_pais', $headline->source());
        $this->assertSame('Some news title', $headline->title());
    }

    public function test_it_throws_exception_if_news_already_exists_in_source(): void
    {
        $repository = $this->createStub(NewsHeadlineRepository::class);
        $idGenerator = $this->createStub(NewsHeadlineIdGenerator::class);

        $repository
            ->method('existByUrlInSource')
            ->willReturn(true);

        $action = new CreateFeedAction($repository, $idGenerator);

        $this->expectException(NewsHeadlineExistInSourceException::class);

        $action->execute(
            'el_pais',
            'Duplicated news',
            'https://example.com/news'
        );
    }


    public function test_it_does_not_generate_id_nor_save_when_news_exists(): void
    {
        $repository = $this->createMock(NewsHeadlineRepository::class);
        $idGenerator = $this->createMock(NewsHeadlineIdGenerator::class);

        $repository
            ->method('existByUrlInSource')
            ->willReturn(true);

        $repository
            ->expects($this->never())
            ->method('save');

        $idGenerator
            ->expects($this->never())
            ->method('generate');

        $action = new CreateFeedAction($repository, $idGenerator);

        $this->expectException(NewsHeadlineExistInSourceException::class);

        $action->execute(
            'el_pais',
            'Duplicated news',
            'https://example.com/news'
        );
    }

    public function test_it_assigns_next_position_for_source_and_day(): void
    {
        $repository = $this->createMock(NewsHeadlineRepository::class);
        $idGenerator = $this->createStub(NewsHeadlineIdGenerator::class);

        $idGenerator
            ->method('generate')
            ->willReturn(
                NewsHeadlineId::fromString('headline-id')
            );

        $repository
            ->method('existByUrlInSource')
            ->willReturn(false);

        $repository
            ->method('nextPositionForSourceAndDay')
            ->willReturn(5);

        $repository
            ->expects($this->once())
            ->method('save')
            ->with(
                $this->callback(
                    static fn (NewsHeadline $headline) =>
                        $headline->position() === 5
                )
            );

        $action = new CreateFeedAction($repository, $idGenerator);

        $action->execute(
            'el_pais',
            'Some news title',
            'https://example.com/news'
        );
    }

}
