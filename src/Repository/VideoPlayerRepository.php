<?php

namespace App\Repository;

use App\Entity\VideoPlayer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<VideoPlayer>
 *
 * @method VideoPlayer|null find($id, $lockMode = null, $lockVersion = null)
 * @method VideoPlayer|null findOneBy(array $criteria, array $orderBy = null)
 * @method VideoPlayer[]    findAll()
 * @method VideoPlayer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VideoPlayerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VideoPlayer::class);
    }

    public function save(VideoPlayer $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(VideoPlayer $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getPlayersForEpisode(int $episodeId): array
    {
        return $this->createQueryBuilder('vp')
            ->where('vp.episode = :episode')
            ->setParameter('episode', $episodeId)
            ->innerJoin('vp.episode','e','WITH', 'e.premiereDate <= NOW()')
            ->getQuery()->getResult()
        ;
    }
}
