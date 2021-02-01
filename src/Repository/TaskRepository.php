<?php

namespace App\Repository;

use App\Entity\Task;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Task|null find($id, $lockMode = null, $lockVersion = null)
 * @method Task|null findOneBy(array $criteria, array $orderBy = null)
 * @method Task[]    findAll()
 * @method Task[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaskRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
    }

    /**
     * @param int $page
     * @return Paginator|Task[]
     */
    public function pagination(int $page = 1): Paginator {
        $dql = $this->createQueryBuilder('task');

        $dql->orderBy('task.createdAt', 'DESC');

        $query = $dql->getQuery();
        $query->setMaxResults(10);
        $query->setFirstResult(($page - 1) * 10);

        return new Paginator($query);
    }

    /**
     * @param int $page
     * @return Paginator
     */
    public function getOpenedTasks(int $page = 1): Paginator {
        $dql = $this->createQueryBuilder('task');

        $dql->where($dql->expr()->eq('task.closed', ':propertyCheck'))
            ->setParameter(':propertyCheck', false)
        ;

        $query = $dql->getQuery();
        $query->setMaxResults(10);
        $query->setFirstResult(($page - 1) * 10);

        return new Paginator($query);
    }

    /**
     * @param int $page
     * @return Paginator
     */
    public function getUnassignedTasks(int $page = 1): Paginator {
        $dql = $this->createQueryBuilder('task');

        $dql->where('task.user IS NULL')
            ->orderBy('task.createdAt', 'DESC');

        $query = $dql->getQuery();
        $query->setMaxResults(10);
        $query->setFirstResult(($page - 1) * 10);

        return new Paginator($query);
    }

    /**
     * @param int $page
     * @return Paginator
     */
    public function getOwnedOpenedTasks(int $page = 1): Paginator {
        $dql = $this->createQueryBuilder('task');

        $dql->where('task.user IS NOT NULL')
            ->andWhere($dql->expr()->eq('task.closed', ':propertyCheck'))
            ->setParameter(':propertyCheck', false)
            ->orderBy('task.createdAt', 'DESC');

        $query = $dql->getQuery();
        $query->setMaxResults(10);
        $query->setFirstResult(($page - 1) * 10);

        return new Paginator($query);
    }
}