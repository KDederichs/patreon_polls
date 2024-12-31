import { AxiosError } from 'axios'
import {
  useMutation,
  UseMutationResult,
  useQueryClient,
} from '@tanstack/react-query'

import { toast } from 'react-toastify'
import { createEntity } from '@/api/api'
import { ApiError } from '@/types/ApiError'
import { GenericHydraItem } from '@/types/GenericHydraItem'

export const useCreateEntity = <T extends GenericHydraItem, K>({
  iri,
  onError = undefined,
  onSuccess = undefined,
}: {
  iri: string
  onError?: (error: AxiosError<ApiError<K>>) => void
  onSuccess?: (data: T) => void
}): UseMutationResult<T, AxiosError<ApiError<K>>, K> => {
  const queryClient = useQueryClient()
  return useMutation<T, AxiosError<ApiError<K>>, K>({
    mutationKey: ['create', iri],
    mutationFn: (data) => createEntity(iri, data),
    onSuccess: (created) => {
      queryClient.setQueryData(
        ['list', iri],
        (oldData: Array<GenericHydraItem> | undefined) => {
          if (undefined === oldData) {
            return [created]
          }
          return [...oldData, created]
        },
      )
      queryClient.setQueryData([created['@id']], created)
      if (onSuccess !== undefined) {
        onSuccess(created)
      }
    },
    onError: (e) => {
      if (onError !== undefined) {
        onError(e)
      } else {
        toast.error(e.message)
      }
    },
  })
}
