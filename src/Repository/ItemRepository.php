<?php

namespace App\Repository;

use App\Entity\Item;
use App\Entity\ItemCollection;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Item>
 */
class ItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Item::class);
    }

    //    /**
    //     * @return Item[] Returns an array of Item objects
    //     */
    public function findAllSortedByDate(): array
    {
        return $this->createQueryBuilder('i')
            ->orderBy('i.dateAdd', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findByCollectionOrderByDate($collection): array
    {
        return $this->createQueryBuilder('i')
            ->where('i.itemCollection = :collection')
            ->setParameter('collection', $collection)
            ->orderBy('i.dateAdd', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    public function getOneItemWithAttributes(int $id): ?Item{
        return $this->createQueryBuilder('item')
            ->select('item','b','s','i','t','d','c','l','ta')
            ->leftJoin('item.itemAttributeBooleanFields','b')
            ->leftJoin('item.itemAttributeStringFields','s')
            ->leftJoin('item.itemAttributeIntegerFields','i')
            ->leftJoin('item.itemAttributeTextFields','t')
            ->leftJoin('item.itemAttributeDateFields','d')
            ->leftJoin('item.tags','ta')
            ->leftJoin('item.likes','l')
            ->leftJoin('item.comments','c')
            ->orderBy('item.dateAdd', 'DESC')
            ->where('item.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }
//        public function findLargestCollections(){
//            return $this->createQueryBuilder('i')
//                ->select('i.itemCollection')
//                ->groupBy('i.itemCollection')
//                ->having('count(i)>1')
//                ->orderBy('count(i)','desc')
//                ->setMaxResults(6)
//                ->getQuery()
//                ->getResult();
//        }

    //    public function findOneBySomeField($value): ?Item
    //    {
    //        return $this->createQueryBuilder('i')
    //            ->andWhere('i.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
