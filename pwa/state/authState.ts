"use client"

import { create } from 'zustand'
import { immer } from 'zustand/middleware/immer'
import { devtools, persist } from 'zustand/middleware'// required for devtools typing


interface AuthState {
  token: string | null
  userIri: string | null,
  updateState: (props: Partial<AuthState>) => void;
}

export const userStore = create<AuthState>()(
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

export const getToken = () => userStore.getState().token
export const getUserIri = () => userStore.getState().userIri
export const userStoreHasHydrated = () => userStore.persist?.hasHydrated()
export const setToken = (token:string|null) => userStore.getState().updateState({ token })
export const setUserIri = (iri: string|null) => userStore.getState().updateState({ userIri: iri })
