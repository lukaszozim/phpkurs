<?php

namespace App\Repository;

use App\Entity\Apk;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Apk>
 *
 * @method Apk|null find($id, $lockMode = null, $lockVersion = null)
 * @method Apk|null findOneBy(array $criteria, array $orderBy = null)
 * @method Apk[]    findAll()
 * @method Apk[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ApkRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Apk::class);
    }

//    /**
//     * @return Apk[] Returns an array of Apk objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Apk
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
