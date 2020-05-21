<?php

namespace App\Repository;

use App\Entity\Country;
use App\Entity\CountryCase;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use function Doctrine\ORM\QueryBuilder;

/**
 * @method CountryCase|null find($id, $lockMode = null, $lockVersion = null)
 * @method CountryCase|null findOneBy(array $criteria, array $orderBy = null)
 * @method CountryCase[]    findAll()
 * @method CountryCase[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CountryCaseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CountryCase::class);
    }

    /**
     * @param Country $country
     * @return CountryCase[]
     */
    public function getAll(Country $country)
    {
        $qb = $this->createQueryBuilder('cc');

        $qb->select('cc')
            ->where('cc.country = :country')
            ->orderBy('cc.caseDate', 'asc')
            ->setParameter('country', $country)
        ;

        return $qb->getQuery()->getResult();

    }

    public function getOldestByCountry(Country $country): ?CountryCase
    {
        $qb = $this->createQueryBuilder('cc');

        $qb
            ->select('cc')
            ->where('cc.country = :country')
            ->orderBy('cc.caseDate', 'ASC')
            ->setMaxResults(1)
            ->setParameters([
                'country' => $country,
            ])
        ;

        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * @param Country $country
     * @return CountryCase|mixed
     */
    public function getNoRecoveredRecords(Country $country)
    {
        $qb = $this->createQueryBuilder('cc');

        $qb->where('cc.country = :country')
            ->andWhere(
                $qb->expr()->isNull('cc.newRecovered')
            )
            ->orderBy('cc.caseDate', 'ASC')
            ->setParameters([
                'country' => $country,
            ])
        ;

        return $qb->getQuery()->getResult();
    }

    /**
     * @param Country $country
     * @return CountryCase|mixed
     */
    public function getNoChangeRecords(Country $country)
    {
        $qb = $this->createQueryBuilder('cc');

        $qb->where('cc.country = :country')
            ->andWhere(
                $qb->expr()->isNull('cc.casesChange')
            )
            ->orderBy('cc.caseDate', 'ASC')
            ->setParameters([
                'country' => $country,
            ])
        ;

        return $qb->getQuery()->getResult();
    }

    /**
     * @param CountryCase $cases
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @return CountryCase|null
     */
    public function getCasesPrevDay(CountryCase $cases)
    {
        $qb = $this->createQueryBuilder('cc');

        // set case day to midnight
        $caseDay = clone $cases->getCaseDate();
        $caseDay->setTime(0, 0, 0);

        $qb
            ->where('cc.country = :country')
            ->andWhere(
                $qb->expr()->lt('cc.caseDate', ':date')
            )
            ->orderBy('cc.caseDate', 'DESC')
            ->setParameters([
                'country' => $cases->getCountry(),
                'date' => $caseDay,
            ])
            ->setMaxResults(1)
        ;

        return $qb->getQuery()->getOneOrNullResult();
    }


    /**
     * Find country in database or return new instance
     *
     * @param \DateTimeInterface $date
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @return CountryCase|null
     */
    public function getDayTotals(\DateTimeInterface $date)
    {
        $qb = $this->createQueryBuilder('cc');

        $qb->select(
            'SUM(cc.total) AS total',
            'SUM(cc.deaths) AS deaths',
            'SUM(cc.recovered) AS recovered',
            'SUM(cc.newDeaths) AS newDeaths',
            'SUM(cc.newCases) AS newCases',
            'SUM(cc.newRecovered) AS newRecovered',
            'SUM(cc.serious) AS serious',
            'SUM(cc.active) AS active'
        )
            ->where('cc.caseDate = :date')
            ->setParameters([
                'date' => $date,
            ])
            ->setMaxResults(1)
        ;

        return $qb->getQuery()->getOneOrNullResult();
    }


    /**
     * Find country in database or return new instance
     *
     * @param \DateTimeInterface $date
     * @return CountryCase[]
     */
    public function getAllCounries(\DateTimeInterface $date)
    {
        $qb = $this->createQueryBuilder('cc');

        $qb->select('cc')
            ->where('cc.caseDate = :date')
            ->setParameters([
                'date' => $date,
            ])
            ->orderBy('cc.active', 'desc')
        ;

        return $qb->getQuery()->getResult();
    }

    public function getForCountryByDate($statDate, $country)
    {
        return $this->findOneBy(['caseDate' => $statDate, 'country' => $country]);
    }

    /**
     * @param Country $country
     * @return CountryCase
     */
    public function getLastByCountryWithChange(Country $country)
    {
        $qb = $this->createQueryBuilder('cc');

        $qb->select('cc')
            ->where('cc.country = :country')
            ->andWhere(
                $qb->expr()->isNotNull('cc.casesChange')
            )
            ->setParameters([
                'country' => $country,
            ])
            ->orderBy('cc.caseDate', 'desc')
            ->setMaxResults(1)
        ;

        return $qb->getQuery()->getOneOrNullResult();
    }


    /**
     * @return CountryCase[]|null
     */
    public function getCountryOrdered(Country $country)
    {
        $qb = $this->createQueryBuilder('cc');

        $qb->select('cc')->orderBy('cc.caseDate', 'ASC')
            ->where('cc.country = :country')
            ->setParameter('country', $country)
        ;

        return $qb->getQuery()->getResult();
    }


    /**
     * @param int $limit
     * @return CountryCase[]
     */
    public function getTopActive(int $limit = 5)
    {
        $qb = $this->createQueryBuilder('cc');

        $qb->select('cc')
            ->orderBy('cc.caseDate', 'desc')
            ->addOrderBy('cc.active', 'desc')
            ->setMaxResults($limit)
        ;;

        return $qb->getQuery()->getResult();
    }

    /**
     * @param int $limit
     * @return CountryCase[]
     */
    public function getTopRecovered(int $limit = 5)
    {
        $qb = $this->createQueryBuilder('cc');

        $qb->select('cc')
            ->orderBy('cc.caseDate', 'desc')
            ->addOrderBy('cc.recovered', 'desc')
            ->setMaxResults($limit)
        ;;

        return $qb->getQuery()->getResult();
    }

    /**
     * @param int $limit
     * @return CountryCase[]
     */
    public function getTopNewRecovered(int $limit = 5)
    {
        $qb = $this->createQueryBuilder('cc');

        $qb->select('cc')
            ->orderBy('cc.caseDate', 'desc')
            ->addOrderBy('cc.newRecovered', 'desc')
            ->setMaxResults($limit)
        ;;

        return $qb->getQuery()->getResult();
    }

    public function getTopCountries()
    {
        $topCountries = [
            'active' => $this->getTopActive(),
            'recovered' => $this->getTopRecovered(),
            'new_recovered' => $this->getTopNewRecovered(),
        ];

        return $topCountries;
    }
}
