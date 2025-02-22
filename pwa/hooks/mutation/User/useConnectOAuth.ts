import { AuthTokenResponse } from '@/types/AuthTokenResponse'
import { AxiosError } from 'axios'
import { useMutation, UseMutationResult } from '@tanstack/react-query'
import { ResultHandlerInterface } from '@/hooks/mutation/ResultHandlerInterface'
import { oauthConnect } from '@/api/api'
import { ApiError } from '@/types/ApiError'
import { OauthInput } from '@/types/mutations/OauthInput'

export const useConnectOAuth = ({
                           onSuccess,
                           onError,
                         }: ResultHandlerInterface<
  AuthTokenResponse,
  AxiosError<ApiError<string>>>): UseMutationResult<
  AuthTokenResponse,
  AxiosError<ApiError<string>>,
  OauthInput
> => {
  return useMutation<
    AuthTokenResponse,
    AxiosError<ApiError<string>>,
    OauthInput
  >({
    mutationKey: ['oauth', 'connect'],
    mutationFn: oauthConnect,
    onSuccess,
    onError,
  })
}
