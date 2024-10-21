import axios from "axios";
import {LoginInput} from "@/types/mutations/User/LoginInput";
import {AuthTokenResponse} from "@/types/AuthTokenResponse";
import { getToken } from '@/state/userState'
import { CreatorConversionResponse } from '@/types/mutations/User/CreatorConversionResponse'

const publicAxiosInstance = axios.create({
  headers: {
    Accept: 'application/json',
    'Content-Type': 'application/json',
  },
})

const privateAxiosInstance = axios.create({
  headers: {
    Accept: 'application/ld+json',
    'Content-Type': 'application/json',
  },
})
// Request interceptor for API calls
privateAxiosInstance.interceptors.request.use(
  async (config) => {
    const accessToken = getToken()
    if (null !== accessToken) {
      config.headers['Authorization'] = `Bearer ${accessToken}`
    }
    return config
  },
  (error) => {
    void Promise.reject(error)
  },
)

export const loginPatreon = async ({
                                     code,
                                   }: LoginInput): Promise<AuthTokenResponse> => {
  return publicAxiosInstance
    .get<AuthTokenResponse>(`/oauth/patreon?code=${code}&state=None`)
    .then((response) => response.data)
}

export const convertPatreonCreator = async ({
                                     code,
                                   }: LoginInput): Promise<CreatorConversionResponse> => {
  return privateAxiosInstance
    .get<CreatorConversionResponse>(`/creator/oauth/patreon?code=${code}&state=None`, {
      headers: {
        Accept: 'application/json'
      }
    })
    .then((response) => response.data)
}

export const connectPatreon = async ({
                                              code,
                                            }: LoginInput): Promise<AuthTokenResponse> => {
  return privateAxiosInstance
    .get<AuthTokenResponse>(`/oauth/patreon?code=${code}&state=None`, {
      headers: {
        Accept: 'application/json'
      }
    })
    .then((response) => response.data)
}
