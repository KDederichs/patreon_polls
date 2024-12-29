import { AxiosError } from 'axios'
import { useMutation, UseMutationResult } from '@tanstack/react-query'
import { ResultHandlerInterface } from '@/hooks/mutation/ResultHandlerInterface'
import { createEntity } from '@/api/api'
import { ApiError } from '@/types/ApiError'
import { Poll } from '@/types/entity/Poll'
import { PollCreateInput } from '@/types/mutations/Poll/PollCreateInput'

export const useCreatePoll = ({
  onSuccess,
  onError,
}: ResultHandlerInterface<
  Poll,
  AxiosError<ApiError<PollCreateInput>>
>): UseMutationResult<
  Poll,
  AxiosError<ApiError<PollCreateInput>>,
  PollCreateInput
> => {
  return useMutation<
    Poll,
    AxiosError<ApiError<PollCreateInput>>,
    PollCreateInput
  >({
    mutationKey: ['poll', 'create'],
    mutationFn: (payload) => createEntity<Poll>('/api/polls', payload),
    onSuccess,
    onError,
  })
}
