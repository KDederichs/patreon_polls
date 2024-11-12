"use client"

import { create } from 'zustand'
import { immer } from 'zustand/middleware/immer'
import { devtools, persist } from 'zustand/middleware'// required for devtools typing


interface AuthState {
  token: string | null
  userIri: string | null,
  updateState: (props: Partial<AuthState>) => void;
}

export const useAuthStore = create<AuthState>()(
  persist(
    immer(
      (set,get)=> ({
          token: null,
          userIri: null,
          updateState: (props) => {
            set({
              ...get(),
              ...props
            })
          }
        })
    ),
    {
      name: 'user-storage',
    },
  )
)

export const getToken = () => useAuthStore.getState().token
export const getUserIri = () => useAuthStore.getState().userIri
export const userStoreHasHydrated = () => useAuthStore.persist?.hasHydrated()
export const setToken = (token:string|null) => useAuthStore.getState().updateState({ token })
export const setUserIri = (iri: string|null) => useAuthStore.getState().updateState({ userIri: iri })
