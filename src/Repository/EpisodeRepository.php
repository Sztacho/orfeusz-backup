<?php

namespace App\Repository;

use App\Entity\Episode;
use App\Entity\User;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Episode>
 *
 * @method Episode|null find($id, $lockMode = null, $lockVersion = null)
 * @method Episode|null findOneBy(array $criteria, array $orderBy = null)
 * @method Episode[]    findAll()
 * @method Episode[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EpisodeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Episode::class);
    }

    public function save(Episode $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Episode $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function countTranslatedEpisodesInCurrentMonthForUser(User $user): int
    {
        $currentMonth = (new DateTimeImmutable())->format('Y-m');
        return $this->createQueryBuilder('e')
            ->select('COUNT(e)')
            ->join('e.translateBy', 'u')
            ->where('u = :user')
            ->andWhere('e.createdAt >= :startOfMonth')
            ->andWhere('e.createdAt < :startOfNextMonth')
            ->setParameter('user', $user)
            ->setParameter('startOfMonth', $currentMonth . '-01')
            ->setParameter('startOfNextMonth', (new DateTimeImmutable($currentMonth . '-01'))->modify('first day of next month'))
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function countTranslatedEpisodesInCurrentMonth(): int
    {
        $currentMonth = (new DateTimeImmutable())->format('Y-m');
        return $this->createQueryBuilder('e')
            ->select('COUNT(e)')
            ->andWhere('e.createdAt >= :startOfMonth')
            ->andWhere('e.createdAt < :startOfNextMonth')
            ->setParameter('startOfMonth', $currentMonth . '-01')
            ->setParameter('startOfNextMonth', (new DateTimeImmutable($currentMonth . '-01'))->modify('first day of next month'))
            ->getQuery()
            ->getSingleScalarResult();
    }
}
