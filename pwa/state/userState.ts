"use client"

import { create } from 'zustand'
import { immer } from 'zustand/middleware/immer'
import { devtools, persist } from 'zustand/middleware'// required for devtools typing


interface UserState {
  token: string | null
  isPatreonCreator: boolean
  isSubscribestarCreator: boolean
  setToken: (token: string| null) => void
  setPatreonCreatorStatus: (status: boolean) => void
  setSubscribestarCreatorStatus: (status: boolean) => void
}

export const userStore = create<UserState>()(
  persist(
    immer(
      (set)=> ({
          token: null,
          isPatreonCreator: false,
          isSubscribestarCreator: false,
          setToken: (newToken) => set(
            (state) => {
              state.token = newToken
            }
          ),
          setPatreonCreatorStatus: (newState) => set(
            (state) => {
              state.isPatreonCreator = newState
            }
          ),
          setSubscribestarCreatorStatus: (newState) => set(
            (state) => {
              state.isSubscribestarCreator = newState
            }
          )
        })
    ),
    {
      name: 'user-storage',
    },
  )
)

export const getToken = () => userStore((state) => state.token)
export const setToken = (token:string|null) => userStore.getState().setToken(token)
export const setIsPatreonCreator = (isCreator: boolean) => userStore.getState().setPatreonCreatorStatus(isCreator)
export const setIsSubscribeStarCreator = (isCreator: boolean) => userStore.getState().setSubscribestarCreatorStatus(isCreator)
