<?php

declare(strict_types=1);

namespace App\State;

use ApiPlatform\Doctrine\Common\State\PersistProcessor;
use ApiPlatform\Doctrine\Common\State\RemoveProcessor;
use ApiPlatform\Doctrine\Orm\State\Options;
use ApiPlatform\Metadata\DeleteOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\State\ProcessorInterface;
use App\Event\StateProcessor\StatePostDeleteEvent;
use App\Event\StateProcessor\StatePostPersistEvent;
use App\Event\StateProcessor\StatePostUpdateEvent;
use App\Event\StateProcessor\StatePrePersistEvent;
use App\Event\StateProcessor\StatePreUpdateEvent;
use App\Mapper\AbstractApiToObjectMapper;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfonycasts\MicroMapper\MicroMapperInterface;

readonly class EntityClassDtoStateProcessor implements ProcessorInterface
{
    public function __construct(
        #[Autowire(service: PersistProcessor::class)] private ProcessorInterface $persistProcessor,
        #[Autowire(service: RemoveProcessor::class)] private ProcessorInterface $removeProcessor,
        private MicroMapperInterface $microMapper,
        private EventDispatcherInterface $dispatcher,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        $stateOptions = $operation->getStateOptions();
        \assert($stateOptions instanceof Options);
        $entityClass = $stateOptions->getEntityClass();
        $originalEntity = null;
        $isUpdate = ($operation instanceof Patch) || ($operation instanceof Put);
        $isPersist = $operation instanceof Post;
        if ($isUpdate) {
            $originalEntity = clone $this->mapDtoToEntity($data, $entityClass, ['mode' => AbstractApiToObjectMapper::POPULATION_MODE_PASSTHROUGH]);
        }
        $entity = $this->mapDtoToEntity($data, $entityClass);
        if ($operation instanceof DeleteOperationInterface) {
            $this->removeProcessor->process($entity, $operation, $uriVariables, $context);
            $this->dispatcher->dispatch(new StatePostDeleteEvent($entityClass));

            return null;
        }
        if ($isPersist) {
            $this->dispatcher->dispatch(new StatePrePersistEvent($entityClass, $entity, $data));
        }
        if ($isUpdate) {
            $this->dispatcher->dispatch(new StatePreUpdateEvent($entityClass, $entity, $originalEntity, $data));
        }
        $this->persistProcessor->process($entity, $operation, $uriVariables, $context);
        if ($isPersist) {
            $this->dispatcher->dispatch(new StatePostPersistEvent($entityClass, $entity));
        }
        if ($isUpdate) {
            $this->dispatcher->dispatch(new StatePostUpdateEvent($entityClass, $entity, $data));
        }

        return $this->microMapper->map($entity, $operation->getClass(), ['entity' => $entity]);
    }

    private function mapDtoToEntity(object $dto, string $entityClass, array $context = []): object
    {
        return $this->microMapper->map($dto, $entityClass, $context);
    }
}
