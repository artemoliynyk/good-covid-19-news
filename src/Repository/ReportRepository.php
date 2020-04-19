<?php

namespace App\Repository;

use App\Entity\Report;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Report|null find($id, $lockMode = null, $lockVersion = null)
 * @method Report|null findOneBy(array $criteria, array $orderBy = null)
 * @method Report[]    findAll()
 * @method Report[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReportRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Report::class);
    }

    public function getDowloadQueue(int $limit = 10)
    {
        if ($limit < 1) {
            $limit = 10;
        }

        $qb = $this->createQueryBuilder('r');

        $qb->select('r')
            ->where('r.downloaded = :false')
            ->setMaxResults($limit)
            ->setParameters([
                'false' => false,
            ]);

        return $qb->getQuery()->getResult();
    }

    public function getProcessQueue(int $limit = 10)
    {
        if ($limit < 1) {
            $limit = 10;
        }

        $qb = $this->createQueryBuilder('r');

        $qb->select('r')
            ->where('r.downloaded = :true')
            ->andWhere('r.processed = :false')
            ->setParameters([
                'true' => true,
                'false' => false,
            ])
            ->setMaxResults($limit);

        return $qb->getQuery()->getResult();
    }
}
