import { AuthTokenResponse } from '@/types/AuthTokenResponse'
import { AxiosError } from 'axios'
import { useMutation, UseMutationResult } from '@tanstack/react-query'
import { LoginInput } from '@/types/mutations/User/LoginInput'
import { ResultHandlerInterface } from '@/hooks/mutation/ResultHandlerInterface'
import { loginPatreon } from '@/api/api'
import { ApiError } from '@/types/ApiError'

export const useLogin = ({
                           onSuccess,
                           onError,
                            provider
                         }: ResultHandlerInterface<
  AuthTokenResponse,
  AxiosError<ApiError<string>>> & {provider: string|null}): UseMutationResult<
  AuthTokenResponse,
  AxiosError<ApiError<string>>,
  LoginInput
> => {
  return useMutation<
    AuthTokenResponse,
    AxiosError<ApiError<string>>,
    LoginInput
  >({
    mutationKey: ['user', 'login'],
    mutationFn: provider === 'patreon' ? loginPatreon : loginPatreon,
    onSuccess,
    onError,
  })
}
