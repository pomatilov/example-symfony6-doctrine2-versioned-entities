<?php

namespace App\DTO\Company;

use App\Request\ParamConverter\ConvertableInterface;

class CompanyDTO implements ConvertableInterface
{
    private string $fullName;
    private ?int $taxId = null;
    private ?int $registrationReasonCode = null;
    private ?int $registrationNumber = null;
    private ?\DateTime $registrationDate = null;

    public function getFullName(): string
    {
        return $this->fullName;
    }

    public function setFullName(string $fullName): self
    {
        $this->fullName = $fullName;

        return $this;
    }

    public function getTaxId(): ?int
    {
        return $this->taxId;
    }

    public function setTaxId(?int $taxId): self
    {
        $this->taxId = $taxId;

        return $this;
    }

    public function getRegistrationReasonCode(): ?int
    {
        return $this->registrationReasonCode;
    }

    public function setRegistrationReasonCode(?int $registrationReasonCode): self
    {
        $this->registrationReasonCode = $registrationReasonCode;

        return $this;
    }

    public function getRegistrationNumber(): ?int
    {
        return $this->registrationNumber;
    }

    public function setRegistrationNumber(?int $registrationNumber): self
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
