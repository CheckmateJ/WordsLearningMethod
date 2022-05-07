<?php

namespace App\Repository;

use App\Entity\LearningPlan;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method LearningPlan|null find($id, $lockMode = null, $lockVersion = null)
 * @method LearningPlan|null findOneBy(array $criteria, array $orderBy = null)
 * @method LearningPlan[]    findAll()
 * @method LearningPlan[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LearningPlanRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LearningPlan::class);
    }

    // /**
    //  * @return LearningPlan[] Returns an array of LearningPlan objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?LearningPlan
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
