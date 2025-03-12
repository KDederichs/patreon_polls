import { toast } from 'react-toastify'
import { ApiError } from '@/types/ApiError'
import { AxiosError } from 'axios'

export const showApiError = (error: AxiosError<ApiError<any>>) => {
  const errorMessages = error.response?.data.violations
  if (errorMessages !== undefined && errorMessages.length > 0) {
    errorMessages.forEach((violation) => {
      toast.error(violation.message)
    })
  } else {
    toast.error(error.response?.data.detail ?? error.message)
  }
}
