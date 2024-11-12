"use client"
import { Spinner } from '@nextui-org/spinner'
import { useLogout } from '@/hooks/mutation/User/useLogout'
import { toast } from 'react-toastify'
import { useEffect } from 'react'
import { setToken, setUserIri } from '@/state/authState'


export default function LogoutPate() {
  const logoutMutator = useLogout({
    onSuccess: () => {
      setToken(null)
      setUserIri(null)
    },
    onError: (error) => {
      toast.error(error.response?.data.detail ?? 'An error has occurred.')
    }
  })

  useEffect(() => {
    logoutMutator.mutate()
  },[])

  return (
    <div className="flex h-full w-full items-center justify-center">
      <div className="flex w-full max-w-md flex-col gap-4 rounded-large bg-content1 px-8 pb-10 pt-6 shadow-small">
        <>
          <p className="pb-2 text-xl font-medium">Logout in progress, you will be redirected...</p>
          <div className="flex flex-col w-full justify-center">
            <Spinner size="lg" />
          </div>
        </>
      </div>
    </div>
  )
}
