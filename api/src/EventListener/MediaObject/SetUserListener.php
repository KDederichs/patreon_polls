<?php

namespace App\EventListener\MediaObject;

use App\Entity\MediaObject;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Events;
use Symfony\Bundle\SecurityBundle\Security;

#[AsEntityListener(event: Events::prePersist, method: 'onPrePersist', entity: MediaObject::class)]
final readonly class SetUserListener
{
    public function __construct(
        private Security $security
    )
    {

    }

    public function onPrePersist(MediaObject $entity, PrePersistEventArgs $event): void
    {
        $user = $this->security->getUser();
        if (!$user) {
            return;
        }
        assert($user instanceof User);
        assert($entity instanceof MediaObject);

        $entity->setUploadedBy($user);
    }
}
