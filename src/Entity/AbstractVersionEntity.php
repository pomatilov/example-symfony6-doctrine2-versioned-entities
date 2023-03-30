<?php

namespace App\Entity;

use App\Entity\Traits\TechCommentTrait;
use App\Entity\Traits\TimestampableTrait;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

abstract class AbstractVersionEntity extends AbstractEntity
{
    use TimestampableTrait;
    use TechCommentTrait;

    public const IS_VALID_TRUE = 'Y';
    public const IS_VALID_FALSE = 'N';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::BIGINT, nullable: false, options: ['comment' => 'ID версии записи'])]
    protected ?string $id = null;

    #[ORM\Column(type: Types::BIGINT, nullable: true, options: ['comment' => 'ID записи'])]
    protected ?string $idd = null;

    #[ORM\Column(name: 'vfrom', type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => 'Дата начала версии'])]
    protected ?\DateTime $versionFrom = null;

    #[ORM\Column(name: 'vto', type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => 'Дата окончания версии'])]
    protected ?\DateTime $versionTo = null;

    #[ORM\Column(name: 'cax', type: Types::BIGINT, nullable: false, options: ['comment' => 'ID транзакции, в рамках которой запись была создана'])]
    protected string $cax;

    #[ORM\Column(name: 'eax', type: Types::BIGINT, nullable: true, options: ['comment' => 'ID транзакции, в рамках которой запись была изменена'])]
    protected ?string $eax = null;

    #[ORM\Column(name: 'iax', type: Types::BIGINT, nullable: true, options: ['comment' => 'ID транзакции, в рамках которой запись была инвалидирована'])]
    protected ?string $iax = null;

    #[ORM\Column(name: 'is_valid', type: Types::STRING, length: 1, nullable: false, options: ['comment' => 'Статус валидности записи'])]
    protected string $isValid;

    public function __construct()
    {
        $this->isValid = self::IS_VALID_TRUE;
        $this->versionFrom = new \DateTime();
    }

    public function __clone()
    {
        if ($this->id) {
            $this->setId(null);
        }
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(?string $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getIdd(): ?string
    {
        return $this->idd;
    }

    public function setIdd(?string $idd): self
    {
        $this->idd = $idd;

        return $this;
    }

    public function getVersionFrom(): ?\DateTime
    {
        return $this->versionFrom;
    }

    public function setVersionFrom(?\DateTime $versionFrom): self
    {
        $this->versionFrom = $versionFrom;

        return $this;
    }

    public function getVersionTo(): ?\DateTime
    {
        return $this->versionTo;
    }

    public function setVersionTo(?\DateTime $versionTo): self
    {
        $this->versionTo = $versionTo;

        return $this;
    }

    public function getCax(): string
    {
        return $this->cax;
    }

    public function setCax(string $cax): self
    {
        $this->cax = $cax;

        return $this;
    }

    public function getEax(): ?string
    {
        return $this->eax;
    }

    public function setEax(?string $eax): self
    {
        $this->eax = $eax;

        return $this;
    }

    public function getIax(): ?string
    {
        return $this->iax;
    }

    public function setIax(?string $iax): self
    {
        $this->iax = $iax;

        return $this;
    }

    public function getIsValid(): string
    {
        return $this->isValid;
    }

    public function setIsValid(string $isValid): self
    {
        $this->isValid = $isValid;

        return $this;
    }
}
