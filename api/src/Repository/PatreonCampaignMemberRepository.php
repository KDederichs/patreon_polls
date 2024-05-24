<?php

namespace App\Repository;

use App\Entity\PatreonCampaign;
use App\Entity\PatreonCampaignMember;
use Doctrine\Persistence\ManagerRegistry;

class PatreonCampaignMemberRepository   extends AbstractBaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PatreonCampaignMember::class);
    }

    public function findByCampaignAndPatreonUserId(PatreonCampaign $campaign, string $patreonUserId): ?PatreonCampaignMember
    {
        return $this->findOneBy([
            'campaign' => $campaign,
            'patreonUserId' => $patreonUserId
        ]);
    }

    public function getExistingIds(array $existingIdsToCheck): array
    {
        if (empty($existingIdsToCheck)) {
            return [];
        }

        $qb = $this->createQueryBuilder('pcm');
        return array_map(
            static fn ($elem) => $elem['member_id'],
            $qb
                ->select('pcm.patreonUserId as member_id')
                ->where(
                    $qb->expr()->in(
                        'pcm.patreonUserId', ':ids'
                    )
                )
                ->setParameter('ids', $existingIdsToCheck)
                ->getQuery()
                ->getArrayResult()
        );
    }
}
