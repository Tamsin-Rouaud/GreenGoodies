<?php

namespace App\Repository;

use App\Entity\Order;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Order>
 */
class OrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }
// src/Repository/OrderRepository.php
public function findByUser(User $user): array
{
    return $this->createQueryBuilder('o')
        ->andWhere('o.user = :user')
        ->setParameter('user', $user)
        ->orderBy('o.created_at', 'DESC')
        ->getQuery()
        ->getResult();
}

public function findValidatedByUser(User $user): array
{
    return $this->createQueryBuilder('o')
        ->andWhere('o.user = :user')
        ->andWhere('o.isValidated = true')
        ->setParameter('user', $user)
        ->orderBy('o.created_at', 'DESC')
        ->getQuery()
        ->getResult();
}

public function findValidatedOrdersByUser(User $user): array
{
    return $this->createQueryBuilder('o')
        ->andWhere('o.user = :user')
        ->andWhere('o.isValidated = true')
        ->setParameter('user', $user)
        ->orderBy('o.created_at', 'DESC')
        ->getQuery()
        ->getResult();
}


}
