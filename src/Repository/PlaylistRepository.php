<?php

namespace App\Repository;

use App\Entity\Playlist;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Playlist>
 *
 * @method Playlist|null find($id, $lockMode = null, $lockVersion = null)
 * @method Playlist|null findOneBy(array $criteria, array $orderBy = null)
 * @method Playlist[]    findAll()
 * @method Playlist[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlaylistRepository extends ServiceEntityRepository
{
    /**
     * 
     * @param ManagerRegistry $registryCréation du constructeur
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Playlist::class);
    }

    /**
     * Ajout d'une playlist
     */
    public function add(Playlist $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    

    /**
     * Suppression d'une playlist
     */
    public function remove(Playlist $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    
    private const FORMATION = 'p.formations';
    private const ID = 'p.id';
    private const NAME = 'p.name';
   
    
    
    /**
     * Retourne toutes les playlists triées sur le nom de la playlist
     * @param type $champ
     * @param type $ordre
     * @return Playlist[]
     */
    public function findAllOrderByName($ordre): array{
        return $this->createQueryBuilder('p')

                ->leftjoin(self::FORMATION, 'f')
                ->groupBy(self::ID)
                ->orderBy(self::NAME, $ordre)
                ->getQuery()
                ->getResult();       
    }
    
        /**
     * Retourne toutes les playlists triées sur le nom de la formation
     * @param type $champ
     * @param type $ordre
     * @return Playlist[]
     */
    public function findAllOrderByNbFormations($ordre): array{
        return $this->createQueryBuilder('p')

                ->leftjoin(self::FORMATION, 'f')
                ->groupBy(self::ID)
                ->orderBy('count(f.title)', $ordre)
                ->getQuery()
                ->getResult();       
    }

    /**
     * Enregistrements dont un champ contient une valeur
     * ou tous les enregistrements si la valeur est vide
     * @param type $champ
     * @param type $valeur
     * @param type $table si $champ dans une autre table
     * @return Playlist[]
     */
    public function findByContainValue($champ, $valeur, $table=""): array{
        if($valeur==""){
            return $this->findAllOrderByName('ASC');
        }    
        if($table==""){      
            return $this->createQueryBuilder('p')

                    ->leftjoin(self::FORMATION, 'f')
                    ->where('p.'.$champ.' LIKE :valeur')
                    ->setParameter('valeur', '%'.$valeur.'%')
                    ->groupBy(self::ID)
                    ->orderBy(self::NAME, 'ASC')
                    ->getQuery()
                    ->getResult();              
        }else{   
            return $this->createQueryBuilder('p')
                    ->leftjoin(self::FORMATION, 'f')
                    ->leftjoin('f.categories', 'c')
                    ->where('c.'.$champ.' LIKE :valeur')
                    ->setParameter('valeur', '%'.$valeur.'%')
                    ->groupBy(self::ID)
                    ->orderBy(self::NAME, 'ASC')
                    ->getQuery()
                    ->getResult();                          
        }           
    }    

}
