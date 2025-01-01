import { ResultHandlerInterface } from '@/hooks/mutation/ResultHandlerInterface'
import { AxiosError } from 'axios'
import { ApiError } from '@/types/ApiError'
import { useMutation, UseMutationResult } from '@tanstack/react-query'
import { createEntity } from '@/api/api'
import { PollVote } from '@/types/entity/PollVote'
import { CreatePollVoteInput } from '@/types/mutations/PollVote/CreatePollVoteInput'

export const useCreatePollVote = ({
  onSuccess,
  onError,
}: ResultHandlerInterface<
  PollVote,
  AxiosError<ApiError<CreatePollVoteInput>>
>): UseMutationResult<
  PollVote,
  AxiosError<ApiError<CreatePollVoteInput>>,
  CreatePollVoteInput
> => {
  return useMutation<
    PollVote,
    AxiosError<ApiError<CreatePollVoteInput>>,
    CreatePollVoteInput
  >({
    mutationKey: ['poll_vote', 'create'],
    mutationFn: (payload) => createEntity<PollVote>('/api/poll_votes', payload),
    onSuccess,
    onError,
  })
}
