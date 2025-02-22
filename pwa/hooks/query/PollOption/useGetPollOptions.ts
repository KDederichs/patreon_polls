import {
  keepPreviousData,
  useQuery,
  UseQueryResult,
} from '@tanstack/react-query'
import { getListEntities } from '@/api/api'
import { PollOption } from '@/types/entity/PollOption'

export const useGetPollOptions = ({
  pollId,
}: {
  pollId: string
}): UseQueryResult<PollOption[]> => {
  return useQuery<PollOption[]>({
    queryKey: ['list', `/api/polls/${pollId}/options`],
    queryFn: () => getListEntities<PollOption>(`/api/polls/${pollId}/options`),
    placeholderData: keepPreviousData,
    enabled: pollId !== '',
  })
}
