import { AxiosError } from 'axios'
import { ApiError } from '@/types/ApiError'
import {
  useMutation,
  UseMutationResult,
  useQueryClient,
} from '@tanstack/react-query'
import { syncSubscribestar } from '@/api/api'
import { toast } from 'react-toastify'

export const useSyncSubscribestarTiers = ({
  subscribestarUserId,
}: {
  subscribestarUserId: string
}): UseMutationResult<void, AxiosError<ApiError<string>>, void> => {
  const queryClient = useQueryClient()

  return useMutation<void, AxiosError<ApiError<string>>, void>({
    mutationKey: ['subscribestar', 'sync'],
    mutationFn: () => syncSubscribestar(),
    onSuccess: () => {
      queryClient.refetchQueries({
        queryKey: [`/api/subscribestar_users/${subscribestarUserId}/tiers`],
      })
    },
    onError: (error) => {
      toast.error(error.response?.data.detail ?? 'An error has occurred.')
    },
  })
}
