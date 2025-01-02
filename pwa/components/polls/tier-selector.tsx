'use client'

import { Spinner } from '@nextui-org/spinner'
import { Checkbox, cn } from '@nextui-org/react'
import { useEffect, useState } from 'react'
import { GenericCampaignTier } from '@/types/entity/GenericCampaignTier'
import { UseQueryResult } from '@tanstack/react-query'

interface Props {
  id: string
  onTierSelectUpdate: (selectedTiers: GenericCampaignTier[]) => void
  tierLoader: ({ id }: { id: string }) => UseQueryResult<GenericCampaignTier[]>
}

export default function TierSelector({
  id,
  onTierSelectUpdate,
  tierLoader,
}: Props) {
  const { data: patreonCampaignTiers, isLoading: patreonCampaignTiersLoading } =
    tierLoader({ id })

  const [selectedTiers, setSelectedTiers] = useState<GenericCampaignTier[]>([])

  useEffect(() => {
    onTierSelectUpdate(selectedTiers)
  }, [selectedTiers, onTierSelectUpdate])

  if (id === '') {
    return null
  }

  if ((patreonCampaignTiers ?? []).length === 0) {
    if (patreonCampaignTiersLoading) {
      return (
        <Spinner
          className={'mt-5 w-full content-center items-center justify-center'}
          size={'lg'}
          label={'Tiers are loading, please wait'}
        />
      )
    }

    return (
      <p className={'mb-2 mt-5'}>
        This campaign does not seem to have any tiers.
      </p>
    )
  }

  return (
    <>
      <h2 className="mt-5 text-large">Which tiers can vote?</h2>
      {patreonCampaignTiers!.map((tier) => (
        <Checkbox
          key={tier.id}
          aria-label={tier.tierName}
          isSelected={
            selectedTiers.find((searchTier) => searchTier.id === tier.id) !==
            undefined
          }
          onValueChange={(checked) => {
            if (checked) {
              setSelectedTiers([...selectedTiers, tier])
            } else {
              setSelectedTiers(
                selectedTiers.filter((searchTier) => searchTier.id !== tier.id),
              )
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
          <div className="flex w-full justify-between gap-2">
            {tier.tierName}
          </div>
        </Checkbox>
      ))}
    </>
  )
}
