import { ResultHandlerInterface } from '@/hooks/mutation/ResultHandlerInterface'
import { AxiosError } from 'axios'
import { ApiError } from '@/types/ApiError'
import { useMutation, UseMutationResult } from '@tanstack/react-query'
import { deleteEntity } from '@/api/api'

export const useDeletePollVote = ({
  onSuccess,
  onError,
}: ResultHandlerInterface<void, AxiosError<ApiError<void>>>): UseMutationResult<
  void,
  AxiosError<ApiError<void>>,
  string
> => {
  return useMutation<void, AxiosError<ApiError<void>>, string>({
    mutationKey: ['poll_vote', 'delete'],
    mutationFn: (iri) => deleteEntity(iri),
    onSuccess,
    onError,
  })
}
