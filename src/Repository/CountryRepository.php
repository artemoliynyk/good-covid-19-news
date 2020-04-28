<?php

namespace App\Repository;

use App\Entity\Country;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Country|null find($id, $lockMode = null, $lockVersion = null)
 * @method Country|null findOneBy(array $criteria, array $orderBy = null)
 * @method Country[]    findAll()
 * @method Country[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CountryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Country::class);
    }

    public function getAllOrdered()
    {
        return $this->createQueryBuilder('c')->orderBy('c.name', 'ASC')->getQuery()->getResult();
    }


    public function getLastUpdateAt(): ?\DateTime
    {
        $qb = $this->createQueryBuilder('c');

        $qb->select('c')->orderBy('c.updatedAt', 'desc')->setMaxResults(1);

        /** @var Country $country */
        try {
            $country = $qb->getQuery()->getSingleResult();

            return $country->getUpdatedAt();
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Find country in database or return new instance
     *
     * @param string $name
     * @return Country|null
     */
    public function findOrCreate(string $name)
    {
        $country = $this->findOneBy(['name' => $name]);

        if (!$country instanceof Country) {
            $country = new Country($name);
        }

        return $country;
    }
}
