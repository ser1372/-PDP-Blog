<?php

namespace App\Repository;

use App\Entity\Post;
use App\Enum\PostStatusEnum;
use App\Enum\PostTypeEnum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Post>
 */
class PostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }

    public function getLast(int $limit = 6)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.active = :active')
            ->andWhere('p.status != :status')
            ->andWhere('p.type != :type')
            ->setParameter('active', true)
            ->setParameter('status', PostStatusEnum::MODERATION->value)
            ->setParameter('type',PostTypeEnum::NEWS->value)
            ->orderBy('p.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->useQueryCache(true)
            ->getResult();
    }

    public function getPostBySlug(string $slug): ?Post
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.slug = :slug')
            ->setParameter('slug', $slug)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getNews(int $limit = 4): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.active = :active')
            ->andWhere('p.status != :status')
            ->andWhere('p.type = :type')
            ->setParameter('active', true)
            ->setParameter('status', PostStatusEnum::MODERATION->value)
            ->setParameter('type', PostTypeEnum::NEWS->value)
            ->orderBy('RANDOM()')
            ->setMaxResults($limit)
            ->getQuery()
            ->useQueryCache(true)
            ->getResult();
    }
}
