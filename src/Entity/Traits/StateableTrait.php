<?php

namespace App\Entity\Traits;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

trait StateableTrait
{
    #[ORM\Column(name: 'state', type: Types::STRING, length: 32, nullable: false, options: ['comment' => 'Статус записи'])]
    protected string $state;

    #[ORM\Column(name: 'state_date', type: Types::DATETIME_MUTABLE, nullable: false, options: ['comment' => 'Дата последнего изменения статуса записи'])]
    #[Gedmo\Timestampable(on: 'change', field: ['state'])]
    protected \DateTime $stateDate;

    public function getState(): string
    {
        return $this->state;
    }

    public function setState(string $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function getStateDate(): \DateTime
    {
        return $this->stateDate;
    }

    public function setStateDate(\DateTime $stateDate): self
    {
        $this->stateDate = $stateDate;

        return $this;
    }
}
