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

namespace TheCadien\Bundle\SuluNewsBundle\Entity\Factory;

use Sulu\Component\Persistence\RelationTrait;
use Sulu\Component\Rest\Exception\EntityNotFoundException;
use TheCadien\Bundle\SuluNewsBundle\Entity\News;

class NewsFactory extends AbstractFactory implements NewsFactoryInterface
{
    use RelationTrait;

    /**
     * @var MediaFactory
     */
    private $mediaFactory;

    /**
     * @var TagFactory
     */
    private $tagFactory;

    /**
     * NewsFactory constructor.
     */
    public function __construct(
        MediaFactoryInterface $mediaFactory,
        TagFactoryInterface $tagFactory
    ) {
        $this->mediaFactory = $mediaFactory;
        $this->tagFactory = $tagFactory;
    }

    /**
     * @throws \Exception
     */
    public function generateNewNewsFromRequest(array $data): News
    {
        $news = new News();

        if ($this->getProperty($data, 'title')) {
            $news->setTitle($this->getProperty($data, 'title'));
        }

        if ($this->getProperty($data, 'teaser')) {
            $news->setTeaser($this->getProperty($data, 'teaser'));
        }

        if ($this->getProperty($data, 'header')) {
            $news->setHeader($this->mediaFactory->generateMedia($data['header']));
        }

        if ($this->getProperty($data, 'published_at')) {
            $news->setPublishedAt(new \DateTime($this->getProperty($data, 'published_at')));
        }

        if ($this->getProperty($data, 'content')) {
            $news->setContent($this->getProperty($data, 'content'));
        }

        if ($tags = $this->getProperty($data, 'tags')) {
            $this->tagFactory->processTags($news, $tags);
        }

        $news->setCreated(new \DateTime());

        return $news;
    }

    /**
     * @throws EntityNotFoundException
     * @throws \Exception
     */
    public function updateNewsFromRequest(array $data, News $news): News
    {
        if ($this->getProperty($data, 'title')) {
            $news->setTitle($this->getProperty($data, 'title'));
        }

        if ($this->getProperty($data, 'teaser')) {
            $news->setTeaser($this->getProperty($data, 'teaser'));
        }

        if ($this->getProperty($data, 'header')) {
            $news->setHeader($this->mediaFactory->generateMedia($data['header']));
        }

        if ($this->getProperty($data, 'published_at')) {
            $news->setPublishedAt(new \DateTime($this->getProperty($data, 'published_at')));
        }

        if ($this->getProperty($data, 'content')) {
            $news->setContent($this->getProperty($data, 'content'));
        }

        if ($tags = $this->getProperty($data, 'tags')) {
            $this->tagFactory->processTags($news, $tags);
        }

        $news->setChanged(new \DateTime());

        return $news;
    }
}
