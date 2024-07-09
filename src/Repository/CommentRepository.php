<?php

namespace App\Repository;

use App\Entity\Comment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Comment>
 *
 * @method Comment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comment[]    findAll()
 * @method Comment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }

    public function save(Comment $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Comment $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function countCommentsByFilters(array $filters): float
    {
        return count($this->findCommentsByFilters($filters));
    }

    public function findCommentsByFilters(array $filters, $limit = null, $offset = null): array
    {
        if (!($filters['id'] ?? false)) {
            return [];
        }

        $qb = $this->createQueryBuilder('c')
            ->join('c.anime', 'a')
            ->where('a.id = :animeId')
            ->andWhere('c.isActive = 1')
            ->andWhere('c.comment is null')
            ->setParameter('animeId', $filters['id']);

        if ($limit && $offset) {
            $qb->orderBy('a.' . ($filters['orderBy'] ?? 'releaseDate'), $filters['order'] ?? 'DESC')
                ->setMaxResults($limit)
                ->setFirstResult($offset);
        }

        $this->setFilters($qb, $filters);

        return $qb->getQuery()->getResult();
    }

    private function setFilters(QueryBuilder $qb, array $filters): void
    {
        if (isset($filters['dateFrom'])) {
            $qb->andWhere('DATE_FORMAT(c.createdAt, "%Y-%m-%d") >= :dateFrom')
                ->setParameter('dateFrom', $filters['dateFrom']);
        }

        if (isset($filters['dateTo'])) {
            $qb->andWhere('DATE_FORMAT(c.createdAt, "%Y-%m-%d") <= :dateTo')
                ->setParameter('dateTo', $filters['dateTo']);
        }
    }
}
