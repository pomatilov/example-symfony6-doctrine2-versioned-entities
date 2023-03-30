<?php

namespace App\Doctrine\ORM\Filter;

use App\Entity\AbstractVersionEntity;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Filter\SQLFilter;
use Psr\Log\LoggerAwareTrait;

class VersionedEntityVersionFilter extends SQLFilter
{
    use LoggerAwareTrait;

    public const FILTER_NAME = 'versioned_entity_version';

    public const VERSION_DATE_PARAMETER = 'version_date';
    public const VERSION_DATE_FORMAT_PARAMETER = 'version_date_format';

    public const DATE_FORMAT_PGSQL = 'YYYY-MM-DD HH24:MI:SS';
    public const DATE_FORMAT_PHP = 'Y-m-d H:i:s';

    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias): string
    {
        $targetEntityClassName = $targetEntity->getReflectionClass()->name;
        $versionDate = $this->getParameter(self::VERSION_DATE_PARAMETER);

        if (is_subclass_of($targetEntityClassName, AbstractVersionEntity::class) && !empty($versionDate)) {
            // $this->logger?->info("[VersionedEntityVersionFilter] Add filter for: $targetEntityClassName");

            $versionDateFormat = $this->getParameter(self::VERSION_DATE_FORMAT_PARAMETER);

            return sprintf("TO_DATE(%s, %s) BETWEEN {$targetTableAlias}.vfrom and {$targetTableAlias}.vto", $versionDate, $versionDateFormat);
        }

        return '';
    }
}
