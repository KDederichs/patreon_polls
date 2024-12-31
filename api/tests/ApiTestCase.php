<?php

namespace App\Tests;

use App\Entity\Poll;
use App\Entity\User;
use App\Factory\MemberEntitledTierFactory;
use App\Factory\PatreonCampaignFactory;
use App\Factory\PatreonCampaignMemberFactory;
use App\Factory\PatreonCampaignTierFactory;
use App\Factory\PatreonPollVoteConfigFactory;
use App\Factory\PatreonUserFactory;
use App\Factory\PollFactory;
use App\Factory\UserFactory;
use Carbon\CarbonImmutable;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Browser\HttpOptions;
use Zenstruck\Browser\Test\HasBrowser;
use Zenstruck\Foundry\Persistence\Proxy;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

abstract class ApiTestCase extends KernelTestCase
{
    use HasBrowser {
        browser as baseKernelBrowser;
    }
    use Factories;
    use ResetDatabase;

    protected function browser(array $options = [], array $server = [])
    {
        return $this->baseKernelBrowser($options, $server)
            ->setDefaultHttpOptions(
                HttpOptions::create()
                    ->withHeader('Accept', 'application/ld+json')
                    ->withHeader('Content-Type', 'application/ld+json')
            )
        ;
    }

    protected function setUpPoll(
        User&Proxy $user,
        User&Proxy $owner,
        bool $allowImages = false,
        ?CarbonImmutable $endsAt = null,
        int $votingPower = 1,
        bool $addOptions = false,
        bool $limitedVotes = false,
        ?int $maxOptionAdd = null,
        ?int $numberOfVotes = null
    ): Poll&Proxy
    {
        $poll = PollFactory::createOne([
            'allowPictures' => $allowImages,
            'createdBy' => $owner,
            'endsAt' => $endsAt,
        ]);
        $patreonUser = PatreonUserFactory::createOne([
            'user' => $user,
        ]);
        $campaign = PatreonCampaignFactory::createOne([
            'campaignOwner' => $owner,
        ]);
        $campaignTier = PatreonCampaignTierFactory::createOne([
            'campaign' => $campaign
        ]);
        $voteConfig = PatreonPollVoteConfigFactory::createOne([
            'poll' => $poll,
            'campaignTier' => $campaignTier,
            'votingPower' => $votingPower,
            'numberOfVotes' => $numberOfVotes,
            'maxOptionAdd' => $maxOptionAdd,
            'addOptions' => $addOptions,
            'limitedVotes' => $limitedVotes
        ]);
        $membership = PatreonCampaignMemberFactory::createOne([
            'patreonUser' => $patreonUser,
            'campaign' => $campaign,
        ]);
        MemberEntitledTierFactory::createOne([
            'campaignMember' => $membership,
            'tier' => $campaignTier
        ]);

        return $poll;
    }
}
