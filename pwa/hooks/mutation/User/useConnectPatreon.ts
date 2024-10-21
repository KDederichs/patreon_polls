
import { AxiosError } from 'axios'
import { useMutation, UseMutationResult } from '@tanstack/react-query'
import { LoginInput } from '@/types/mutations/User/LoginInput'
import { ResultHandlerInterface } from '@/hooks/mutation/ResultHandlerInterface'
import { connectPatreon, convertPatreonCreator } from '@/api/api'
import { ApiError } from '@/types/ApiError'
import { CreatorConversionResponse } from '@/types/mutations/User/CreatorConversionResponse'
import { AuthTokenResponse } from '@/types/AuthTokenResponse'

export const useConnectPatreon = ({
                           onSuccess,
                           onError,
                         }: ResultHandlerInterface<
  AuthTokenResponse,
  AxiosError<ApiError<string>>>): UseMutationResult<
  AuthTokenResponse,
  AxiosError<ApiError<string>>,
  LoginInput
> => {
  return useMutation<
    AuthTokenResponse,
    AxiosError<ApiError<string>>,
    LoginInput
  >({
    mutationKey: ['user', 'connect', 'patreon'],
    mutationFn: connectPatreon,
    onSuccess,
    onError,
  })
}
