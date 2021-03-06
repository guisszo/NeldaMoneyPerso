<?php

namespace App\Repository;

use App\Entity\Tarifs;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Tarifs|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tarifs|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tarifs[]    findAll()
 * @method Tarifs[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TarifsRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Tarifs::class);
    }

    /**
     * @return Tarifs[] Returns an array of Tarifs objects
     */

    public function findtarif($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.borneInferieure <= :val ')
            ->andWhere(' t.borneSuperieure >= :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getResult()
        ;
    }
    

}
