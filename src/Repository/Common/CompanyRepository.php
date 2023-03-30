<?php

namespace App\Repository\Common;

use App\Entity\AbstractVersionEntity;
use App\Entity\Common\Company;
use App\Repository\VersionEntityRepositoryInterface;
use App\Repository\VersionEntityRepositoryTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CompanyRepository extends ServiceEntityRepository implements VersionEntityRepositoryInterface
{
    use VersionEntityRepositoryTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Company::class);
    }

    /**
     * Создание новой компании
     *
     * @param string $fullname
     * @param int|null $taxId
     * @param int|null $registrationReasonCode
     * @param int|null $registrationNumber
     * @param \DateTime|null $registrationDate
     *
     * @return Company|null
     */
    public function createCompany(
        string $fullname,
        ?int $taxId,
        ?int $registrationReasonCode,
        ?int $registrationNumber,
        ?\DateTime $registrationDate,
    ): ?Company {
        $versionFrom = new \DateTime();

        if (isset($registrationDate) && $registrationDate < $versionFrom) {
            $versionFrom = $registrationDate;
        }

        return $this->updateVersion(
            (new Company()),
            $versionFrom->setTime(0, 0, 0, 0),
            AbstractVersionEntity::getMaxPossibleDate(),
            [
                'fullname' => $fullname,
                'taxId' => $taxId,
                'registrationReasonCode' => $registrationReasonCode,
                'registrationNumber' => $registrationNumber,
                'registrationDate' => $registrationDate?->setTime(0, 0, 0, 0),
            ]
        );
    }

    /**
     * Создание новой версии на указанный период
     *
     * @param Company $companyVersion
     * @param \DateTime $newVersionFrom
     * @param \DateTime $newVersionTo
     * @param string $fullname
     * @param int|null $taxId
     * @param int|null $registrationReasonCode
     * @param int|null $registrationNumber
     * @param \DateTime|null $registrationDate
     *
     * @return Company|null
     */
    public function createCompanyVersion(
        Company | int $entityIdd,
        \DateTimeInterface $newVersionFrom,
        \DateTimeInterface $newVersionTo,
        string $fullname,
        ?int $taxId,
        ?int $registrationReasonCode,
        ?int $registrationNumber,
        ?\DateTime $registrationDate,
    ): ?Company {
        $idd = $entityIdd instanceof Company
            ? $entityIdd->getIdd()
            : $entityIdd
        ;

        $this->updateEntityVersions($idd, $newVersionFrom, $newVersionTo);

        return $this->updateVersion(
            (new Company())->setIdd($idd),
            $newVersionFrom,
            $newVersionTo,
            [
                'fullname' => $fullname,
                'taxId' => $taxId,
                'registrationReasonCode' => $registrationReasonCode,
                'registrationNumber' => $registrationNumber,
                'registrationDate' => $registrationDate?->setTime(0, 0, 0, 0),
            ]
        );
    }

    /**
     * Обновление данных по всем версиям
     *
     * @param Company|int $entityIdd
     * @param string $fullname
     * @param int|null $taxId
     * @param int|null $registrationReasonCode
     * @param int|null $registrationNumber
     * @param \DateTime|null $registrationDate
     *
     * @return Company|null
     */
    public function updateCompany(
        Company | int $entityIdd,
        string $fullname,
        ?int $taxId,
        ?int $registrationReasonCode,
        ?int $registrationNumber,
        ?\DateTime $registrationDate,
    ): void {
        /** @var array<Company> */
        $companyVersions = $this->findAllVersions($entityIdd instanceof Company ? $entityIdd->getIdd() : $entityIdd);

        foreach ($companyVersions as $companyVersion) {
            $this->updateVersion(
                $companyVersion,
                $companyVersion->getVersionFrom(),
                $companyVersion->getVersionTo(),
                [
                    'fullname' => $fullname,
                    'taxId' => $taxId,
                    'registrationReasonCode' => $registrationReasonCode,
                    'registrationNumber' => $registrationNumber,
                    'registrationDate' => $registrationDate,
                ]
            );

            $this->invalidateVersion($companyVersion);
        }
    }

    /**
     * Создание клона версии с новым периодом и установка новых параметров сущности
     *
     * @param Company $companyVersion
     * @param \DateTime $newVersionFrom
     * @param \DateTime $newVersionTo
     * @param array $entityParameters Associative array with keys: fullname, taxId, registrationReasonCode, registrationNumber, registrationDate
     *
     * @return Company
     */
    public function updateVersion(
        AbstractVersionEntity $companyVersion,
        \DateTimeInterface $newVersionFrom,
        \DateTimeInterface $newVersionTo,
        array $entityParameters,
    ): Company {
        /** @var Company */
        $clonedVersion = $this->cloneVersion($companyVersion)
            ->setVersionFrom($newVersionFrom)
            ->setVersionTo($newVersionTo)
        ;

        if (array_key_exists('fullname', $entityParameters)) {
            $clonedVersion->setFullName($entityParameters['fullname']);
        }

        if (array_key_exists('taxId', $entityParameters)) {
            $clonedVersion->setTaxId($entityParameters['taxId']);
        }

        if (array_key_exists('registrationReasonCode', $entityParameters)) {
            $clonedVersion->setRegistrationReasonCode($entityParameters['registrationReasonCode']);
        }

        if (array_key_exists('registrationNumber', $entityParameters)) {
            $clonedVersion->setRegistrationNumber($entityParameters['registrationNumber']);
        }

        if (array_key_exists('registrationDate', $entityParameters)) {
            $clonedVersion->setRegistrationDate($entityParameters['registrationDate']);
        }

        $this->getEntityManager()->persist($clonedVersion);

        return $clonedVersion;
    }
}
