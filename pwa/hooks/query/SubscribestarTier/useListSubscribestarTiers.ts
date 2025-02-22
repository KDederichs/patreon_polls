import {
  keepPreviousData,
  useQuery,
  UseQueryResult,
} from '@tanstack/react-query'
import { getListEntities } from '@/api/api'
import { GenericCampaignTier } from '@/types/entity/GenericCampaignTier'

export const useListSubscribestarTiers = ({
  id,
}: {
  id: string
}): UseQueryResult<GenericCampaignTier[]> => {
  return useQuery<GenericCampaignTier[]>({
    queryKey: [`/api/subscribestar_users/${id}/tiers`],
    queryFn: () =>
      getListEntities<GenericCampaignTier>(
        `/api/subscribestar_users/${id}/tiers`,
      ),
    placeholderData: keepPreviousData,
    staleTime: 60 * 1000,
    enabled: id !== '',
  })
}
