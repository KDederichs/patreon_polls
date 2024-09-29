<?php

namespace App\Message;

use Symfony\Component\Uid\Uuid;

readonly class FetchCampaignMembersMessage implements AsyncMessageInterface
{
    public function __construct(
        private Uuid $campaignId,
    )
    {

    }

    public function getCampaignId(): Uuid
    {
        return $this->campaignId;
    }
}
