<?php

namespace App\Doctrine\ORM\EventListener;

use App\Doctrine\ORM\Filter\VersionedEntityVersionFilter;
use App\Entity\AbstractVersionEntity;
use App\Services\Versioning\TransactionalService;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\Persistence\ObjectRepository;

class VersionedEntityListener
{
    private bool $isPostPersistUpdate = false;

    public function __construct(private TransactionalService $transactionalService)
    {
    }

    /**
     * Set CAX (create) transaction identifier and default "version to" before object persisted
     *
     * @param LifecycleEventArgs $args
     *
     * @return void
     */
    public function prePersist(LifecycleEventArgs $args): void
    {
        if ($this->isEntitySupported($args->getObject()) === false) {
            return;
        }

        /** @var AbstractVersionEntity */
        $entity = $args->getObject();

        $entity
            ->setCax($this->transactionalService->getTransactionId())
            ->setVersionTo($entity->getVersionTo() ?? AbstractVersionEntity::getMaxPossibleDate())
        ;
    }

    /**
     * Set object idd after object persisted, if not installed
     *
     * @param LifecycleEventArgs $args
     *
     * @return void
     */
    public function postPersist(LifecycleEventArgs $args): void
    {
        if ($this->isEntitySupported($args->getObject()) === false) {
            return;
        }

        /** @var AbstractVersionEntity */
        $entity = $args->getObject();

        if ($entity->getIdd() === null) {
            $entity->setIdd($entity->getId());

            $this->isPostPersistUpdate = true;

            $args->getObjectManager()->persist($entity);
            $args->getObjectManager()->flush();
        }
    }

    /**
     * Set EAX (invalidate) transaction identifier if object wasn`t invalidated
     * Set IAX (invalidate) transaction identifier if object was invalidated
     *
     * @param PreUpdateEventArgs $args
     *
     * @return void
     */
    public function preUpdate(PreUpdateEventArgs $args): void
    {
        if ($this->isEntitySupported($args->getObject()) === false) {
            return;
        }

        if ($this->isPostPersistUpdate && $args->hasChangedField('idd')) {
            $this->isPostPersistUpdate = false;
            return;
        }

        /** @var AbstractVersionEntity */
        $entity = $args->getObject();

        if ($args->hasChangedField('isValid') === false) {
            $entity->setEax($this->transactionalService->getTransactionId());
        } elseif ($args->getNewValue('isValid') === AbstractVersionEntity::IS_VALID_FALSE) {
            $entity->setIax($this->transactionalService->getTransactionId());
        }
    }

    private function isEntitySupported($entity): bool
    {
        return $entity instanceof AbstractVersionEntity;
    }
}
