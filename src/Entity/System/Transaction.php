<?php

namespace App\Entity\System;

use App\Entity\Traits\StateableTrait;
use App\Entity\Traits\TechCommentTrait;
use App\Entity\Traits\TimestampableTrait;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity()]
#[ORM\Table(name: 'com_transaction')]
class Transaction
{
    use TimestampableTrait;
    use TechCommentTrait;
    use StateableTrait;

    public const STATE_WORKING = 'working';
    public const STATE_FAILED = 'failed';
    public const STATE_SUCCESS = 'success';

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'SEQUENCE')]
    #[ORM\SequenceGenerator(sequenceName: 'com_transaction_seq', initialValue: 1000000)]
    #[ORM\Column(type: Types::BIGINT, options: ['comment' => 'ID транзакции'])]
    private string $id;

    #[ORM\Column(name: 'session_id', type: Types::STRING, nullable: false, options: ['comment' => 'ID сессии'])]
    private string $sessionId;

    #[ORM\Column(name: 'name', type: Types::STRING, length: 128, nullable: false, options: ['comment' => 'Наименование действия, в рамках которого происходит транзакция'])]
    private string $name;

    public function __construct()
    {
        $this->setStateDate(new \DateTime());
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getSessionId(): string
    {
        return $this->sessionId;
    }

    public function setSessionId(string $sessionId): self
    {
        $this->sessionId = $sessionId;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }
}
