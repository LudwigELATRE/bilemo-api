<?php

namespace App\Repository;

use App\Entity\Enterprise;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Enterprise>
 */
class EnterpriseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Enterprise::class);
    }


    public function findAllByUuidEnterprise(string $enterpriseUuid): array
    {
        $products = $this->createQueryBuilder('p')
            ->join('p.enterprise', 'e')
            ->where('e.uuid = :enterpriseUuid')
            ->setParameter('enterpriseUuid', $enterpriseUuid)
            ->getQuery()
            ->getResult();
        dd($products);
    }

    //    /**
    //     * @return Enterprise[] Returns an array of Enterprise objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('e.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Enterprise
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
