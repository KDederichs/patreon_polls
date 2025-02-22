import {
  keepPreviousData,
  useQuery,
  UseQueryResult,
} from '@tanstack/react-query'
import { getListEntities } from '@/api/api'
import { GenericCampaignTier } from '@/types/entity/GenericCampaignTier'

export const useListPatreonCampaignTiers = ({
  id,
}: {
  id: string
}): UseQueryResult<GenericCampaignTier[]> => {
  return useQuery<GenericCampaignTier[]>({
    queryKey: [`/api/patreon_campaigns/${id}/tiers`],
    queryFn: () =>
      getListEntities<GenericCampaignTier>(
        `/api/patreon_campaigns/${id}/tiers`,
      ),
    placeholderData: keepPreviousData,
    staleTime: 60 * 1000,
    enabled: id !== '',
  })
}
