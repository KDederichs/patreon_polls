<?php

namespace App\Controller;

use App\Entity\MemberEntitledTier;
use App\Entity\PatreonCampaign;
use App\Entity\PatreonCampaignMember;
use App\Entity\PatreonCampaignTier;
use App\Entity\SubscribestarSubscription;
use App\Repository\PatreonCampaignMemberRepository;
use App\Repository\PatreonCampaignRepository;
use App\Repository\PatreonCampaignTierRepository;
use App\Repository\PatreonCampaignWebhookRepository;
use App\Repository\PatreonUserRepository;
use App\Repository\SubscribestarSubscriptionRepository;
use App\Repository\SubscribestarTierRepository;
use App\Repository\SubscribestarUserRepository;
use App\Service\SubscribestarService;
use App\Util\SentryHelper;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;

class WebhookController extends AbstractController
{

    #[Route('webhook/patreon', name: 'patreon_webhooks', methods: ['POST'])]
    public function webhookPatreon(
        Request $request,
        PatreonCampaignRepository $campaignRepository,
        PatreonCampaignWebhookRepository $campaignWebhookRepository,
        PatreonCampaignMemberRepository $campaignMemberRepository,
        PatreonCampaignTierRepository $tierRepository,
        PatreonUserRepository $patreonUserRepository,
        LoggerInterface $logger
    ): Response
    {
        $requestSignature = $request->headers->get('x-patreon-signature');
        $payload = $request->getContent();
        $decodedPayload = json_decode($payload, true);

        SentryHelper::addContext('webhookData', [
            'payload' => $decodedPayload,
            'sigHeader' => $requestSignature,
        ]);

        $campaignId = $decodedPayload['data']['relationships']['campaign']['data']['id'] ?? '';
        /** @var PatreonCampaign|null $campaign */
        $campaign = $campaignRepository->findByPatreonCampaignId($campaignId);

        if (!$campaign) {
            throw new BadRequestHttpException('Campaign not found');
        }
        $webhook = $campaignWebhookRepository->findByCampaign($campaign);

        if (!$webhook || $requestSignature !== hash_hmac('md5', $payload, $webhook->getSecret())) {
            $logger->error('Could not verify webhook signature');
            throw new AccessDeniedHttpException('Could not verify signature');
        }
        $userId = $decodedPayload['data']['relationships']['user']['data']['id'];

        $member = $campaignMemberRepository->findByCampaignAndPatreonUserId($campaign, $userId);
        if (!$member) {
            $member = new PatreonCampaignMember();
            $member
                ->setCampaign($campaign)
                ->setPatreonUserId($userId);
            $campaignMemberRepository->persist($member);
        }
        if (!$member->getPatreonUser()) {
            $member->setPatreonUser($patreonUserRepository->findByPatreonId($member->getPatreonUserId()));
        }
        foreach ($member->getEntitledTiers() as $tier) {
            $campaignMemberRepository->remove($tier);
        }
        $includes = $decodedPayload['included'];
        foreach (($decodedPayload['data']['relationships']['currently_entitled_tiers']['data'] ?? []) as $entitled) {
            $tier = $tierRepository->findByPatreonTierId($entitled['id']);
            if (!$tier) {
                $tier = new PatreonCampaignTier();
                $tier
                    ->setCampaign($campaign)
                    ->setPatreonTierId($entitled['id']);

                foreach ($includes as $include) {
                    if ($include['type'] !== 'tier') {
                        continue;
                    }

                    if ($include['id'] === $entitled['id']) {
                        $tier
                            ->setTierName($include['attributes']['title'])
                            ->setAmountInCents($include['attributes']['amount_cents'])
                        ;
                    }
                }

                $campaignMemberRepository->persist($tier);
            }
            $memberEntitlement = new MemberEntitledTier();
            $memberEntitlement
                ->setTier($tier)
                ->setCampaignMember($member);
            $campaignMemberRepository->persist($memberEntitlement);
        }
        $campaignMemberRepository->save();
        return new Response();
    }

    #[Route('webhook/subscribestar', name: 'subscribestar_webhooks', methods: ['POST'])]
    public function webhookSubscribestar(
        Request $request,
        #[Autowire(env: 'SUBSCRIBESTAR_SECRET_WEBHOOK')] string $secret,
        SubscribestarSubscriptionRepository $subscriptionRepository,
        SubscribestarUserRepository $subscribestarUserRepository,
        SubscribestarTierRepository $subscribestarTierRepository,
        SubscribestarService $subscribestarService,
    ): Response
    {
        $requestSignature = $request->headers->get('x-subscribestar-signature');
        $body = $request->getContent();
        if ($requestSignature === hash_hmac('md5', $body, $secret)) {
            throw $this->createAccessDeniedException();
        }

        $subscribedEvents = ['new_subscription', 'recurring_pledge_decreased', 'recurring_pledge_increased', 'subscription_cancelled'];
        $decodedBody = json_decode($body, true);
        if (!in_array($decodedBody['event'], $subscribedEvents)) {
            return new Response();
        }


        $subscriberId = $decodedBody['payload']['subscriber']['id'];
        $subscriptionId = $decodedBody['payload']['subscription']['id'];
        $subscriptionTierId = $decodedBody['payload']['subscription']['tier_id'];
        $creatorProfileId = $decodedBody['payload']['subscription']['profile_id'];
        $cancelled = $decodedBody['payload']['subscription']['cancelled'];

        $subscriberUser = $subscribestarUserRepository->findBySubscribestarId($subscriberId);
        $creatorUser = $subscribestarUserRepository->findBySubscribestarId($creatorProfileId);

        if (null === $subscriberUser || null === $creatorUser) {
            return new Response();
        }

        $subscription = $subscriptionRepository->findBySubscribestarId($subscriptionId);
        if (!$subscription) {
            $subscription = new SubscribestarSubscription();
            $subscription
                ->setSubscribestarId($subscriptionId)
                ->setContentProviderId($creatorProfileId)
                ->setSubscribestarUser($subscriberUser)
                ->setSubscribedTo($creatorUser)
            ;
        }

        $tier = $subscribestarTierRepository->findBySubscribestarTierId($subscriptionTierId);

        if (null === $tier) {
            $subscribestarService->refreshTiers($creatorUser);
        }

        $tier = $subscribestarTierRepository->findBySubscribestarTierId($subscriptionTierId);

        $subscription
            ->setTierId($subscriptionTierId)
            ->setActive(!$cancelled)
            ->setSubscribestarTier($tier);

        $subscriptionRepository->persist($subscription);
        $subscriptionRepository->save();
        return new Response();
    }
}
