<?php

namespace App\MessageHandler;

use App\Entity\MemberEntitledTier;
use App\Entity\PatreonCampaign;
use App\Entity\PatreonCampaignMember;
use App\Message\FetchCampaignMembersMessage;
use App\Repository\PatreonCampaignMemberRepository;
use App\Repository\PatreonCampaignRepository;
use App\Repository\PatreonCampaignTierRepository;
use App\Service\PatreonService;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class FetchCampaignMembersHandler implements LoggerAwareInterface
{
    private ?LoggerInterface $logger = null;

    public function __construct(
        private readonly PatreonCampaignRepository $campaignRepository,
        private readonly PatreonCampaignMemberRepository $campaignMemberRepository,
        private readonly PatreonCampaignTierRepository $campaignTierRepository,
        private readonly PatreonService $patreonService,
    )
    {

    }

    public function __invoke(FetchCampaignMembersMessage $campaignMembersMessage): void
    {
        $campaign = $this->campaignRepository->find($campaignMembersMessage->getCampaignId());
        if (!$campaign) {
            $this->logger?->error('Trying to fetch members for non existing campaign: '. $campaignMembersMessage->getCampaignId());
            return;
        }

        $cursor = null;
        $tierCache = [];
        do {
            /** @var PatreonCampaign $dbCampaign */
            $dbCampaign = $this->campaignRepository->find($campaign->getId());
            $payload = $this->patreonService->doFetchMembersRequest($dbCampaign, $cursor);
            $cursor = $payload['meta']['pagination']['cursors']['next'] ?? null;

            $batchIds = [];

            foreach ($payload['data'] as $memberData) {
                if (($memberData['type'] ?? null) !== 'member') {
                    continue;
                }
                $batchIds[] = $memberData['relationships']['user']['data']['id'];
            }

            $exisingIds = $this->campaignMemberRepository->getExistingIds($batchIds);
            $newMemberIds = array_diff($batchIds, $exisingIds);

            foreach ($payload['data'] as $memberData) {
                if (($memberData['type'] ?? null) !== 'member') {
                    continue;
                }
                if (in_array($memberData['relationships']['user']['data']['id'], $newMemberIds, true)) {
                    $member = new PatreonCampaignMember();
                    $member
                        ->setCampaign($dbCampaign)
                        ->setPatreonUserId($memberData['relationships']['user']['data']['id']);
                    $this->campaignMemberRepository->persist($member);
                } else {
                    /** @var PatreonCampaignMember $member */
                    $member = $this->campaignMemberRepository->findByCampaignAndPatreonUserId($dbCampaign,$memberData['relationships']['user']['data']['id']);
                }

                foreach ($member->getEntitledTiers() as $tier) {
                    $this->campaignMemberRepository->remove($tier);
                }

                foreach (($memberData['relationships']['currently_entitled_tiers']['data'] ?? []) as $entitled) {
                    if (!array_key_exists($entitled['id'], $tierCache)) {
                        $tierCache[$entitled['id']] = $this->campaignTierRepository->findByPatreonTierId($entitled['id']);
                    }

                    $memberEntitlement = new MemberEntitledTier();
                    $memberEntitlement
                        ->setCampaignMember($member)
                        ->setTier($tierCache[$entitled['id']]);
                    $this->campaignMemberRepository->persist($memberEntitlement);
                }
            }
            $this->campaignMemberRepository->save();
            $this->campaignMemberRepository->clear();
            $tierCache = [];
        } while ($cursor !== null);
    }

    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }
}
