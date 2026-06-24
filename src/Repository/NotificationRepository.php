<?php
namespace App\Repository;

use App\Entity\Notification;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Notification>
 */
class NotificationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Notification::class);
    }

    public function findUnreadByUser(int $userId): array
    {
        return $this->createQueryBuilder('n')
            ->where('n.client = :userId')
            ->andWhere('n.lu = false')
            ->setParameter('userId', $userId)
            ->orderBy('n.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function countUnreadByUser(int $userId): int
    {
        return $this->createQueryBuilder('n')
            ->select('COUNT(n.id)')
            ->where('n.client = :userId')
            ->andWhere('n.lu = false')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function markAllAsRead(int $userId): void
    {
        $this->createQueryBuilder('n')
            ->update()
            ->set('n.lu', ':lu')
            ->where('n.client = :userId')
            ->setParameter('lu', true)
            ->setParameter('userId', $userId)
            ->getQuery()
            ->execute();
    }
}
