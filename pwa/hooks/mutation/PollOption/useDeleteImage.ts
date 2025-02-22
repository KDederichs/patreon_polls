import { ResultHandlerInterface } from '@/hooks/mutation/ResultHandlerInterface'
import { AxiosError } from 'axios'
import { ApiError } from '@/types/ApiError'
import { useMutation, UseMutationResult } from '@tanstack/react-query'
import { deleteEntity } from '@/api/api'

export const useDeleteImage = ({
  onSuccess,
  onError,
}: ResultHandlerInterface<void, AxiosError<ApiError<void>>>): UseMutationResult<
  void,
  AxiosError<ApiError<void>>,
  string
> => {
  return useMutation<void, AxiosError<ApiError<void>>, string>({
    mutationKey: ['media_object', 'delete'],
    mutationFn: (iri) => deleteEntity(iri),
    onSuccess,
    onError,
  })
}
