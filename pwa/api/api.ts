import axios from "axios";
import {LoginInput} from "@/types/mutations/User/LoginInput";
import {AuthTokenResponse} from "@/types/AuthTokenResponse";
import { getToken } from '@/state/userState'

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
    .get<AuthTokenResponse>(`/oauth/check-patreon?code=${code}&state=None`)
    .then((response) => response.data)
}
