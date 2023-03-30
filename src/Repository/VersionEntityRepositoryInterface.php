<?php

namespace App\Repository;

use App\Entity\AbstractVersionEntity;

interface VersionEntityRepositoryInterface
{
    /**
     * @return array<AbstractVersionEntity>
     */
    public function findAll(): array;

    /**
     * @param int $entityIdd
     *
     * @return array<AbstractVersionEntity>
     */
    public function findAllVersions(int $entityIdd): array;

    /**
     * @param int $entityIdd
     * @param \DateTimeInterface $versionDateFrom
     * @param \DateTimeInterface|null $versionDateTo
     *
     * @return array
     */
    public function findVersionsInPeriod(int $entityIdd, \DateTimeInterface $versionDateFrom, ?\DateTimeInterface $versionDateTo = null): array;

    /**
     * @param int $entityIdd
     * @param \DateTimeInterface $versionDate
     *
     * @return AbstractVersionEntity|null
     */
    public function findVersion(int $entityIdd, \DateTimeInterface $versionDate): ?AbstractVersionEntity;

    /**
     * @param int $entityIdd
     * @param \DateTimeInterface $versionDate
     *
     * @return AbstractVersionEntity|null
     */
    public function findPreviousVersion(int $entityIdd, \DateTimeInterface $versionDate): ?AbstractVersionEntity;

    /**
     * @param int $entityIdd
     * @param \DateTimeInterface $versionDate
     *
     * @return AbstractVersionEntity|null
     */
    public function findNextVersion(int $entityIdd, \DateTimeInterface $versionDate): ?AbstractVersionEntity;

    /**
     * @param AbstractVersionEntity $entityVersion
     *
     * @return void
     */
    public function invalidateVersion(AbstractVersionEntity $entityVersion): void;

    /**
     * @param AbstractVersionEntity $entityVersion
     * @param \DateTimeInterface $newVersionFrom
     * @param \DateTimeInterface $newVersionTo
     * @param array $entityParameters
     *
     * @return AbstractVersionEntity
     */
    public function updateVersion(AbstractVersionEntity $entityVersion, \DateTimeInterface $newVersionFrom, \DateTimeInterface $newVersionTo, array $entityParameters): AbstractVersionEntity;

    /**
     * @param AbstractVersionEntity $entityVersion
     *
     * @return AbstractVersionEntity
     */
    public function cloneVersion(AbstractVersionEntity $entityVersion): AbstractVersionEntity;
}
