import { ResultHandlerInterface } from '@/hooks/mutation/ResultHandlerInterface'
import { AxiosError } from 'axios'
import { ApiError } from '@/types/ApiError'
import { useMutation, UseMutationResult } from '@tanstack/react-query'
import { createEntity } from '@/api/api'
import { PollOption } from '@/types/entity/PollOption'
import { CreatePollOptionInput } from '@/types/mutations/PollOption/CreatePollOptionInput'
import { useAuthStore } from '@/state/authState'
import { ConnectRequestResponse } from '@/types/mutations/Oauth/ConnectRequestResponse'
import { ConnectRequestInput } from '@/types/mutations/Oauth/ConnectRequestInput'

export const useConnectResource = ({
  onSuccess,
  onError,
}: ResultHandlerInterface<
  ConnectRequestResponse,
  AxiosError<ApiError<ConnectRequestInput>>
>): UseMutationResult<
  ConnectRequestResponse,
  AxiosError<ApiError<ConnectRequestInput>>,
  ConnectRequestInput
> => {
  return useMutation<
    ConnectRequestResponse,
    AxiosError<ApiError<ConnectRequestInput>>,
    ConnectRequestInput
  >({
    mutationKey: ['connect', 'oauth', 'resource'],
    mutationFn: (payload) =>
      createEntity<ConnectRequestResponse>(payload.uri, payload),
    onSuccess,
    onError,
  })
}
