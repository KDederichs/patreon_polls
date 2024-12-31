<?php

declare(strict_types=1);

namespace App\State;

use ApiPlatform\Doctrine\Orm\AbstractPaginator;
use ApiPlatform\Doctrine\Orm\Paginator;
use ApiPlatform\Doctrine\Orm\State\CollectionProvider;
use ApiPlatform\Doctrine\Orm\State\ItemProvider;
use ApiPlatform\Metadata\CollectionOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\Pagination\ArrayPaginator;
use ApiPlatform\State\Pagination\TraversablePaginator;
use ApiPlatform\State\ProviderInterface;
use App\Pagination\PartialApiPaginator;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfonycasts\MicroMapper\MicroMapperInterface;

readonly class EntityToDtoStateProvider implements ProviderInterface
{
    public function __construct(
        #[Autowire(service: CollectionProvider::class)] private ProviderInterface $collectionProvider,
        #[Autowire(service: ItemProvider::class)] private ProviderInterface $itemProvider,
        private MicroMapperInterface $microMapper,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $resourceClass = $operation->getClass();
        if ($operation instanceof CollectionOperationInterface) {
            $entities = $this->collectionProvider->provide($operation, $uriVariables, $context);
            $dtos = [];
            foreach ($entities as $entity) {
                $dtos[] = $this->mapEntityToDto($entity, $resourceClass, $context);
            }

            if ($entities instanceof Paginator) {
                return new TraversablePaginator(
                    new \ArrayIterator($dtos),
                    $entities->getCurrentPage(),
                    $entities->getItemsPerPage(),
                    $entities->getTotalItems()
                );
            }
            if ($entities instanceof AbstractPaginator) {
                return new PartialApiPaginator(
                    new \ArrayIterator($dtos),
                    $entities->getCurrentPage(),
                    $entities->getItemsPerPage(),
                );
            }

            return new ArrayPaginator(
                $dtos, 0, \count($dtos)
            );
        }
        $entity = $this->itemProvider->provide($operation, $uriVariables, $context);
        if (!$entity) {
            return null;
        }

        return $this->mapEntityToDto($entity, $resourceClass, $context);
    }

    private function mapEntityToDto(object $entity, string $resourceClass, array $context): object
    {
        return $this->microMapper->map($entity, $resourceClass, $context);
    }
}
