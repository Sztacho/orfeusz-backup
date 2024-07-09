<?php

namespace App\Repository;

use App\Entity\Season;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Season>
 *
 * @method Season|null find($id, $lockMode = null, $lockVersion = null)
 * @method Season|null findOneBy(array $criteria, array $orderBy = null)
 * @method Season[]    findAll()
 * @method Season[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SeasonRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Season::class);
    }

    public function save(Season $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Season $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getLast4ActiveSeasons(): array
    {
        $active = $this->findOneBy(['active' => true]);

        $elements = $this->createQueryBuilder('s')
            ->orderBy('s.year', 'DESC')
            ->addOrderBy('s.sequence', 'DESC')
            ->where('s.year <= :year')
            ->setParameter('year', $active->getYear())
            ->getQuery()
            ->getResult();

        foreach ($elements as $key => $element) {
            if ($element->getId() == $active->getId()) {
                break;
            }
            unset($elements[$key]);
        }

        return array_slice($elements, 0, 4);
    }
}
