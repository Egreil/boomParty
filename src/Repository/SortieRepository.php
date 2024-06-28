<?php

namespace App\Repository;

use App\Entity\Sortie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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
        return $this->createQueryBuilder('s')
            ->select('s', 'campus', 'lieu', 'etat', 'organisateur', 'participants')
            ->leftJoin('s.campus', 'campus')
            ->leftJoin('s.lieu', 'lieu')
            ->leftJoin('s.etat', 'etat')
            ->leftJoin('s.organisateur', 'organisateur')
            ->leftJoin('s.participants', 'participants')
            ->getQuery()
            ->getResult();
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
        //Création de l'identificateur de la sortie
        $rsm = new ResultSetMappingBuilder($this->getEntityManager());
        //On dit à l'identificateur que la sortie sera de type Sortie
        $rsm->addRootEntityFromClassMetadata('App\Entity\Sortie', 's');
//        $rsm->addJoinedEntityFromClassMetadata('App\Entity\Etat', 'e', 's', 'id', array('id' => 'etat_id'));
        //Recupération des éléments de la sortie uniquement après jointure avec l'état pour pouvoir récupérer un tableau de Sortie
        $sql="SELECT s.* FROM sortie as s join  etat  as e on e.id=s.etat_id  WHERE (TIMESTAMPDIFF(MONTH,s.date_heure_debut, NOW()))>=1 AND e.libelle<>'Historisée';";
        $query=$this->getEntityManager()->createNativeQuery($sql,$rsm);
        //Execution de la requête
        $result=$query->getResult();
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

    //    public function findByCodePostal(){
//        $qb = $this->createQueryBuilder('s');
//        $qb->leftJoin('s.codePostal', 'cp');
//        $query =$qb->getQuery();
//    }
    public function findSortiesByCityAndPlace(): array
    {
        return $this->createQueryBuilder('s')
            ->leftJoin('s.lieu', 'lieu')
            ->leftJoin('lieu.ville','ville')
            ->addSelect('lieu')
            ->addSelect('ville')
            ->getQuery()
            ->getResult();

    }

    //TODO faire une requete qui calcule le temps debut + duree
    public function findSortiesCommencees(){

        $qb = $this->createQueryBuilder('s');
        $qb->innerJoin('s.etat', 'e')->addSelect('e')
            ->andWhere("e.libelle != 'Historisée'");
        $query = $qb->getQuery();
        $result=$query->getResult();
        return $result;
    }
    //TODO faire une requete qui calcule le temps debut + duree
    public function findSortiesPassees(){
        $rsm = new ResultSetMappingBuilder($this->getEntityManager());
        $rsm->addRootEntityFromClassMetadata('App\Entity\Sortie', 's');
        $sql="SELECT s. FROM sortie as s join  etat  as e on s.etat_id=e.id  WHERE (TIMESTAMPDIFF(MINUTE,ADDDATE(s.date_heure_debut, INTERVAL s.duree HOUR), NOW()))>=0 AND e.libelle='Activité en cours';";
        $query=$this->getEntityManager()->createNativeQuery($sql,$rsm);
        $result=$query->getResult();
        return $result;
    }

}
