<?php

declare(strict_types=1);

namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

abstract class AbstractBaseRepository extends ServiceEntityRepository
{
    private ManagerRegistry $registry;

    public function __construct(ManagerRegistry $registry, string $entityClass)
    {
        parent::__construct($registry, $entityClass);
        $this->registry = $registry;
    }

    public function getEntityManager(): EntityManagerInterface
    {
        if (!parent::getEntityManager()->isOpen()) {
            $this->registry->resetManager();
        }

        return parent::getEntityManager();
    }

    public function save(): void
    {
        $this->getEntityManager()->flush();
    }

    public function persist(mixed $object): void
    {
        $this->getEntityManager()->persist($object);
    }

    public function remove(mixed $object): void
    {
        $this->getEntityManager()->remove($object);
    }

    public function refresh(mixed $object): void
    {
        $this->getEntityManager()->refresh($object);
    }

    public function clear(): void
    {
        $this->getEntityManager()->clear();
    }
}
