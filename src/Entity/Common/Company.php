<?php

namespace App\Entity\Common;

use App\Entity\AbstractVersionEntity;
use App\Repository\Common\CompanyRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CompanyRepository::class)]
#[ORM\Table(name: 'com_company')]
class Company extends AbstractVersionEntity
{
    #[ORM\Column(name: 'full_name', type: Types::STRING, length: 256, nullable: false, options: ['comment' => 'Наименование компании'])]
    private string $fullName;

    #[ORM\Column(name: 'tax_id', type: Types::BIGINT, length: 12, nullable: true, options: ['comment' => 'ИНН'])]
    private ?string $taxId;

    #[ORM\Column(name: 'registration_reason_code', type: Types::BIGINT, length: 9, nullable: true, options: ['comment' => 'КПП'])]
    private ?string $registrationReasonCode;

    #[ORM\Column(name: 'registration_number', type: Types::BIGINT, nullable: true, options: ['comment' => 'ОГРН'])]
    private ?string $registrationNumber;

    #[ORM\Column(name: 'registration_date', type: Types::DATE_MUTABLE, nullable: true, options: ['comment' => 'Дата регистрации'])]
    private ?\DateTime $registrationDate;

    public function getFullName(): string
    {
        return $this->fullName;
    }

    public function setFullName(string $fullName): self
    {
        $this->fullName = $fullName;

        return $this;
    }

    public function getTaxId(): ?string
    {
        return $this->taxId;
    }

    public function setTaxId(?string $taxId): self
    {
        $this->taxId = $taxId;

        return $this;
    }

    public function getRegistrationReasonCode(): ?string
    {
        return $this->registrationReasonCode;
    }

    public function setRegistrationReasonCode(?string $registrationReasonCode): self
    {
        $this->registrationReasonCode = $registrationReasonCode;

        return $this;
    }

    public function getRegistrationNumber(): ?string
    {
        return $this->registrationNumber;
    }

    public function setRegistrationNumber(?string $registrationNumber): self
    {
        $this->registrationNumber = $registrationNumber;

        return $this;
    }

    public function getRegistrationDate(): ?\DateTime
    {
        return $this->registrationDate;
    }

    public function setRegistrationDate(?\DateTime $registrationDate): self
    {
        $this->registrationDate = $registrationDate;

        return $this;
    }
}
