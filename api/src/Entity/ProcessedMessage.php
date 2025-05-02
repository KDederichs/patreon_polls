<?php

namespace App\Entity;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Id;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Uid\Uuid;
use Zenstruck\Messenger\Monitor\History\Model\ProcessedMessage as BaseProcessedMessage;
use Doctrine\ORM\Mapping as ORM;
use Zenstruck\Messenger\Monitor\History\Model\Results;

#[ORM\Entity(readOnly: true)]
#[ORM\Table('messenger_processed_messages')]
class ProcessedMessage extends BaseProcessedMessage
{
    #[Id]
    #[Column(type: UuidType::NAME)]
    private Uuid $id;

    public function __construct(Envelope $envelope, Results $results, ?\Throwable $exception = null)
    {
        $this->id = Uuid::v7();
        parent::__construct($envelope, $results, $exception);
    }


    public function id(): string|int|\Stringable|null
    {
        return $this->id;
    }
}
