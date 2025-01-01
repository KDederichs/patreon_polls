import {
  keepPreviousData,
  useQuery,
  UseQueryResult,
} from '@tanstack/react-query'
import { getListEntities } from '@/api/api'
import { PollVote } from '@/types/entity/PollVote'
import { useAuthStore } from '@/state/authState'

export const useGetMyVotes = ({
  pollId,
}: {
  pollId: string
}): UseQueryResult<PollVote[]> => {
  const isAuthenticated = useAuthStore((state) => state.token !== null)
  return useQuery<PollVote[]>({
    queryKey: ['list', `/api/polls/${pollId}/my-votes`],
    queryFn: () => getListEntities<PollVote>(`/api/polls/${pollId}/my-votes`),
    placeholderData: keepPreviousData,

    enabled: pollId !== '' || !isAuthenticated,
  })
}
