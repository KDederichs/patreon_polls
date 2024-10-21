"use client"

import React, { useState } from 'react'
import {Spinner} from "@nextui-org/spinner";
import { useRouter, useSearchParams } from 'next/navigation'
import { useLogin } from '@/hooks/mutation/User/useLogin'
import { toast } from 'react-toastify'
import { setIsPatreonCreator, setIsSubscribeStarCreator, setToken } from '@/state/userState'

export default function LoginCheckPage() {

  const searchParams = useSearchParams()
  const code = searchParams.get('code')
  const provider = searchParams.get('provider')
  const [authSuccess, setAuthSuccess] = useState<boolean|undefined>(undefined)
  const router = useRouter()

  const getToken = useLogin({
    provider,
    onSuccess: (tokenResponse) => {
      setToken(tokenResponse.token)
      setIsPatreonCreator(tokenResponse.isPatreonCreator)
      setIsSubscribeStarCreator(tokenResponse.isSubscribestarCreator)
      setAuthSuccess(true)
      router.push('/user/polls')
    },
    onError: (error) => {
      console.log(error.response)
      toast.error(error.response?.data.detail ?? 'An error has occurred.')
      setAuthSuccess(false)
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
  },[code])

  return (
    <div className="flex h-full w-full items-center justify-center">
      <div className="flex w-full max-w-md flex-col gap-4 rounded-large bg-content1 px-8 pb-10 pt-6 shadow-small">
        {authSuccess === undefined ? (
          <>
            <p className="pb-2 text-xl font-medium">Please wait while we authenticate you...</p>
            <div className="flex flex-col w-full justify-center">
              <Spinner size="lg" />
            </div>
          </>
        ) : null}
        {authSuccess === true ? (
          <>
            <p className="pb-2 text-xl font-medium">Authentication successful, you should be redirected now.</p>
          </>
        ) : null
          }
        {authSuccess === false ? (
          <>
            <p className="pb-2 text-xl font-medium">Error during authentication, please try again.</p>
          </>
        ) :  null}
      </div>
    </div>
  )
}
