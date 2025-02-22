import { AxiosError } from 'axios'
import { useMutation, UseMutationResult } from '@tanstack/react-query'
import { ResultHandlerInterface } from '@/hooks/mutation/ResultHandlerInterface'
import { logout } from '@/api/api'
import { ApiError } from '@/types/ApiError'

export const useLogout = ({
                                  onSuccess,
                                  onError,
                                }: ResultHandlerInterface<
  void,
  AxiosError<ApiError<string>>>): UseMutationResult<
  void,
  AxiosError<ApiError<string>>,
  void
> => {
  return useMutation<
    void,
    AxiosError<ApiError<string>>,
    void
  >({
    mutationKey: ['user', 'logout'],
    mutationFn: logout,
    onSuccess,
    onError,
  })
}
