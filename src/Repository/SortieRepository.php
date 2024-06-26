<?php

namespace App\Repository;

use App\Entity\Sortie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Sortie>
 */
class SortieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sortie::class);
    }

    public function findSorties(){
        //return $this->createQueryBuilder('s')
        return $this->findAll();
    }


//    public function findSortiesByCityAndPlace(int $villeId, int $lieuId): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.lieu = :lieuId')
//            ->setParameter('lieuId', $lieuId)
//            ->leftJoin('s.lieu', 'lieu')
//            ->andWhere('lieu.ville = :villeId')
//            ->setParameter('villeId', $villeId)
//            ->leftJoin('lieu.ville', 'ville')
//            ->getQuery()
//            ->getResult();
//    }
//    /**
//     * @return Sortie[] Returns an array of Sortie objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Sortie
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
