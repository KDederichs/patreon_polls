import { ResultHandlerInterface } from '@/hooks/mutation/ResultHandlerInterface'
import { AxiosError } from 'axios'
import { ApiError } from '@/types/ApiError'
import { useMutation, UseMutationResult } from '@tanstack/react-query'
import { createEntity } from '@/api/api'
import { PollOption } from '@/types/entity/PollOption'
import { CreatePollOptionInput } from '@/types/mutations/PollOption/CreatePollOptionInput'
import { useAuthStore } from '@/state/authState'

export const useCreatePollOption = ({
  onSuccess,
  onError,
}: ResultHandlerInterface<
  PollOption,
  AxiosError<ApiError<CreatePollOptionInput>>
>): UseMutationResult<
  PollOption,
  AxiosError<ApiError<CreatePollOptionInput>>,
  CreatePollOptionInput
> => {
  return useMutation<
    PollOption,
    AxiosError<ApiError<CreatePollOptionInput>>,
    CreatePollOptionInput
  >({
    mutationKey: ['poll_option', 'create'],
    mutationFn: (payload) =>
      createEntity<PollOption>('/api/poll_options', payload),
    onSuccess,
    onError,
  })
}
