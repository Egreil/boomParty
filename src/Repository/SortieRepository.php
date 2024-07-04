<?php

namespace App\Repository;

use App\Entity\Sortie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;


/**
 * @extends ServiceEntityRepository<Sortie>
 */
class SortieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sortie::class);
        //$this->security->getUser();
    }

    public function findAll(): array
    {
        $sorties = $this->findAll();
        return $sorties;
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


    public function findSortiesByFilters($data,$user)
    //$nom,$campus, $dateDebut, $dateFin, $organisateur, $inscrit, $nonInscrit, $sortiePasse,$user)
    {
        $qb = $this->createQueryBuilder('s');

        if ($data['campus']) {
            $qb->andWhere('s.campus = :campus')
                ->setParameter('campus', $data['campus']);
        }

        if ($data['nom']) {
            $qb->andWhere('s.nom LIKE :nom')
                ->setParameter('nom', '%'.$data['nom'].'%');
        }

        if ($data['dateDebut']  && $data['dateFin'] ) {
            $qb->andWhere('s.dateHeureDebut BETWEEN :dateDebut AND :dateFin')
                ->setParameter('dateDebut', $data['dateDebut'])
                ->setParameter('dateFin', $data['dateFin']);
        } elseif ($data['dateDebut']) {
            $qb->andWhere('s.dateHeureDebut >= :dateDebut')
                ->setParameter('dateDebut', $data['dateDebut']);
        } elseif ($data['dateFin']) {
            $qb->andWhere('s.dateHeureDebut <= :dateFin')
                ->setParameter('dateFin', $data['dateFin']);
        }

        if ($data['organisateur']) {
            $qb->andWhere('s.organisateur = :user')
                ->setParameter('user', $user);
        }

        if ($data['inscrit']) {
            $qb->andWhere(':user MEMBER OF s.participants')
                ->setParameter('user', $user);
        }

        if ($data['nonInscrit']) {
            $qb->andWhere(':user NOT MEMBER OF s.participants')
                ->setParameter('user', $user);
        }

        if ($data['sortiePasse']) {
            $qb->andWhere('s.dateHeureDebut < :now')
                ->setParameter('now', new \DateTime());
        }

        //dd($qb->getQuery()->getResult());

        return $qb->getQuery()->getResult();
    }

    public function findSortiesCommencees(){

//        $qb = $this->createQueryBuilder('s');
//        $qb->innerJoin('s.etat', 'e')->addSelect('e')
//            ->andWhere("e.libelle != 'Historisée'");
        $rsm = new ResultSetMappingBuilder($this->getEntityManager());
        $rsm->addRootEntityFromClassMetadata('App\Entity\Sortie', 's');
        $sql="SELECT s.* FROM sortie as s join  etat  as e on s.etat_id=e.id  WHERE (TIMESTAMPDIFF(MINUTE,s.date_heure_debut, NOW()))>=0 AND (e.libelle='Cloturée' OR e.libelle='Ouverte');";
        //$query = $qb->getQuery();
        $query=$this->getEntityManager()->createNativeQuery($sql,$rsm);
        $result=$query->getResult();
        return $result;
    }

    public function findSortiesPassees(){
        $rsm = new ResultSetMappingBuilder($this->getEntityManager());
        $rsm->addRootEntityFromClassMetadata('App\Entity\Sortie', 's');
        $sql="SELECT s.* FROM sortie as s join  etat  as e on s.etat_id=e.id  WHERE (TIMESTAMPDIFF(MINUTE,ADDDATE(s.date_heure_debut, INTERVAL s.duree HOUR), NOW()))>=0 AND e.libelle='Activité en cours';";
        $query=$this->getEntityManager()->createNativeQuery($sql,$rsm);
        $result=$query->getResult();
        return $result;
    }


}
