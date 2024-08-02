<?php

namespace App\EventListener;

use App\Entity\Enterprise;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Ramsey\Uuid\Uuid;

class EnterpriseListener
{
    public function prePersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof Enterprise) {
            return;
        }

        $formattedName = str_replace(' ', '-', strtolower($entity->getName()));
        $entity->setName($formattedName);
        $entity->setUuid(Uuid::uuid4()->toString());
    }

    public function preUpdate(LifecycleEventArgs $args): void
    {
        $this->prePersist($args);
    }
}