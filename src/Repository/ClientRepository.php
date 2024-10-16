<?php

namespace App\Repository;

use App\Entity\Client;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Client>
 */
class ClientRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Client::class);
    }

        /**
         * @return Client[] Returns an array of Client objects
         */
        public function findByEmailAndPlatform($email, int $platformId): array
        {
            return $this->createQueryBuilder('c')
                ->andWhere('c.email = :e')
                ->andWhere('c.platformId = :pId')
                ->setParameter('e', $email)
                ->setParameter('pId', $platformId)
                ->orderBy('c.id', 'ASC')
                ->getQuery()
                ->getResult()
            ;
        }

    //    public function findOneBySomeField($value): ?Client
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
