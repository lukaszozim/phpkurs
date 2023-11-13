<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\Address;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function save(User $user) : User {
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush($user);

        return $user;
    }

    public function delete(User $user) : User {
        $this->getEntityManager()->remove($user);
        $this->getEntityManager()->flush();

        return $user;
    }

    public function deleteAddress(Address $address): bool
    {
        $this->getEntityManager()->remove($address);
        $this->getEntityManager()->flush();
        // Check if the address is still in the database after deletion attempt
        $addressStillExists = $this->getEntityManager()->contains($address);

        if ($addressStillExists) {
            return false;
        }

            return true;
    }

//    public function flushAddress(Address $address): Address
//    {
//        $this->getEntityManager()->flush();
//
//        return $address;
//    }

//    /**
//     * @return User[] Returns an array of User objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('u.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?User
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
