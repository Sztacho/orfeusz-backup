<?php

namespace App\Repository;

use App\Entity\Anime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\ArrayParameterType;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Anime>
 *
 * @method Anime|null find($id, $lockMode = null, $lockVersion = null)
 * @method Anime|null findOneBy(array $criteria, array $orderBy = null)
 * @method Anime[]    findAll()
 * @method Anime[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AnimeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Anime::class);
    }

    public function save(Anime $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Anime $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function countAnimeByFilters(array $filters): int
    {
        return count($this->findAnimeByFilters($filters));
    }

    public function findAnimeByFilters(array $filters, int $limit = null, int $offset = null): array
    {
        $qb = $this->createQueryBuilder('a');

        $qb = $this->setFilters($qb, $filters);

        if ($limit && $offset) {
            $qb->orderBy('a.' . ($filters['orderBy'] ?? 'releaseDate'), $filters['order'] ?? 'DESC')
                ->setMaxResults($limit)
                ->setFirstResult($offset);
        }

        return $this->filterByRating($qb->getQuery()->getResult(), $filters['rating'] ?? null);
    }

    private function setFilters(QueryBuilder $qb, array $filters): QueryBuilder
    {
        if (isset($filters['name'])) {
            $qb->andWhere('a.name LIKE :name')
                ->setParameter('name', '%' . $filters['name'] . '%');
        }

        if (isset($filters['tags'])) {
            $qb->innerJoin('a.tags', 't')
                ->andWhere('t.name IN (:tags)')
                ->setParameter('tags', $filters['tags'], ArrayParameterType::STRING);
        }

        if (isset($filters['season'])) {
            $qb->andWhere('a.season = :season')
                ->setParameter('season', $filters['season']);
        }

        if(isset($filters['seasonName'])) {
            $qb->innerJoin('a.season','s')
                ->andWhere('s.season = :season')
                ->setParameter('season', $filters['seasonName']);
            ;
        }

        if(isset($filters['seasonYear'])) {
            $qb->innerJoin('a.season','ss')
                ->andWhere('ss.year = :year')
                ->setParameter('year', $filters['seasonYear']);
            ;
        }

        if (isset($filters['dateFrom'])) {
            $qb->andWhere('DATE_FORMAT(a.releaseDate, "%Y-%m-%d") >= :dateFrom')
                ->setParameter('dateFrom', $filters['dateFrom']);
        }

        if (isset($filters['dateTo'])) {
            $qb->andWhere('DATE_FORMAT(a.releaseDate, "%Y-%m-%d") <= :dateTo')
                ->setParameter('dateTo', $filters['dateTo']);
        }

        if (isset($filters['ageRatingSystem'])) {
            $qb->andWhere('a.ageRatingSystem = :ageRatingSystem')
                ->setParameter('ageRatingSystem', $filters['ageRatingSystem']);
        }

        if (isset($filters['studios'])) {
            $qb->innerJoin('a.studios', 's')
                ->andWhere('s.name IN (:studios)')
                ->setParameter('studios', $filters['studios'], ArrayParameterType::STRING);
        }

        return $qb;
    }

    /** @param Anime[] $animeList */
    private function filterByRating(array $animeList, ?float $rating): array
    {
        if (!$rating) {
            return $animeList;
        }

        return array_filter($animeList, function (Anime $anime) use ($rating) {
            return $anime->getAverageRating() >= $rating;
        });
    }
}
