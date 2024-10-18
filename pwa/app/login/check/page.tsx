"use client"

import React from 'react'
import {Spinner} from "@nextui-org/spinner";
import { useSearchParams } from 'next/navigation'
import { useLogin } from '@/hooks/mutation/User/useLogin'
import { toast } from 'react-toastify'

export default function LoginCheckPage() {

  const searchParams = useSearchParams()
  const code = searchParams.get('code')
  const provider = searchParams.get('provider')

  const getToken = useLogin({
    provider,
    onSuccess: (tokenResponse) => {
      window.localStorage.setItem('token', tokenResponse.token)
      toast.success(tokenResponse.token)
    },
    onError: (error) => {
      toast.error(error.response?.data.description ?? 'An error has occurred.')
    }
  })

  React.useEffect(() => {
    if (code !== null) {
      if (!getToken.isPending && !getToken.isError) {
        getToken.mutate({
          code
        })
      }
    }
  },[code, getToken])

  return (
    <div className="flex h-full w-full items-center justify-center">
      <div className="flex w-full max-w-md flex-col gap-4 rounded-large bg-content1 px-8 pb-10 pt-6 shadow-small">
        <p className="pb-2 text-xl font-medium">Please wait while we authenticate you...</p>
        <div className="flex flex-col w-full justify-center">
          <Spinner size="lg" />
        </div>
      </div>
    </div>
  )
}
