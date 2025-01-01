<?php

namespace App\Doctrine;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use App\Entity\Poll;
use App\Entity\User;
use App\Security\UserOwnedInterface;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Finder\Exception\AccessDeniedException;

readonly class PollExtension implements QueryCollectionExtensionInterface
{
    public function __construct(
        private Security $security
    )
    {

    }

    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, ?Operation $operation = null, array $context = []): void
    {
        $this->addWhere($queryBuilder, $resourceClass);
    }

    public function addWhere(QueryBuilder $queryBuilder, string $resourceClass): void
    {
        if (!is_a($resourceClass, Poll::class, true)) {
            return;
        }

        $user = $this->security->getUser();

        if (!$user instanceof User) {
            //Let firewall handle authentication
            return;
        }

        $rootAlias = $queryBuilder->getRootAliases()[0];
        $queryBuilder
            ->andWhere(\sprintf('%s.createdBy = :user', $rootAlias))
            ->setParameter('user', $user)
        ;
    }
}
