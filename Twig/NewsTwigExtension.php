<?php

declare(strict_types=1);

/*
 * This file is part of TheCadien/SuluNewsBundle.
 *
 * (c) Oliver Kossin
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace TheCadien\Bundle\SuluNewsBundle\Twig;

use Doctrine\Common\Cache\Cache;
use TheCadien\Bundle\SuluNewsBundle\Entity\News;
use TheCadien\Bundle\SuluNewsBundle\Repository\NewsRepository;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Extension to handle news in frontend.
 */
class NewsTwigExtension extends AbstractExtension
{
    public function __construct(
        private readonly Cache $cache,
        private readonly NewsRepository $newsRepository
    ) {
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('sulu_resolve_news', [$this, 'resolveNewsFunction']),
            new TwigFunction('sulu_resolve_news_list', [$this, 'resolveNewsListFunction']),
            new TwigFunction('sulu_resolve_news_latest', [$this, 'resolveNewsLatestFunction']),
        ];
    }

    public function resolveNewsFunction(int $id): ?News
    {
        if ($this->cache->contains($id)) {
            return $this->cache->fetch($id);
        }

        $news = $this->newsRepository->find($id);
        if (null === $news) {
            return null;
        }

        $this->cache->save($id, $news);

        return $news;
    }

    /**
     * @return array<News>
     */
    public function resolveNewsListFunction(): array
    {
        return $this->newsRepository->getPublishedNews();
    }

    public function resolveNewsLatestFunction(): ?News
    {
        return $this->newsRepository->getLatestPublishedNews();
    }
}
