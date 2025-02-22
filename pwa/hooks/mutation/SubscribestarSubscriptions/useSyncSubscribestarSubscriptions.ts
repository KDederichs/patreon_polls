import { AxiosError } from 'axios'
import { ApiError } from '@/types/ApiError'
import {
  useMutation,
  UseMutationResult,
  useQueryClient,
} from '@tanstack/react-query'
import { syncSubscribestarSubscriptions } from '@/api/api'
import { toast } from 'react-toastify'

export const useSyncSubscribestarSubscriptions = (): UseMutationResult<
  void,
  AxiosError<ApiError<string>>,
  void
> => {
  const queryClient = useQueryClient()

  return useMutation<void, AxiosError<ApiError<string>>, void>({
    mutationKey: ['subscribestar', 'sync', 'subscriptions'],
    mutationFn: () => syncSubscribestarSubscriptions(),
    onSuccess: () => {
      toast.success(
        'Subscription syncing has started, you should soon have access to your subscriptions.',
      )
    },
    onError: (error) => {
      toast.error(error.response?.data.detail ?? 'An error has occurred.')
    },
  })
}
