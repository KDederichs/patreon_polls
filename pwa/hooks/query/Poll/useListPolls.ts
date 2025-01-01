import {
  keepPreviousData,
  useQuery,
  UseQueryResult,
} from '@tanstack/react-query'
import { getListEntities } from '@/api/api'
import { PatreonCampaignTier } from '@/types/entity/PatreonCampaignTier'
import { Poll } from '@/types/entity/Poll'

export const useListPolls = (): UseQueryResult<Poll[]> => {
  return useQuery<Poll[]>({
    queryKey: ['list', '/api/polls'],
    queryFn: () => getListEntities<Poll>('/api/polls'),
    placeholderData: keepPreviousData,
    staleTime: 60 * 1000,
  })
}
