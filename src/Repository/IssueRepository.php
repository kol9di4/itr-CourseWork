<?php

namespace App\Repository;

use App\Entity\Issue;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Issue>
 */
class IssueRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Issue::class);
    }
        public function findIssuesOrderAsc($user): array
        {
            return $this->createQueryBuilder('i')
                ->andWhere('i.user = :val')
                ->setParameter('val', $user)
                ->orderBy('i.id', 'DESC')
                ->getQuery()
                ->getResult()
            ;
        }

    //    public function findOneBySomeField($value): ?Issue
    //    {
    //        return $this->createQueryBuilder('i')
    //            ->andWhere('i.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
