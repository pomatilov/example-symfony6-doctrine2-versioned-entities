<?php

namespace App\Doctrine\ORM\Filter;

use App\Entity\AbstractVersionEntity;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Filter\SQLFilter;
use Psr\Log\LoggerAwareTrait;

class VersionedEntityValidFilter extends SQLFilter
{
    use LoggerAwareTrait;

    public const FILTER_NAME = 'versioned_entity_valid';

    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias): string
    {
        $targetEntityClassName = $targetEntity->getReflectionClass()->name;

        if (is_subclass_of($targetEntityClassName, AbstractVersionEntity::class)) {
            // $this->logger?->info("[VersionedEntityValidFilter] Add filter for: $targetEntityClassName");

            return sprintf("%s.is_valid = '%s'", $targetTableAlias, AbstractVersionEntity::IS_VALID_TRUE);
        }

        return '';
    }
}
