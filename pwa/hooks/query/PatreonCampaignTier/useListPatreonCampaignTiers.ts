import { keepPreviousData, useQuery, UseQueryResult } from '@tanstack/react-query'
import { getListEntities } from '@/api/api'
import { PatreonCampaignTier } from '@/types/entity/PatreonCampaignTier'

export const useListPatreonCampaignTiers = (
  {campaignId}: {campaignId: string}
): UseQueryResult<PatreonCampaignTier[]> => {
  return useQuery<PatreonCampaignTier[]>({
    queryKey: [`/api/patreon_campaigns/${campaignId}/tiers`],
    queryFn: () => getListEntities<PatreonCampaignTier>(`/api/patreon_campaigns/${campaignId}/tiers`),
    placeholderData: keepPreviousData,
    staleTime: 60 * 1000,
    enabled: campaignId !== ""
  })
}
