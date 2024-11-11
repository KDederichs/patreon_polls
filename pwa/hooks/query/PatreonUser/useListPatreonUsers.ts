import { keepPreviousData, useQuery, UseQueryResult } from '@tanstack/react-query'
import { ListResponse } from '@/types/ListResponse'
import { getHydraList } from '@/api/api'
import { PatreonUser } from '@/types/entity/PatreonUser'

export const useListPatreonUsers = (): UseQueryResult<ListResponse<PatreonUser>> => {
  return useQuery<ListResponse<PatreonUser>>({
    queryKey: ['/patreon_users'],
    queryFn: () => getHydraList<PatreonUser>('/patreon_users'),
    placeholderData: keepPreviousData,
    staleTime: 3600 * 1000
  })
}
