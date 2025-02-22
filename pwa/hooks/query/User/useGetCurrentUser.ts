import { useQuery, UseQueryResult } from '@tanstack/react-query'
import { User } from '@/types/entity/User'
import { getUserIri } from '@/state/authState'
import { getEntityByIri } from '@/api/api'

export const useGetCurrentUser = (): UseQueryResult<User> => {
  const userIri = getUserIri()
  return useQuery<User>({
    queryKey: [userIri],
    queryFn: () => getEntityByIri<User>(userIri!),
    enabled: userIri !== null,
    staleTime: 3600 * 1000
  })
}
