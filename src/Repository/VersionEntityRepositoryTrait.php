<?php

namespace App\Repository;

use App\Doctrine\ORM\Filter\VersionedEntityVersionFilter as VersionFilter;
use App\Entity\AbstractVersionEntity;

trait VersionEntityRepositoryTrait
{
    /**
     * @return array<AbstractVersionEntity>
     */
    public function findAll(): array
    {
        $this->getEntityManager()->getFilters()
            ->disable(VersionFilter::FILTER_NAME)
        ;

        return parent::findAll();
    }

    /**
     * @param int $entityIdd
     *
     * @return array<AbstractVersionEntity>
     */
    public function findAllVersions(int $entityIdd): array
    {
        $this->getEntityManager()->getFilters()
            ->disable(VersionFilter::FILTER_NAME)
        ;

        return $this->createQueryBuilder('ent')
            ->andWhere('ent.idd = :entityIdd')
            ->setParameter('entityIdd', $entityIdd)
            ->addOrderBy('ent.versionFrom', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @param int $entityIdd
     * @param \DateTimeInterface $versionDateFrom
     * @param \DateTimeInterface|null $versionDateTo
     *
     * @return array<AbstractVersionEntity>
     */

    public function findVersionsInPeriod(
        int $entityIdd,
        \DateTimeInterface $versionDateFrom,
        ?\DateTimeInterface $versionDateTo = null,
    ): array {
        if (is_null($versionDateTo)) {
            $versionDateTo = AbstractVersionEntity::getMaxPossibleDate();
        }

        $versionFilter = $this->getEntityManager()->getFilters()
            ->disable(VersionFilter::FILTER_NAME)
        ;

        $qb = $this->createQueryBuilder('ent')
            ->andWhere('ent.idd = :entityIdd')
            ->setParameter('entityIdd', $entityIdd)
        ;

        $qb
            ->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->between('ent.versionFrom', "STR_TO_DATE(:dateFrom, :dateFormat)", "STR_TO_DATE(:dateTo, :dateFormat)"),
                    $qb->expr()->between('COALESCE(ent.versionTo, MAXDATE())', "STR_TO_DATE(:dateFrom, :dateFormat)", "STR_TO_DATE(:dateTo, :dateFormat)"),
                    $qb->expr()->between("STR_TO_DATE(:dateFrom, :dateFormat)", 'ent.versionFrom', 'COALESCE(ent.versionTo, MAXDATE())'),
                    $qb->expr()->between("STR_TO_DATE(:dateTo, :dateFormat)", 'ent.versionFrom', 'COALESCE(ent.versionTo, MAXDATE())')
                )
            )
            ->setParameter('dateFrom', $versionDateFrom->format('d.m.Y'))
            ->setParameter('dateTo', $versionDateTo->format('d.m.Y'))
            ->setParameter('dateFormat', 'dd.mm.yyyy')
        ;

        return $qb
            ->addOrderBy('ent.versionFrom', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @param int $entityIdd
     * @param \DateTimeInterface $versionDate
     *
     * @return AbstractVersionEntity|null
     */
    public function findVersion(int $entityIdd, \DateTimeInterface $versionDate): ?AbstractVersionEntity
    {
        $versionFilter = $this->getEntityManager()->getFilters()
            ->enable(VersionFilter::FILTER_NAME)
        ;

        $versionFilter->setParameter(
            VersionFilter::VERSION_DATE_PARAMETER,
            $versionDate->format(VersionFilter::DATE_FORMAT_PHP)
        );

        return $this->createQueryBuilder('ent')
            ->andWhere('ent.idd = :entityIdd')
            ->setParameter('entityIdd', $entityIdd)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * @param int $entityIdd
     * @param \DateTimeInterface $versionDate
     *
     * @return AbstractVersionEntity|null
     */
    public function findPreviousVersion(int $entityIdd, \DateTimeInterface $versionDate): ?AbstractVersionEntity
    {
        $this->getEntityManager()->getFilters()
            ->disable(VersionFilter::FILTER_NAME)
        ;

        return $this->createQueryBuilder('ent')
            ->andWhere('ent.idd = :entityIdd')
            ->setParameter('entityIdd', $entityIdd)
            ->andWhere('ent.versionTo < :versionDate')
            ->setParameter('versionDate', $versionDate)
            ->addOrderBy('ent.versionFrom', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * @param int $entityIdd
     * @param \DateTimeInterface $versionDate
     *
     * @return AbstractVersionEntity|null
     */
    public function findNextVersion(int $entityIdd, \DateTimeInterface $versionDate): ?AbstractVersionEntity
    {
        $this->getEntityManager()->getFilters()
            ->disable(VersionFilter::FILTER_NAME)
        ;

        return $this->createQueryBuilder('ent')
            ->andWhere('ent.idd = :entityIdd')
            ->setParameter('entityIdd', $entityIdd)
            ->andWhere('ent.versionFrom > :versionDate')
            ->setParameter('versionDate', $versionDate)
            ->addOrderBy('ent.versionFrom', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * @param AbstractVersionEntity $entityVersion
     *
     * @return void
     */
    public function invalidateVersion(AbstractVersionEntity $entityVersion): void
    {
        $entityVersion->setIsValid(AbstractVersionEntity::IS_VALID_FALSE);

        $this->getEntityManager()->persist($entityVersion);
    }

    /**
     * @param AbstractVersionEntity $entityVersion
     *
     * @return AbstractVersionEntity
     */
    public function cloneVersion(AbstractVersionEntity $entityVersion): AbstractVersionEntity
    {
        return clone $entityVersion;
    }

    /**
     * @param int $entityIdd
     * @param \DateTime $newVersionFrom
     * @param \DateTime $newVersionTo
     *
     * @return void
     */
    protected function updateEntityVersions(
        int $entityIdd,
        \DateTime $newVersionFrom,
        \DateTime $newVersionTo
    ): void {
        /** @var array<AbstractVersionEntity> */
        $entityVersions = $this->findVersionsInPeriod(
            $entityIdd,
            $newVersionFrom,
            $newVersionTo,
        );

        $versionsToInvalidate = [];

        foreach ($entityVersions as $entityVersion) {
            // Полная замена при одинаковых периодах, либо если новый период полностью покрывает старый
            if ($newVersionFrom <= $entityVersion->getVersionFrom() && $newVersionTo >= $entityVersion->getVersionTo()) {
                $versionsToInvalidate[$entityVersion->getId()] = $entityVersion;

                continue;
            }

            // Разделить версию, если она полностью покрывает новый период
            if ($newVersionFrom >= $entityVersion->getVersionFrom() && $newVersionTo <= $entityVersion->getVersionTo()) {
                if ($newVersionFrom->getTimestamp() !== $entityVersion->getVersionFrom()->getTimestamp()) {
                    $this->getEntityManager()->persist(
                        $this->cloneVersion($entityVersion)
                            ->setVersionTo((clone $newVersionFrom)->modify('-1 second'))
                    );
                }

                if ($newVersionTo->getTimestamp() !== $entityVersion->getVersionTo()->getTimestamp()) {
                    $this->getEntityManager()->persist(
                        $this->cloneVersion($entityVersion)
                            ->setVersionFrom((clone $newVersionTo)->modify('+1 second'))
                    );
                }

                $versionsToInvalidate[$entityVersion->getId()] = $entityVersion;

                continue;
            }

            // Создание клона со старыми данными, если пересекается только передняя граница версии
            if ($newVersionFrom >= $entityVersion->getVersionFrom() && $newVersionFrom <= $entityVersion->getVersionTo()) {
                $this->getEntityManager()->persist(
                    $this->cloneVersion($entityVersion)
                        ->setVersionTo((clone $newVersionFrom)->modify('-1 second'))
                );

                $versionsToInvalidate[$entityVersion->getId()] = $entityVersion;

                continue;
            }

            // Создание клона со старыми данными, если пересекается только задняя граница версии
            if ($newVersionTo >= $entityVersion->getVersionFrom() && $newVersionTo <= $entityVersion->getVersionTo()) {
                $this->getEntityManager()->persist(
                    $this->cloneVersion($entityVersion)
                        ->setVersionFrom((clone $newVersionTo)->modify('+1 second'))
                );

                $versionsToInvalidate[$entityVersion->getId()] = $entityVersion;

                continue;
            }
        }

        foreach ($versionsToInvalidate as $versionToInvalidate) {
            $this->invalidateVersion($versionToInvalidate);
        }
    }
}
