import {
  keepPreviousData,
  useQuery,
  UseQueryResult,
} from '@tanstack/react-query'
import { ListResponse } from '@/types/ListResponse'
import { getHydraList } from '@/api/api'
import { OauthUser } from '@/types/entity/OauthUser'

export const useListSubscribestarUser = (): UseQueryResult<
  ListResponse<OauthUser>
> => {
  return useQuery<ListResponse<OauthUser>>({
    queryKey: ['/api/subscribestar_users'],
    queryFn: () => getHydraList<OauthUser>('/api/subscribestar_users'),
    placeholderData: keepPreviousData,
    staleTime: 3600 * 1000,
  })
}
