<?php

namespace App\Repository;

use App\Entity\CountryCasesChange;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method CountryCasesChange|null find($id, $lockMode = null, $lockVersion = null)
 * @method CountryCasesChange|null findOneBy(array $criteria, array $orderBy = null)
 * @method CountryCasesChange[]    findAll()
 * @method CountryCasesChange[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CasesChangeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CountryCasesChange::class);
    }
}
