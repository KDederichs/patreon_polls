'use client'

import React, { useState } from 'react'
import { Spinner } from '@nextui-org/spinner'
import { useRouter, useSearchParams } from 'next/navigation'
import { useConnectOAuth } from '@/hooks/mutation/User/useConnectOAuth'
import { toast } from 'react-toastify'
import { setToken, setUserIri } from '@/state/authState'
import { useListPatreonUsers } from '@/hooks/query/PatreonUser/useListPatreonUsers'
import { useListSubscribestarUser } from '@/hooks/query/SubscribestarUser/useListSubscribestarUser'

export default function LoginCheckPage() {
  const searchParams = useSearchParams()
  const { refetch: refetchPatreonUsers } = useListPatreonUsers()
  const { refetch: refetchSubscribestarUsers } = useListSubscribestarUser()
  const code = searchParams.get('code')
  const state = searchParams.get('state')
  const [authSuccess, setAuthSuccess] = useState<boolean | undefined>(undefined)
  const [mode, setMode] = useState<'login' | 'connect' | undefined>(undefined)
  const router = useRouter()

  const getToken = useConnectOAuth({
    onSuccess: (tokenResponse) => {
      setToken(tokenResponse.token)
      setUserIri(tokenResponse.userIri)
      setMode(tokenResponse.mode)
      if (tokenResponse.mode === 'login') {
        setAuthSuccess(true)
        router.push('/user/polls')
      } else {
        setAuthSuccess(true)
        void refetchPatreonUsers()
        void refetchSubscribestarUsers()
        router.push('/user/settings')
      }
    },
    onError: (error) => {
      toast.error(error.response?.data.detail ?? 'An error has occurred.')
      setAuthSuccess(false)
    },
  })

  React.useEffect(() => {
    if (code !== null && state !== null) {
      if (!getToken.isPending && !getToken.isError) {
        getToken.mutate({
          code,
          state,
        })
      }
    }
  }, [code, state])

  return (
    <div className="flex h-full w-full items-center justify-center">
      <div className="flex w-full max-w-md flex-col gap-4 rounded-large bg-content1 px-8 pb-10 pt-6 shadow-small">
        {authSuccess === undefined ? (
          <>
            <p className="pb-2 text-xl font-medium">
              Please wait while we authenticate you and do not close this
              window...
            </p>
            <div className="flex w-full flex-col justify-center">
              <Spinner size="lg" />
            </div>
          </>
        ) : null}
        {authSuccess === true ? (
          <>
            {mode === 'login' ? (
              <p className="pb-2 text-xl font-medium">
                Authentication successful, you should be redirected now.
              </p>
            ) : null}
            {mode === 'connect' ? (
              <p className="pb-2 text-xl font-medium">
                Your account has been connected. You can close this window now.
              </p>
            ) : null}
          </>
        ) : null}
        {authSuccess === false ? (
          <>
            <p className="pb-2 text-xl font-medium">
              There has been an error, please try again.
            </p>
          </>
        ) : null}
      </div>
    </div>
  )
}
