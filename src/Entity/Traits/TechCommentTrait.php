<?php

namespace App\Entity\Traits;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

trait TechCommentTrait
{
    #[ORM\Column(name: 'tech_comm', type: Types::STRING, length: 1024, nullable: true, options: ['comment' => 'Технический комментарий'])]
    protected ?string $techCommment;

    public function getTechCommment(): string
    {
        return $this->techCommment;
    }

    public function setTechCommment(string $techCommment): self
    {
        $this->techCommment = $techCommment;

        return $this;
    }
}
