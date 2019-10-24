<?php

namespace App\Repository;

use App\Entity\Transaction;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Transaction|null find($id, $lockMode = null, $lockVersion = null)
 * @method Transaction|null findOneBy(array $criteria, array $orderBy = null)
 * @method Transaction[]    findAll()
 * @method Transaction[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TransactionRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Transaction::class);
    }

    /**
     * @return Transaction[] Returns an array of Transaction objects
     */

    // public function findtarif($value)
    // {
    //     return $this->createQueryBuilder('t')
    //         ->andWhere('t.borneInferieure >= :val >= t.borneSuperieure')
    //         ->setParameter('val', $value)
    //         ->getQuery()
    //         ->getResult()
    //     ;
    // }
 

 
    public function findTransactionsEnvoi($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.userEnvoi = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findTransactionsRetrait($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.userRetrait = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getResult()
        ;
    }
    
public function RechercheDateE($debut,$fin,$user){
    // $de = new \DateTime($debut->format('Y-m-d')."00-00-00");
    // $a = new \DateTime($fin->format('Y-m-d')."23-59-59");
    $de = $debut;
    $a = $fin;

    $qb = $this->createQueryBuilder('t');

    $qb
    //->andWhere('t.SentAt BETWEEN :de AND :a')
    ->andWhere('t.SentAt >= :de')
    ->andWhere('t.SentAt <= :a')
    ->andWhere('t.userEnvoi =:val')
    ->setParameter('de',$de)
    ->setParameter('a',$a)
    ->setParameter('val',$user);
    
    return $qb
    ->getQuery()
    ->getResult();
}

public function RechercheDateR($debut,$fin,$user){
    $de = new \DateTime($debut->format('Y-m-d')."00-00-00");
    $a = new \DateTime($fin->format('Y-m-d')."23-59-59");

    $qb = $this->createQueryBuilder('t');
    
    $qb
    ->andWhere('t.sentAt BETWEEN :de AND :a')
    ->andWhere('t.userRetrait =:val')
    ->setParameter('de',$de)
    ->setParameter('a',$a)
    ->setParameter('val',$user);
    
    return $qb
    ->getQuery()
    ->getResult();
}
    
}
