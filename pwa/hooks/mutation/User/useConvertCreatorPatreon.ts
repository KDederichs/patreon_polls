
import { AxiosError } from 'axios'
import { useMutation, UseMutationResult } from '@tanstack/react-query'
import { LoginInput } from '@/types/mutations/User/LoginInput'
import { ResultHandlerInterface } from '@/hooks/mutation/ResultHandlerInterface'
import { convertPatreonCreator } from '@/api/api'
import { ApiError } from '@/types/ApiError'
import { CreatorConversionResponse } from '@/types/mutations/User/CreatorConversionResponse'

export const useConvertCreatorPatreon = ({
                           onSuccess,
                           onError,
                         }: ResultHandlerInterface<
  CreatorConversionResponse,
  AxiosError<ApiError<string>>>): UseMutationResult<
  CreatorConversionResponse,
  AxiosError<ApiError<string>>,
  LoginInput
> => {
  return useMutation<
    CreatorConversionResponse,
    AxiosError<ApiError<string>>,
    LoginInput
  >({
    mutationKey: ['user', 'enable_patreon_creator'],
    mutationFn: convertPatreonCreator,
    onSuccess,
    onError,
  })
}
