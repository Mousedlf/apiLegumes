<?php

namespace App\Repository;

use App\Entity\Client;
use App\Entity\Platform;
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
        public function findByEmailAndPlatform($email, Platform $platform): Client
        {
            return $this->createQueryBuilder('c')
                ->andWhere('c.email = :e')
                ->andWhere('c.fromPlatform = :p')
                ->setParameter('e', $email)
                ->setParameter('p', $platform)
                ->orderBy('c.id', 'ASC')
                ->getQuery()
                ->getOneOrNullResult()
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
