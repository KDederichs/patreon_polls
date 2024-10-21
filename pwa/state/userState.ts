"use client"

import { create } from 'zustand'
import { immer } from 'zustand/middleware/immer'
import { devtools, persist } from 'zustand/middleware'// required for devtools typing


interface UserState {
  token: string | null
  patreonUsername: string | null,
  subscribestarUsername: string | null,
  isPatreonCreator: boolean
  isSubscribestarCreator: boolean
  updateState: (props: Partial<UserState>) => void;
}

export const userStore = create<UserState>()(
  persist(
    immer(
      (set,get)=> ({
          token: null,
          isPatreonCreator: false,
          isSubscribestarCreator: false,
          subscribestarUsername: null,
          patreonUsername: null,
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

export const getToken = () => userStore((state) => state.token)
export const isPatreonCreator = () => userStore.getState().isPatreonCreator
export const isSubscribestartCreator = () => userStore.getState().isSubscribestarCreator
export const getPatreonUsername = () => userStore.getState().patreonUsername
export const getSubscribestarUsername = () => userStore.getState().subscribestarUsername
export const userStoreHasHydrated = () => userStore.persist.hasHydrated()
export const setToken = (token:string|null) => userStore.getState().updateState({ token })
export const setIsPatreonCreator = (isCreator: boolean) => userStore.getState().updateState({isPatreonCreator : isCreator})
export const setIsSubscribeStarCreator = (isCreator: boolean) => userStore.getState().updateState({ isPatreonCreator: isCreator })
export const setPatreonUsername = (username: string|null) => userStore.getState().updateState({ patreonUsername: username })
export const setSubscribestarUsername = (username: string|null) => userStore.getState().updateState({ subscribestarUsername: username })
