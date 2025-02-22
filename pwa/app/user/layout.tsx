"use client"

import React, { useEffect } from 'react'
import { getToken, useAuthStore, userStoreHasHydrated } from '@/state/authState'
import { useRouter } from 'next/navigation'
import { useGetCurrentUser } from '@/hooks/query/User/useGetCurrentUser'

export default function SettingsLayout({
                                     children,
                                   }: {
  children: React.ReactNode;
}) {

  const isAuthenticated = useAuthStore((state) => state.token !== null)
  const isHydrated = userStoreHasHydrated()
  const router = useRouter()

  const userFetcher = useGetCurrentUser();

  useEffect(() => {
    if (isHydrated && !isAuthenticated) {
      router.push('/login')
    }

    if (isHydrated && isAuthenticated) {
      userFetcher.refetch()
    }
  }, [isHydrated, isAuthenticated])


  return (
    <>
      {children}
    </>
  )

}
