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

namespace TheCadien\Bundle\SuluNewsBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Sulu\Component\SmartContent\Orm\DataProviderRepositoryInterface;
use TheCadien\Bundle\SuluNewsBundle\Entity\News;

/**
 * Class NewsRepository.
 */
class NewsRepository extends EntityRepository implements DataProviderRepositoryInterface
{
    /**
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function save(News $news): void
    {
        $this->getEntityManager()->persist($news);
        $this->getEntityManager()->flush();
    }

    private function getPublishedNewsQueryBuilder(): \Doctrine\ORM\QueryBuilder
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        return $qb->select('n')
            ->from('NewsBundle:News', 'n')
            ->where('n.enabled = 1')
            ->andWhere('n.publishedAt <= :created')
            ->setParameter('created', \date('Y-m-d H:i:s'))
            ->orderBy('n.publishedAt', 'DESC');
    }

    public function getPublishedNews(): array
    {
        $qb = $this->getPublishedNewsQueryBuilder();

        $news = $qb->getQuery()->getResult();

        if (!$news) {
            return [];
        }

        return $news;
    }

    public function getLatestPublishedNews(): ?News
    {
        $qb = $this->getPublishedNewsQueryBuilder();

        $news = $qb->setMaxResults(1)->getQuery()->getOneOrNullResult();

        if (!$news) {
            return null;
        }

        return $news;
    }

    public function findById(int $id): ?News
    {
        $news = $this->find($id);
        if (!$news) {
            return null;
        }

        return $news;
    }

    /**
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function remove(int $id): void
    {
        $this->getEntityManager()->remove(
            $this->getEntityManager()->getReference(
                $this->getClassName(),
                $id,
            ),
        );
        $this->getEntityManager()->flush();
    }

    public function findByFilters($filters, $page, $pageSize, $limit, $locale, $options = [])
    {
        return $this->getPublishedNews();
    }
}
