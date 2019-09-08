<?php

namespace App\Repository;

use App\Entity\Utilisateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Utilisateur|null find($id, $lockMode = null, $lockVersion = null)
 * @method Utilisateur|null findOneBy(array $criteria, array $orderBy = null)
 * @method Utilisateur[]    findAll()
 * @method Utilisateur[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UtilisateurRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Utilisateur::class);
    }

    /**
     * @return Utilisateur[] Returns an array of Utilisateur objects
     */
    
    public function findByRoles()
    {
        
        return $this->createQueryBuilder('u')
            ->Where('u.partenaire Is Null')
            ->andWhere('u.id !=1')
            
            ->getQuery()
            ->getResult()
        ;
        
    }
    /**
     * @return Utilisateur[] Returns an array of Utilisateur objects
     */
    
    public function findPartenaire()
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.roles LIKE :roles')
            ->setParameter('roles', '%"'."ROLE_PARTENAIRE".'"%')
            
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return Utilisateur[] Returns an array of Utilisateur objects
     */
    
    public function findUsers(Utilisateur $utilisateur)
    {
        return $this->createQueryBuilder('u')
         
            ->andWhere('u != :val')
            ->andWhere('u.partenaire = :val2')
            ->setParameter('val', $utilisateur)
            ->setParameter('val2', $utilisateur->getPartenaire())
            ->getQuery()
            ->getResult()
        ;
    }
    
    

    /*
    public function findOneBySomeField($value): ?Utilisateur
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
