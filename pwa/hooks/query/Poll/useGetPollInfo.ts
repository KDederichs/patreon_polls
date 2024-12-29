import {
  keepPreviousData,
  useQuery,
  UseQueryResult,
} from '@tanstack/react-query'
import { getEntityByIri } from '@/api/api'
import { Poll } from '@/types/entity/Poll'

export const useGetPollInfo = ({
  pollId,
}: {
  pollId: string
}): UseQueryResult<Poll> => {
  return useQuery<Poll>({
    queryKey: [`/api/polls/${pollId}`],
    queryFn: () => getEntityByIri<Poll>(`/api/polls/${pollId}`),
    placeholderData: keepPreviousData,
    staleTime: 60 * 1000,
    enabled: pollId !== '',
  })
}
