<?php

namespace App\Repository;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Category>
 */
class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    public function getAll(bool $showInSlider = false)
    {
        $qb = $this->createQueryBuilder('c');

        if ($showInSlider) {
            $qb->andWhere('c.showInSlider = :showInSlider')
                ->setParameter('showInSlider', true);
        }

        return $qb->getQuery()->getResult();
    }
}
