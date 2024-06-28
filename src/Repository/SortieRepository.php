<?php

namespace App\Repository;

use App\Entity\Sortie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
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
        $rsm = new ResultSetMappingBuilder($this->getEntityManager());
        $rsm->addRootEntityFromClassMetadata('App\Entity\Sortie', 's');
        $sql="SELECT * FROM sortie as s";
        $query=$this->getEntityManager()->createNativeQuery($sql,$rsm);
        //return $this->createQueryBuilder('s')
        return $query->getResult();
    }
    public function findSortiesAHistoriser()
{
//    Option1:dql, doctrine query langage
//        $sql="SELECT s FROM sortie as s join  etat  as e on e.id=s.etat_id  WHERE (TIMESTAMPDIFF(MONTH,s.date_heure_debut, NOW()))>=1 and e.libelle!='Historisé';";

//        Option2: dql avec QueryBuilder
//        $dq=$this->createQueryBuilder('s')
//                ->join('App\Entity\Etat','e','e.id=s.etat_id')
//            ->andWhere("TIMESTAMPDIFF(MONTH,s.date_heure_debut, NOW()))>=1 and e.libelle!='Historisé'");
//        $query=$this->getEntityManager()->createQuery($dq);


        //Option3: native query
        $rsm = new ResultSetMappingBuilder($this->getEntityManager());
        $rsm->addRootEntityFromClassMetadata('App\Entity\Sortie', 's');
        $sql="SELECT * FROM sortie as s join  etat  as e on e.id=s.etat_id  WHERE (TIMESTAMPDIFF(MONTH,s.date_heure_debut, NOW()))>=1 AND e.libelle<>'Historisée';";
        $query=$this->getEntityManager()->createNativeQuery($sql,$rsm);

        //        $result=$this->getEntityManager()->getConnection()->executeQuery($sql);
        //var_dump($query);
//        $result=$result->fetchAllAssociative();
        $result=$query->getResult();
        //var_dump($result);
        return $result;
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
