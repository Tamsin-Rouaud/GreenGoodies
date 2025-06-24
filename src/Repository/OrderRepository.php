<?php

namespace App\Repository;

use App\Entity\Order;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository dédié à l'entité Order
 * Permet d'effectuer des requêtes personnalisées sur les commandes
 */
class OrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

  
    /**
     * Alias plus explicite de la méthode précédente
     * Utilisée dans AccountController pour afficher les commandes dans "Mon compte"
     */
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
