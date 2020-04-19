<?php

namespace App\Repository;

use App\Entity\DailyChange;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method DailyChange|null find($id, $lockMode = null, $lockVersion = null)
 * @method DailyChange|null findOneBy(array $criteria, array $orderBy = null)
 * @method DailyChange[]    findAll()
 * @method DailyChange[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DailyChangeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DailyChange::class);
    }
}
