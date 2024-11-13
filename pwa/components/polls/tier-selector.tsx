'use client'

import { Spinner } from '@nextui-org/spinner'
import { useListPatreonCampaignTiers } from '@/hooks/query/PatreonCampaignTier/useListPatreonCampaignTiers'
import { Checkbox, cn } from '@nextui-org/react'
import { useEffect, useState } from 'react'
import { PatreonCampaignTier } from '@/types/entity/PatreonCampaignTier'

interface Props {
  campaignId: string,
  onTierSelectUpdate: (selectedTiers: PatreonCampaignTier[]) => void
}

export default function TierSelector(
  {campaignId, onTierSelectUpdate} : Props
) {
  const {data: patreonCampaignTiers, isLoading: patreonCampaignTiersLoading } = useListPatreonCampaignTiers({campaignId})

  const [selectedTiers, setSelectedTiers] = useState<PatreonCampaignTier[]>([])

  useEffect(() => {
    onTierSelectUpdate(selectedTiers)
  }, [selectedTiers, onTierSelectUpdate])

  if (campaignId === '') {
    return null;
  }

  if ((patreonCampaignTiers ?? []).length === 0) {
    if (patreonCampaignTiersLoading) {
      return <Spinner className={'mt-5 justify-center items-center content-center w-full' } size={'lg'} label={'Tiers are loading, please wait'}/>
    }

    return <p className={'mt-5 mb-2'}>
      This campaign does not seem to have any tiers.
    </p>
  }

  return <>
    <h2 className="text-large mt-5">
      Which tiers can vote?
    </h2>
    {
      patreonCampaignTiers!.map((tier) =>
        <Checkbox
          key={tier.id}
          aria-label={tier.tierName}
          isSelected={selectedTiers.find((searchTier) => searchTier.id === tier.id) !== undefined}
          onValueChange={(checked) => {
            if (checked) {
              setSelectedTiers([...selectedTiers, tier])
            } else {
              setSelectedTiers(selectedTiers.filter((searchTier) => searchTier.id !== tier.id))
            }
          }}
          classNames={{
            base: cn(
              'inline-flex w-full max-w-md bg-content1',
              'hover:bg-content2 items-center justify-start',
              'cursor-pointer rounded-lg gap-2 p-4 border-2 border-transparent',
              'data-[selected=true]:border-primary',
              'mt-1 mb-1',
            ),
            label: 'w-full',
          }}
        >
          <div className="w-full flex justify-between gap-2">
            {tier.tierName}
          </div>
        </Checkbox>)
    }
  </>
}
