import { AxiosError } from 'axios'
import { ApiError } from '@/types/ApiError'
import { useMutation, UseMutationResult, useQueryClient } from '@tanstack/react-query'
import {  syncPatreon } from '@/api/api'
import { toast } from 'react-toastify'

export const useSyncPatreonCampaigns = (): UseMutationResult<
  void,
  AxiosError<ApiError<string>>,
  void
> => {

  const queryClient = useQueryClient();

  return useMutation<
    void,
    AxiosError<ApiError<string>>,
    void
  >({
    mutationKey: ['patreon', 'sync'],
    mutationFn: syncPatreon,
    onSuccess: () => {
      queryClient.refetchQueries({
        queryKey: ['/api/patreon_campaigns']
      })
    },
    onError: (error) => {
      toast.error(error.response?.data.detail ?? 'An error has occurred.')
    },
  })
}
