"use client"

import React, { useEffect } from 'react'
import { getToken, userStoreHasHydrated } from '@/state/userState'
import { useRouter } from 'next/navigation'

export default function SettingsLayout({
                                     children,
                                   }: {
  children: React.ReactNode;
}) {

  const isAuthenticated = getToken() !== null
  const isHydrated = userStoreHasHydrated()
  const router = useRouter()

  useEffect(() => {
    if (isHydrated && !isAuthenticated) {
      router.push('/login')
    }
  }, [isHydrated, isAuthenticated])

  return (
    <>
      {children}
    </>
  )

}
