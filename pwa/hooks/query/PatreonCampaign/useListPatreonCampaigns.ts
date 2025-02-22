import { keepPreviousData, useQuery, UseQueryResult } from '@tanstack/react-query'
import { getListEntities } from '@/api/api'
import { PatreonCampaign } from '@/types/entity/PatreonCampaign'

export const useListPatreonCampaigns = (
  {enabled}: {enabled: boolean}
): UseQueryResult<PatreonCampaign[]> => {
  return useQuery<PatreonCampaign[]>({
    queryKey: ['/api/patreon_campaigns'],
    queryFn: () => getListEntities<PatreonCampaign>('/api/patreon_campaigns'),
    placeholderData: keepPreviousData,
    staleTime: 60 * 1000,
    enabled
  })
}
