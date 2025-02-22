import { ResultHandlerInterface } from '@/hooks/mutation/ResultHandlerInterface'
import { AxiosError } from 'axios'
import { ApiError } from '@/types/ApiError'
import { useMutation, UseMutationResult } from '@tanstack/react-query'
import { createEntityMutliPart } from '@/api/api'
import { MediaObject } from '@/types/entity/MediaObject'

export const useUploadImage = ({
  onSuccess,
  onError,
}: ResultHandlerInterface<
  MediaObject,
  AxiosError<ApiError<FormData>>
>): UseMutationResult<
  MediaObject,
  AxiosError<ApiError<FormData>>,
  FormData
> => {
  return useMutation<MediaObject, AxiosError<ApiError<FormData>>, FormData>({
    mutationKey: ['media_object', 'create'],
    mutationFn: (payload) =>
      createEntityMutliPart<MediaObject>('/api/media_objects', payload),
    onSuccess,
    onError,
  })
}
