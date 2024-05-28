<?php

namespace App\Repository;

use App\Entity\ItemCollection;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ItemCollection>
 */
class ItemCollectionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ItemCollection::class);
    }

    public function findTop($value): array
    {
        return $this->createQueryBuilder('i')
//            ->orderBy('i.items','ASC')
            ->setMaxResults(6)
            ->getQuery()
            ->getResult()
        ;
    }
//    public function findOneBySomeField($value): ?ItemCollection
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.id = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
