<?php

namespace App\Repository;

use App\Entity\DailyStat;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use function Doctrine\ORM\QueryBuilder;

/**
 * @method DailyStat|null find($id, $lockMode = null, $lockVersion = null)
 * @method DailyStat|null findOneBy(array $criteria, array $orderBy = null)
 * @method DailyStat[]    findAll()
 * @method DailyStat[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DailyStatRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DailyStat::class);
    }

    /**
     * @return DailyStat|mixed
     */
    public function getLastRecord()
    {
        $qb = $this->createQueryBuilder('ds');

        $qb->select('ds')
            ->orderBy('ds.day', 'DESC')
            ->setMaxResults(1);

        return $qb->getQuery()->getSingleResult();
    }

    /**
     * @return DailyStat|mixed
     */
    public function getNoChangeDays()
    {
        $qb = $this->createQueryBuilder('ds');

        $qb->where(
            $qb->expr()->isNull('ds.dailyChange')
        )
            ->orderBy('ds.day', 'ASC');

        return $qb->getQuery()->getResult();
    }


    /**
     * @param DailyStat $dailyStat
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @return DailyStat|null
     */
    public function getPrevDayStat(DailyStat $dailyStat)
    {
        $qb = $this->createQueryBuilder('ds');

        $qb
            ->where(
                $qb->expr()->lt('ds.day', ':day')
            )
            ->orderBy('ds.day', 'DESC')
            ->setParameters([
                'day' => $dailyStat->getDay(),
            ])
            ->setMaxResults(1);

        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * @return DailyStat[]|null
     */
    public function getOrdered()
    {
        $qb = $this->createQueryBuilder('ds');

        $qb->select('ds')->orderBy('ds.day', 'ASC');

        return $qb->getQuery()->getResult();
    }
}
