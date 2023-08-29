<?php

namespace App\Repository;

use App\Entity\TableTeamTournament;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TableTeamTournament>
 *
 * @method TableTeamTournament|null find($id, $lockMode = null, $lockVersion = null)
 * @method TableTeamTournament|null findOneBy(array $criteria, array $orderBy = null)
 * @method TableTeamTournament[]    findAll()
 * @method TableTeamTournament[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TableTeamTournamentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TableTeamTournament::class);
    }

//    /**
//     * @return TableTeamTournament[] Returns an array of TableTeamTournament objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('t.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?TableTeamTournament
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
